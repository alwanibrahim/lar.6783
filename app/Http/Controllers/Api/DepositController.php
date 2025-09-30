<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\AffiliateCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $deposits = $request->user()->deposits()->latest()->paginate(15);
        return response()->json($deposits);
    }

    public function store(Request $request)
    {
        $request->validate([
            'method'  => 'required|string',
            'amount'  => 'required|numeric|min:1000',
        ]);

        $user = Auth::user();

        $merchantRef = 'DEP-' . $user->id . '-' . time(); // generate otomatis
        $customerPhone = $user->phone ?? '081111111111';

        // generate signature
        $merchantCode = env('TRIPAY_MERCHANT');
        $privateKey   = env('TRIPAY_PRIVATE_KEY');
        $signature    = hash_hmac('sha256', $merchantCode . $merchantRef . $request->amount, $privateKey);

        // Simpan ke DB dulu
        $deposit = Deposit::create([
            'user_id'        => $user->id,
            'amount'         => $request->amount,
            'status'         => 'pending',
            'reference'      => $merchantRef,
            'payment_method' => $request->input('method'),
        ]);

        // request ke Tripay
        $response = Http::withToken(env('TRIPAY_API_KEY'))
            ->post('https://tripay.co.id/api-sandbox/transaction/create', [
                'method'         => $request->input('method'),
                'merchant_ref'   => $merchantRef,
                'amount'         => $request->amount,
                'customer_name'  => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $customerPhone,
                'order_items'    => [[
                    'sku'      => 'DEP-' . $deposit->id,
                    'name'     => 'Deposit Saldo',
                    'price'    => $request->amount,
                    'quantity' => 1,
                ]],
                'return_url'     => $request->input('return_url'),
                'callback_url'   => $request->input('callback_url'),
                'expired_time'   => time() + (60 * 60),
                'signature'      => $signature,
            ])->json();

        // update deposit dengan payment_url (kalau Tripay sukses)
        if (isset($response['data']['checkout_url'])) {
            $deposit->update([
                'payment_url' => $response['data']['checkout_url'],
            ]);
        }

        return response()->json([
            'message' => 'Deposit created',
            'deposit' => $deposit,
            'tripay'  => $response,
        ]);
    }





    public function show(Deposit $deposit)
    {
        return response()->json($deposit);
    }


    public function updateStatus(Request $request, Deposit $deposit)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,failed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $deposit->update(['status' => $request->status]);

        // If deposit is completed, update user balance and create affiliate commission
        if ($request->status === 'completed') {
            $user = $deposit->user;
            $user->increment('balance', $deposit->amount);

            // Create affiliate commission if user was referred
            if ($user->referred_by) {
                $commissionRate = 0.1; // 10% commission
                $commissionAmount = $deposit->amount * $commissionRate;

                AffiliateCommission::create([
                    'referrer_id' => $user->referred_by,
                    'referred_id' => $user->id,
                    'deposit_id' => $deposit->id,
                    'commission_amount' => $commissionAmount,
                ]);

                // Add commission to referrer balance
                $user->referrer->increment('balance', $commissionAmount);
            }
        }

        return response()->json([
            'message' => 'Deposit status updated successfully',
            'deposit' => $deposit->fresh(),
        ]);
    }
}
