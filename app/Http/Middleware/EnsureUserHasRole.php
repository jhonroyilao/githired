<?php

namespace App\Http\Middleware;

use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserHasRole
{
    public function __construct(private ResolveUserDestinationRouteAction $resolveDestination) {}

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(
        Request $request,
        Closure $next,
        string $role,
    ): Response {
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        if ($user->role !== $role) {
            return new RedirectResponse(route($this->resolveDestination->handle($user)));
        }

        return $next($request);
    }
}
