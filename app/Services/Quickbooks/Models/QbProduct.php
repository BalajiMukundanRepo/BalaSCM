<?php

namespace App\Services\Quickbooks\Models;

use App\Models\Product;

class QbProduct
{
    public ?string $qb_id = null;

    public string $name = '';

    public string $description = '';

    public float $price = 0.0;

    public ?string $income_account_ref = null;

    public ?string $expense_account_ref = null;

    public string $type = 'NonInventory';

    public bool $active = true;

    public bool $taxable = false;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function toProduct(): array
    {
        return [
            'product_key' => $this->name,
            'notes' => $this->description,
            'price' => $this->price,
            'cost' => 0,
            'quantity' => 1,
            'custom_value1' => $this->qb_id,
            'custom_value2' => $this->type,
        ];
    }

    public static function fromProduct(Product $product): self
    {
        $qbProduct = new self();
        $qbProduct->name = $product->product_key ?: '';
        $qbProduct->description = $product->notes ?: '';
        $qbProduct->price = (float) $product->price;
        $qbProduct->active = ! $product->is_deleted;
        $qbProduct->qb_id = $product->custom_value1;
        $qbProduct->type = $product->custom_value2 ?: 'NonInventory';

        return $qbProduct;
    }
}
