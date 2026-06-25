<?php

namespace App\Actions\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class RegisterUserAction
{
    /**
     * @param  array{name: string, email: string, password: string, role: string}  $attributes
     */
    public function handle(array $attributes): User
    {
        return DB::transaction(function () use ($attributes): User {
            $user = User::create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => $attributes['password'],
                'role' => $attributes['role'],
            ]);

            if ($attributes['role'] === UserRole::Applicant->value) {
                $user->profile()->create([]);
            }

            return $user;
        });
    }
}
