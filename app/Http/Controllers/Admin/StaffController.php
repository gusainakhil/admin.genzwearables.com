<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffRequest;
use App\Http\Requests\Admin\UpdateStaffRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class StaffController extends Controller
{
    public function index(): View
    {
        $staffUsers = User::query()
            ->where('role', 'staff')
            ->latest()
            ->paginate(20);

        return view('admin.staff.index', compact('staffUsers'));
    }

    public function create(): View
    {
        return view('admin.staff.create');
    }

    public function store(StoreStaffRequest $request): RedirectResponse
    {
        User::create($request->validated() + [
            'role' => 'staff',
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff user created successfully');
    }

    public function edit(User $staff): View
    {
        abort_unless($staff->role === 'staff', 404);

        return view('admin.staff.edit', compact('staff'));
    }

    public function update(UpdateStaffRequest $request, User $staff): RedirectResponse
    {
        abort_unless($staff->role === 'staff', 404);

        $validated = $request->validated();

        if (! $request->filled('password')) {
            unset($validated['password']);
        }

        $staff->update($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff user updated successfully');
    }

    public function destroy(User $staff): RedirectResponse
    {
        abort_unless($staff->role === 'staff', 404);

        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff user deleted successfully');
    }
}
