<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($request->user()->id),
            ],
        ]);

        $user = auth()->user();

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        return back()->with(
            'success',
            'Profile updated successfully.'
        );
    }
}
