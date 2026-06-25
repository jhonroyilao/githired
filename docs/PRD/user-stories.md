# GitHired User Stories

Source documents:
- `README.md`
- `docs/PRD/basic-job-portal-mvp.md`
- `docs/PRD/ai-job-match-mvp.md`
- `docs/PRD/github-issue-backlog.md`

GitHired uses three product roles: `admin`, `employer`, and `applicant`.
This document uses "Applicant" as the job seeker role requested by the business,
mapped to the existing `applicant` role in the product and database.

## Admin User Stories

### Admin Story 1: Moderate Pending Jobs

As an admin, I want to review pending employer job posts so that only approved
jobs appear in public and applicant job browsing.

Acceptance criteria:
- Admin can view a queue of pending job posts.
- Admin can inspect job title, company, location, type, category, salary,
  description, requirements, and required skills before making a decision.
- Pending, draft, rejected, closed, and soft-deleted jobs do not appear in
  public browsing.
- Non-admin users cannot access the moderation queue.

### Admin Story 2: Approve Job Posts

As an admin, I want to approve valid employer job posts so that applicants can
discover and apply to them.

Acceptance criteria:
- Admin can approve a pending job post.
- Approved jobs become `active`.
- Approved jobs appear in public and applicant browsing.
- Approval metadata is recorded, including approver and timestamp.
- Employer receives an in-app notification when a job is approved.

### Admin Story 3: Reject Job Posts

As an admin, I want to reject unsuitable job posts with a reason so that
employers understand what needs to be fixed.

Acceptance criteria:
- Admin can reject a pending job post.
- Rejected jobs remain hidden from public and applicant browsing.
- A rejection reason is required and stored.
- Rejection metadata is recorded, including rejector and timestamp.
- Employer receives an in-app notification when a job is rejected.

### Admin Story 4: Hide Or Soft-Delete Jobs

As an admin, I want to hide or soft-delete existing job posts so that invalid or
outdated jobs are removed from browsing without losing historical records.

Acceptance criteria:
- Admin can hide or soft-delete an existing job post.
- Hidden or soft-deleted jobs no longer appear in public browsing.
- Existing applications for the job remain available in historical application
  records.
- Deletion metadata is recorded where supported by the schema.
- Non-admin users cannot hide or soft-delete jobs.

## Employer User Stories

### Employer Story 1: Register And Access Employer Dashboard

As an employer, I want to register, log in, and land on my employer dashboard so
that I can manage my company, jobs, and applicants.

Acceptance criteria:
- Employer can register and log in with email and password.
- Employer is redirected to `/employer/dashboard` after login.
- Employer cannot access applicant or admin dashboards.
- Unauthenticated users are redirected to login.

### Employer Story 2: Manage Company Profile

As an employer, I want to create and edit my company profile so that job seekers
can understand who is hiring.

Acceptance criteria:
- Employer can create one company profile.
- Employer can edit company name, slug, logo path, website, industry, size,
  location, and description.
- Company slugs are unique.
- Employer cannot edit another employer's company profile.

### Employer Story 3: Create And Manage Job Posts

As an employer, I want to create, edit, view, and manage my own job posts so
that I can publish hiring opportunities through GitHired.

Acceptance criteria:
- Employer can create job posts with the required PRD job fields.
- New submitted job posts default to `pending` until admin approval.
- Employer can see each job status: `draft`, `pending`, `active`, `closed`, or
  `rejected`.
- Employer can edit only their own job posts where product rules allow.
- Pending jobs are hidden from public and applicant browsing until approved.

### Employer Story 4: Review Applicants

As an employer, I want to view applicants for my own job posts so that I can
evaluate candidates.

Acceptance criteria:
- Employer can see applications submitted to their own jobs.
- Employer can view applicant profile details, cover letter, resume metadata or
  link, application status, and submitted date.
- Employer cannot view applicants for another employer's jobs.
- Empty states are shown when a job has no applicants.

### Employer Story 5: Update Application Status

As an employer, I want to update application statuses so that applicants know
where they are in the hiring process.

Acceptance criteria:
- Employer can update applications for their own jobs to `pending`,
  `interview`, `hired`, or `rejected`.
- Every status change creates an `application_status_logs` entry.
- Status log records include previous status, new status, actor, and timestamp.
- Applicant receives an in-app notification when their application status
  changes.
- Invalid or unauthorized status changes are rejected.

## Applicant User Stories

### Applicant Story 1: Register And Access Applicant Dashboard

As an Applicant, I want to register, log in, and land on my applicant dashboard
so that I can manage my job search.

Acceptance criteria:
- Applicant can register and log in with email and password as an applicant.
- Applicant onboarding captures desired job type, work preference, and
  experience level during registration or first-run setup.
- Desired job type is stored in `profiles.desired_job_type`.
- Work preference is stored in `profiles.work_preference`.
- Experience level is stored in `profiles.experience_level`.
- Preference values use the same vocabulary as job listings so matching can
  compare profile preferences directly against job fields.
- Preferences contribute to profile completeness.
- Preferences are used as soft matching signals, not strict filters, unless a
  future feature explicitly lets the applicant mark them as required.
- Applicant is redirected to `/applicant/dashboard` after login.
- Applicant cannot access employer or admin dashboards.
- Dashboard shows relevant application stats, recent applications, recommended
  jobs, and profile completeness where available.

### Applicant Story 2: Manage Applicant Profile

As an Applicant, I want to create and update my applicant profile so that
employers and matching features can understand my background.

Acceptance criteria:
- Applicant can create a profile when one does not exist.
- Applicant can update name, headline, bio, location, phone, website or social
  links, desired job type, work preference, experience level, and skills.
- Desired job type supports `full-time`, `part-time`, `contract`, and
  `internship`.
- Work preference supports `remote`, `onsite`, and `hybrid`.
- Experience level supports `entry`, `mid`, and `senior`.
- Skills are stored in a consistent structured format.
- Applicant cannot edit another user's profile.
- Profile data is reusable for future AI job matching.

### Applicant Story 3: Upload Resume

As an Applicant, I want to upload a PDF resume so that I can apply to jobs and
receive better future job recommendations.

Acceptance criteria:
- Applicant can upload a PDF resume.
- Non-PDF uploads are rejected with validation errors.
- The newest resume is marked as the current resume.
- Previous resumes remain stored but are no longer current.
- Applicant cannot view or replace another user's resume.
- Resume text can be extracted and stored for future AI matching.

### Applicant Story 4: Browse And Search Jobs

As an Applicant, I want to browse, search, and filter approved active jobs so
that I can find relevant opportunities.

Acceptance criteria:
- Applicant can browse only active, approved, non-deleted jobs.
- Applicant can search and filter by keyword, location, job type, category, and
  experience level.
- Hidden job statuses are excluded from lists and direct access.
- Empty results show a useful empty state.

### Applicant Story 5: View Job Details

As an Applicant, I want to view job details so that I can decide whether to
apply.

Acceptance criteria:
- Applicant can view active approved job details.
- Job detail includes title, company, location, salary, type, category,
  experience level, description, requirements, and required skills.
- Applicant sees an apply action when authenticated.
- Employer and admin controls are not shown to Applicants.

### Applicant Story 6: Apply To Jobs

As an Applicant, I want to apply to a job with my resume and optional cover
letter so that employers can review my candidacy.

Acceptance criteria:
- Applicant can apply to an active approved job.
- Applicant can include an optional cover letter.
- Current resume document is attached when available.
- Duplicate applications to the same job are blocked.
- Applications to inactive, hidden, rejected, closed, or deleted jobs are
  blocked.
- New applications start in `pending` status.

### Applicant Story 7: Track Applications

As an Applicant, I want to track my submitted applications so that I know my
current hiring status.

Acceptance criteria:
- Applicant can see only their own submitted applications.
- Application list shows job, company, submitted date, and current status.
- Application statuses support `pending`, `interview`, `hired`, and
  `rejected`.
- Applicant can view status history where available.
- Applicant receives in-app notifications when application status changes.

### Applicant Story 8: Receive AI Recommended Jobs

As an Applicant, I want to see jobs that fit my profile and resume, with clear
reasons why they match, so that I can apply faster and more confidently.

Acceptance criteria:
- Applicant with completed profile and resume sees 3-5 AI recommended active
  jobs on the applicant dashboard.
- Recommendations exclude inactive, closed, rejected, deleted, hidden, and
  already-applied jobs.
- Recommendations rank jobs higher when job type, work preference, and
  experience level match the applicant's onboarding preferences.
- Recommendations do not hide otherwise relevant jobs only because one
  preference does not match.
- Each recommendation includes job title, company, location, salary when
  available, match percentage, explanation, matching skills, missing or
  suggested skills, and a `View Job` action.
- Applicant without a resume can still receive profile-based recommendations.
- Applicant without enough profile data sees a profile-completion prompt.
- OpenAI failure does not break the dashboard; deterministic recommendations
  are shown as a fallback.
- Resume and profile content are sent to AI services only from the server.
