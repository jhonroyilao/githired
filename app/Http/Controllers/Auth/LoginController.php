<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthenticateUserAction;
use App\Actions\Auth\ResolveUserDashboardRouteAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class LoginController extends Controller
{
    public function create(ResolveUserDashboardRouteAction $resolveDashboardRoute): View|RedirectResponse
    {
        if ($user = Auth::user()) {
            return redirect()->route($resolveDashboardRoute->handle($user));
        }

        return view('auth.login');
    }

    public function store(
        LoginRequest $request,
        AuthenticateUserAction $authenticateUser,
        ResolveUserDashboardRouteAction $resolveDashboardRoute,
    ): RedirectResponse {
        if (! $authenticateUser->handle($request->credentials(), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route($resolveDashboardRoute->handle(Auth::user()), absolute: false));
    }
}
