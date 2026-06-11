# 📋 Job Portal System — Laravel Blueprint
**Tech Stack:** PHP Laravel · Bootstrap · MySQL · Blade Templates
**Purpose:** Structure planning only — no code, no migrations, no Blade logic yet.

---

## 1. 📄 Views Structure (Blade Files)

All views live under `resources/views/`. They are organized by role.

---

### 🌐 Guest / Public Pages
`resources/views/public/`

| File | Purpose |
|---|---|
| `home.blade.php` | Landing page — hero banner, featured jobs, CTA buttons |
| `jobs/index.blade.php` | Browse all job listings with search bar and filters |
| `jobs/show.blade.php` | Single job details page — description, employer info, Apply button |
| `about.blade.php` | About the platform — optional static page |
| `contact.blade.php` | Contact form for general inquiries |

---

### 🔐 Auth Pages
`resources/views/auth/`

| File | Purpose |
|---|---|
| `login.blade.php` | Login form (shared for all roles) |
| `register.blade.php` | Registration form with role selector (Applicant or Employer) |
| `passwords/email.blade.php` | Forgot password — send reset email |
| `passwords/reset.blade.php` | Reset password form |

---

### 👤 Applicant Pages
`resources/views/applicant/`

| File | Purpose |
|---|---|
| `dashboard.blade.php` | Applicant home — application status summary, recent activity |
| `profile/edit.blade.php` | Edit personal profile — name, contact info, bio |
| `resume/upload.blade.php` | Upload or replace resume (PDF) |
| `resume/view.blade.php` | Preview uploaded resume |
| `applications/index.blade.php` | List all submitted applications with status badges |
| `applications/show.blade.php` | Single application detail — job info, current status, timeline |

---

### 🏢 Employer Pages
`resources/views/employer/`

| File | Purpose |
|---|---|
| `dashboard.blade.php` | Employer home — job post stats, recent applicants summary |
| `profile/edit.blade.php` | Edit company profile — company name, logo, description |
| `jobs/index.blade.php` | List all jobs posted by this employer |
| `jobs/create.blade.php` | Form to create a new job posting |
| `jobs/edit.blade.php` | Edit an existing job posting |
| `jobs/show.blade.php` | View a specific job with list of applicants |
| `applicants/index.blade.php` | View all applicants across all job postings |
| `applicants/show.blade.php` | View a specific applicant's resume and profile |

---

### 🛡️ Admin Pages
`resources/views/admin/`

| File | Purpose |
|---|---|
| `dashboard.blade.php` | Admin home — system stats: users, jobs, applications |
| `users/index.blade.php` | List all users (applicants + employers) |
| `users/show.blade.php` | View a specific user's profile and activity |
| `jobs/index.blade.php` | List all job postings — with approve/reject actions |
| `jobs/show.blade.php` | Review a specific job post |
| `applications/index.blade.php` | Monitor all applications system-wide |

---

### 🧱 Shared / Layout Files
`resources/views/layouts/`

| File | Purpose |
|---|---|
| `app.blade.php` | Main layout — navbar, footer, Bootstrap CDN |
| `guest.blade.php` | Minimal layout for auth pages (login/register) |
| `admin.blade.php` | Admin-specific layout with sidebar |

`resources/views/partials/`

| File | Purpose |
|---|---|
| `navbar.blade.php` | Top navigation bar |
| `footer.blade.php` | Site footer |
| `alerts.blade.php` | Flash message alerts (success, error, warning) |
| `job-card.blade.php` | Reusable job listing card component |
| `status-badge.blade.php` | Reusable application status badge (pending, hired, etc.) |

---

## 2. 🧭 Routes Structure

All routes are defined in `routes/web.php`. No code yet — just the planned groupings and URL patterns.

---

### Public Routes (No Middleware)

```
GET  /                     → Home page
GET  /jobs                 → Browse all jobs
GET  /jobs/{id}            → View single job
GET  /about                → About page
GET  /contact              → Contact page
POST /contact              → Submit contact form
```

---

### Auth Routes

```
GET  /login                → Show login form
POST /login                → Handle login
GET  /register             → Show register form
POST /register             → Handle registration
POST /logout               → Logout user
GET  /password/reset       → Forgot password form
POST /password/email       → Send reset link
GET  /password/reset/{token} → Reset password form
POST /password/update      → Update password
```

---

### Applicant Routes (Middleware: `auth` + `role:applicant`)

```
GET  /applicant/dashboard           → Applicant dashboard
GET  /applicant/profile/edit        → Edit profile
PUT  /applicant/profile             → Update profile
GET  /applicant/resume              → View resume
POST /applicant/resume              → Upload resume
GET  /applicant/applications        → List all applications
GET  /applicant/applications/{id}   → View single application
POST /jobs/{id}/apply               → Submit application for a job
```

---

### Employer Routes (Middleware: `auth` + `role:employer`)

```
GET    /employer/dashboard           → Employer dashboard
GET    /employer/profile/edit        → Edit company profile
PUT    /employer/profile             → Update profile
GET    /employer/jobs                → List my job postings
GET    /employer/jobs/create         → Create new job form
POST   /employer/jobs                → Store new job
GET    /employer/jobs/{id}/edit      → Edit job form
PUT    /employer/jobs/{id}           → Update job
DELETE /employer/jobs/{id}           → Delete job
GET    /employer/jobs/{id}/applicants → View applicants for a job
GET    /employer/applicants/{id}     → View a specific applicant
PATCH  /employer/applications/{id}/status → Update application status
```

---

### Admin Routes (Middleware: `auth` + `role:admin`)

```
GET    /admin/dashboard              → Admin dashboard
GET    /admin/users                  → List all users
GET    /admin/users/{id}             → View user detail
DELETE /admin/users/{id}             → Delete/ban user
GET    /admin/jobs                   → List all job posts
PATCH  /admin/jobs/{id}/approve      → Approve a job post
PATCH  /admin/jobs/{id}/reject       → Reject a job post
DELETE /admin/jobs/{id}              → Remove a job post
GET    /admin/applications           → Monitor all applications
```

---

### Middleware Plan

| Middleware Name | Purpose |
|---|---|
| `auth` | Ensures user is logged in |
| `role:applicant` | Restricts to applicant role only |
| `role:employer` | Restricts to employer role only |
| `role:admin` | Restricts to admin role only |

> These role-check middlewares will be created in `app/Http/Middleware/`.

---

## 3. 🧠 Controllers Structure

All controllers live in `app/Http/Controllers/`. Organized by role subfolder.

---

### `AuthController`
**Location:** `app/Http/Controllers/AuthController.php`

Responsibilities:
- Show login and registration forms
- Handle login, registration, logout
- Redirect users to the correct dashboard based on role after login

---

### `JobController`
**Location:** `app/Http/Controllers/JobController.php`

Responsibilities:
- Display public job listings (index, show)
- Handle job search and filter logic (by title, location, category)
- Used by both guests and authenticated users for browsing

---

### `Applicant/DashboardController`
**Location:** `app/Http/Controllers/Applicant/DashboardController.php`

Responsibilities:
- Show applicant dashboard with summary statistics
- Show recent application activity

---

### `Applicant/ProfileController`
**Location:** `app/Http/Controllers/Applicant/ProfileController.php`

Responsibilities:
- Show and update applicant profile
- Handle resume upload and storage

---

### `Applicant/ApplicationController`
**Location:** `app/Http/Controllers/Applicant/ApplicationController.php`

Responsibilities:
- Submit a job application
- List all applications for the logged-in applicant
- Show single application detail and status history

---

### `Employer/DashboardController`
**Location:** `app/Http/Controllers/Employer/DashboardController.php`

Responsibilities:
- Show employer dashboard with job post stats and applicant counts

---

### `Employer/JobController`
**Location:** `app/Http/Controllers/Employer/JobController.php`

Responsibilities:
- CRUD operations for employer's own job postings
- View applicants per job posting

---

### `Employer/ApplicantController`
**Location:** `app/Http/Controllers/Employer/ApplicantController.php`

Responsibilities:
- View applicant profiles and resumes
- Update application status (pending → interview → hired / rejected)

---

### `Admin/DashboardController`
**Location:** `app/Http/Controllers/Admin/DashboardController.php`

Responsibilities:
- Show admin dashboard with platform-wide stats

---

### `Admin/UserController`
**Location:** `app/Http/Controllers/Admin/UserController.php`

Responsibilities:
- List all users
- View user details
- Delete or suspend user accounts

---

### `Admin/JobController`
**Location:** `app/Http/Controllers/Admin/JobController.php`

Responsibilities:
- List all job posts system-wide
- Approve or reject job posts pending moderation
- Force-delete any job post

---

### `Admin/ApplicationController`
**Location:** `app/Http/Controllers/Admin/ApplicationController.php`

Responsibilities:
- View all applications system-wide for monitoring

---

## 4. 🗂 Suggested Folder Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── JobController.php          ← public job browsing
│   │   ├── Applicant/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProfileController.php
│   │   │   └── ApplicationController.php
│   │   ├── Employer/
│   │   │   ├── DashboardController.php
│   │   │   ├── JobController.php
│   │   │   └── ApplicantController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── UserController.php
│   │       ├── JobController.php
│   │       └── ApplicationController.php
│   └── Middleware/
│       ├── CheckRole.php              ← custom role-check middleware
│       └── RedirectIfAuthenticated.php
├── Models/
│   ├── User.php
│   ├── Job.php
│   ├── Application.php
│   ├── Resume.php
│   └── EmployerProfile.php
resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php
    │   ├── guest.blade.php
    │   └── admin.blade.php
    ├── partials/
    │   ├── navbar.blade.php
    │   ├── footer.blade.php
    │   ├── alerts.blade.php
    │   ├── job-card.blade.php
    │   └── status-badge.blade.php
    ├── auth/
    ├── public/
    ├── applicant/
    ├── employer/
    └── admin/
routes/
└── web.php                            ← all routes in one file for now
```

---

## 5. 📘 Simple Development Guide

### Step 1 — Order of Implementation (What to Build First)

Follow this order to avoid blocking each other:

```
Phase 1 — Foundation
  1. Auth system (login, register, role-based redirect)
  2. Layouts + partials (navbar, footer, alerts)
  3. Public job listing page (index + show — no auth needed)

Phase 2 — Core Features
  4. Employer: post jobs (create, edit, delete)
  5. Applicant: browse jobs + apply + upload resume
  6. Application status system (pending → interview → hired/rejected)

Phase 3 — Dashboards
  7. Applicant dashboard (view own applications + statuses)
  8. Employer dashboard (view applicants per job, update status)

Phase 4 — Admin Panel
  9. Admin: moderate job posts (approve/reject)
  10. Admin: manage users and monitor applications

Phase 5 — Polish
  11. Job search and filtering
  12. Flash messages, form validation, error pages
  13. Final UI cleanup with Bootstrap
```

---

### Step 2 — How to Divide Work in a Team

Suggested team split (adjust for your group size):

| Member | Area |
|---|---|
| Member 1 | Auth system + layouts + partials |
| Member 2 | Public pages + job browsing + search |
| Member 3 | Applicant module (profile, resume, applications) |
| Member 4 | Employer module (job CRUD, view applicants, update status) |
| Member 5 | Admin panel (moderation, user management) |

> 💡 Tip: If you have fewer than 5 members, combine Member 1+2 or Member 4+5.

---

### Step 3 — How to Avoid Git Conflicts

Follow these simple Git rules:

1. **Each person works on their own branch.**
   Name branches clearly: `feature/auth`, `feature/employer-jobs`, `feature/admin-panel`

2. **Never commit directly to `main`.**
   Always work on your feature branch, then open a Pull Request to merge.

3. **Pull from `main` before starting new work.**
   Run `git pull origin main` each time you begin a session.

4. **Divide files by role — one person owns one folder.**
   Member 3 only touches `resources/views/applicant/` and `app/Http/Controllers/Applicant/`.
   This prevents two people from editing the same file.

5. **Communicate before touching shared files.**
   Shared files like `web.php`, `app.blade.php`, and `navbar.blade.php` should be edited
   by one designated person (Member 1), or changes should be coordinated via chat first.

6. **Commit small and often.**
   Don't save up a week of work into one giant commit. Small commits = smaller conflicts.

---

### Step 4 — How Routing + Views Connect Logically

Think of it as a pipeline:

```
User visits URL
      ↓
Route in web.php matches the URL
      ↓
Route calls the correct Controller method
      ↓
Controller prepares data (from Model)
      ↓
Controller returns a View (Blade file)
      ↓
Blade file renders HTML using the data
```

**Example (Applicant views their applications):**

```
URL:        /applicant/applications
Route:      → ApplicationController@index  (in Applicant group)
Controller: fetches all applications for logged-in user
View:       resources/views/applicant/applications/index.blade.php
```

Always keep this pipeline in mind. If something doesn't show up on screen, trace back:
Did the route exist? → Did it point to the right controller? → Did the controller pass data to the right view?

---

### Step 5 — Best Practices for Laravel Teams

1. **Use `@extends` and `@section` in every Blade file.**
   Every page should extend a layout (`layouts/app`) and yield content into `@section('content')`.

2. **Keep controllers thin.**
   Controllers should only receive input, talk to models, and return views.
   No complex logic inside controllers — that goes in the Model or a Service class later.

3. **Name routes.**
   Use named routes like `route('applicant.applications.index')` instead of hardcoded URLs.
   This makes it easy to change URLs without breaking links everywhere.

4. **Use `middleware groups` in routes.**
   Group all applicant routes under one `->middleware(['auth', 'role:applicant'])` block.
   This keeps web.php clean and easy to read.

5. **Test each feature before merging.**
   Before opening a Pull Request, manually test your feature in the browser.
   Check that forms submit, redirects work, and the right pages load.

6. **Use `.env` for environment config — never hardcode credentials.**
   Database name, app URL, and mail settings all go in `.env`, not in PHP files.

7. **Keep the `public/` folder tidy.**
   Put Bootstrap CSS/JS links in your layout file. Put any custom CSS in `public/css/app.css`.

---
