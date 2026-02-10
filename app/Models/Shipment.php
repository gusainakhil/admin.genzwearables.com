<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'courier_name',
        'tracking_number',
        'shipped_date',
        'delivery_date',
    ];

    protected $casts = [
        'shipped_date' => 'datetime',
        'delivery_date' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
