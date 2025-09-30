<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'invite_link_or_email',
        'assigned_user_id',
        'status',
        'sent_at',
        'clicked_at',
        'accepted_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'clicked_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class, 'invite_id');
    }
}
