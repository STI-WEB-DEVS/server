<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;

  protected $fillable = [
    'uuid',
    'name',
    'price',
    'stock_quantity',  // ← must be here
];

    protected $casts = [
        'stock_quantity' => 'integer', // ← new
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    // -------------------------------------------------------------------------
    // Stock helpers
    // -------------------------------------------------------------------------

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity === 0;
    }

    public function isLowStock(int $threshold = 5): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= $threshold;
    }

    public function decrementStock(int $qty = 1): void
    {
        if ($this->stock_quantity < $qty) {
            throw new \RuntimeException("Insufficient stock for product: {$this->name}");
        }

        $this->decrement('stock_quantity', $qty);
    }

    public function incrementStock(int $qty = 1): void
    {
        $this->increment('stock_quantity', $qty);
    }
}