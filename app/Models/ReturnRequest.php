<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequest extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'order_item_id',
        'request_type',
        'requested_by',
        'product_variant_id',
        'tracking_id',
        'courier_name',
        'link',
        'product_images',
        'reason',
        'status',
    ];

    protected $casts = [
        'product_images' => 'array',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
