<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class TransactionController extends Controller
{

    public function create(Request $request)
    {
        // ambil data dari request
        $method = $request->input('method');
        $amount = $request->input('amount');
        $merchantRef = $request->input('merchant_ref');

        // contoh generate signature (kalau perlu Tripay)
        $merchantCode = env('TRIPAY_MERCHANT');
        $privateKey   = env('TRIPAY_PRIVATE_KEY');

        $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey);

        // panggil Tripay API
        $response = Http::withToken(env('TRIPAY_API_KEY'))
            ->post('https://tripay.co.id/api-sandbox/transaction/create', [
                'method' => 'QRIS',
                'merchant_ref' => $merchantRef,
                'amount' => $amount,
                'customer_name' => $request->input('customer_name'),
                'customer_email' => $request->input('customer_email'),
                'customer_phone' => $request->input('customer_phone'),
                'order_items' => $request->input('order_items'),
                'return_url' => $request->input('return_url'),
                'callback_url' => $request->input('callback_url'),
                'expired_time' => time() + (60 * 60),
                'signature' => $signature,
            ]);

        return response()->json($response->json());
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
