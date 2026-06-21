@extends('layouts.main')

@section('title', 'GitHired')



@push('styles')
<link rel="stylesheet" href="{{ asset('custom/landing.css') }}">
@endpush



@section('content')

<style>
    .gh-main { padding: 0 !important; }
    .gh-wrapper { display: block !important; }
    .gh-footer  { display: none; } 
</style>

{{-- ─── HERO ─────────────────────────────────────────────────── --}}
<section class="hero">
    <div class="hero-inner">
        <div>
            <h1 class="hero-title">
                Connecting Great Talent
                <span>With Great Opportunities.</span>
            </h1>
            <p class="hero-sub">
                GitHired brings job discovery, applications, and recruiter workflows into one structured platform. No inbox chaos. No scattered spreadsheets.
            </p>
            <div class="hero-actions">
                <a href="{{ route('register') }}" class="btn-gh-primary" style="padding:0.65rem 1.4rem; font-size:0.875rem;">
                    <i class="bi bi-arrow-right"></i>
                    Create free account
                </a>
                <a href="#how" class="btn-gh-outline" style="padding:0.65rem 1.4rem; font-size:0.875rem; border-color:rgba(255,255,255,0.15); color:rgba(255,255,255,0.65);">
                    See how it works
                </a>
            </div>
<!--
            <div class="hero-stats">
                <div>
                    <div class="hero-stat-num">3,200<span>+</span></div>
                    <div class="hero-stat-label">Active job listings</div>
                </div>
                <div>
                    <div class="hero-stat-num">840<span>+</span></div>
                    <div class="hero-stat-label">Companies hiring</div>
                </div>
                <div>
                    <div class="hero-stat-num">12k<span>+</span></div>
                    <div class="hero-stat-label">Applicants placed</div>
                </div>
            </div> -->
        </div>

        {{-- Mock UI cards --}}
        <div class="hero-visual">
            <img src="{{ asset('hero-pic2.svg') }}" height="500" width="600" alt="Hero Graphic">
        </div>
    </div>
</section>

{{-- ─── HOW IT WORKS ──────────────────────────────────────────── --}}
<section class="land-section land-section-surface" id="how">
    <div class="land-container">
        <div class="land-eyebrow"><i class="fa-regular fa-map"></i> How it works</div>
        <h2 class="land-title">From browsing to hired, in one place.</h2>
        <p class="land-sub">GitHired structures every step of the job search and hiring process so nothing falls through the cracks.</p>

        <div class="how-grid">
            <div class="how-card">
                <span class="how-step">01</span>
                <div class="how-icon-img"> <img src="{{ asset('build.svg') }}" alt=""></div>                
                <h3>Build your profile</h3>
                <p>Showcase your skills, experience, and projects. Stand out beyond a plain resume with a structured, searchable profile.</p>
            </div>
            <div class="how-card">
                <span class="how-step">02</span>
                <div class="how-icon-img"> <img src="{{ asset('discover.svg') }}" alt=""></div>                
                <h3>Discover the right roles</h3>
                <p>Filter listings by role, tech stack, location, or salary. Every listing is validated before it goes live.</p>
            </div>
            <div class="how-card">
                <span class="how-step">03</span>
                <div class="how-icon-img"> <img src="{{ asset('track.svg') }}" alt=""></div>                
                <h3>Apply and track progress</h3>
                <p>Apply directly through the platform. Track your application status in real time — under review, shortlisted, or hired.</p>
            </div>
        </div>
    </div>
</section>

{{-- ─── FEATURE: Job Seekers ──────────────────────────────────── --}}
<section class="land-section land-section-light" id="features">
    <div class="land-container">
        <div class="land-split">
            <div>
                <div class="land-eyebrow"><i class="fa-regular fa-user"></i> For job seekers</div>
                <h2 class="split-title">Your skills deserve more than a PDF.</h2>
                <p class="split-body">Build a dynamic profile that surfaces your actual work — projects, stack, and experience — visible only to employers you choose to apply to.</p>
                <ul class="feat-list">
                    <li><i class="fa-solid fa-check"></i> Skills and tech-stack tagging</li>
                    <li><i class="fa-solid fa-check"></i> Project showcase section</li>
                    <li><i class="fa-solid fa-check"></i> Resume upload alongside profile</li>
                    <li><i class="fa-solid fa-check"></i> Real-time application status tracking</li>
                    <li><i class="fa-solid fa-check"></i> Role, location, and salary filtering</li>
                </ul>
            </div>

            <div>
            <img src="{{ asset('jobseekers.svg') }}" height="400" width="600" alt="jobskeers graphic">
            </div>
        </div>
    </div>
</section>

{{-- ─── FEATURE: Recruiters ───────────────────────────────────── --}}
<section class="land-section land-section-dark">
    <div class="land-container">
        <div class="land-split reverse">
            <div>
                <div class="land-eyebrow land-eyebrow-dim"><i class="fa-regular fa-briefcase"></i> For recruiters</div>
                <h2 class="split-title">Stop managing hiring from your inbox.</h2>
                <p class="split-body">Post openings, define requirements, and manage every incoming application from a single dashboard. Update statuses in real time — no more email threads and scattered spreadsheets.</p>
                <ul class="feat-list">
                    <li><i class="fa-solid fa-check"></i> Structured job posting with requirements</li>
                    <li><i class="fa-solid fa-check"></i> Centralized applicant pipeline</li>
                    <li><i class="fa-solid fa-check"></i> One-click status updates</li>
                    <li><i class="fa-solid fa-check"></i> Candidate profile review in platform</li>
                    <li><i class="fa-solid fa-check"></i> Company branding page</li>
                </ul>
            </div>

            <div>
            <img src="{{ asset('recruiters.svg') }}" height="400" width="600" alt="jobskeers graphic">
            </div>
        </div>
    </div>
</section>



{{-- ─── MEET THE DEVELOPERS ───────────────────────────────────── --}}
<section class="land-section land-section-light" id="team">
    <div class="land-container">
        <div class="land-eyebrow"><i class="fa-regular fa-code"></i> The team</div>
        <h2 class="land-title">Meet the developers.</h2>
        <p class="land-sub">GitHired is built by a small team of developers who believe the hiring process should work better for everyone involved.</p>

        <div class="team-grid">
            @php
            $team = [
                [
                    'initials' => 'JA',
                    'name' => 'John Alejandro De Vera',
                    'role' => 'Fullstack Developer',
                    'image' => 'roy.png'
                ],
                [
                    'initials' => 'JR',
                    'name' => 'Jhon Roy Ilao',
                    'role' => 'Fullstack Developer & UI/UX',
                    'image' => 'roy.png'
                ],
                [
                    'initials' => 'FM',
                    'name' => 'Frances Lorraine Montemayor',
                    'role' => 'Full-stack Developer',
                    'image' => 'roy.png'
                ],
                [
                    'initials' => 'NY',
                    'name' => 'Noreen Yau',
                    'role' => 'Fullstack Developer',
                    'image' => 'roy.png'
                ],
            ];
            @endphp

            @foreach($team as $member)
            <div class="team-card">
                <div class="team-avatar">
                    <img src="{{ asset($member['image']) }}" alt="{{ $member['name'] }}">
                </div>
                <div class="team-info">
                    <div class="team-name">{{ $member['name'] }}</div>
                    <div class="team-role">{{ $member['role'] }}</div>
                    <div class="team-socials">
                        <a href="#" class="team-social"><i class="fa-brands fa-github"></i></a>
                        <a href="#" class="team-social"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── CTA BAND ──────────────────────────────────────────────── --}}
<section class="cta-band">
    <div class="cta-inner land-container">
        <h2>Ready to find your next role?</h2>
        <p>Create a free account and start applying to verified listings today. No noise, no spam.</p>
        <div class="cta-actions">
            <a href="{{ route('register') }}" class="btn-land-white round">
                <i class="fa-regular fa-user-plus"></i> Create free account
            </a>
            <a href="{{ route('register') }}" class="btn-land-outline">
                <i class="fa-regular fa-briefcase"></i> Post a job opening
            </a>
        </div>
    </div>
</section>

{{-- ─── LANDING FOOTER (replaces the layout's simple footer) ─── --}}
<footer class="land-footer">
    <div class="land-footer-inner">
        <div>
         <a href="{{ url('/') }}" class="gh-navbar-brand" style="font-size: 1rem;">
            <img src="gh-logo-main.svg" alt="GitHired Logo" width="28" height="28" style="display: block;"> 
            GitHired
        </a>
            <p class="land-footer-brand-desc">A job portal designed to connect real talent with real opportunities. Built to simplify hiring for everyone involved.</p>
        </div>
        <div>
            <div class="land-footer-col-title">Platform</div>
            <a href="{{ route('jobs.index') }}" class="land-footer-link">Browse jobs</a>
            <a href="{{ route('register') }}" class="land-footer-link">Post a job</a>
            @auth
            <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="land-footer-link">Dashboard</a>
            @endauth
        </div>
        <div>
            <div class="land-footer-col-title">Useful links</div>
            <a href="#" class="land-footer-link">Home</a>
            <a href="#features" class="land-footer-link">Features</a>
            <a href="#how" class="land-footer-link">How it works</a>
            <a href="#team" class="land-footer-link">Team</a>
        </div>
        <div>
            <div class="land-footer-col-title">Help</div>
            <a href="#" class="land-footer-link">FAQ</a>
            <a href="#" class="land-footer-link">Terms &amp; Conditions</a>
            <a href="#" class="land-footer-link">Privacy Policy</a>
            <a href="#" class="land-footer-link">Contact</a>
        </div>
    </div>
    <div class="land-footer-bottom">
        <span>&copy; {{ date('Y') }} GitHired. All rights reserved.</span>
        <span>Built for real opportunities.</span>
    </div>
</footer>

@endsection
