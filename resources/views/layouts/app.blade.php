<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GitHired') — Find Your Next Role</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Sans:opsz,wght@9..40,400;9..40,500&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ─── Design Tokens ─────────────────────────────────────── */
        :root {
            --gh-navy:       #0F172A;
            --gh-navy-soft:  #1E293B;
            --gh-amber:      #D97706;
            --gh-amber-lt:   #FEF3C7;
            --gh-slate:      #F8FAFC;
            --gh-border:     #E7E2D8;
            --gh-muted:      #78716C;
            --gh-text:       #1C1917;
            --gh-success:    #10B981;
            --gh-warning:    #F59E0B;
            --gh-danger:     #EF4444;
            --gh-info:       #3B82F6;

            --font-display:  'Plus Jakarta Sans', sans-serif;
            --font-body:     'DM Sans', sans-serif;

            --nav-height:    64px;
            --sidebar-w:     240px;
            --radius-sm:     6px;
            --radius-md:     10px;
            --radius-lg:     14px;
        }

        /* ─── Base ───────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            background: var(--gh-slate);
            color: var(--gh-text);
            font-size: 15px;
            line-height: 1.6;
            margin: 0;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-display);
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        /* ─── Top Navbar ─────────────────────────────────────────── */
        .gh-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            height: var(--nav-height);
            background: var(--gh-navy);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .gh-navbar-brand {
            font-family: var(--font-display);
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.03em;
            flex-shrink: 0;
        }

        .gh-navbar-brand .brand-icon {
            width: 30px;
            height: 30px;
            background: var(--gh-amber);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            color: #fff;
        }

        .gh-nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            flex: 1;
        }

        .gh-nav-link {
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.4rem 0.75rem;
            border-radius: var(--radius-sm);
            transition: color 0.15s, background 0.15s;
        }

        .gh-nav-link:hover,
        .gh-nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.08);
        }

        .gh-nav-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-left: auto;
        }

        .gh-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--gh-amber);
            color: #fff;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            cursor: pointer;
            border: 2px solid rgba(255,255,255,0.12);
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
            background: #fff;
            border-right: 1px solid var(--gh-border);
            padding: 1.5rem 0;
            position: sticky;
            top: var(--nav-height);
            height: calc(100vh - var(--nav-height));
            overflow-y: auto;
        }

        .gh-sidebar-section {
            padding: 0 1rem 1rem;
        }

        .gh-sidebar-label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gh-muted);
            padding: 0 0.75rem;
            margin-bottom: 0.5rem;
        }

        .gh-sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-sm);
            color: var(--gh-muted);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s;
            border-left: 2px solid transparent;
            margin-bottom: 2px;
        }

        .gh-sidebar-link:hover {
            background: var(--gh-slate);
            color: var(--gh-navy);
            border-left-color: var(--gh-border);
        }

        .gh-sidebar-link.active {
            background: var(--gh-amber-lt);
            color: var(--gh-amber);
            border-left-color: var(--gh-amber);
            font-weight: 600;
        }

        .gh-sidebar-link i {
            font-size: 1rem;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }

        .gh-sidebar-badge {
            margin-left: auto;
            background: var(--gh-amber);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 1px 7px;
            border-radius: 99px;
        }

        /* ─── Main Content ───────────────────────────────────────── */
        .gh-main {
            flex: 1;
            padding: 2rem 2.5rem;
            min-width: 0;
        }

        /* ─── Page Header ────────────────────────────────────────── */
        .gh-page-header {
            margin-bottom: 2rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--gh-border);
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
        }

        .gh-page-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--gh-navy);
            margin: 0;
            line-height: 1.2;
        }

        .gh-page-subtitle {
            font-size: 0.875rem;
            color: var(--gh-muted);
            margin: 0.25rem 0 0;
        }

        /* ─── Cards ──────────────────────────────────────────────── */
        .gh-card {
            background: #fff;
            border: 1px solid var(--gh-border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
        }

        .gh-card-title {
            font-family: var(--font-display);
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--gh-navy);
            margin-bottom: 1rem;
        }

        /* ─── Stat Cards ─────────────────────────────────────────── */
        .gh-stat {
            background: #fff;
            border: 1px solid var(--gh-border);
            border-radius: var(--radius-md);
            padding: 1.25rem 1.5rem;
        }

        .gh-stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--gh-muted);
            margin-bottom: 0.4rem;
        }

        .gh-stat-value {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            color: var(--gh-navy);
            line-height: 1;
        }

        /* ─── Stat Card Color Modifiers ──────────────────────────── */
        .gh-stat-success { background: #D1FAE5; border-color: #A7F3D0; }
        .gh-stat-success .gh-stat-label, .gh-stat-success .gh-stat-value { color: #065F46; }

        .gh-stat-warning { background: #FEF9C3; border-color: #FEF08A; }
        .gh-stat-warning .gh-stat-label, .gh-stat-warning .gh-stat-value { color: #854D0E; }

        .gh-stat-danger  { background: #FEE2E2; border-color: #FECACA; }
        .gh-stat-danger .gh-stat-label, .gh-stat-danger .gh-stat-value { color: #991B1B; }

        .gh-stat-info    { background: #DBEAFE; border-color: #BFDBFE; }
        .gh-stat-info .gh-stat-label, .gh-stat-info .gh-stat-value { color: #1D4ED8; }

        /* ─── Status Badges ──────────────────────────────────────── */
        .gh-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 99px;
            letter-spacing: 0.02em;
        }

        .gh-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .gh-badge-pending  { background: #FEF9C3; color: #854D0E; }
        .gh-badge-interview{ background: #DBEAFE; color: #1D4ED8; }
        .gh-badge-hired    { background: #D1FAE5; color: #065F46; }
        .gh-badge-rejected { background: #FEE2E2; color: #991B1B; }
        .gh-badge-active   { background: #D1FAE5; color: #065F46; }
        .gh-badge-draft    { background: var(--gh-border); color: var(--gh-muted); }

        /* ─── Buttons ────────────────────────────────────────────── */
        .btn-gh-primary {
            background: var(--gh-amber);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            padding: 0.5rem 1.25rem;
            font-family: var(--font-body);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-gh-primary:hover  { background: #B45309; color: #fff; }
        .btn-gh-primary:active { transform: scale(0.98); }

        .btn-gh-outline {
            background: transparent;
            color: var(--gh-navy);
            border: 1px solid var(--gh-border);
            border-radius: var(--radius-sm);
            padding: 0.5rem 1.25rem;
            font-family: var(--font-body);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .gh-navbar .btn-gh-outline {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.4);
        }

        .gh-navbar .btn-gh-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-color: #fff;
        }

        .btn-gh-outline:hover { border-color: var(--gh-amber); color: var(--gh-amber); background: var(--gh-amber-lt); }

        /* ─── Tables ─────────────────────────────────────────────── */
        .gh-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.875rem;
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid var(--gh-border);
        }

        .gh-table thead th {
            background: var(--gh-navy-soft);
            color: #fff;
            font-family: var(--font-display);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.875rem 1rem;
            text-align: left;
            border-bottom: none;
        }

        .gh-table td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--gh-border);
            color: var(--gh-navy);
            vertical-align: middle;
            background: #fff;
        }

        .gh-table tr:last-child td { border-bottom: none; }
        
        .gh-table tbody tr td { transition: background 0.15s; }
        .gh-table tbody tr:hover td { background: var(--gh-indigo-lt); }

        /* ─── Form Controls ──────────────────────────────────────── */
        .gh-input {
            width: 100%;
            border: 1px solid var(--gh-border);
            border-radius: var(--radius-sm);
            padding: 0.5rem 0.875rem;
            font-family: var(--font-body);
            font-size: 0.875rem;
            color: var(--gh-navy);
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }

        .gh-input:focus {
            border-color: var(--gh-amber);
            box-shadow: 0 0 0 3px rgba(217,119,6,0.15);
        }

        .gh-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gh-navy);
            margin-bottom: 0.35rem;
            display: block;
        }

        /* ─── Flash Alerts ───────────────────────────────────────── */
        .gh-alert {
            padding: 0.875rem 1.25rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.25rem;
        }

        .gh-alert-success { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
        .gh-alert-warning  { background: #FEF9C3; color: #854D0E; border: 1px solid #FEF08A; }
        .gh-alert-danger  { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
        .gh-alert-info    { background: #DBEAFE; color: #1D4ED8; border: 1px solid #BFDBFE; }

        /* ─── Job Card (reusable) ─────────────────────────────────── */
        .gh-job-card {
            background: #fff;
            border: 1px solid var(--gh-border);
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.5rem;
            transition: border-color 0.15s, box-shadow 0.15s;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .gh-job-card:hover {
            border-color: var(--gh-amber);
            box-shadow: 0 4px 20px rgba(217,119,6,0.10);
            color: inherit;
        }

        .gh-job-company-logo {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sm);
            background: var(--gh-amber-lt);
            border: 1px solid var(--gh-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--gh-amber);
            flex-shrink: 0;
        }

        /* ─── Divider ────────────────────────────────────────────── */
        .gh-divider {
            border: none;
            border-top: 1px solid var(--gh-border);
            margin: 1.5rem 0;
        }

        /* ─── Footer ─────────────────────────────────────────────── */
        .gh-footer {
            background: var(--gh-navy);
            color: rgba(255,255,255,0.45);
            text-align: center;
            font-size: 0.8rem;
            padding: 1.25rem;
            margin-top: auto;
        }

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
            <span class="brand-icon"><i class="bi bi-briefcase-fill"></i></span>
            GitHired
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
                <a href="{{ route('login') }}" class="btn-gh-outline" style="padding: 0.375rem 1rem; font-size:0.825rem;">
                    Sign in
                </a>
                <a href="{{ route('register') }}" class="btn-gh-primary" style="padding: 0.375rem 1rem; font-size:0.825rem;">
                    Get started
                </a>
            @endguest

            @auth
                {{-- Notification Bell --}}
                <button style="background:none;border:none;color:rgba(255,255,255,0.65);font-size:1.1rem;cursor:pointer;padding:4px;">
                    <i class="bi bi-bell"></i>
                </button>

                {{-- Avatar Dropdown --}}
                <div class="dropdown">
                    <div class="gh-avatar dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end" style="font-size:0.875rem; min-width:180px; border: 1px solid var(--gh-border); border-radius: var(--radius-md); box-shadow: 0 8px 24px rgba(0,0,0,0.1);">
                        <li>
                            <span class="dropdown-item-text" style="font-weight:600; color: var(--gh-navy);">
                                {{ auth()->user()->name }}
                            </span>
                            <span class="dropdown-item-text" style="font-size:0.75rem; color:var(--gh-muted); padding-top:0;">
                                {{ auth()->user()->email }}
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route(auth()->user()->role . '.dashboard') }}">
                                <i class="bi bi-grid me-2"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Sign out
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
                        <i class="bi bi-grid-1x2"></i> Dashboard
                    </a>
                    <a href="{{ route('applicant.applications.index') }}" class="gh-sidebar-link {{ request()->routeIs('applicant.applications.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i> Applications
                        {{-- <span class="gh-sidebar-badge">3</span> --}}
                    </a>
                    <a href="{{ route('applicant.resume') }}" class="gh-sidebar-link {{ request()->routeIs('applicant.resume*') ? 'active' : '' }}">
                        <i class="bi bi-upload"></i> Resume
                    </a>
                    <a href="{{ route('applicant.profile.edit') }}" class="gh-sidebar-link {{ request()->routeIs('applicant.profile*') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i> Profile
                    </a>
                </div>
            @endif

            @if(auth()->user()->role === 'employer')
                <div class="gh-sidebar-section">
                    <div class="gh-sidebar-label">Employer</div>
                    <a href="{{ route('employer.dashboard') }}" class="gh-sidebar-link {{ request()->routeIs('employer.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2"></i> Dashboard
                    </a>
                    <a href="{{ route('employer.jobs.index') }}" class="gh-sidebar-link {{ request()->routeIs('employer.jobs.*') ? 'active' : '' }}">
                        <i class="bi bi-briefcase"></i> Job Postings
                    </a>
                    <a href="{{ route('employer.applicants.index') }}" class="gh-sidebar-link {{ request()->routeIs('employer.applicants.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Applicants
                    </a>
                    <a href="{{ route('employer.profile.edit') }}" class="gh-sidebar-link {{ request()->routeIs('employer.profile*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i> Company Profile
                    </a>
                </div>
            @endif

            <div class="gh-sidebar-section" style="margin-top:auto; border-top: 1px solid var(--gh-border); padding-top:1rem;">
                <a href="{{ route('jobs.index') }}" class="gh-sidebar-link">
                    <i class="bi bi-search"></i> Browse Jobs
                </a>
            </div>
        </aside>
        @endauth

        {{-- ─── MAIN CONTENT ────────────────────────────────────── --}}
        <main class="gh-main">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="gh-alert gh-alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="gh-alert gh-alert-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('error'))
                <div class="gh-alert gh-alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="gh-alert gh-alert-info">
                    <i class="bi bi-info-circle-fill"></i>
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