<?php

namespace App\Models;

use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'marketplace_item_id',
        'remaining_credits',
        'price',
        'status',
        'stripe_payment_intent_id',
        'activated_at',
    ];

    protected $casts = [
        'remaining_credits' => 'integer',
        'price' => 'float',
        'activated_at' => 'datetime',

        'status' => PaymentStatusEnum::class // You can use an enum if you have one for status
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function marketplaceItem()
    {
        return $this->belongsTo(MarketplaceItem::class);
    }
}

