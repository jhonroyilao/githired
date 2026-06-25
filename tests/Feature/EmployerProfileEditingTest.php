<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployerProfileEditingTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_update_company_profile_from_editor(): void
    {
        $user = $this->employerWithCompany();

        $response = $this->actingAs($user)
            ->put(route('employer.company.update'), $this->validPayload([
                'name' => 'Updated Careers',
                'slug' => '',
                'industry' => 'Developer Tools',
                'description' => 'We build developer hiring workflows.',
            ]));

        $response->assertRedirect(route('employer.company.edit', absolute: false));
        $response->assertSessionHas('status', 'Company profile updated successfully!');
        $this->assertDatabaseHas('companies', [
            'user_id' => $user->id,
            'name' => 'Updated Careers',
            'slug' => 'updated-careers',
            'industry' => 'Developer Tools',
            'description' => 'We build developer hiring workflows.',
        ]);
    }

    public function test_employer_logo_update_uses_configured_image_disk(): void
    {
        config([
            'filesystems.image_disk' => 'supabase-images',
            'filesystems.disks.supabase-images.bucket' => 'images',
        ]);
        Storage::fake('supabase-images');
        /** @var FilesystemAdapter $imageStorage */
        $imageStorage = Storage::disk('supabase-images');

        $user = $this->employerWithCompany([
            'logo_path' => 'images/company-logos/current-logo.jpg',
        ]);
        $imageStorage->put('company-logos/current-logo.jpg', 'logo');

        $response = $this->actingAs($user)
            ->put(route('employer.company.update'), $this->validPayload([
                'logo' => UploadedFile::fake()->image('new-logo.jpg'),
            ]));

        $response->assertRedirect(route('employer.company.edit', absolute: false));
        $imageStorage->assertMissing('company-logos/current-logo.jpg');

        $logoPath = $user->company()->first()->logo_path;
        $this->assertNotNull($logoPath);
        $this->assertStringStartsWith('company-logos/', $logoPath);
        $imageStorage->assertExists($logoPath);
    }

    public function test_employer_can_remove_logo_from_profile_editor(): void
    {
        Storage::fake('public');
        /** @var FilesystemAdapter $publicStorage */
        $publicStorage = Storage::disk('public');

        $user = $this->employerWithCompany([
            'logo_path' => 'company-logos/current-logo.jpg',
        ]);
        $publicStorage->put('company-logos/current-logo.jpg', 'logo');

        $response = $this->actingAs($user)
            ->put(route('employer.company.update'), $this->validPayload([
                'remove_logo' => '1',
            ]));

        $response->assertRedirect(route('employer.company.edit', absolute: false));
        $publicStorage->assertMissing('company-logos/current-logo.jpg');
        $this->assertNull($user->company()->first()->logo_path);
    }

    public function test_incomplete_employer_is_redirected_from_company_profile_editor(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);

        $this->actingAs($user)
            ->get(route('employer.company.edit'))
            ->assertRedirect(route('employer.onboarding.company', absolute: false));

        $this->actingAs($user)
            ->put(route('employer.company.update'), $this->validPayload())
            ->assertRedirect(route('employer.onboarding.company', absolute: false));

        $this->assertDatabaseMissing('companies', [
            'user_id' => $user->id,
        ]);
    }

    public function test_company_profile_editor_uses_onboarding_validation_rules(): void
    {
        $user = $this->employerWithCompany();

        $response = $this->actingAs($user)
            ->from(route('employer.company.edit'))
            ->put(route('employer.company.update'), $this->validPayload([
                'slug' => 'Invalid Slug',
                'description' => str_repeat('A', 1001),
            ]));

        $response->assertRedirect(route('employer.company.edit', absolute: false));
        $response->assertSessionHasErrors(['slug', 'description']);
        $this->assertDatabaseMissing('companies', [
            'user_id' => $user->id,
            'slug' => 'Invalid Slug',
        ]);
    }

    public function test_employer_can_update_password_from_company_profile_editor(): void
    {
        $user = $this->employerWithCompany();

        $response = $this->actingAs($user)
            ->from(route('employer.company.edit'))
            ->put(route('employer.company.password.update'), [
                'current_password' => 'password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ]);

        $response->assertRedirect(route('employer.company.edit', absolute: false));
        $response->assertSessionHas('status', 'Password updated successfully.');
        $this->assertTrue(Hash::check('new-secure-password', $user->refresh()->password));
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Acme Careers',
            'slug' => 'acme-careers',
            'industry' => 'Software',
            'size' => '11-50 employees',
            'location' => 'Manila, Philippines',
            'website' => 'https://acme.example.com',
            'description' => 'We build hiring tools.',
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $companyOverrides
     */
    private function employerWithCompany(array $companyOverrides = []): User
    {
        $user = User::factory()->create([
            'name' => 'Eli Employer',
            'role' => UserRole::Employer->value,
        ]);

        $user->company()->create(array_merge($this->validPayload(), $companyOverrides));

        return $user;
    }
}
