<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RegisterUserAction;
use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class RegisterController extends Controller
{
    public function create(ResolveUserDestinationRouteAction $resolveDestination): View|RedirectResponse
    {
        if ($user = Auth::user()) {
            return redirect()->route($resolveDestination->handle($user));
        }

        return view('auth.register');
    }

    public function store(
        RegisterRequest $request,
        RegisterUserAction $registerUser,
        ResolveUserDestinationRouteAction $resolveDestination,
    ): RedirectResponse {
        $user = $registerUser->handle($request->userAttributes());

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($resolveDestination->handle($user));
    }
}
