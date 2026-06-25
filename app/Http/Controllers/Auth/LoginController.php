<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthenticateUserAction;
use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class LoginController extends Controller
{
    public function create(ResolveUserDestinationRouteAction $resolveDestination): View|RedirectResponse
    {
        if ($user = Auth::user()) {
            return redirect()->route($resolveDestination->handle($user));
        }

        return view('auth.login');
    }

    public function store(
        LoginRequest $request,
        AuthenticateUserAction $authenticateUser,
        ResolveUserDestinationRouteAction $resolveDestination,
    ): RedirectResponse {
        if ($user = Auth::user()) {
            return redirect()->route($resolveDestination->handle($user));
        }

        if (! $authenticateUser->handle($request->credentials(), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route($resolveDestination->handle(Auth::user()), absolute: false));
    }
}
