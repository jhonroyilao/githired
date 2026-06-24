<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\ResumeDocument;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OnboardingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_applicant_basic_profile_step_persists_and_advances(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Applicant->value,
        ]);

        $response = $this->actingAs($user)->post(route('applicant.onboarding.profile.store'), [
            'name' => 'Ada Applicant',
            'location' => 'Manila, Philippines',
            'phone' => '+63 912 345 6789',
        ]);

        $response->assertRedirect(route('applicant.onboarding.summary', absolute: false));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Ada Applicant',
        ]);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'location' => 'Manila, Philippines',
            'phone' => '+63 912 345 6789',
        ]);
    }

    public function test_applicant_can_remove_uploaded_avatar_during_onboarding(): void
    {
        Storage::fake('public');
        /** @var FilesystemAdapter $publicStorage */
        $publicStorage = Storage::disk('public');

        $user = $this->applicantWithBasicProfile();
        $publicStorage->put('avatars/current-avatar.jpg', 'avatar');
        $user->profile()->update([
            'avatar_path' => 'avatars/current-avatar.jpg',
        ]);

        $response = $this->actingAs($user)->post(route('applicant.onboarding.profile.store'), [
            'name' => 'Ada Applicant',
            'location' => 'Manila, Philippines',
            'phone' => '+63 912 345 6789',
            'remove_avatar' => '1',
        ]);

        $response->assertRedirect(route('applicant.onboarding.summary', absolute: false));
        $publicStorage->assertMissing('avatars/current-avatar.jpg');
        $this->assertNull($user->profile()->first()->avatar_path);
    }

    public function test_replacing_applicant_avatar_removes_previous_file(): void
    {
        Storage::fake('public');
        /** @var FilesystemAdapter $publicStorage */
        $publicStorage = Storage::disk('public');

        $user = $this->applicantWithBasicProfile();
        $publicStorage->put('avatars/current-avatar.jpg', 'avatar');
        $user->profile()->update([
            'avatar_path' => 'avatars/current-avatar.jpg',
        ]);

        $response = $this->actingAs($user)->post(route('applicant.onboarding.profile.store'), [
            'name' => 'Ada Applicant',
            'location' => 'Manila, Philippines',
            'phone' => '+63 912 345 6789',
            'avatar' => UploadedFile::fake()->image('new-avatar.jpg'),
        ]);

        $response->assertRedirect(route('applicant.onboarding.summary', absolute: false));
        $publicStorage->assertMissing('avatars/current-avatar.jpg');

        $avatarPath = $user->profile()->first()->avatar_path;
        $this->assertNotNull($avatarPath);
        $publicStorage->assertExists($avatarPath);
    }

    public function test_applicant_summary_step_persists_structured_skills_and_advances(): void
    {
        $user = $this->applicantWithBasicProfile();

        $response = $this->actingAs($user)->post(route('applicant.onboarding.summary.store'), [
            'headline' => 'Frontend Developer',
            'bio' => 'I build accessible Laravel interfaces.',
            'skills' => 'Laravel, Tailwind CSS, Laravel',
        ]);

        $response->assertRedirect(route('applicant.onboarding.preferences', absolute: false));
        $this->assertSame(['Laravel', 'Tailwind CSS'], $user->profile()->first()->skills);
    }

    public function test_whitespace_only_applicant_summary_is_rejected(): void
    {
        $user = $this->applicantWithBasicProfile();

        $response = $this->actingAs($user)
            ->from(route('applicant.onboarding.summary'))
            ->post(route('applicant.onboarding.summary.store'), [
                'headline' => '   ',
                'bio' => '   ',
                'skills' => '   ',
            ]);

        $response->assertRedirect(route('applicant.onboarding.summary', absolute: false));
        $response->assertSessionHasErrors(['headline', 'bio', 'skills']);
    }

    public function test_invalid_applicant_preferences_are_rejected(): void
    {
        $user = $this->applicantWithSummary();

        $response = $this->actingAs($user)
            ->from(route('applicant.onboarding.preferences'))
            ->post(route('applicant.onboarding.preferences.store'), [
                'desired_job_type' => 'freelance',
                'work_preference' => 'remote',
                'experience_level' => 'entry',
            ]);

        $response->assertRedirect(route('applicant.onboarding.preferences', absolute: false));
        $response->assertSessionHasErrors('desired_job_type');
        $this->assertDatabaseMissing('profiles', [
            'user_id' => $user->id,
            'desired_job_type' => 'freelance',
        ]);
    }

    public function test_applicant_dashboard_is_gated_until_onboarding_is_complete(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Applicant->value,
        ]);

        $response = $this->actingAs($user)->get(route('applicant.dashboard'));

        $response->assertRedirect(route('applicant.onboarding.profile', absolute: false));
    }

    public function test_applicant_links_complete_onboarding_without_resume_document(): void
    {
        $user = $this->applicantWithPreferences();

        $response = $this->actingAs($user)->post(route('applicant.onboarding.links.store'), [
            'github' => 'https://github.com/ada',
            'linkedin' => null,
            'website' => null,
        ]);

        $response->assertRedirect(route('applicant.dashboard', absolute: false));
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'github' => 'https://github.com/ada',
        ]);
        $this->assertSame(0, ResumeDocument::query()->count());
    }

    public function test_applicant_can_move_back_and_forward_after_finishing_onboarding(): void
    {
        $user = $this->applicantWithPreferences();

        $this->actingAs($user)->post(route('applicant.onboarding.links.store'), [
            'github' => 'https://github.com/ada',
            'linkedin' => null,
            'website' => null,
        ])->assertRedirect(route('applicant.dashboard', absolute: false));

        $this->actingAs($user)->get(route('applicant.onboarding.summary'))->assertOk();
        $this->actingAs($user)->get(route('applicant.onboarding.links'))->assertOk();
    }

    public function test_resume_upload_renders_without_requiring_resume_upload(): void
    {
        $user = $this->applicantWithPreferences();

        $response = $this->actingAs($user)->get(route('applicant.onboarding.links'));

        $response->assertOk();
        $response->assertSee('Resume upload');
        $response->assertSee('Upload your PDF resume');
        $response->assertSee('type="file"', false);
        $response->assertSee('Remove selected file');
    }

    public function test_applicant_can_upload_resume_from_onboarding_links(): void
    {
        Storage::fake('local');
        Queue::fake();

        $user = $this->applicantWithPreferences();
        $file = UploadedFile::fake()->create('ada-resume.pdf', 200, 'application/pdf');

        $response = $this->actingAs($user)->post(route('applicant.resume.store'), [
            'resume' => $file,
            'redirect_to' => 'applicant.onboarding.links',
        ]);

        $response->assertRedirect(route('applicant.onboarding.links', absolute: false));

        $resume = ResumeDocument::sole();
        $this->assertSame($user->id, $resume->user_id);
        $this->assertTrue($resume->is_current);
        Storage::disk('local')->assertExists($resume->file_path);
        $this->assertSame($resume->file_path, $user->profile()->first()->resume_path);
    }

    public function test_resume_upload_from_onboarding_preserves_unsaved_link_inputs(): void
    {
        Storage::fake('local');
        Queue::fake();

        $user = $this->applicantWithPreferences();

        $response = $this->actingAs($user)->post(route('applicant.resume.store'), [
            'resume' => UploadedFile::fake()->create('ada-resume.pdf', 200, 'application/pdf'),
            'redirect_to' => 'applicant.onboarding.links',
            'github' => 'https://github.com/unsaved',
            'linkedin' => 'https://www.linkedin.com/in/unsaved',
            'website' => 'https://example.com/unsaved',
        ]);

        $response->assertRedirect(route('applicant.onboarding.links', absolute: false));
        $response->assertSessionHasInput('github', 'https://github.com/unsaved');
        $response->assertSessionHasInput('linkedin', 'https://www.linkedin.com/in/unsaved');
        $response->assertSessionHasInput('website', 'https://example.com/unsaved');
    }

    public function test_employer_company_setup_persists_and_advances_to_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);

        $response = $this->actingAs($user)->post(route('employer.onboarding.company.store'), [
            'name' => 'Acme Careers',
            'slug' => '',
            'industry' => 'Software',
            'size' => '11-50 employees',
            'location' => 'Manila, Philippines',
            'description' => 'We build hiring tools.',
        ]);

        $response->assertRedirect(route('employer.dashboard', absolute: false));
        $this->assertDatabaseHas('companies', [
            'user_id' => $user->id,
            'name' => 'Acme Careers',
            'slug' => 'acme-careers',
        ]);
    }

    public function test_employer_can_remove_uploaded_company_logo_during_onboarding(): void
    {
        Storage::fake('public');
        /** @var FilesystemAdapter $publicStorage */
        $publicStorage = Storage::disk('public');

        $user = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);
        $publicStorage->put('company-logos/current-logo.jpg', 'logo');
        $user->company()->create([
            'name' => 'Acme Careers',
            'slug' => 'acme-careers',
            'logo_path' => 'company-logos/current-logo.jpg',
            'industry' => 'Software',
            'size' => '11-50 employees',
            'location' => 'Manila, Philippines',
            'description' => 'We build hiring tools.',
        ]);

        $response = $this->actingAs($user)->post(route('employer.onboarding.company.store'), [
            'name' => 'Acme Careers',
            'slug' => 'acme-careers',
            'industry' => 'Software',
            'size' => '11-50 employees',
            'location' => 'Manila, Philippines',
            'description' => 'We build hiring tools.',
            'remove_logo' => '1',
        ]);

        $response->assertRedirect(route('employer.dashboard', absolute: false));
        $publicStorage->assertMissing('company-logos/current-logo.jpg');
        $this->assertNull($user->company()->first()->logo_path);
    }

    public function test_replacing_company_logo_removes_previous_file(): void
    {
        Storage::fake('public');
        /** @var FilesystemAdapter $publicStorage */
        $publicStorage = Storage::disk('public');

        $user = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);
        $publicStorage->put('company-logos/current-logo.jpg', 'logo');
        $user->company()->create([
            'name' => 'Acme Careers',
            'slug' => 'acme-careers',
            'logo_path' => 'company-logos/current-logo.jpg',
            'industry' => 'Software',
            'size' => '11-50 employees',
            'location' => 'Manila, Philippines',
            'description' => 'We build hiring tools.',
        ]);

        $response = $this->actingAs($user)->post(route('employer.onboarding.company.store'), [
            'name' => 'Acme Careers',
            'slug' => 'acme-careers',
            'industry' => 'Software',
            'size' => '11-50 employees',
            'location' => 'Manila, Philippines',
            'description' => 'We build hiring tools.',
            'logo' => UploadedFile::fake()->image('new-logo.jpg'),
        ]);

        $response->assertRedirect(route('employer.dashboard', absolute: false));
        $publicStorage->assertMissing('company-logos/current-logo.jpg');

        $logoPath = $user->company()->first()->logo_path;
        $this->assertNotNull($logoPath);
        $publicStorage->assertExists($logoPath);
    }

    public function test_duplicate_company_slug_is_rejected(): void
    {
        Company::query()->create([
            'user_id' => User::factory()->create(['role' => UserRole::Employer->value])->id,
            'name' => 'Existing Company',
            'slug' => 'existing-company',
            'industry' => 'Software',
            'size' => '1-10 employees',
            'location' => 'Manila, Philippines',
            'description' => 'Existing employer.',
        ]);

        $user = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);

        $response = $this->actingAs($user)
            ->from(route('employer.onboarding.company'))
            ->post(route('employer.onboarding.company.store'), [
                'name' => 'Existing Company',
                'slug' => 'existing-company',
                'industry' => 'Software',
                'size' => '11-50 employees',
                'location' => 'Cebu, Philippines',
                'description' => 'Another employer.',
            ]);

        $response->assertRedirect(route('employer.onboarding.company', absolute: false));
        $response->assertSessionHasErrors('slug');
    }

    public function test_employer_cannot_submit_applicant_onboarding(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);

        $response = $this->actingAs($user)->post(route('applicant.onboarding.profile.store'), [
            'name' => 'Wrong Role',
            'location' => 'Manila, Philippines',
            'phone' => '+63 912 345 6789',
        ]);

        $response->assertRedirect(route('employer.onboarding.company', absolute: false));
    }

    public function test_applicant_visiting_employer_onboarding_is_redirected_to_applicant_onboarding(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Applicant->value,
        ]);

        $response = $this->actingAs($user)->get(route('employer.onboarding.company'));

        $response->assertRedirect(route('applicant.onboarding.profile', absolute: false));
    }

    public function test_complete_employer_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);

        $user->company()->create([
            'name' => 'Acme Careers',
            'slug' => 'acme-careers',
            'industry' => 'Software',
            'size' => '11-50 employees',
            'location' => 'Manila, Philippines',
            'description' => 'We build hiring tools.',
        ]);

        $response = $this->actingAs($user)->get(route('employer.dashboard'));

        $response->assertOk();
        $response->assertSee('Employer dashboard');
    }

    private function applicantWithBasicProfile(): User
    {
        $user = User::factory()->create([
            'name' => 'Ada Applicant',
            'role' => UserRole::Applicant->value,
        ]);

        $user->profile()->create([
            'location' => 'Manila, Philippines',
            'phone' => '+63 912 345 6789',
        ]);

        return $user;
    }

    private function applicantWithSummary(): User
    {
        $user = $this->applicantWithBasicProfile();

        $user->profile()->update([
            'headline' => 'Frontend Developer',
            'bio' => 'I build accessible Laravel interfaces.',
            'skills' => ['Laravel', 'Tailwind CSS'],
        ]);

        return $user;
    }

    private function applicantWithPreferences(): User
    {
        $user = $this->applicantWithSummary();

        $user->profile()->update([
            'desired_job_type' => 'full-time',
            'work_preference' => 'remote',
            'experience_level' => 'entry',
        ]);

        return $user;
    }
}
