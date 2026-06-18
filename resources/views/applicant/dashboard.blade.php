@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- ═══ PAGE HEADER ═══════════════════════════════════════════ --}}
<div class="gh-page-header">
    <div>
        <div class="gh-page-eyebrow">Welcome back</div>
        <h1 class="gh-page-title">Hi, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
        <p class="gh-page-subtitle">Here's what's happening with your job search today.</p>
    </div>
    <a href="{{ route('jobs.index') }}" class="btn-gh btn-gh-primary">
        <i class="bi bi-search"></i> Browse Jobs
    </a>
</div>

{{-- ═══ STAT CARDS ═══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="gh-stat gh-stat-mint">
            <div class="gh-stat-icon"><i class="bi bi-file-earmark-text"></i></div>
            <div class="gh-stat-value">{{ $stats['total'] }}</div>
            <div class="gh-stat-label">Total Applications</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="gh-stat gh-stat-amber">
            <div class="gh-stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="gh-stat-value">{{ $stats['pending'] }}</div>
            <div class="gh-stat-label">Pending Review</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="gh-stat gh-stat-blue">
            <div class="gh-stat-icon"><i class="bi bi-people"></i></div>
            <div class="gh-stat-value">{{ $stats['interview'] }}</div>
            <div class="gh-stat-label">Interviews</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="gh-stat gh-stat-green">
            <div class="gh-stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="gh-stat-value">{{ $stats['hired'] }}</div>
            <div class="gh-stat-label">Hired</div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- ═══ LEFT COLUMN: Recent Applications ═══════════════════ --}}
    <div class="col-lg-8">
        <div class="gh-card mb-3">
            <div class="gh-card-header">
                <div class="gh-card-title">Recent Applications</div>
                <a href="{{ route('applicant.applications.index') }}"
                   class="btn-gh btn-gh-outline" style="padding:0.3rem 0.85rem; font-size:0.8rem;">
                    View all
                </a>
            </div>

            @if($recentApplications->isEmpty())
                {{-- Empty state --}}
                <div class="gh-empty">
                    <div class="gh-empty-icon"><i class="bi bi-inbox"></i></div>
                    <div class="gh-empty-title">No applications yet</div>
                    <p class="gh-empty-text">
                        Once you apply to a job, you'll see its status here — from pending review all the way to hired.
                    </p>
                    <a href="{{ route('jobs.index') }}" class="btn-gh btn-gh-primary">
                        <i class="bi bi-search"></i> Browse Open Jobs
                    </a>
                </div>
            @else
                <div class="gh-table-wrap">
                    <table class="gh-table">
                        <thead>
                            <tr>
                                <th>Job</th>
                                <th>Company</th>
                                <th>Applied</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentApplications as $app)
                            <tr>
                                <td>
                                    <div style="font-family: var(--font-display); font-weight:600; color: var(--ebony-deep);">
                                        {{ $app->jobListing->title ?? 'Job removed' }}
                                    </div>
                                    <div style="font-size:0.77rem; color: var(--text-muted);">
                                        {{ $app->jobListing->location ?? '—' }}
                                    </div>
                                </td>
                                <td>{{ $app->jobListing->company->name ?? '—' }}</td>
                                <td style="color: var(--text-muted); font-size:0.825rem;">
                                    {{ $app->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <span class="gh-badge gh-badge-{{ $app->status }}">
                                        {{ $app->statusLabel() }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ═══ RECOMMENDED JOBS ═══════════════════════════════ --}}
        <div class="gh-card">
            <div class="gh-card-header">
                <div class="gh-card-title">Recommended for You</div>
                <a href="{{ route('jobs.index') }}"
                   class="btn-gh btn-gh-outline" style="padding:0.3rem 0.85rem; font-size:0.8rem;">
                    See more
                </a>
            </div>

            @if($recommendedJobs->isEmpty())
                <div class="gh-empty">
                    <div class="gh-empty-icon"><i class="bi bi-briefcase"></i></div>
                    <div class="gh-empty-title">No new recommendations</div>
                    <p class="gh-empty-text">
                        You've either applied to everything available or there are no active job posts right now. Check back soon.
                    </p>
                </div>
            @else
                <div class="d-flex flex-column gap-2">
                    @foreach($recommendedJobs as $job)
                    <a href="{{ route('jobs.show', $job->id) }}" class="gh-job-card" style="text-decoration:none;">
                        <div class="gh-job-logo">
                            {{ strtoupper(substr($job->company->name ?? 'CO', 0, 2)) }}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="gh-job-title">{{ $job->title }}</div>
                            <div class="gh-job-meta">
                                <span><i class="bi bi-building"></i> {{ $job->company->name ?? 'Company' }}</span>
                                <span><i class="bi bi-geo-alt"></i> {{ $job->location }}</span>
                                <span><i class="bi bi-clock"></i> {{ ucfirst($job->type) }}</span>
                            </div>
                        </div>
                        <span class="gh-badge gh-badge-new" style="flex-shrink:0;">
                            {{ $job->salaryRange() }}
                        </span>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ RIGHT COLUMN: Profile Card ═══════════════════════ --}}
    <div class="col-lg-4">

        {{-- Profile Completeness --}}
        <div class="gh-card mb-3">
            <div class="gh-card-title">Profile Strength</div>

            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="position:relative; width:64px; height:64px; flex-shrink:0;">
                    <svg width="64" height="64" viewBox="0 0 64 64" style="transform: rotate(-90deg);">
                        <circle cx="32" cy="32" r="28" fill="none" stroke="#E5E7EB" stroke-width="6"></circle>
                        <circle cx="32" cy="32" r="28" fill="none" stroke="#22C55E" stroke-width="6"
                                stroke-dasharray="{{ 2 * 3.1416 * 28 }}"
                                stroke-dashoffset="{{ 2 * 3.1416 * 28 * (1 - $profileCompleteness / 100) }}"
                                stroke-linecap="round"></circle>
                    </svg>
                    <div style="position:absolute; top:0; left:0; width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-family: var(--font-display); font-weight:800; font-size:0.95rem; color: var(--ebony-deep);">
                        {{ $profileCompleteness }}%
                    </div>
                </div>
                <div>
                    <div style="font-family: var(--font-display); font-weight:700; font-size:0.875rem; color: var(--ebony-deep);">
                        @if($profileCompleteness >= 80)
                            Looking great!
                        @elseif($profileCompleteness >= 40)
                            Almost there
                        @else
                            Let's get started
                        @endif
                    </div>
                    <div style="font-size:0.78rem; color: var(--text-muted);">
                        Complete your profile to stand out to employers.
                    </div>
                </div>
            </div>

            <a href="{{ route('applicant.profile.edit') }}" class="btn-gh btn-gh-secondary w-100 justify-content-center">
                <i class="bi bi-pencil-square"></i> Complete Profile
            </a>
        </div>

        {{-- Quick Actions --}}
        <div class="gh-card mb-3">
            <div class="gh-card-title">Quick Actions</div>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('applicant.resume') }}" class="gh-sidebar-item" style="background: var(--surface-2); border-radius: var(--r-sm);">
                    <i class="bi bi-upload"></i> Upload / Update Resume
                </a>
                <a href="{{ route('applicant.applications.index') }}" class="gh-sidebar-item" style="background: var(--surface-2); border-radius: var(--r-sm);">
                    <i class="bi bi-clipboard-check"></i> Track My Applications
                </a>
                <a href="{{ route('jobs.index') }}" class="gh-sidebar-item" style="background: var(--surface-2); border-radius: var(--r-sm);">
                    <i class="bi bi-funnel"></i> Filter Job Search
                </a>
            </div>
        </div>

        {{-- Tip Card --}}
        <div class="gh-card" style="background: var(--mint-glass); border-color: rgba(34,197,94,0.2);">
            <div style="display:flex; gap:10px;">
                <i class="bi bi-lightbulb-fill" style="color: var(--mint-dark); font-size:1.1rem; flex-shrink:0; margin-top:2px;"></i>
                <div>
                    <div style="font-family: var(--font-display); font-weight:700; font-size:0.84rem; color: var(--ebony-deep); margin-bottom:3px;">
                        Pro tip
                    </div>
                    <div style="font-size:0.81rem; color: var(--text-secondary); line-height:1.5;">
                        Tailor your cover letter for each application — recruiters notice the difference.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection