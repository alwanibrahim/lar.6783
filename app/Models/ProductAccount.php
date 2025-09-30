<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'username',
        'password',
        'is_used',
    ];

    protected $casts = [
        'is_used' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class, 'account_id');
    }
}
