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

## Visual System

GitHired should feel like a focused hiring workspace, not a generic job board.
The UI direction is precise, structured, and status-aware: applicants should
know what to do next, employers should be able to scan work queues quickly, and
admins should see moderation state without visual noise.

### Color Tokens

Use the brand palette as semantic tokens instead of scattering raw hex values
through Blade views.

| Token | Hex | Use |
| --- | --- | --- |
| `neutral-950` | `#171B18` | Highest contrast text and deep active states |
| `neutral-900` | `#27302B` | Primary ink, headers, navigation, strong surfaces |
| `neutral-800` | `#394139` | Secondary dark surfaces and hover states |
| `neutral-600` | `#525252` | Body-muted text and secondary metadata |
| `neutral-300` | `#C9CEC5` | Strong borders, dividers, disabled edges |
| `neutral-200` | `#E5E7E2` | Default borders and section separators |
| `neutral-100` | `#F2F4EF` | Tinted page bands and quiet input backgrounds |
| `neutral-50` | `#FAFAFA` | Main page background |
| `primarygreen` | `#91C93C` | Primary action, positive progress, selected state |
| `primarygreen-700` | `#5F8F22` | Pressed/hover action state and high-contrast green text |
| `primarygreen-100` | `#EEF8DF` | Soft success/action background |
| `primarygreen-50` | `#F7FCE9` | Very light action tint for panels |
| `signal-blue` | `#3E8F84` | Informational states, profile/resume completeness |
| `signal-amber` | `#D99222` | Pending/review state |
| `signal-red` | `#C4513D` | Rejected/error state |

`resources/css/app.css` defines these as Tailwind v4 `@theme` color tokens and
as semantic CSS variables under `:root`.

### Typography

- Display: `Cabinet Grotesk` for restrained headings, dashboard section labels,
  and empty-state headlines.
- Body: `Manrope` for form fields, tables, cards, and long interface copy.
- Utility: `IBM Plex Mono` for compact metadata, IDs, timestamps, scores, and
  moderation labels.

When fonts are wired into the shared layout, load only the weights required by
the built screens. Until then, the CSS tokens fall back to system fonts.

### Layout Direction

The core layout should use a "hiring desk" model: a calm surface, strong left
alignment, and work items grouped as queues rather than decorative cards.

```text
+--------------------------------------------------------------+
| top bar: GitHired / role switch / account                    |
+--------------+-----------------------------------------------+
| section rail | page title + primary action                   |
| status nav   | filters / queue controls                      |
|              |                                               |
|              | work queue rows or applicant/job cards        |
|              | detail panel when a row is selected           |
+--------------+-----------------------------------------------+
```

For auth screens, use a narrower split layout:

```text
+-----------------------------+-------------------------------+
| product mark + short proof   | login/register form           |
| role-specific copy           | validation + focused action   |
+-----------------------------+-------------------------------+
```

Registration displays role choices as `Job seeker` and `Recruiter`, but form
values must stay mapped to the backend roles `applicant` and `employer`.

Applicant onboarding should follow `docs/PRD/user-stories.md` rather than early
mockup step counts:

1. Role selection: display `Job seeker`, store `applicant`.
2. Basic profile: name, avatar, location, phone, email display/edit path.
3. Applicant summary: professional headline, short bio, skills.
4. Job preferences: desired job type, work preference, experience level.
5. Links and resume: GitHub, LinkedIn, website/portfolio, PDF resume upload.

Employer onboarding should use the recruiter label in UI and store `employer`,
then move into company profile setup: company name, logo, website, industry,
size, location, and description.

### Signature Element

The memorable element should be a vertical "match rail": a thin left-side strip
using `primarygreen`, amber, red, and teal markers to show where a person, job,
or application sits in the hiring process. It is specific to GitHired because
the product is about moving work through hiring states. Use it in lists,
dashboards, and detail headers; do not use it as decoration on every panel.

### Self-Critique

This avoids the common dark-site-plus-acid-green default by keeping the product
mostly light and operational. The risk is the match rail: it gives the system a
recognizable motif, but it must stay sparse. If every component gets a colored
stripe, the UI will become noisy and the status language will lose meaning.

## Working Path

The previous frontend decision smoke-test route was removed during the UI reset.
Going forward, the smoke test for the frontend stack is `npm run build`, plus a
Blade-rendering feature test for whichever screen is being reintroduced.

## Verification

- `npm run build` should complete successfully.
- `php artisan test` should pass.
- New screens should have at least one feature test proving the named route
  renders the intended Blade view.
