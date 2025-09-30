<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'account_id',
        'invite_id',
        'status',
        'instructions_sent',
    ];

    protected $casts = [
        'instructions_sent' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function account()
    {
        return $this->belongsTo(ProductAccount::class, 'account_id');
    }

    public function invite()
    {
        return $this->belongsTo(ProductInvite::class, 'invite_id');
    }
}
