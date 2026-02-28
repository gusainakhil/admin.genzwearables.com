<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyPage extends Model
{
    protected $fillable = [
        'privacy_policy',
        'terms_and_conditions',
        'return_and_refund',
    ];
}
