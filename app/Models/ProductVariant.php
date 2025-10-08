<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'duration',
        'price',
        'original_price',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',

    ];

    public function getIsReadyAttribute(): bool
    {
        return $this->status === 'READY';
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
