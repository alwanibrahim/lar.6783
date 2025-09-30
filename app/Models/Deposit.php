<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'reference',
        'payment_method',
        'payment_url',
    ];


    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function affiliateCommissions()
    {
        return $this->hasMany(AffiliateCommission::class);
    }
}
