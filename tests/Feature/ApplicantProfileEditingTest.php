<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicantProfileEditingTest extends TestCase
{
    use RefreshDatabase;

    public function test_applicant_can_create_missing_profile_from_editor(): void
    {
        $user = User::factory()->create([
            'name' => 'Ada Applicant',
            'role' => UserRole::Applicant->value,
        ]);

        $response = $this->actingAs($user)->put(route('applicant.profile.update'), $this->validPayload([
            'name' => 'Ada Lovelace',
            'skills' => 'Laravel, Tailwind CSS, Laravel',
        ]));

        $response->assertRedirect(route('applicant.profile.edit', absolute: false));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Ada Lovelace',
        ]);
        $this->assertSame(['Laravel', 'Tailwind CSS'], $user->profile()->first()->skills);
    }

    public function test_applicant_can_update_all_profile_fields(): void
    {
        $user = $this->applicantWithProfile();

        $response = $this->actingAs($user)->put(route('applicant.profile.update'), $this->validPayload([
            'name' => 'Updated Applicant',
            'headline' => 'Senior Laravel Developer',
            'bio' => 'I build reliable hiring workflows.',
            'location' => 'Cebu, Philippines',
            'phone' => '+63 917 000 0000',
            'website' => 'https://ada.example.com',
            'linkedin' => 'https://www.linkedin.com/in/ada',
            'github' => 'https://github.com/ada',
            'desired_job_type' => 'contract',
            'work_preference' => 'hybrid',
            'experience_level' => 'senior',
            'skills' => 'PHP, Laravel, PostgreSQL',
        ]));

        $response->assertRedirect(route('applicant.profile.edit', absolute: false));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Applicant',
        ]);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'headline' => 'Senior Laravel Developer',
            'bio' => 'I build reliable hiring workflows.',
            'location' => 'Cebu, Philippines',
            'phone' => '+63 917 000 0000',
            'website' => 'https://ada.example.com',
            'linkedin' => 'https://www.linkedin.com/in/ada',
            'github' => 'https://github.com/ada',
            'desired_job_type' => 'contract',
            'work_preference' => 'hybrid',
            'experience_level' => 'senior',
        ]);
        $this->assertSame(['PHP', 'Laravel', 'PostgreSQL'], $user->profile()->first()->skills);
    }

    public function test_profile_editor_validates_required_and_enum_fields(): void
    {
        $user = $this->applicantWithProfile();

        $response = $this->actingAs($user)
            ->from(route('applicant.profile.edit'))
            ->put(route('applicant.profile.update'), $this->validPayload([
                'headline' => '   ',
                'bio' => '   ',
                'skills' => ' , , ',
                'github' => 'not-a-url',
                'desired_job_type' => 'freelance',
            ]));

        $response->assertRedirect(route('applicant.profile.edit', absolute: false));
        $response->assertSessionHasErrors(['headline', 'bio', 'skills', 'github', 'desired_job_type']);
        $this->assertDatabaseMissing('profiles', [
            'user_id' => $user->id,
            'desired_job_type' => 'freelance',
        ]);
    }

    public function test_applicant_profile_update_is_scoped_to_authenticated_user(): void
    {
        $user = $this->applicantWithProfile();
        $otherUser = $this->applicantWithProfile([
            'headline' => 'Do Not Change',
        ]);

        $response = $this->actingAs($user)->put(route('applicant.profile.update'), $this->validPayload([
            'user_id' => $otherUser->id,
            'headline' => 'Only My Profile Changes',
        ]));

        $response->assertRedirect(route('applicant.profile.edit', absolute: false));
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'headline' => 'Only My Profile Changes',
        ]);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $otherUser->id,
            'headline' => 'Do Not Change',
        ]);
    }

    public function test_employer_cannot_access_applicant_profile_editor(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);

        $this->actingAs($user)
            ->get(route('applicant.profile.edit'))
            ->assertRedirect(route('employer.onboarding.company', absolute: false));

        $this->actingAs($user)
            ->put(route('applicant.profile.update'), $this->validPayload())
            ->assertRedirect(route('employer.onboarding.company', absolute: false));
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ada Applicant',
            'headline' => 'Frontend Developer',
            'bio' => 'I build accessible Laravel interfaces.',
            'location' => 'Manila, Philippines',
            'phone' => '+63 912 345 6789',
            'website' => 'https://ada.example.com',
            'linkedin' => 'https://www.linkedin.com/in/ada',
            'github' => 'https://github.com/ada',
            'desired_job_type' => 'full-time',
            'work_preference' => 'remote',
            'experience_level' => 'entry',
            'skills' => 'Laravel, Tailwind CSS',
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $profileOverrides
     */
    private function applicantWithProfile(array $profileOverrides = []): User
    {
        $user = User::factory()->create([
            'name' => 'Ada Applicant',
            'role' => UserRole::Applicant->value,
        ]);

        $user->profile()->create(array_merge([
            'headline' => 'Frontend Developer',
            'bio' => 'I build accessible Laravel interfaces.',
            'location' => 'Manila, Philippines',
            'phone' => '+63 912 345 6789',
            'website' => 'https://ada.example.com',
            'linkedin' => 'https://www.linkedin.com/in/ada',
            'github' => 'https://github.com/ada',
            'desired_job_type' => 'full-time',
            'work_preference' => 'remote',
            'experience_level' => 'entry',
            'skills' => ['Laravel', 'Tailwind CSS'],
        ], $profileOverrides));

        return $user;
    }
}
