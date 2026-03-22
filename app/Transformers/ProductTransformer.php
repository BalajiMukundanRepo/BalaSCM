<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    public function transform(Product $product): array
    {
        return [
            'id' => (int) $product->id,
            'product_key' => $product->product_key ?: '',
            'notes' => $product->notes ?: '',
            'cost' => (float) $product->cost,
            'price' => (float) $product->price,
            'quantity' => (float) $product->quantity,
            'is_deleted' => (bool) $product->is_deleted,
            'created_at' => $product->created_at ? $product->created_at->toDateTimeString() : '',
            'updated_at' => $product->updated_at ? $product->updated_at->toDateTimeString() : '',
        ];
    }
}
