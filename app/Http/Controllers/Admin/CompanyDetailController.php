<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCompanyDetailRequest;
use App\Models\CompanyDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyDetailController extends Controller
{
    public function index(): View
    {
        $companyDetail = CompanyDetail::query()->first();

        return view('admin.company-details.index', compact('companyDetail'));
    }

    public function update(UpdateCompanyDetailRequest $request): RedirectResponse
    {
        $companyDetail = CompanyDetail::query()->first() ?? new CompanyDetail;
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            if ($companyDetail->logo) {
                Storage::disk('public')->delete($companyDetail->logo);
            }

            $validated['logo'] = $request->file('logo')->store('company', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($companyDetail->favicon) {
                Storage::disk('public')->delete($companyDetail->favicon);
            }

            $validated['favicon'] = $request->file('favicon')->store('company', 'public');
        }

        $companyDetail->fill($validated);
        $companyDetail->save();

        return back()->with('success', 'Company details saved successfully');
    }
}
