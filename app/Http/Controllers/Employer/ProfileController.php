<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $company = Auth::user()->company;
        return view('employer.profile.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $company = Auth::user()->company;

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', Rule::unique('companies', 'slug')->ignore($company->id)],
            'industry'    => ['required', 'string', 'max:255'],
            'size'        => ['required', 'string', 'max:255'],
            'location'    => ['required', 'string', 'max:255'],
            'website'     => ['nullable', 'url', 'max:255'],
            'description' => ['required', 'string'],
            'logo'        => ['nullable', 'image', 'max:10240'], // 10MB max
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        
        if ($request->boolean('remove_logo')) {
            if ($company->logo_path) {
                Storage::delete($company->logo_path);
            }
            $company->logo_path = null;
        } elseif ($request->hasFile('logo')) {
            
            if ($company->logo_path) {
                Storage::delete($company->logo_path);
            }
            $company->logo_path = $request->file('logo')->store('companies/logos', 'public');
        }

        
        $company->update([
            'name'        => $validated['name'],
            'slug'        => $validated['slug'] ?? $company->slug,
            'industry'    => $validated['industry'],
            'size'        => $validated['size'],
            'location'    => $validated['location'],
            'website'     => $validated['website'],
            'description' => $validated['description'],
            'logo_path'   => $company->logo_path,
        ]);

        return back()->with('status', 'Company profile updated successfully!');
    }
}