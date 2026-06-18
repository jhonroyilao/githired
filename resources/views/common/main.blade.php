<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GitHired')</title>

    {{-- Fonts: Inter only --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;1,14..32,400&display=swap" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=cabinet-grotesk@400,500,600,700,800&display=swap" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="gh-logo-main.svg">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ─── Design Tokens ─────────────────────────────────────── */
        :root {
            /* Brand palette */
            --gh-black:      #141414;
            --gh-blue:       #1A73E9;
            --gh-blue-dark:  #1558b0;
            --gh-blue-lt:    #EAF2FF;
            --gh-gray:       #575757;
            --gh-surface:    #F6F4F5;
            --gh-white:      #ffffff;
            --gh-border:     #E4E2E3;
            --gh-muted:      #8A8A8A;

            /* Status: Pending */
            --s-pending-bg:     #FCF5DB;
            --s-pending-color:  #D48B24;

            /* Status: Interview */
            --s-interview-bg:    #EFEAFF;
            --s-interview-color: #693FE8;

            /* Status: Active */
            --s-active-bg:    #E2F3FF;
            --s-active-color: #0083F1;

            /* Status: Hired */
            --s-hired-bg:    #E1FAE7;
            --s-hired-color: #31B74B;

            /* Status: Rejected */
            --s-rejected-bg:    #FDEFEE;
            --s-rejected-color: #C14F34;

            /* Status: Draft */
            --s-draft-bg:    #F1F1F1;
            --s-draft-color: #575757;
            
            --font-heading: 'Cabinet Grotesk', sans-serif;
            --font-body: 'Inter', sans-serif;

            --nav-height:  62px;
            --sidebar-w:   228px;
            --radius-sm:   6px;
            --radius-md:   10px;
            --radius-lg:   14px;
        }

        /* ─── Base ───────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 400;
            letter-spacing: -0.01em;
            background: var(--gh-surface);
            color: var(--gh-black);
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
            letter-spacing: -0.03em;
        }
        /* ─── Top Navbar ─────────────────────────────────────────── */
        .gh-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            height: var(--nav-height);
            background: var(--gh-black);
            display: flex;
            align-items: center;
            padding: 0 3.5rem;
            gap: 1.0rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .gh-navbar-brand {
            font-family: var(--font-body);
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 9px;
            letter-spacing: -0.04em;
            flex-shrink: 0;
        }

        .gh-navbar-brand .brand-icon {
            width: 28px;
            height: 28px;
            background: var(--gh-blue);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: #fff;
        }

        .gh-nav-links {
            display: flex;
            align-items: center;
            gap: 0.15rem;
            flex: 1;
        }

        .gh-nav-link {
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.4rem 0.8rem;
            border-radius: var(--radius-sm);
            transition: color 0.15s, background 0.15s;
        }

        .gh-nav-link:hover,
        .gh-nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.07);
        }

        .gh-nav-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-left: auto;
        }

        .gh-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--gh-blue);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid rgba(255,255,255,0.1);
            letter-spacing: 0;
        }

        /* ─── Page Layout ────────────────────────────────────────── */
        .gh-wrapper {
            display: flex;
            min-height: calc(100vh - var(--nav-height));
        }

        /* ─── Sidebar ────────────────────────────────────────────── */
        .gh-sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--gh-white);
            border-right: 1px solid var(--gh-border);
            padding: 1.25rem 0;
            position: sticky;
            top: var(--nav-height);
            height: calc(100vh - var(--nav-height));
            overflow-y: auto;
        }

        .gh-sidebar-section {
            padding: 0 0.75rem 1rem;
        }

        .gh-sidebar-label {
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: var(--gh-muted);
            padding: 0 0.625rem;
            margin-bottom: 0.4rem;
        }

        .gh-sidebar-link {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 0.45rem 0.625rem;
            border-radius: var(--radius-sm);
            color: var(--gh-gray);
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s;
            margin-bottom: 1px;
        }

        .gh-sidebar-link:hover {
            background: var(--gh-surface);
            color: var(--gh-black);
        }

        .gh-sidebar-link.active {
            background: var(--gh-blue-lt);
            color: var(--gh-blue);
            font-weight: 600;
        }

        .gh-sidebar-link i {
            font-size: 0.9rem;
            width: 16px;
            text-align: center;
            flex-shrink: 0;
        }

        .gh-sidebar-badge {
            margin-left: auto;
            background: var(--gh-blue);
            color: #fff;
            font-size: 0.68rem;
            font-weight: 600;
            padding: 1px 7px;
            border-radius: 99px;
        }

        /* ─── Main Content ───────────────────────────────────────── */
        .gh-main {
            flex: 1;
            padding: 2rem 2.25rem;
            min-width: 0;
        }

        /* ─── Page Header ────────────────────────────────────────── */
        .gh-page-header {
            margin-bottom: 1.75rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--gh-border);
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
        }

        .gh-page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gh-black);
            margin: 0;
            line-height: 1.2;
        }

        .gh-page-subtitle {
            font-size: 0.85rem;
            color: var(--gh-muted);
            margin: 0.2rem 0 0;
            font-weight: 400;
        }

        /* ─── Cards ──────────────────────────────────────────────── */
        .gh-card {
            background: var(--gh-white);
            border: 1px solid var(--gh-border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
        }

        .gh-card-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--gh-black);
            margin-bottom: 1rem;
        }

        /* ─── Stat Cards ─────────────────────────────────────────── */
        .gh-stat {
            background: var(--gh-white);
            border: 1px solid var(--gh-border);
            border-radius: var(--radius-md);
            padding: 1.25rem 1.4rem;
        }

        .gh-stat-label {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--gh-muted);
            margin-bottom: 0.35rem;
        }

        .gh-stat-value {
            font-size: 1.9rem;
            font-weight: 700;
            color: var(--gh-black);
            line-height: 1;
            letter-spacing: -0.04em;
        }

        /* Stat card status-matched modifiers */
        .gh-stat-pending  { background: var(--s-pending-bg);  border-color: var(--s-pending-color); }
        .gh-stat-pending  .gh-stat-label,
        .gh-stat-pending  .gh-stat-value  { color: var(--s-pending-color); }

        .gh-stat-interview { background: var(--s-interview-bg); border-color: var(--s-interview-color); }
        .gh-stat-interview .gh-stat-label,
        .gh-stat-interview .gh-stat-value { color: var(--s-interview-color); }

        .gh-stat-active   { background: var(--s-active-bg);   border-color: var(--s-active-color); }
        .gh-stat-active   .gh-stat-label,
        .gh-stat-active   .gh-stat-value   { color: var(--s-active-color); }

        .gh-stat-hired    { background: var(--s-hired-bg);    border-color: var(--s-hired-color); }
        .gh-stat-hired    .gh-stat-label,
        .gh-stat-hired    .gh-stat-value    { color: var(--s-hired-color); }

        .gh-stat-rejected { background: var(--s-rejected-bg); border-color: var(--s-rejected-color); }
        .gh-stat-rejected .gh-stat-label,
        .gh-stat-rejected .gh-stat-value { color: var(--s-rejected-color); }

        .gh-stat-draft    { background: var(--s-draft-bg);    border-color: var(--s-draft-color); }
        .gh-stat-draft    .gh-stat-label,
        .gh-stat-draft    .gh-stat-value    { color: var(--s-draft-color); }

        /* ─── Status Labels/Badges ───────────────────────────────── */
        .gh-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 10px 3px 8px;
            border-radius: 99px;
            border: 1px solid;
            letter-spacing: 0.01em;
        }

        .gh-badge i {
            font-size: 0.72rem;
        }

        .gh-badge-pending   { background: var(--s-pending-bg);   color: var(--s-pending-color);   border-color: var(--s-pending-color); }
        .gh-badge-interview { background: var(--s-interview-bg);  color: var(--s-interview-color); border-color: var(--s-interview-color); }
        .gh-badge-active    { background: var(--s-active-bg);    color: var(--s-active-color);    border-color: var(--s-active-color); }
        .gh-badge-hired     { background: var(--s-hired-bg);     color: var(--s-hired-color);     border-color: var(--s-hired-color); }
        .gh-badge-rejected  { background: var(--s-rejected-bg);  color: var(--s-rejected-color);  border-color: var(--s-rejected-color); }
        .gh-badge-draft     { background: var(--s-draft-bg);     color: var(--s-draft-color);     border-color: var(--s-draft-color); }

        /* ─── Buttons ────────────────────────────────────────────── */
        .btn-gh-primary {
            background: var(--gh-blue);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            padding: 0.5rem 1.2rem;
            font-family: var(--font-body);
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-gh-primary:hover  { background: var(--gh-blue-dark); color: #fff; }
        .btn-gh-primary:active { transform: scale(0.98); }

        .btn-gh-outline {
            background: transparent;
            color: var(--gh-black);
            border: 1px solid var(--gh-border);
            border-radius: var(--radius-sm);
            padding: 0.5rem 1.2rem;
            font-family: var(--font-body);
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s, color 0.15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-gh-outline:hover { border-color: var(--gh-blue); color: var(--gh-blue); background: var(--gh-blue-lt); }

        .gh-navbar .btn-gh-outline {
            color: rgba(255,255,255,0.75);
            border-color: rgba(255,255,255,0.2);
        }

        .gh-navbar .btn-gh-outline:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
            border-color: rgba(255,255,255,0.4);
        }

        /* ─── Tables ─────────────────────────────────────────────── */
        .gh-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.85rem;
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid var(--gh-border);
        }

        .gh-table thead th {
            background: var(--gh-black);
            color: rgba(255,255,255,0.85);
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            padding: 0.825rem 1rem;
            text-align: left;
            border-bottom: none;
        }

        .gh-table td {
            padding: 0.825rem 1rem;
            border-bottom: 1px solid var(--gh-border);
            color: var(--gh-black);
            vertical-align: middle;
            background: var(--gh-white);
        }

        .gh-table tr:last-child td { border-bottom: none; }
        .gh-table tbody tr:hover td { background: var(--gh-surface); }

        /* ─── Form Controls ──────────────────────────────────────── */
        .gh-input {
            width: 100%;
            border: 1px solid var(--gh-border);
            border-radius: var(--radius-sm);
            padding: 0.5rem 0.875rem;
            font-family: var(--font-body);
            font-size: 0.875rem;
            color: var(--gh-black);
            background: var(--gh-white);
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }

        .gh-input:focus {
            border-color: var(--gh-blue);
            box-shadow: 0 0 0 3px rgba(26,115,233,0.12);
        }

        .gh-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--gh-black);
            margin-bottom: 0.35rem;
            display: block;
        }

        /* ─── Flash Alerts ───────────────────────────────────────── */
        .gh-alert {
            padding: 0.825rem 1.1rem;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.25rem;
            border: 1px solid;
        }

        .gh-alert-success { background: var(--s-hired-bg);    color: var(--s-hired-color);    border-color: var(--s-hired-color); }
        .gh-alert-warning { background: var(--s-pending-bg);  color: var(--s-pending-color);  border-color: var(--s-pending-color); }
        .gh-alert-danger  { background: var(--s-rejected-bg); color: var(--s-rejected-color); border-color: var(--s-rejected-color); }
        .gh-alert-info    { background: var(--s-active-bg);   color: var(--s-active-color);   border-color: var(--s-active-color); }

        /* ─── Job Card ───────────────────────────────────────────── */
        .gh-job-card {
            background: var(--gh-white);
            border: 1px solid var(--gh-border);
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.4rem;
            transition: border-color 0.15s, box-shadow 0.15s;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .gh-job-card:hover {
            border-color: var(--gh-blue);
            box-shadow: 0 4px 18px rgba(26,115,233,0.09);
            color: inherit;
        }

        .gh-job-company-logo {
            width: 42px;
            height: 42px;
            border-radius: var(--radius-sm);
            background: var(--gh-blue-lt);
            border: 1px solid var(--gh-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--gh-blue);
            flex-shrink: 0;
        }

        /* ─── Dropdown tweaks ────────────────────────────────────── */
        .dropdown-menu {
            font-family: var(--font-body);
            font-size: 0.85rem;
            border: 1px solid var(--gh-border);
            border-radius: var(--radius-md);
            box-shadow: 0 8px 24px rgba(0,0,0,0.09);
        }

        .dropdown-item:hover { background: var(--gh-surface); }
        .dropdown-item.text-danger:hover { background: var(--s-rejected-bg); color: var(--s-rejected-color) !important; }

        /* ─── Divider ────────────────────────────────────────────── */
        .gh-divider {
            border: none;
            border-top: 1px solid var(--gh-border);
            margin: 1.5rem 0;
        }

        /* ─── Footer ─────────────────────────────────────────────── */
        .gh-footer {
            background: var(--gh-black);
            color: rgba(255,255,255,0.35);
            text-align: center;
            font-size: 0.78rem;
            padding: 1.1rem;
            margin-top: auto;
        }

        /* ─── Bell icon button ───────────────────────────────────── */
        .gh-icon-btn {
            background: none;
            border: none;
            color: rgba(255,255,255,0.5);
            font-size: 1rem;
            cursor: pointer;
            padding: 5px 7px;
            border-radius: var(--radius-sm);
            transition: color 0.15s, background 0.15s;
            line-height: 1;
        }

        .gh-icon-btn:hover { color: #fff; background: rgba(255,255,255,0.07); }

        /* ─── Responsive ─────────────────────────────────────────── */
        @media (max-width: 768px) {
            .gh-sidebar { display: none; }
            .gh-main { padding: 1.25rem; }
            .gh-navbar { padding: 0 1rem; }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- ═══ TOP NAVBAR ═════════════════════════════════════════════ --}}
    <nav class="gh-navbar">
        <a href="{{ url('/') }}" class="gh-navbar-brand">
            <img src="gh-logo-main.svg" alt="Logo" width="35" height="35"> GitHired
        </a>

        <div class="gh-nav-links d-none d-md-flex">
            <a href="{{ route('jobs.index') }}" class="gh-nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}">
                Browse Jobs
            </a>

            @auth
                @if(auth()->user()->role === 'employer')
                    <a href="{{ route('employer.jobs.index') }}" class="gh-nav-link {{ request()->routeIs('employer.*') ? 'active' : '' }}">
                        My Postings
                    </a>
                @endif

                @if(auth()->user()->role === 'applicant')
                    <a href="{{ route('applicant.applications.index') }}" class="gh-nav-link {{ request()->routeIs('applicant.*') ? 'active' : '' }}">
                        My Applications
                    </a>
                @endif
            @endauth
        </div>

        <div class="gh-nav-actions">
            @guest
                <a href="{{ route('login') }}" class="btn-gh-outline" style="padding: 0.375rem 0.9rem;">
                    Sign in
                </a>
                <a href="{{ route('register') }}" class="btn-gh-primary" style="padding: 0.375rem 0.9rem;">
                    Get started
                </a>
            @endguest

            @auth
                <button class="gh-icon-btn">
                    <i class="fa-regular fa-bell"></i>
                </button>

                <div class="dropdown">
                    <div class="gh-avatar dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width:190px;">
                        <li>
                            <span class="dropdown-item-text" style="font-weight:600; color: var(--gh-black); font-size:0.85rem;">
                                {{ auth()->user()->name }}
                            </span>
                            <span class="dropdown-item-text" style="font-size:0.75rem; color:var(--gh-muted); padding-top:0;">
                                {{ auth()->user()->email }}
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route(auth()->user()->role . '.dashboard') }}">
                                <i class="fa-regular fa-grid-2 me-2" style="width:14px;"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fa-regular fa-circle-user me-2" style="width:14px;"></i> Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa-regular fa-arrow-right-from-bracket me-2" style="width:14px;"></i> Sign out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </nav>

    {{-- ═══ BODY WRAPPER ════════════════════════════════════════════ --}}
    <div class="gh-wrapper">

        {{-- ─── SIDEBAR (only when logged in) ──────────────────── --}}
        @auth
        <aside class="gh-sidebar">
            @if(auth()->user()->role === 'applicant')
                <div class="gh-sidebar-section">
                    <div class="gh-sidebar-label">Applicant</div>
                    <a href="{{ route('applicant.dashboard') }}" class="gh-sidebar-link {{ request()->routeIs('applicant.dashboard') ? 'active' : '' }}">
                        <i class="fa-regular fa-grid-2"></i> Dashboard
                    </a>
                    <a href="{{ route('applicant.applications.index') }}" class="gh-sidebar-link {{ request()->routeIs('applicant.applications.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-file-lines"></i> Applications
                    </a>
                    <a href="{{ route('applicant.resume') }}" class="gh-sidebar-link {{ request()->routeIs('applicant.resume*') ? 'active' : '' }}">
                        <i class="fa-regular fa-file-arrow-up"></i> Resume
                    </a>
                    <a href="{{ route('applicant.profile.edit') }}" class="gh-sidebar-link {{ request()->routeIs('applicant.profile*') ? 'active' : '' }}">
                        <i class="fa-regular fa-circle-user"></i> Profile
                    </a>
                </div>
            @endif

            @if(auth()->user()->role === 'employer')
                <div class="gh-sidebar-section">
                    <div class="gh-sidebar-label">Employer</div>
                    <a href="{{ route('employer.dashboard') }}" class="gh-sidebar-link {{ request()->routeIs('employer.dashboard') ? 'active' : '' }}">
                        <i class="fa-regular fa-grid-2"></i> Dashboard
                    </a>
                    <a href="{{ route('employer.jobs.index') }}" class="gh-sidebar-link {{ request()->routeIs('employer.jobs.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-briefcase"></i> Job Postings
                    </a>
                    <a href="{{ route('employer.applicants.index') }}" class="gh-sidebar-link {{ request()->routeIs('employer.applicants.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-users"></i> Applicants
                    </a>
                    <a href="{{ route('employer.profile.edit') }}" class="gh-sidebar-link {{ request()->routeIs('employer.profile*') ? 'active' : '' }}">
                        <i class="fa-regular fa-building"></i> Company Profile
                    </a>
                </div>
            @endif

            <div class="gh-sidebar-section" style="border-top: 1px solid var(--gh-border); padding-top: 0.875rem; margin-top: 0.5rem;">
                <a href="{{ route('jobs.index') }}" class="gh-sidebar-link">
                    <i class="fa-regular fa-magnifying-glass"></i> Browse Jobs
                </a>
            </div>
        </aside>
        @endauth

        {{-- ─── MAIN CONTENT ────────────────────────────────────── --}}
        <main class="gh-main">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="gh-alert gh-alert-success">
                    <i class="fa-regular fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="gh-alert gh-alert-warning">
                    <i class="fa-solid fa-hourglass-half"></i>
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('error'))
                <div class="gh-alert gh-alert-danger">
                    <i class="fa-regular fa-circle-xmark"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="gh-alert gh-alert-info">
                    <i class="fa-regular fa-toggle-on"></i>
                    {{ session('info') }}
                </div>
            @endif

            {{-- Page Content --}}
            @yield('content')

        </main>
    </div>

    {{-- ═══ FOOTER ══════════════════════════════════════════════════ --}}
    <footer class="gh-footer">
        &copy; {{ date('Y') }} GitHired. Built for real opportunities.
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>