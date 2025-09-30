<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAccount;
use App\Models\ProductInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'accounts', 'invites'])
            ->paginate(15);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:account,invite,family',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::create($validator->validated());

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load('category'),
        ], 201);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'accounts', 'invites']);
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|string|max:255',
            'type'        => 'sometimes|in:account,invite,family',
            'price'       => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->update($validator->validated());

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product->load('category'),
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function addAccount(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $account = ProductAccount::create([
            'product_id' => $product->id,
            'username'   => $request->username,
            'password'   => $request->password,
        ]);

        return response()->json([
            'message' => 'Account added successfully',
            'account' => $account,
        ], 201);
    }

    public function addInvite(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'invite_link_or_email' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $invite = ProductInvite::create([
            'product_id'            => $product->id,
            'invite_link_or_email'  => $request->invite_link_or_email,
        ]);

        return response()->json([
            'message' => 'Invite added successfully',
            'invite'  => $invite,
        ], 201);
    }
}
