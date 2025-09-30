<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'deposit_id',
        'commission_amount',
    ];

    protected $casts = [
        'commission_amount' => 'decimal:2',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }
}
