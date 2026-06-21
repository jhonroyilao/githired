<?php

namespace App\Actions\Auth;

use App\Models\User;

final class RegisterUserAction
{
    /**
     * @param  array{name: string, email: string, password: string, role: string}  $attributes
     */
    public function handle(array $attributes): User
    {
        return User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
            'role' => $attributes['role'],
        ]);
    }
}
