# Frontend Decision: Blade + Tailwind

## Decision

GitHired MVP frontend work will use Laravel Blade templates with Tailwind CSS.
Laravel routes and controllers remain responsible for request handling and view
rendering. New product screens should be built as Blade views and reusable Blade
partials/components, styled with Tailwind utilities from the Vite pipeline.

## Why This Direction

- The app is already a Laravel application with Blade views, controllers, and
  Vite configured.
- Tailwind CSS is already installed through the Vite plugin, so the MVP can move
  toward a consistent utility-first styling system without introducing a
  separate frontend runtime.
- Blade keeps routing, authorization, validation errors, flash messages, and
  form workflows close to Laravel conventions, which is the fastest path for
  the current job portal MVP.

## Rejected Alternatives

- React plus Tailwind is not selected for the MVP. It would add client-side
  routing, component state, bundling, and integration decisions that are not
  required for the current server-rendered workflows.
- Blade plus Bootstrap is not the future default. Existing Bootstrap-based pages
  may remain during transition, but new MVP UI should not add new Bootstrap
  dependencies or patterns.

## Implementation Conventions

- New UI routes should be declared in Laravel route files and handled by
  controllers when the screen has meaningful logic.
- Controllers should return Blade views with explicit data arrays instead of
  exposing new JSON endpoints unless the feature specifically needs asynchronous
  behavior.
- New styling should use Tailwind utility classes and small reusable Blade
  partials/components for repeated UI such as alerts, form controls, cards, and
  status labels.
- Shared CSS belongs in `resources/css/app.css` only when a Tailwind utility
  composition or design token is reused across multiple screens.
- Existing Bootstrap CDN/layout usage can stay until those screens are touched
  by later MVP issues.

## Working Path

The `/frontend-decision` route renders a minimal Blade page that loads
`resources/css/app.css` through `@vite` and uses Tailwind classes. This route is
the smoke test for the selected frontend stack.

## Verification

- `npm run build` should complete successfully.
- `php artisan test` should include a feature test proving
  `/frontend-decision` returns a rendered Blade/Tailwind decision page.
