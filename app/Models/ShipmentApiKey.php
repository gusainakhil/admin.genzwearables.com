<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentApiKey extends Model
{
    protected $table = 'shipment_api_key';

    protected $fillable = [
        'provider',
        'api_email',
        'api_password',
        'api_token',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'api_password' => 'encrypted',
            'api_token' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }
}
