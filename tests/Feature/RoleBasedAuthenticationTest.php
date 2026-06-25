<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class RoleBasedAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_applicant_can_register_and_is_redirected_to_applicant_onboarding(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Ada Applicant',
            'email' => 'ada@example.com',
            'role' => UserRole::Applicant->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('applicant.onboarding.profile', absolute: false));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'ada@example.com',
            'role' => UserRole::Applicant->value,
        ]);
        $this->assertDatabaseHas('profiles', [
            'user_id' => User::where('email', 'ada@example.com')->value('id'),
        ]);
    }

    public function test_employer_can_register_and_is_redirected_to_employer_onboarding(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Eli Employer',
            'email' => 'eli@example.com',
            'role' => UserRole::Employer->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('employer.onboarding.company', absolute: false));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'eli@example.com',
            'role' => UserRole::Employer->value,
        ]);
    }

    public function test_public_admin_registration_is_rejected(): void
    {
        $response = $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Admin User',
            'email' => 'admin-register@example.com',
            'role' => UserRole::Admin->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('register', absolute: false));
        $response->assertSessionHasErrors('role');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'admin-register@example.com',
        ]);
    }

    public function test_incomplete_applicant_login_redirects_to_applicant_onboarding(): void
    {
        $user = User::factory()->create([
            'email' => 'applicant@example.com',
            'role' => UserRole::Applicant->value,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'applicant@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('applicant.onboarding.profile', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_complete_applicant_login_redirects_to_applicant_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'applicant-render@example.com',
            'role' => UserRole::Applicant->value,
        ]);

        $user->profile()->create($this->completeApplicantProfileAttributes());

        $response = $this->post(route('login.store'), [
            'email' => 'applicant-render@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('applicant.dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_incomplete_employer_login_redirects_to_employer_onboarding(): void
    {
        $user = User::factory()->create([
            'email' => 'employer@example.com',
            'role' => UserRole::Employer->value,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'employer@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('employer.onboarding.company', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_complete_employer_login_redirects_to_employer_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'complete-employer@example.com',
            'role' => UserRole::Employer->value,
        ]);

        $user->company()->create($this->completeCompanyAttributes());

        $response = $this->post(route('login.store'), [
            'email' => 'complete-employer@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('employer.dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => UserRole::Admin->value,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_login_shows_validation_feedback_without_authenticating(): void
    {
        User::factory()->create([
            'email' => 'invalid-login@example.com',
            'password' => 'password',
            'role' => UserRole::Applicant->value,
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'invalid-login@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Applicant->value,
        ]);

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login', absolute: false));
        $this->assertGuest();
    }

    public function test_complete_applicant_dashboard_shows_session_context_and_logout(): void
    {
        $user = User::factory()->create([
            'name' => 'Ada Applicant',
            'email' => 'dashboard-applicant@example.com',
            'role' => UserRole::Applicant->value,
        ]);

        $user->profile()->create($this->completeApplicantProfileAttributes());

        $response = $this->actingAs($user)->get(route('applicant.dashboard'));

        $response->assertOk();
        $response->assertSee('Applicant dashboard');
        $response->assertSee('dashboard-applicant@example.com');
        $response->assertSee(route('applicant.dashboard'), false);
        $response->assertSee(route('applicant.resume'), false);
        $response->assertSee('Find jobs');
        $response->assertSee('Resume');
        $response->assertSee(route('applicant.applications.index'), false);
        $response->assertDontSee('href="#"', false);
        $response->assertSee('Log out');
    }

    public function test_applicant_dashboard_accepts_scalar_date_filter(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Applicant->value,
        ]);

        $user->profile()->create($this->completeApplicantProfileAttributes());

        $this->actingAs($user)
            ->get(route('applicant.dashboard', ['date_posted' => 'Last 7 Days']))
            ->assertOk();
    }

    public function test_complete_employer_dashboard_shows_company_context_and_logout(): void
    {
        $user = User::factory()->create([
            'name' => 'Eli Employer',
            'email' => 'dashboard-employer@example.com',
            'role' => UserRole::Employer->value,
        ]);

        $user->company()->create($this->completeCompanyAttributes());

        $response = $this->actingAs($user)->get(route('employer.dashboard'));

        $response->assertOk();
        $response->assertSee('Employer dashboard');
        $response->assertSee('Acme Careers');
        $response->assertSee(route('employer.dashboard'), false);
        $response->assertSee(route('employer.onboarding.company'), false);
        $response->assertDontSee(route('applicant.dashboard'), false);
        $response->assertSee('Company profile');
        $response->assertDontSee('href="#"', false);
        $response->assertSee('Log out');
    }

    public function test_admin_dashboard_uses_admin_navigation(): void
    {
        $user = User::factory()->create([
            'name' => 'Ari Admin',
            'email' => 'dashboard-admin@example.com',
            'role' => UserRole::Admin->value,
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Admin dashboard');
        $response->assertSee(route('admin.dashboard'), false);
        $response->assertDontSee(route('applicant.dashboard'), false);
        $response->assertDontSee(route('employer.dashboard'), false);
        $response->assertSee('dashboard-admin@example.com');
        $response->assertDontSee('href="#"', false);
    }

    public function test_dashboard_navbar_skips_items_without_valid_destinations(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Applicant->value,
        ]);

        $html = Blade::render(
            '<x-dashboard-navbar :user="$user" :nav-items="$navItems" />',
            [
                'user' => $user,
                'navItems' => [
                    ['label' => 'Missing route', 'route' => 'missing.dashboard.route'],
                    ['label' => 'Find jobs', 'route' => 'applicant.dashboard'],
                ],
            ],
        );

        $this->assertStringNotContainsString('href="#"', $html);
        $this->assertStringNotContainsString('Missing route', $html);
        $this->assertStringContainsString(route('applicant.dashboard'), $html);
    }

    public function test_dashboard_navbar_shows_applicant_avatar(): void
    {
        $user = User::factory()->create([
            'name' => 'Ada Applicant',
            'role' => UserRole::Applicant->value,
        ]);
        $user->profile()->create(array_merge($this->completeApplicantProfileAttributes(), [
            'avatar_path' => 'avatars/ada.png',
        ]));

        $html = Blade::render('<x-dashboard-navbar :user="$user" />', [
            'user' => $user->fresh('profile'),
        ]);

        $this->assertStringContainsString('http://localhost/storage/avatars/ada.png', $html);
        $this->assertStringContainsString('alt="Ada Applicant"', $html);
    }

    public function test_dashboard_navbar_shows_employer_company_logo(): void
    {
        $user = User::factory()->create([
            'name' => 'Eli Employer',
            'role' => UserRole::Employer->value,
        ]);
        $user->company()->create(array_merge($this->completeCompanyAttributes(), [
            'logo_path' => 'company-logos/acme.png',
        ]));

        $html = Blade::render('<x-dashboard-navbar :user="$user" />', [
            'user' => $user->fresh('company'),
        ]);

        $this->assertStringContainsString('http://localhost/storage/company-logos/acme.png', $html);
        $this->assertStringContainsString('alt="Acme Careers"', $html);
    }

    public function test_incomplete_authenticated_user_visiting_login_is_redirected_to_onboarding(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('employer.onboarding.company', absolute: false));
    }

    public function test_complete_authenticated_user_visiting_login_is_redirected_to_role_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);

        $user->company()->create($this->completeCompanyAttributes());

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('employer.dashboard', absolute: false));
    }

    public function test_authenticated_user_visiting_register_is_redirected_to_allowed_destination(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin->value,
        ]);

        $response = $this->actingAs($user)->get(route('register'));

        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_incomplete_authenticated_user_visiting_register_is_redirected_to_onboarding(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Applicant->value,
        ]);

        $response = $this->actingAs($user)->get(route('register'));

        $response->assertRedirect(route('applicant.onboarding.profile', absolute: false));
    }

    public function test_authenticated_user_posting_login_is_redirected_without_switching_accounts(): void
    {
        $currentUser = User::factory()->create([
            'email' => 'current-user@example.com',
            'role' => UserRole::Admin->value,
        ]);

        $otherUser = User::factory()->create([
            'email' => 'other-user@example.com',
            'role' => UserRole::Applicant->value,
        ]);

        $response = $this->actingAs($currentUser)->post(route('login.store'), [
            'email' => 'other-user@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertAuthenticatedAs($currentUser);
        $this->assertNotEquals($otherUser->id, auth()->id());
    }

    public function test_authenticated_user_posting_register_is_redirected_without_creating_account(): void
    {
        $currentUser = User::factory()->create([
            'email' => 'current-admin@example.com',
            'role' => UserRole::Admin->value,
        ]);

        $response = $this->actingAs($currentUser)->post(route('register.store'), [
            'name' => 'New Applicant',
            'email' => 'new-applicant@example.com',
            'role' => UserRole::Applicant->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertAuthenticatedAs($currentUser);
        $this->assertDatabaseMissing('users', [
            'email' => 'new-applicant@example.com',
        ]);
    }

    public function test_applicant_routes_redirect_non_applicant_users_to_their_own_destination(): void
    {
        $employer = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);
        $employer->company()->create($this->completeCompanyAttributes());

        $admin = User::factory()->create([
            'role' => UserRole::Admin->value,
        ]);

        $this->actingAs($employer)
            ->get(route('applicant.dashboard'))
            ->assertRedirect(route('employer.dashboard', absolute: false));

        $this->actingAs($admin)
            ->get(route('applicant.dashboard'))
            ->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_employer_routes_redirect_non_employer_users_to_their_own_destination(): void
    {
        $applicant = User::factory()->create([
            'role' => UserRole::Applicant->value,
        ]);
        $applicant->profile()->create($this->completeApplicantProfileAttributes());

        $admin = User::factory()->create([
            'role' => UserRole::Admin->value,
        ]);

        $this->actingAs($applicant)
            ->get(route('employer.dashboard'))
            ->assertRedirect(route('applicant.dashboard', absolute: false));

        $this->actingAs($admin)
            ->get(route('employer.dashboard'))
            ->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_admin_routes_redirect_non_admin_users_to_their_own_destination(): void
    {
        $applicant = User::factory()->create([
            'role' => UserRole::Applicant->value,
        ]);
        $applicant->profile()->create($this->completeApplicantProfileAttributes());

        $employer = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);
        $employer->company()->create($this->completeCompanyAttributes());

        $this->actingAs($applicant)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('applicant.dashboard', absolute: false));

        $this->actingAs($employer)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('employer.dashboard', absolute: false));
    }

    /**
     * @return array<string, mixed>
     */
    private function completeApplicantProfileAttributes(): array
    {
        return [
            'headline' => 'Frontend Developer',
            'bio' => 'I build accessible Laravel interfaces.',
            'location' => 'Manila, Philippines',
            'phone' => '+63 912 345 6789',
            'github' => 'https://github.com/ada',
            'desired_job_type' => 'full-time',
            'work_preference' => 'remote',
            'experience_level' => 'entry',
            'skills' => ['Laravel', 'Tailwind CSS'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function completeCompanyAttributes(): array
    {
        return [
            'name' => 'Acme Careers',
            'slug' => 'acme-careers',
            'industry' => 'Software',
            'size' => '11-50 employees',
            'location' => 'Manila, Philippines',
            'description' => 'We build hiring tools.',
        ];
    }
}
