<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_applicant_can_register_and_is_redirected_to_applicant_dashboard(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Ada Applicant',
            'email' => 'ada@example.com',
            'role' => UserRole::Applicant->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('applicant.dashboard', absolute: false));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'ada@example.com',
            'role' => UserRole::Applicant->value,
        ]);
    }

    public function test_employer_can_register_and_is_redirected_to_employer_dashboard(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Eli Employer',
            'email' => 'eli@example.com',
            'role' => UserRole::Employer->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('employer.dashboard', absolute: false));
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

    public function test_applicant_login_redirects_to_applicant_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'applicant@example.com',
            'role' => UserRole::Applicant->value,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'applicant@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('applicant.dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_applicant_dashboard_redirect_target_remains_available(): void
    {
        User::factory()->create([
            'email' => 'applicant-render@example.com',
            'role' => UserRole::Applicant->value,
        ]);

        $response = $this->followingRedirects()->post(route('login.store'), [
            'email' => 'applicant-render@example.com',
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertSee('Applicant dashboard');
    }

    public function test_employer_login_redirects_to_employer_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'employer@example.com',
            'role' => UserRole::Employer->value,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'employer@example.com',
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

    public function test_authenticated_user_visiting_login_is_redirected_to_role_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('employer.dashboard', absolute: false));
    }

    public function test_authenticated_user_visiting_register_is_redirected_to_role_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin->value,
        ]);

        $response = $this->actingAs($user)->get(route('register'));

        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }
}
