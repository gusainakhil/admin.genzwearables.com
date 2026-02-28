<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePolicyPageRequest;
use App\Models\PolicyPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PolicyPageController extends Controller
{
    public function index(): View
    {
        $policyPage = PolicyPage::query()->first() ?? new PolicyPage;

        return view('admin.policies.index', compact('policyPage'));
    }

    public function update(UpdatePolicyPageRequest $request): RedirectResponse
    {
        $policyPage = PolicyPage::query()->first() ?? new PolicyPage;
        $policyPage->fill($request->validated());
        $policyPage->save();

        return back()->with('success', 'Policy pages updated successfully');
    }
}
