# GitHired

GitHired is a Laravel job portal for applicants, employers, and moderation-only
admins. The database has been extended to support the core job portal workflow
plus a future applicant-facing AI job matching feature.

Last reviewed: 2026-06-19

## Features

### Applicant

- Role-based account support for applicants.
- Applicant profile data: headline, bio, location, contact links, job search
  preferences, avatar, and skills.
- PDF resume document model with extracted text support for future AI matching.
- Approved job browsing data model with category, location, type, experience,
  salary, and full-text search support.
- Job applications with cover letters, resume references, unique applicant/job
  constraint, and status tracking.
- Application status history through `application_status_logs`.
- In-app notifications for application and moderation events.
- Saved jobs schema and model support.

### Employer

- Role-based account support for employers.
- Company profile model with slug, logo, website, industry, size, location, and
  description.
- Employer-owned job listings.
- Job moderation lifecycle fields for submitted, approved, rejected, closed,
  published, expired, and soft-deleted jobs.
- Applicant review data through applications, employer notes, and status
  updates.

### Admin

- Role-based account support for admins.
- Moderation-oriented job workflow:
  - approve jobs
  - reject jobs with a reason
  - close jobs
  - soft-delete or hide jobs while preserving historical applications
- Moderation audit fields: `approved_by`, `rejected_by`, `deleted_by`, and
  related timestamps.

### AI-Ready Features

- Resume text extraction storage through `resume_documents.extracted_text`.
- AI job match cache through `ai_job_matches`.
- Match score, score breakdown, matching skills, missing skills, explanation,
  suggested action, provider/model metadata, and input hashes.
- Postgres JSONB skill fields and GIN indexes for profile/job skill matching.

## Tech Stack

- PHP 8.3+
- Laravel 13
- Laravel UI
- Vite 8
- Bootstrap 5 and Bootstrap Icons
- Tailwind CSS 4 via `@tailwindcss/vite`
- Postgres/Neon-oriented schema
- SQLite-compatible migrations for local/test smoke checks where possible
- PHPUnit 12

## Setup

Install PHP, Composer, Node.js, and npm first.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

For SQLite local development:

```bash
touch database/database.sqlite
php artisan migrate --seed
```

For Postgres or Neon, configure the `DB_*` values in `.env`, verify the target
branch/endpoint, then run migrations without seeding:

```bash
php artisan migrate
```

Only run `php artisan db:seed` against local databases or disposable dev/test
branches. The default seeder creates sample users, companies, jobs, and
applications, and should not be run against production or shared branches.

Use a direct Neon connection string for schema migrations. Pooled Neon
connections are better for application traffic, but migration tools may rely on
session behavior that transaction pooling does not preserve.

Run the app and Vite in separate terminals:

```bash
php artisan serve
npm run dev
```

Or run the combined development script:

```bash
composer run dev
```

Resume PDF text extraction runs through Laravel queues. Local development uses
`QUEUE_CONNECTION=sync` by default so extraction completes automatically during
upload. If you switch to `QUEUE_CONNECTION=database`, keep a queue worker
running as well:

```bash
php artisan queue:listen --tries=1 --timeout=0
```

The combined `composer run dev` script already starts that worker.

Build frontend assets:

```bash
npm run build
```

Run tests:

```bash
composer test
```

## Seeded Accounts

All seeded accounts use the password `password`.

| Role | Email |
| --- | --- |
| Admin | `admin@githired.com` |
| Employer | `marco@techph.com` |
| Applicant | `juan@email.com` |

The default route setup does not yet wire the login form to authentication, so
these accounts are mainly useful after auth routes/controllers are connected or
when signing in through a temporary local flow.

## Active Routes

The current application-facing routes are defined in `routes/web.php`. Laravel
also exposes framework routes such as `up` and local storage routes.

| Method | Path | Name | Current behavior |
| --- | --- | --- | --- |
| GET | `/` | none | Renders the landing page |
| GET | `/mockup` | none | Renders the UI mockup page |
| GET | `/jobs` | `jobs.index` | Placeholder string; duplicated route |
| GET | `/jobs/{id}` | `jobs.show` | Placeholder job detail string |
| GET | `/login` | `login` | Placeholder string |
| GET | `/register` | `register` | Placeholder string |
| GET | `/applicant/dashboard` | `applicant.dashboard` | Uses applicant dashboard controller behind `auth` |
| GET | `/applicant/applications` | `applicant.applications.index` | Placeholder string |
| GET | `/applicant/resume` | `applicant.resume` | Placeholder string |
| GET | `/applicant/profile/edit` | `applicant.profile.edit` | Placeholder string |

## Database Schema

The base Laravel/domain migrations are extended by a split schema migration set:

| Area | Concern |
| --- | --- |
| Auth and ownership | Future auth-provider fields and one-to-one owner constraints |
| Resume documents | Resume metadata and extracted text |
| Applications | Application to resume linkage |
| Job moderation | Job approval, rejection, closing, and soft-delete metadata |
| Notifications | Notification metadata and read timestamps |
| AI job matches | Cached AI job match results |
| Query support | Portable foreign-key and dashboard indexes |
| Postgres optimization | Constraints, JSONB/citext/numeric conversions, partial indexes, GIN indexes, and full-text search |

### Domain Tables

| Table | Purpose |
| --- | --- |
| `users` | Account records, roles, Laravel auth, future external auth ids |
| `profiles` | Applicant profile and skills |
| `companies` | Employer company profile |
| `job_categories` | Job category metadata |
| `job_listings` | Employer job posts, moderation lifecycle, soft deletion, search data |
| `applications` | Applicant submissions to jobs |
| `application_status_logs` | Application status history |
| `resume_documents` | Resume file metadata and extracted text |
| `saved_jobs` | Applicant saved jobs |
| `notifications` | In-app notifications |
| `ai_job_matches` | Cached AI recommendations and explanations |

### Status Values

`users.role`:

- `applicant`
- `employer`
- `admin`

`job_listings.status`:

- `draft`
- `pending`
- `active`
- `closed`
- `rejected`

`applications.status` and application status log values:

- `pending`
- `interview`
- `hired`
- `rejected`

`resume_documents.extraction_status`:

- `pending`
- `ready`
- `failed`

`ai_job_matches.generation_status`:

- `pending`
- `ready`
- `failed`

### Postgres Optimizations

- `users.email` is converted to `citext` on Postgres for case-insensitive
  email handling.
- `profiles.skills`, `job_listings.skills_required`, notification data, and AI
  match JSON fields are converted to JSONB on Postgres.
- Job salary fields are converted to `numeric(12,2)` on Postgres.
- `job_listings.search_vector` is generated for full-text job search.
- Partial indexes support active job browsing, admin pending queues, unread
  notifications, current resumes, and external auth uniqueness.
- Replacing a user's current resume must unset the old current resume and insert
  the new current resume in the same database transaction.
- GIN indexes support full-text search and JSONB skill matching.
- Foreign-key and composite indexes support dashboard and review queries.

## ERD

```mermaid
erDiagram
    USERS {
        bigint id PK
        text name
        citext email
        text role
        text password
        text auth_provider
        text external_auth_id
        timestamptz email_verified_at
        timestamptz created_at
        timestamptz updated_at
    }

    PROFILES {
        bigint id PK
        bigint user_id FK
        text headline
        text bio
        text location
        text phone
        text website
        text linkedin
        text github
        text desired_job_type
        text work_preference
        text experience_level
        text resume_path
        text avatar_path
        jsonb skills
        timestamptz created_at
        timestamptz updated_at
    }

    COMPANIES {
        bigint id PK
        bigint user_id FK
        text name
        text slug
        text logo_path
        text website
        text industry
        text size
        text location
        text description
        timestamptz created_at
        timestamptz updated_at
    }

    JOB_CATEGORIES {
        bigint id PK
        text name
        text slug
        text icon
        timestamptz created_at
        timestamptz updated_at
    }

    JOB_LISTINGS {
        bigint id PK
        bigint user_id FK
        bigint company_id FK
        bigint category_id FK
        text title
        text slug
        text location
        text location_type
        text type
        text experience_level
        text description
        text requirements
        jsonb skills_required
        numeric salary_min
        numeric salary_max
        text salary_currency
        text status
        text rejection_reason
        timestamptz submitted_at
        timestamptz approved_at
        bigint approved_by FK
        timestamptz rejected_at
        bigint rejected_by FK
        timestamptz closed_at
        timestamptz published_at
        timestamptz expires_at
        bigint views_count
        timestamptz deleted_at
        bigint deleted_by FK
        text delete_reason
        tsvector search_vector
        timestamptz created_at
        timestamptz updated_at
    }

    APPLICATIONS {
        bigint id PK
        bigint user_id FK
        bigint job_listing_id FK
        bigint resume_document_id FK
        text cover_letter
        text resume_path
        text status
        text employer_notes
        timestamptz status_updated_at
        timestamptz created_at
        timestamptz updated_at
    }

    APPLICATION_STATUS_LOGS {
        bigint id PK
        bigint application_id FK
        text old_status
        text new_status
        bigint changed_by FK
        text note
        timestamptz created_at
    }

    RESUME_DOCUMENTS {
        bigint id PK
        bigint user_id FK
        text file_path
        text original_name
        text mime_type
        bigint file_size
        text extracted_text
        text content_hash
        text extraction_status
        text extraction_error
        boolean is_current
        timestamptz created_at
        timestamptz updated_at
    }

    SAVED_JOBS {
        bigint id PK
        bigint user_id FK
        bigint job_listing_id FK
        timestamptz created_at
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        text type
        text title
        text message
        text link
        jsonb data
        boolean is_read
        timestamptz read_at
        timestamptz created_at
    }

    AI_JOB_MATCHES {
        bigint id PK
        bigint user_id FK
        bigint job_listing_id FK
        bigint resume_document_id FK
        numeric match_score "nullable until ready"
        jsonb score_breakdown
        jsonb matching_skills
        jsonb missing_skills
        text explanation
        text suggested_action
        text provider
        text model
        text prompt_version
        text profile_hash
        text resume_hash
        text job_hash
        text generation_status
        text error_message
        timestamptz generated_at
        timestamptz created_at
        timestamptz updated_at
    }

    USERS ||--o| PROFILES : has
    USERS ||--o| COMPANIES : owns
    USERS ||--o{ JOB_LISTINGS : posts
    USERS ||--o{ APPLICATIONS : submits
    USERS ||--o{ RESUME_DOCUMENTS : uploads
    USERS ||--o{ SAVED_JOBS : saves
    USERS ||--o{ NOTIFICATIONS : receives
    USERS ||--o{ AI_JOB_MATCHES : gets
    USERS ||--o{ APPLICATION_STATUS_LOGS : changes

    USERS ||--o{ JOB_LISTINGS : approves
    USERS ||--o{ JOB_LISTINGS : rejects
    USERS ||--o{ JOB_LISTINGS : deletes

    COMPANIES ||--o{ JOB_LISTINGS : has
    JOB_CATEGORIES ||--o{ JOB_LISTINGS : groups
    JOB_LISTINGS ||--o{ APPLICATIONS : receives
    JOB_LISTINGS ||--o{ SAVED_JOBS : saved_as
    JOB_LISTINGS ||--o{ AI_JOB_MATCHES : matched_for

    APPLICATIONS ||--o{ APPLICATION_STATUS_LOGS : logs
    RESUME_DOCUMENTS ||--o{ APPLICATIONS : used_for
    RESUME_DOCUMENTS ||--o{ AI_JOB_MATCHES : informs
```

## Project Structure

```text
app/
  Http/Controllers/
    Applicant/DashboardController.php
    Applicant/ApplicationController.php
    Applicant/ProfileController.php
    Employer/*.php
    Admin/*.php
    AuthController.php
    JobController.php
  Models/
    AiJobMatch.php
    AppNotification.php
    Application.php
    ApplicationStatusLog.php
    Company.php
    JobCategory.php
    JobListing.php
    Profile.php
    ResumeDocument.php
    SavedJob.php
    User.php
database/
  migrations/
  seeders/DatabaseSeeder.php
resources/
  views/
routes/
  web.php
```

## Tests

The test suite currently contains Laravel's default example tests:

- `tests/Feature/ExampleTest.php` checks that `/` returns HTTP 200.
- `tests/Unit/ExampleTest.php` checks a basic truth assertion.

Current verification performed for the schema work:

- PHP syntax checks for split migrations and affected models.
- `composer test`.
- Fresh temp SQLite migration smoke test.
- Rollback of the schema extension migrations.

Broader feature tests still need to be added for auth, role authorization, job
moderation, applications, notifications, and AI match generation.

## Next Priorities

1. Wire Laravel auth routes/controllers and role redirects.
2. Add role middleware and register applicant, employer, and admin route groups.
3. Implement public job browse/detail pages using `JobListing`.
4. Implement applicant profile, PDF resume upload, application submission, and
   application tracking.
5. Implement employer company profile, job CRUD, applicant review, and status
   updates.
6. Implement admin moderation for pending, approved, rejected, closed, and
   soft-deleted jobs.
7. Implement in-app notifications.
8. Add AI job matching service after the basic portal workflows are stable.
