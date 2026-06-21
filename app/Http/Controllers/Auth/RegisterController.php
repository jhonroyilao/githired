<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RegisterUserAction;
use App\Actions\Auth\ResolveUserDashboardRouteAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class RegisterController extends Controller
{
    public function create(ResolveUserDashboardRouteAction $resolveDashboardRoute): View|RedirectResponse
    {
        if ($user = Auth::user()) {
            return redirect()->route($resolveDashboardRoute->handle($user));
        }

        return view('auth.register');
    }

    public function store(
        RegisterRequest $request,
        RegisterUserAction $registerUser,
        ResolveUserDashboardRouteAction $resolveDashboardRoute,
    ): RedirectResponse {
        $user = $registerUser->handle($request->userAttributes());

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($resolveDashboardRoute->handle($user));
    }
}
