<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductVariantController extends Controller
{
    public function index()
    {
        $variants = ProductVariant::with('product:id,name')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Variants fetched successfully',
            'data' => $variants
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'duration' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:READY,NOT_READY',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $variant = ProductVariant::create($validator->validated());

        return response()->json([
            'message' => 'Variant created successfully',
            'data' => $variant
        ], 201);
    }

    public function show(ProductVariant $productVariant)
    {
        return response()->json($productVariant->load('product'));
    }

    public function update(Request $request, ProductVariant $productVariant)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'duration' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:READY,NOT_READY',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $productVariant->update($validator->validated());

        return response()->json([
            'message' => 'Variant updated successfully',
            'data' => $productVariant
        ]);
    }

    public function destroy(ProductVariant $productVariant)
    {
        $productVariant->delete();

        return response()->json(['message' => 'Variant deleted successfully']);
    }
}
