<?php

namespace App\Http\Controllers\Onboarding;

use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

abstract class AbstractOnboardingController extends Controller
{
    /**
     * @return array<string, int>
     */
    abstract protected function stepOrder(): array;

    abstract protected function flowKey(): string;

    protected function rememberStep(Request $request, string $routeName): void
    {
        if ($user = $request->user()) {
            $sessionKey = $this->sessionKey($user->getKey());
            $currentRoute = $request->session()->get($sessionKey);

            if (! is_string($currentRoute) || $this->routeOrder($routeName) >= $this->routeOrder($currentRoute)) {
                $request->session()->put($sessionKey, $routeName);
            }
        }
    }

    protected function redirectIfUnavailable(
        Request $request,
        ResolveUserDestinationRouteAction $resolveDestination,
        string $currentRoute,
        ?string $nextRoute,
        string $expectedRole,
    ): ?RedirectResponse {
        $user = $request->user();

        if ($user->role !== $expectedRole) {
            return redirect()->route($resolveDestination->handle($user));
        }

        $allowedRoute = $this->allowedRoute($request, $nextRoute, $currentRoute);

        if ($this->routeOrder($currentRoute) > $this->routeOrder($allowedRoute)) {
            return redirect()->route($allowedRoute);
        }

        $this->rememberStep($request, $currentRoute);

        return null;
    }

    private function allowedRoute(Request $request, ?string $nextRoute, string $currentRoute): string
    {
        $allowedRoute = $nextRoute ?? $this->restoredRoute($request) ?? $currentRoute;

        $restoredRoute = $this->restoredRoute($request);

        if ($restoredRoute !== null && $this->routeOrder($restoredRoute) > $this->routeOrder($allowedRoute)) {
            $allowedRoute = $restoredRoute;
        }

        return $allowedRoute;
    }

    private function restoredRoute(Request $request): ?string
    {
        $user = $request->user();

        return $user ? $request->session()->get($this->sessionKey($user->getKey())) : null;
    }

    private function routeOrder(string $route): int
    {
        return $this->stepOrder()[$route] ?? 999;
    }

    private function sessionKey(int $userId): string
    {
        return sprintf('onboarding.%s.%d.route', $this->flowKey(), $userId);
    }
}