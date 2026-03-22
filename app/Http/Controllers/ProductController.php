<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Product;
use App\Transformers\ProductTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user->getCompany();

        if (! $company) {
            return $this->errorResponse('No company found', 404);
        }

        $query = Product::where('company_id', $company->id);

        return $this->listResponse($query, new ProductTransformer(), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_key' => 'required|string|max:255',
        ]);

        $user = $request->user();
        $company = $user->getCompany();

        if (! $company) {
            return $this->errorResponse('No company found', 404);
        }

        $product = Product::create(array_merge(
            $request->only([
                'product_key', 'notes', 'cost', 'price', 'quantity',
                'tax_name1', 'tax_rate1', 'tax_name2', 'tax_rate2', 'tax_name3', 'tax_rate3',
                'custom_value1', 'custom_value2', 'custom_value3', 'custom_value4',
                'in_stock_quantity', 'stock_notification', 'stock_notification_threshold',
                'max_quantity', 'product_image',
            ]),
            ['company_id' => $company->id, 'user_id' => $user->id]
        ));

        Activity::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'activity_type_id' => Activity::CREATE_PRODUCT,
            'ip' => $request->ip(),
        ]);

        return $this->itemResponse($product, new ProductTransformer());
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        return $this->itemResponse($product, new ProductTransformer());
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $product->update($request->only([
            'product_key', 'notes', 'cost', 'price', 'quantity',
            'tax_name1', 'tax_rate1', 'tax_name2', 'tax_rate2', 'tax_name3', 'tax_rate3',
            'custom_value1', 'custom_value2', 'custom_value3', 'custom_value4',
            'in_stock_quantity', 'stock_notification', 'stock_notification_threshold',
            'max_quantity', 'product_image',
        ]));

        Activity::create([
            'company_id' => $product->company_id,
            'user_id' => $request->user()->id,
            'activity_type_id' => Activity::UPDATE_PRODUCT,
            'ip' => $request->ip(),
        ]);

        return $this->itemResponse($product->fresh(), new ProductTransformer());
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        Activity::create([
            'company_id' => $product->company_id,
            'user_id' => $request->user()->id,
            'activity_type_id' => Activity::DELETE_PRODUCT,
            'ip' => $request->ip(),
        ]);

        $product->is_deleted = true;
        $product->save();
        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}
