@extends('common.main')

@section('content')

{{-- ============================================================
     SECTION 0: PAGE HEADER
     Used by: all pages (public, applicant, employer, admin)
     ============================================================ --}}
<div class="gh-page-header">
    <div>
        <h1 class="gh-page-title">UI Mockup — Job Portal System</h1>
        <p class="gh-page-subtitle">Every component, badge, form element, and layout pattern used across all roles</p>
    </div>
</div>


{{-- ============================================================
     SECTION 1: FLASH ALERTS
     Partial: partials/alerts.blade.php
     Used on: every page after form submissions
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Flash Alerts <small class="text-muted fs-6 fw-normal">— partials/alerts.blade.php</small></h2>
    
    <div class="gh-alert gh-alert-success">
        <i class="bi bi-check-circle-fill"></i>
        <strong>Success!</strong> Your application was submitted successfully.
    </div>

    <div class="gh-alert gh-alert-warning">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <strong>Warning!</strong> Your resume is missing. Employers may not review your application.
    </div>

    <div class="gh-alert gh-alert-danger">
        <i class="bi bi-exclamation-circle-fill"></i>
        <strong>Error!</strong> Something went wrong. Please try again.
    </div>

    <div class="gh-alert gh-alert-info">
        <i class="bi bi-info-circle-fill"></i>
        <strong>Info:</strong> This job post is pending admin approval.
    </div>
</div>


{{-- ============================================================
     SECTION 2: STATUS BADGES
     Partial: partials/status-badge.blade.php
     Used on: applications/index, applications/show, employer applicants view
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Status Badges <small class="text-muted fs-6 fw-normal">— partials/status-badge.blade.php</small></h2>
    <p class="text-muted mb-3">Application status pipeline: <code>pending → interview → hired / rejected</code></p>
    <div class="d-flex flex-wrap gap-2">
        <span class="gh-badge gh-badge-pending">Pending</span>
        <span class="gh-badge gh-badge-interview">Interview</span>
        <span class="gh-badge gh-badge-hired">Hired</span>
        <span class="gh-badge gh-badge-rejected">Rejected</span>
        
        {{-- Admin Statuses --}}
        <span class="gh-badge gh-badge-draft">Under Review</span>
        <span class="gh-badge gh-badge-active">Approved</span>
        <span class="gh-badge gh-badge-rejected">Rejected by Admin</span>
    </div>
</div>


{{-- ============================================================
     SECTION 3: JOB CARD
     Partial: partials/job-card.blade.php
     Used on: public/jobs/index, employer/jobs/index, admin/jobs/index
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Job Cards <small class="text-muted fs-6 fw-normal">— partials/job-card.blade.php</small></h2>

    {{-- Single job card --}}
    <div class="gh-job-card mb-3" style="max-width: 560px;">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="d-flex gap-3">
                <div class="gh-job-company-logo">AC</div>
                <div>
                    <h5 class="gh-card-title mb-1" style="margin-bottom: 0;">Software Engineer — Full Stack</h5>
                    <p class="text-muted mb-0 small"><strong>Acme Corp</strong> &nbsp;·&nbsp; 📍 Metro Manila</p>
                </div>
            </div>
            <span class="gh-badge gh-badge-draft">Under Review</span>
        </div>
        
        <p class="text-muted small mb-3">🕐 Full-time &nbsp;·&nbsp; ₱60,000 – ₱90,000 / mo</p>
        
        <div class="d-flex gap-2 flex-wrap mb-2">
            <span class="badge bg-light text-dark border">PHP</span>
            <span class="badge bg-light text-dark border">Laravel</span>
            <span class="badge bg-light text-dark border">MySQL</span>
        </div>
        
        <hr class="gh-divider">
        
        <div class="d-flex gap-2">
            <a href=" " class="btn-gh-primary">View Details</a>
            <a href=" " class="btn-gh-outline">Save Job</a>
        </div>
    </div>

    {{-- Job card grid preview --}}
    <p class="text-muted small">Grid layout (jobs/index page — 3 columns on desktop):</p>
    <div class="row g-3">
        @foreach([
            ['UI/UX Designer', 'DesignHub', 'Makati', 'gh-badge-active', 'Approved'],
            ['Data Analyst', 'DataWorks PH', 'Remote', 'gh-badge-active', 'Approved'],
            ['DevOps Engineer', 'CloudBase', 'BGC', 'gh-badge-draft', 'Under Review'],
        ] as $job)
        <div class="col-md-4">
            <div class="gh-job-card h-100 d-flex flex-column">
                <div class="d-flex justify-content-between mb-2">
                    <h6 class="gh-card-title mb-0">{{ $job[0] }}</h6>
                    <span class="gh-badge {{ $job[3] }}">{{ $job[4] }}</span>
                </div>
                <p class="text-muted small mb-3">{{ $job[1] }} · 📍 {{ $job[2] }}</p>
                <div class="mt-auto">
                    <a href=" " class="btn-gh-outline" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">View</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>


{{-- ============================================================
     SECTION 4: SEARCH BAR + FILTERS
     Used on: public/jobs/index
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Job Search & Filters <small class="text-muted fs-6 fw-normal">— public/jobs/index.blade.php</small></h2>
    <form class="mb-3">
        <div class="row g-2">
            <div class="col-md-5">
                <input type="text" class="gh-input" placeholder="Job title, keyword, or skill…">
            </div>
            <div class="col-md-3">
                <input type="text" class="gh-input" placeholder="Location">
            </div>
            <div class="col-md-2">
                <select class="gh-input">
                    <option selected disabled>Category</option>
                    <option>IT / Software</option>
                    <option>Design</option>
                    <option>Marketing</option>
                    <option>Finance</option>
                    <option>Admin / Office</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-gh-primary w-100 justify-content-center">Search</button>
            </div>
        </div>
    </form>
    <div class="d-flex gap-2 flex-wrap">
        <span class="badge d-flex align-items-center gap-1" style="background: var(--gh-navy-soft); color: #fff; font-weight: 500; padding: 0.4rem 0.75rem;">
            Full-time <i class="bi bi-x" style="cursor: pointer; font-size: 1.1rem; line-height: 0.5;"></i>
        </span>
        <span class="badge d-flex align-items-center gap-1" style="background: var(--gh-navy-soft); color: #fff; font-weight: 500; padding: 0.4rem 0.75rem;">
            Remote <i class="bi bi-x" style="cursor: pointer; font-size: 1.1rem; line-height: 0.5;"></i>
        </span>
        <span class="badge d-flex align-items-center gap-1" style="background: var(--gh-navy-soft); color: #fff; font-weight: 500; padding: 0.4rem 0.75rem;">
            IT / Software <i class="bi bi-x" style="cursor: pointer; font-size: 1.1rem; line-height: 0.5;"></i>
        </span>
        <a href=" " class="small align-self-center fw-medium" style="color: var(--gh-danger); text-decoration: none;">Clear all filters</a>
    </div>
</div>


{{-- ============================================================
     SECTION 5: AUTH FORMS
     Used on: auth/login.blade.php, auth/register.blade.php
     Layout: layouts/guest.blade.php (minimal, no navbar/footer)
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Auth Forms <small class="text-muted fs-6 fw-normal">— auth/login + auth/register</small></h2>

    <div class="row g-4">
        {{-- Login form --}}
        <div class="col-md-5">
            <div class="gh-card h-100" style="background: var(--gh-slate); box-shadow: none;">
                <h4 class="mb-4" style="color: var(--gh-navy); font-family: var(--font-display); font-weight: 700;">Login</h4>
                <div class="mb-3">
                    <label class="gh-label">Email address</label>
                    <input type="email" class="gh-input" placeholder="you@example.com">
                </div>
                <div class="mb-4">
                    <label class="gh-label">Password</label>
                    <input type="password" class="gh-input" placeholder="••••••••">
                    <div class="text-end mt-2">
                        <a href=" " class="small" style="color: var(--gh-indigo); text-decoration: none; font-weight: 500;">Forgot password?</a>
                    </div>
                </div>
                <button class="btn-gh-primary w-100 justify-content-center">Log In</button>
                <p class="text-center text-muted small mt-4 mb-0">Don't have an account? <a href=" " style="color: var(--gh-indigo); text-decoration: none; font-weight: 600;">Register</a></p>
            </div>
        </div>

        {{-- Register form --}}
        <div class="col-md-7">
            <div class="gh-card h-100" style="background: var(--gh-slate); box-shadow: none;">
                <h4 class="mb-4" style="color: var(--gh-navy); font-family: var(--font-display); font-weight: 700;">Create Account</h4>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="gh-label">First Name</label>
                        <input type="text" class="gh-input" placeholder="Maria">
                    </div>
                    <div class="col-6">
                        <label class="gh-label">Last Name</label>
                        <input type="text" class="gh-input" placeholder="Santos">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="gh-label">Email address</label>
                    <input type="email" class="gh-input" placeholder="you@example.com">
                </div>
                <div class="mb-3">
                    <label class="gh-label">Password</label>
                    <input type="password" class="gh-input" placeholder="••••••••">
                </div>
                <div class="mb-4">
                    <label class="gh-label mb-2">I am registering as…</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="role-applicant" checked>
                            <label class="form-check-label" for="role-applicant" style="color: var(--gh-text);">👤 Applicant</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="role-employer">
                            <label class="form-check-label" for="role-employer" style="color: var(--gh-text);">🏢 Employer</label>
                        </div>
                    </div>
                </div>
                <button class="btn-gh-primary w-100 justify-content-center">Create Account</button>
                <p class="text-center text-muted small mt-4 mb-0">Already registered? <a href=" " style="color: var(--gh-indigo); text-decoration: none; font-weight: 600;">Log in</a></p>
            </div>
        </div>
    </div>
</div>


{{-- ============================================================
     SECTION 6: STAT SUMMARY CARDS (DASHBOARDS)
     Used on: applicant/dashboard, employer/dashboard, admin/dashboard
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Dashboard Stat Cards <small class="text-muted fs-6 fw-normal">— **/dashboard.blade.php</small></h2>

    {{-- Applicant stats --}}
    <p class="text-muted small mb-2">Applicant view:</p>
    <div class="row g-3 mb-4">
        @foreach([
            ['5', 'Total Applications', 'gh-stat-info'],
            ['2', 'Pending Review', 'gh-stat-warning'],
            ['1', 'Interview Scheduled', 'gh-stat-success'],
            ['1', 'Rejected', 'gh-stat-danger'],
        ] as $stat)
        <div class="col-6 col-md-3">
            <div class="gh-stat {{ $stat[2] }}">
                <div class="gh-stat-label">{{ $stat[1] }}</div>
                <div class="gh-stat-value">{{ $stat[0] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Employer stats --}}
    <p class="text-muted small mb-2">Employer view:</p>
    <div class="row g-3 mb-4">
        @foreach([
            ['8', 'Active Job Posts', 'gh-stat-info'],
            ['34', 'Total Applicants', 'gh-stat-success'],
            ['12', 'Pending Review', 'gh-stat-warning'],
            ['6', 'Hired This Month', 'gh-stat-success'],
        ] as $stat)
        <div class="col-6 col-md-3">
            <div class="gh-stat {{ $stat[2] }}">
                <div class="gh-stat-label">{{ $stat[1] }}</div>
                <div class="gh-stat-value">{{ $stat[0] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Admin stats --}}
    <p class="text-muted small mb-2">Admin view:</p>
    <div class="row g-3">
        @foreach([
            ['142', 'Total Users', 'gh-stat-info'],
            ['57', 'Job Posts', 'gh-stat-success'],
            ['230', 'Applications', 'gh-stat-info'],
            ['9', 'Pending Approval', 'gh-stat-warning'],
        ] as $stat)
        <div class="col-6 col-md-3">
            <div class="gh-stat {{ $stat[2] }}">
                <div class="gh-stat-label">{{ $stat[1] }}</div>
                <div class="gh-stat-value">{{ $stat[0] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>


{{-- ============================================================
     SECTION 7: DATA TABLES
     Used on: applicant/applications/index, employer/applicants/index,
              admin/users/index, admin/jobs/index, admin/applications/index
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Data Tables <small class="text-muted fs-6 fw-normal">— */index.blade.php</small></h2>

    {{-- Applicant: My Applications table --}}
    <p class="text-muted small mb-2">Applicant — My Applications:</p>
    <div class="table-responsive mb-4">
        <table class="gh-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Applied On</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-muted">1</td>
                    <td style="color: var(--gh-indigo); font-weight: 600;">Software Engineer</td>
                    <td>Acme Corp</td>
                    <td>Jun 1, 2025</td>
                    <td><span class="gh-badge gh-badge-interview">Interview</span></td>
                    <td><a href=" " class="btn-gh-outline" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">View</a></td>
                </tr>
                <tr>
                    <td class="text-muted">2</td>
                    <td style="color: var(--gh-indigo); font-weight: 600;">Backend Developer</td>
                    <td>CloudBase</td>
                    <td>May 28, 2025</td>
                    <td><span class="gh-badge gh-badge-pending">Pending</span></td>
                    <td><a href=" " class="btn-gh-outline" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">View</a></td>
                </tr>
                <tr>
                    <td class="text-muted">3</td>
                    <td style="color: var(--gh-indigo); font-weight: 600;">DevOps Engineer</td>
                    <td>DataWorks PH</td>
                    <td>May 20, 2025</td>
                    <td><span class="gh-badge gh-badge-rejected">Rejected</span></td>
                    <td><a href=" " class="btn-gh-outline" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">View</a></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Admin: Job Posts moderation table --}}
    <p class="text-muted small mb-2">Admin — Manage Job Posts:</p>
    <div class="table-responsive mb-4">
        <table class="gh-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Job Title</th>
                    <th>Employer</th>
                    <th>Posted</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-muted">1</td>
                    <td style="color: var(--gh-indigo); font-weight: 600;">Software Engineer</td>
                    <td>Acme Corp</td>
                    <td>Jun 3, 2025</td>
                    <td><span class="gh-badge gh-badge-draft">Under Review</span></td>
                    <td class="d-flex gap-1">
                        <a href=" " class="btn-gh-primary" style="background: var(--gh-success); padding: 0.25rem 0.6rem; font-size: 0.75rem;">Approve</a>
                        <a href=" " class="btn-gh-primary" style="background: var(--gh-danger); padding: 0.25rem 0.6rem; font-size: 0.75rem;">Reject</a>
                        <a href=" " class="btn-gh-outline" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">View</a>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">2</td>
                    <td style="color: var(--gh-indigo); font-weight: 600;">UI/UX Designer</td>
                    <td>DesignHub</td>
                    <td>Jun 1, 2025</td>
                    <td><span class="gh-badge gh-badge-active">Approved</span></td>
                    <td class="d-flex gap-1">
                        <a href=" " class="btn-gh-outline" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">View</a>
                        <a href=" " class="btn-gh-primary" style="background: var(--gh-danger); padding: 0.25rem 0.6rem; font-size: 0.75rem;">Remove</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Admin: All Users table --}}
    <p class="text-muted small mb-2">Admin — All Users:</p>
    <div class="table-responsive">
        <table class="gh-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-muted">1</td>
                    <td style="color: var(--gh-indigo); font-weight: 600;">Maria Santos</td>
                    <td>maria@example.com</td>
                    <td><span class="badge" style="background: var(--gh-navy-soft); color: #fff; font-weight: 500;">Applicant</span></td>
                    <td>May 10, 2025</td>
                    <td class="d-flex gap-1">
                        <a href=" " class="btn-gh-outline" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">View</a>
                        <a href=" " class="btn-gh-primary" style="background: var(--gh-danger); padding: 0.25rem 0.6rem; font-size: 0.75rem;">Ban</a>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">2</td>
                    <td style="color: var(--gh-indigo); font-weight: 600;">Juan dela Cruz</td>
                    <td>juan@acmecorp.com</td>
                    <td><span class="badge" style="background: var(--gh-indigo); color: #fff; font-weight: 500;">Employer</span></td>
                    <td>Apr 22, 2025</td>
                    <td class="d-flex gap-1">
                        <a href=" " class="btn-gh-outline" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">View</a>
                        <a href=" " class="btn-gh-primary" style="background: var(--gh-danger); padding: 0.25rem 0.6rem; font-size: 0.75rem;">Ban</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


{{-- ============================================================
     SECTION 8: PROFILE EDIT FORMS
     Used on: applicant/profile/edit.blade.php, employer/profile/edit.blade.php
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Profile Edit Forms <small class="text-muted fs-6 fw-normal">— */profile/edit.blade.php</small></h2>

    <div class="row g-4">
        {{-- Applicant profile form --}}
        <div class="col-md-6">
            <div class="gh-card h-100" style="box-shadow: none;">
                <h4 class="mb-4" style="color: var(--gh-navy); font-family: var(--font-display); font-weight: 700;">👤 Applicant Profile</h4>
                <div class="mb-3">
                    <label class="gh-label">Full Name</label>
                    <input type="text" class="gh-input" value="Maria Santos">
                </div>
                <div class="mb-3">
                    <label class="gh-label">Email</label>
                    <input type="email" class="gh-input" value="maria@example.com">
                </div>
                <div class="mb-3">
                    <label class="gh-label">Phone Number</label>
                    <input type="text" class="gh-input" placeholder="+63 9xx xxx xxxx">
                </div>
                <div class="mb-3">
                    <label class="gh-label">Bio / Short Introduction</label>
                    <textarea class="gh-input" rows="3" placeholder="Tell employers a little about yourself…"></textarea>
                </div>
                <div class="mb-4">
                    <label class="gh-label">Skills <span class="text-muted fw-normal" style="font-size: 0.75rem;">(comma-separated)</span></label>
                    <input type="text" class="gh-input" value="PHP, Laravel, MySQL, Vue.js">
                </div>
                <button class="btn-gh-primary">Save Changes</button>
            </div>
        </div>

        {{-- Employer profile form --}}
        <div class="col-md-6">
            <div class="gh-card h-100" style="box-shadow: none;">
                <h4 class="mb-4" style="color: var(--gh-navy); font-family: var(--font-display); font-weight: 700;">🏢 Employer / Company Profile</h4>
                <div class="mb-3">
                    <label class="gh-label">Company Name</label>
                    <input type="text" class="gh-input" value="Acme Corp">
                </div>
                <div class="mb-3">
                    <label class="gh-label">Company Email</label>
                    <input type="email" class="gh-input" value="hr@acmecorp.com">
                </div>
                <div class="mb-3">
                    <label class="gh-label">Industry</label>
                    <select class="gh-input">
                        <option selected>IT / Software</option>
                        <option>Finance</option>
                        <option>Healthcare</option>
                        <option>Education</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="gh-label">Company Logo</label>
                    <input type="file" class="gh-input" accept="image/*" style="padding: 0.375rem 0.75rem;">
                    <div class="mt-2 d-flex align-items-center">
                        <img src=" " alt="Logo Preview" style="width:64px; height:64px; object-fit:cover; border-radius: var(--radius-sm); border: 1px solid var(--gh-border);">
                        <span class="text-muted small ms-3">Current logo</span>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="gh-label">Company Description</label>
                    <textarea class="gh-input" rows="3" placeholder="Describe your company…"></textarea>
                </div>
                <button class="btn-gh-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>


{{-- ============================================================
     SECTION 9: JOB POSTING FORM (CREATE / EDIT)
     Used on: employer/jobs/create.blade.php, employer/jobs/edit.blade.php
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Job Posting Form <small class="text-muted fs-6 fw-normal">— employer/jobs/create + edit</small></h2>
    <div class="gh-card" style="background: var(--gh-slate); box-shadow: none;">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="gh-label">Job Title</label>
                <input type="text" class="gh-input" placeholder="e.g. Senior Laravel Developer">
            </div>
            <div class="col-md-4">
                <label class="gh-label">Employment Type</label>
                <select class="gh-input">
                    <option selected disabled>Select type</option>
                    <option>Full-time</option>
                    <option>Part-time</option>
                    <option>Contract</option>
                    <option>Internship</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="gh-label">Location</label>
                <input type="text" class="gh-input" placeholder="e.g. BGC, Taguig or Remote">
            </div>
            <div class="col-md-3">
                <label class="gh-label">Min Salary (₱)</label>
                <input type="number" class="gh-input" placeholder="50000">
            </div>
            <div class="col-md-3">
                <label class="gh-label">Max Salary (₱)</label>
                <input type="number" class="gh-input" placeholder="90000">
            </div>
            <div class="col-md-6">
                <label class="gh-label">Category</label>
                <select class="gh-input">
                    <option selected disabled>Select category</option>
                    <option>IT / Software</option>
                    <option>Design</option>
                    <option>Marketing</option>
                    <option>Finance</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="gh-label">Application Deadline</label>
                <input type="date" class="gh-input">
            </div>
            <div class="col-12">
                <label class="gh-label">Job Description</label>
                <textarea class="gh-input" rows="5" placeholder="Describe responsibilities, requirements, and perks…"></textarea>
            </div>
            <div class="col-12">
                <label class="gh-label">Required Skills <span class="text-muted fw-normal" style="font-size: 0.75rem;">(comma-separated)</span></label>
                <input type="text" class="gh-input" placeholder="e.g. Laravel, PHP, MySQL, REST API">
            </div>
            <div class="col-12 d-flex gap-2 mt-4">
                <button class="btn-gh-primary">Post Job</button>
                <a href=" " class="btn-gh-outline">Cancel</a>
            </div>
        </div>
    </div>
</div>


{{-- ============================================================
     SECTION 10: RESUME UPLOAD
     Used on: applicant/resume/upload.blade.php
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Resume Upload <small class="text-muted fs-6 fw-normal">— applicant/resume/upload.blade.php</small></h2>
    
    {{-- Kept the 'dropzone' vibe, just mapped to your custom variables --}}
    <div class="p-4" style="max-width: 520px; background: var(--gh-slate); border: 2px dashed var(--gh-border); border-radius: var(--radius-md);">
        <div class="mb-4">
            <label class="gh-label">Upload Resume <span class="text-muted fw-normal" style="font-size: 0.75rem;">(PDF only, max 5MB)</span></label>
            <input type="file" class="gh-input" accept=".pdf" style="padding: 0.375rem 0.75rem; background: #fff;">
        </div>
        
        {{-- Already uploaded state --}}
        <div class="gh-alert gh-alert-success py-2 d-flex align-items-center gap-3 mb-4" style="margin-bottom: 0;">
            <i class="bi bi-paperclip fs-4"></i>
            <div>
                <strong style="font-family: var(--font-display);">resume_maria_santos.pdf</strong><br>
                <span style="font-size: 0.75rem; opacity: 0.85;">Uploaded Jun 1, 2025 · 1.2 MB</span>
            </div>
            <a href=" " class="btn-gh-outline ms-auto" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; background: #fff;">Preview</a>
        </div>
        
        <div class="d-flex gap-2">
            <button class="btn-gh-primary">Replace Resume</button>
        </div>
    </div>
</div>


{{-- ============================================================
     SECTION 11: APPLICATION DETAIL & TIMELINE
     Used on: applicant/applications/show.blade.php
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Application Detail + Timeline <small class="text-muted fs-6 fw-normal">— applicant/applications/show</small></h2>
    <div class="row g-4 mt-1">
        <div class="col-md-6">
            <h4 class="mb-4" style="color: var(--gh-navy); font-family: var(--font-display); font-weight: 700;">Job Info</h4>
            <p class="mb-2"><strong style="color: var(--gh-navy-soft);">Position:</strong> Software Engineer — Full Stack</p>
            <p class="mb-2"><strong style="color: var(--gh-navy-soft);">Company:</strong> Acme Corp</p>
            <p class="mb-2"><strong style="color: var(--gh-navy-soft);">Location:</strong> BGC, Taguig</p>
            <p class="mb-2"><strong style="color: var(--gh-navy-soft);">Applied:</strong> June 1, 2025</p>
            <p class="mb-4 d-flex align-items-center gap-2">
                <strong style="color: var(--gh-navy-soft);">Status:</strong> 
                <span class="gh-badge gh-badge-interview" style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">Interview</span>
            </p>
            <a href=" " class="btn-gh-outline">← Back to Applications</a>
        </div>
        <div class="col-md-6">
            <h4 class="mb-4" style="color: var(--gh-navy); font-family: var(--font-display); font-weight: 700;">Application Timeline</h4>
            <ul class="list-unstyled">
                {{-- Completed Step --}}
                <li class="d-flex gap-3 mb-4">
                    <i class="bi bi-check-circle-fill" style="color: var(--gh-success); font-size: 1.2rem;"></i>
                    <div>
                        <div class="fw-semibold" style="color: var(--gh-navy);">Applied</div>
                        <div class="text-muted small">June 1, 2025 at 10:30 AM</div>
                    </div>
                </li>
                {{-- Completed Step --}}
                <li class="d-flex gap-3 mb-4">
                    <i class="bi bi-check-circle-fill" style="color: var(--gh-success); font-size: 1.2rem;"></i>
                    <div>
                        <div class="fw-semibold" style="color: var(--gh-navy);">Application Reviewed</div>
                        <div class="text-muted small">June 3, 2025</div>
                    </div>
                </li>
                {{-- Current Step --}}
                <li class="d-flex gap-3 mb-4">
                    <i class="bi bi-record-circle-fill" style="color: var(--gh-indigo); font-size: 1.2rem;"></i>
                    <div>
                        <div class="fw-semibold" style="color: var(--gh-indigo);">Interview Scheduled</div>
                        <div class="text-muted small">June 8, 2025 at 2:00 PM (current stage)</div>
                    </div>
                </li>
                {{-- Future Step --}}
                <li class="d-flex gap-3 text-muted">
                    <i class="bi bi-circle" style="color: var(--gh-border); font-size: 1.2rem;"></i>
                    <div>
                        <div class="fw-medium">Final Decision</div>
                        <div class="small">Pending</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>


{{-- ============================================================
     SECTION 12: EMPLOYER — APPLICANT STATUS UPDATER
     Used on: employer/applicants/show.blade.php
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Update Application Status <small class="text-muted fs-6 fw-normal">— employer/applicants/show</small></h2>
    <div class="gh-card" style="max-width: 480px; background: var(--gh-slate); box-shadow: none;">
        <p class="mb-2"><strong style="color: var(--gh-navy-soft);">Applicant:</strong> <span style="color: var(--gh-navy); font-weight: 500;">Maria Santos</span></p>
        <p class="mb-4 d-flex align-items-center gap-2">
            <strong style="color: var(--gh-navy-soft);">Current Status:</strong> 
            <span class="gh-badge gh-badge-pending" style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">Pending</span>
        </p>
        <form>
            <label class="gh-label mb-3" style="font-family: var(--font-display); font-size: 1rem; color: var(--gh-navy);">Change Status To:</label>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn-gh-outline" style="color: var(--gh-indigo); border-color: var(--gh-indigo);">
                    <i class="bi bi-record-circle"></i> Interview
                </button>
                <button type="button" class="btn-gh-primary" style="background: var(--gh-success);">
                    <i class="bi bi-check-circle"></i> Hire
                </button>
                <button type="button" class="btn-gh-primary" style="background: var(--gh-danger);">
                    <i class="bi bi-x-circle"></i> Reject
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ============================================================
     SECTION 13: CONTACT FORM
     Used on: public/contact.blade.php
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Contact Form <small class="text-muted fs-6 fw-normal">— public/contact.blade.php</small></h2>
    <div class="gh-card" style="max-width: 520px; background: var(--gh-slate); box-shadow: none;">
        <div class="mb-3">
            <label class="gh-label">Your Name</label>
            <input type="text" class="gh-input" placeholder="Juan dela Cruz">
        </div>
        <div class="mb-3">
            <label class="gh-label">Email</label>
            <input type="email" class="gh-input" placeholder="juan@example.com">
        </div>
        <div class="mb-4">
            <label class="gh-label">Message</label>
            <textarea class="gh-input" rows="4" placeholder="How can we help you?"></textarea>
        </div>
        <button class="btn-gh-primary">Send Message</button>
    </div>
</div>


{{-- ============================================================
     SECTION 14: FORGOT / RESET PASSWORD FORMS
     Used on: auth/passwords/email.blade.php, auth/passwords/reset.blade.php
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Password Reset Forms <small class="text-muted fs-6 fw-normal">— auth/passwords/</small></h2>
    <div class="row g-4">
        <div class="col-md-5">
            <div class="gh-card h-100" style="background: var(--gh-slate); box-shadow: none;">
                <h4 class="mb-3" style="color: var(--gh-navy); font-family: var(--font-display); font-weight: 700;">Forgot Password</h4>
                <p class="text-muted small mb-4">Enter your email and we'll send you a reset link.</p>
                <div class="mb-4">
                    <label class="gh-label">Email address</label>
                    <input type="email" class="gh-input" placeholder="you@example.com">
                </div>
                <button class="btn-gh-primary w-100 justify-content-center">Send Reset Link</button>
            </div>
        </div>
        <div class="col-md-5">
            <div class="gh-card h-100" style="background: var(--gh-slate); box-shadow: none;">
                <h4 class="mb-4" style="color: var(--gh-navy); font-family: var(--font-display); font-weight: 700;">Reset Password</h4>
                <div class="mb-3">
                    <label class="gh-label">New Password</label>
                    <input type="password" class="gh-input" placeholder="••••••••">
                </div>
                <div class="mb-4">
                    <label class="gh-label">Confirm Password</label>
                    <input type="password" class="gh-input" placeholder="••••••••">
                </div>
                <button class="btn-gh-primary w-100 justify-content-center">Update Password</button>
            </div>
        </div>
    </div>
</div>


{{-- ============================================================
     SECTION 15: PAGINATION
     Used on: any index page with many records
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Pagination <small class="text-muted fs-6 fw-normal">— */index pages</small></h2>
    
    <div class="d-flex gap-2">
        <button class="btn-gh-outline" disabled style="padding: 0.4rem 0.8rem;">← Prev</button>
        <button class="btn-gh-primary" style="padding: 0.4rem 0.8rem;">1</button>
        <button class="btn-gh-outline" style="padding: 0.4rem 0.8rem;">2</button>
        <button class="btn-gh-outline" style="padding: 0.4rem 0.8rem;">3</button>
        <button class="btn-gh-outline" style="padding: 0.4rem 0.8rem;">Next →</button>
    </div>
</div>


{{-- ============================================================
     SECTION 16: EMPTY STATE
     Used on: any index page when no records exist yet
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Empty States <small class="text-muted fs-6 fw-normal">— shown when no data exists</small></h2>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="gh-card text-center h-100 d-flex flex-column align-items-center justify-content-center" style="box-shadow: none; background: var(--gh-slate);">
                <div class="fs-1 mb-2">📭</div>
                <h6 class="fw-bold mb-2" style="color: var(--gh-navy);">No applications yet</h6>
                <p class="text-muted small mb-3">Browse open jobs and apply to get started.</p>
                <a href=" " class="btn-gh-primary btn-sm mt-auto" style="padding: 0.35rem 0.8rem; font-size: 0.75rem;">Browse Jobs</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="gh-card text-center h-100 d-flex flex-column align-items-center justify-content-center" style="box-shadow: none; background: var(--gh-slate);">
                <div class="fs-1 mb-2">📋</div>
                <h6 class="fw-bold mb-2" style="color: var(--gh-navy);">No job posts yet</h6>
                <p class="text-muted small mb-3">Create your first job posting to start finding talent.</p>
                <a href=" " class="btn-gh-primary btn-sm mt-auto" style="padding: 0.35rem 0.8rem; font-size: 0.75rem;">Post a Job</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="gh-card text-center h-100 d-flex flex-column align-items-center justify-content-center" style="box-shadow: none; background: var(--gh-slate);">
                <div class="fs-1 mb-2">🔍</div>
                <h6 class="fw-bold mb-2" style="color: var(--gh-navy);">No results found</h6>
                <p class="text-muted small mb-3">Try adjusting your search terms or filters.</p>
                <a href=" " class="btn-gh-outline btn-sm mt-auto" style="padding: 0.35rem 0.8rem; font-size: 0.75rem;">Clear Filters</a>
            </div>
        </div>
    </div>
</div>


{{-- ============================================================
     SECTION 17: BUTTONS & FORM VALIDATION STATES
     Used throughout: all forms and actions
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Buttons & Validation <small class="text-muted fs-6 fw-normal">— global</small></h2>

    <h6 class="text-muted mb-2">Buttons:</h6>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <button class="btn-gh-primary">Primary</button>
        <button class="btn-gh-outline">Outline</button>
        <button class="btn-gh-primary" style="background: var(--gh-success); border-color: var(--gh-success);">Approve</button>
        <button class="btn-gh-primary" style="background: var(--gh-danger); border-color: var(--gh-danger);">Remove</button>
        <button class="btn-gh-primary text-dark" style="background: var(--gh-warning); border-color: var(--gh-warning);">Warn</button>
        <button class="btn-gh-outline" style="color: var(--gh-muted); border-color: var(--gh-border);">Secondary</button>
        <button class="btn-gh-primary btn-sm">Small Primary</button>
        <button class="btn-gh-outline btn-sm">Small Outline</button>
        <button class="btn-gh-primary" disabled>Disabled</button>
        <button class="btn-gh-primary" disabled>
            <span class="spinner-border spinner-border-sm me-1" role="status"></span> Loading…
        </button>
    </div>

    <h6 class="text-muted mb-2">Inline validation errors:</h6>
    <div class="row g-3" style="max-width: 480px;">
        <div class="col-12">
            <label class="gh-label">Email address</label>
            <input type="email" class="gh-input is-invalid" value="not-an-email">
            <div class="invalid-feedback d-block small mt-1" style="color: var(--gh-danger);">Please enter a valid email address.</div>
        </div>
        <div class="col-12">
            <label class="gh-label">Password</label>
            <input type="password" class="gh-input is-valid" value="securepass">
            <div class="valid-feedback d-block small mt-1" style="color: var(--gh-success);">Looks good!</div>
        </div>
    </div>
</div>


{{-- ============================================================
     SECTION 18: MODALS (Delete / Confirm actions)
     Used on: admin actions, employer job delete, ban user
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Confirm / Delete Modals <small class="text-muted fs-6 fw-normal">— cannot be undone</small></h2>

    <button class="btn-gh-primary" style="background: var(--gh-danger); border-color: var(--gh-danger);" data-bs-toggle="modal" data-bs-target="#deleteModal"> Delete Job Post (opens modal)</button>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 1px solid var(--gh-border); border-radius: var(--radius-lg); background: #fff;">
                <div class="modal-header" style="border-bottom: 1px solid var(--gh-border); padding: 1rem 1.5rem;">
                    <h5 class="modal-title fw-bold" style="color: var(--gh-danger); font-family: var(--font-display);"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 1.5rem; color: var(--gh-text);">
                    Are you sure you want to delete the job post <strong>"Software Engineer — Full Stack"</strong>?
                    <div class="mt-2 text-muted small">This action <strong>cannot be undone</strong> and will remove all associated applicant data.</div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--gh-border); padding: 1rem 1.5rem;">
                    <button type="button" class="btn-gh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-gh-primary" style="background: var(--gh-danger); border-color: var(--gh-danger);">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ============================================================
     SECTION 19: HERO BANNER (Landing Page)
     Used on: public/home.blade.php
     ============================================================ --}}
<div class="gh-card mb-4">
    <h2 class="gh-card-title">Hero Banner <small class="text-muted fs-6 fw-normal">— public/home.blade.php</small></h2>
    
    {{-- Warm Orange background with subtle teal accents --}}
    <div class="rounded p-5 text-center" style="background: #D97706; color: #fff;">
        <h2 class="fw-bold display-6 mb-3" style="font-family: var(--font-display);">Find Your Next Opportunity</h2>
        <p class="lead mb-4" style="font-family: var(--font-body); opacity: 0.9;">Connecting Filipino talent with the right employers.</p>
        
        <div class="d-flex justify-content-center gap-3">
            {{-- Teal outline buttons for a subtle accent --}}
            <a href=" " class="btn btn-lg px-4" style="border: 2px solid #147D81; color: #147D81; background: #fff; border-radius: 50px; font-weight: 500; text-decoration: none;">Browse Jobs</a>
            <a href=" " class="btn btn-lg px-4" style="border: 2px solid #fff; color: #fff; background: transparent; border-radius: 50px; font-weight: 500; text-decoration: none;">Post a Job</a>
        </div>
        
        <p class="mt-4 mb-0 small" style="opacity: 0.85; font-weight: 500;">57 jobs available · 142 registered users</p>
    </div>
</div>

@endsection