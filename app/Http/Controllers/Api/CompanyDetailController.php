<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetail;
use Illuminate\Http\JsonResponse;

class CompanyDetailController extends Controller
{
    public function show(): JsonResponse
    {
        $companyDetail = CompanyDetail::query()->first();

        return response()->json([
            'status' => true,
            'data' => [
                'brand_name' => (string) ($companyDetail?->brand_name ?? ''),
                'company_headline' => (string) ($companyDetail?->company_headline ?? ''),
                'logo' => [
                    'path' => $companyDetail?->logo,
                    'url' => $companyDetail?->logo ? asset('storage/'.$companyDetail->logo) : null,
                ],
                'favicon' => [
                    'path' => $companyDetail?->favicon,
                    'url' => $companyDetail?->favicon ? asset('storage/'.$companyDetail->favicon) : null,
                ],
                'address' => (string) ($companyDetail?->address ?? ''),
                'city' => (string) ($companyDetail?->city ?? ''),
                'district' => (string) ($companyDetail?->district ?? ''),
                'pincode' => (string) ($companyDetail?->pincode ?? ''),
                'state' => (string) ($companyDetail?->state ?? ''),
                'country' => (string) ($companyDetail?->country ?? ''),
                'gst_number' => (string) ($companyDetail?->gst_number ?? ''),
                'phone_number1' => (string) ($companyDetail?->phone_number1 ?? ''),
                'phone_number2' => (string) ($companyDetail?->phone_number2 ?? ''),
                'website_name' => (string) ($companyDetail?->website_name ?? ''),
                'support_email' => (string) ($companyDetail?->support_email ?? ''),
                'email_primary' => (string) ($companyDetail?->email_primary ?? ''),
                'email_secondary' => (string) ($companyDetail?->email_secondary ?? ''),
                'additional_info' => (string) ($companyDetail?->additional_info ?? ''),
                'youtube_url' => (string) ($companyDetail?->youtube_url ?? ''),
                'facebook_url' => (string) ($companyDetail?->facebook_url ?? ''),
                'pinterest_url' => (string) ($companyDetail?->pinterest_url ?? ''),
                'twitter_url' => (string) ($companyDetail?->twitter_url ?? ''),
                'linkedin_url' => (string) ($companyDetail?->linkedin_url ?? ''),
            ],
        ]);
    }
}
