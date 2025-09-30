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
        'description',
        'category_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
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
}
