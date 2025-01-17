<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'status', // pending, successful, failed
        'transaction_id',
        'gateway_response'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
}