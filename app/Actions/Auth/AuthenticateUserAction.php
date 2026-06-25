<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;

final class AuthenticateUserAction
{
    /**
     * @param  array{email: string, password: string}  $credentials
     */
    public function handle(array $credentials, bool $remember = false): bool
    {
        return Auth::attempt($credentials, $remember);
    }
}
