<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'price',
        'original_price',
        'description',
        'category_id',
        'features',
        'image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'features' => 'array',
        'image_url' => 'string',
    ];

    public function accounts()
    {
        return $this->hasMany(ProductAccount::class);
    }

    public function invites()
    {
        return $this->hasMany(ProductInvite::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
