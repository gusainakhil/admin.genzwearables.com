<?php

namespace App\Http\Controllers;

use App\Models\PolicyPage;
use Illuminate\View\View;

class PolicyPageController extends Controller
{
    public function privacyPolicy(): View
    {
        return $this->showPage('privacy_policy', 'Privacy Policy');
    }

    public function termsAndConditions(): View
    {
        return $this->showPage('terms_and_conditions', 'Terms and Conditions');
    }

    public function returnAndRefund(): View
    {
        return $this->showPage('return_and_refund', 'Return and Refund Policy');
    }

    protected function showPage(string $field, string $title): View
    {
        $policyPage = PolicyPage::query()->first();
        $content = (string) ($policyPage?->{$field} ?? '');

        return view('policies.show', compact('title', 'content'));
    }
}
