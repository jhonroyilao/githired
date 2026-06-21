<?php

namespace App\Actions\Onboarding;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class UpdateEmployerCompanyAction
{
    /**
     * @param  array{name: string, slug?: string|null, logo?: UploadedFile|null, remove_logo?: bool, website?: string|null, industry: string, size: string, location: string, description: string}  $attributes
     */
    public function handle(User $user, array $attributes): void
    {
        $company = $user->company()->firstOrNew([]);

        $company->fill([
            'name' => $attributes['name'],
            'slug' => $attributes['slug'] ?: Str::slug($attributes['name']),
            'website' => $attributes['website'] ?? null,
            'industry' => $attributes['industry'],
            'size' => $attributes['size'],
            'location' => $attributes['location'],
            'description' => $attributes['description'],
        ]);

        if (($attributes['remove_logo'] ?? false) && $company->logo_path) {
            Storage::disk('public')->delete($company->logo_path);
            $company->logo_path = null;
        }

        if (($attributes['logo'] ?? null) instanceof UploadedFile) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }

            $company->logo_path = $attributes['logo']->store('company-logos', 'public');
        }

        $company->save();
    }
}
