<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends Model
{
    protected $fillable = [
        'brand_name',
        'company_headline',
        'logo',
        'favicon',
        'address',
        'city',
        'district',
        'pincode',
        'state',
        'country',
        'gst_number',
        'phone_number1',
        'phone_number2',
        'website_name',
        'support_email',
        'email_primary',
        'email_secondary',
        'additional_info',
        'youtube_url',
        'facebook_url',
        'pinterest_url',
        'twitter_url',
        'linkedin_url',
    ];
}
