<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Distribution;
use App\Models\Product;
use App\Models\ProductAccount;
use App\Models\ProductInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DistributionController extends Controller
{
    public function index(Request $request)
    {
        $distributions = $request->user()
            ->distributions()
            ->with(['product', 'account', 'invite'])
            ->latest()
            ->paginate(15);

        return response()->json($distributions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $product = Product::findOrFail($request->product_id);

        // Check if user has enough balance
        if ($user->balance < $product->price) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        // Deduct balance
        $user->decrement('balance', $product->price);

        // Find available account or invite based on product type
        $account = null;
        $invite = null;

        if ($product->type === 'account') {
            $account = ProductAccount::where('product_id', $product->id)
                ->where('is_used', false)
                ->first();

            if ($account) {
                $account->update(['is_used' => true]);
            }
        } else {
            $invite = ProductInvite::where('product_id', $product->id)
                ->whereNull('assigned_user_id')
                ->first();

            if ($invite) {
                $invite->update(['assigned_user_id' => $user->id]);
            }
        }

        $distribution = Distribution::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'account_id' => $account ? $account->id : null,
            'invite_id' => $invite ? $invite->id : null,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Distribution created successfully',
            'distribution' => $distribution->load(['product', 'account', 'invite']),
        ], 201);
    }

    public function show(Distribution $distribution)
    {
        // load relasi supaya lengkap
        $distribution->load(['product', 'account', 'invite']);

        return response()->json($distribution);
    }


    public function updateStatus(Request $request, Distribution $distribution)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,sent,completed',
            'instructions_sent' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $distribution->update($validator->validated());

        return response()->json([
            'message' => 'Distribution updated successfully',
            'distribution' => $distribution,
        ]);
    }
}
