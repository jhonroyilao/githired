<x-dashboard-shell title="Applicant Details">
    @php
        $application->loadMissing('statusLogs.changedByUser');
        
        $profile = $application->user->profile;
        $resume = $application->resumeDocument;
        $avatarUrl = \App\Support\StorageUrl::image($profile?->avatar_path);
        $resumeName = $resume?->original_name ?? ($application->resume_path ? basename($application->resume_path) : null);
        $resumeSize = $resume?->file_size
            ? (round($resume->file_size / 1024) >= 1024
                ? number_format($resume->file_size / 1048576, 1).' MB'
                : number_format($resume->file_size / 1024, 1).' KB')
            : null;
        $profileLinks = collect([
            'GitHub' => $profile?->github,
            'LinkedIn' => $profile?->linkedin,
            'Website' => $profile?->website,
        ])->filter();

        $statusConfig = [
            'pending'   => ['label' => 'Pending',   'bg' => 'bg-amber-100',   'text' => 'text-amber-800'],
            'interview' => ['label' => 'Interview', 'bg' => 'bg-violet-100',  'text' => 'text-violet-800'],
            'hired'     => ['label' => 'Hired',     'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800'],
            'rejected'  => ['label' => 'Rejected',  'bg' => 'bg-red-100',     'text' => 'text-red-800'],
        ];
    @endphp

    <div class="max-w-5xl mx-auto space-y-6">
        <a href="{{ route('employer.jobs.applicants', $job) }}" class="text-xs font-bold text-neutral-500 hover:text-neutral-950 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Applicants
        </a>

        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="rounded-2xl bg-blue-50 border border-blue-200 px-5 py-4 text-sm font-bold text-blue-800">
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-2xl bg-red-50 border border-red-200 px-5 py-4 text-sm font-bold text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white border-2 border-neutral-200 rounded-3xl p-8 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-6">
                        <div class="w-28 h-28 rounded-2xl flex items-center justify-center overflow-hidden">
                            <img src="{{ $avatarUrl ?? asset('assets/avatar.svg') }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-neutral-950">{{ $application->user->name }}</h1>
                            @if($profile?->headline)
                                <p class="text-neutral-700 font-black">{{ $profile->headline }}</p>
                            @endif
                            <p class="text-neutral-500 font-bold text-lg">{{ $application->user->email }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                @php
                                    $sc = $statusConfig[$application->status] ?? ['label' => ucfirst($application->status), 'bg' => 'bg-neutral-900', 'text' => 'text-white'];
                                @endphp
                                <span class="px-3 py-1 {{ $sc['bg'] }} {{ $sc['text'] }} text-xs font-black rounded-full uppercase tracking-wider">
                                    {{ $sc['label'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($profile?->bio || $profile?->location || $profile?->phone || ! empty($profile?->skills) || $profileLinks->isNotEmpty())
                    <div class="mt-10">
                        <h3 class="text-sm font-black uppercase text-neutral-400 mb-4 tracking-widest">Profile</h3>

                        @if($profile?->bio)
                            <p class="bg-neutral-50 p-6 rounded-2xl text-neutral-700 leading-relaxed border border-neutral-100">{{ $profile->bio }}</p>
                        @endif

                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            @if($profile?->location)
                                <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400">Location</p>
                                    <p class="mt-1 text-sm font-bold text-neutral-800">{{ $profile->location }}</p>
                                </div>
                            @endif

                            @if($profile?->phone)
                                <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400">Phone</p>
                                    <p class="mt-1 text-sm font-bold text-neutral-800">{{ $profile->phone }}</p>
                                </div>
                            @endif
                        </div>

                        @if(! empty($profile?->skills))
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach($profile->skills as $skill)
                                    <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-black text-neutral-700">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if($profileLinks->isNotEmpty())
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach($profileLinks as $label => $url)
                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="rounded-full border border-neutral-200 px-3 py-1 text-xs font-black text-neutral-700 hover:border-neutral-900">
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-10">
                    <h3 class="text-sm font-black uppercase text-neutral-400 mb-4 tracking-widest">Cover Letter</h3>
                    <div class="bg-neutral-50 p-6 rounded-2xl text-neutral-700 leading-relaxed border border-neutral-100 whitespace-pre-line">
                        {{ $application->cover_letter ?? 'No cover letter provided.' }}
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white border-2 border-neutral-200 rounded-3xl p-6 shadow-sm">
                    <h3 class="text-sm font-black uppercase text-neutral-950 mb-6">Application Info</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-neutral-100 pb-3">
                            <span class="text-xs font-bold text-neutral-400 uppercase">Applied Date</span>
                            <span class="text-xs font-bold text-neutral-950">{{ $application->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-neutral-400 uppercase">Job Title</span>
                            <span class="text-xs font-bold text-neutral-950 text-right">{{ $job->title }}</span>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-neutral-100">
                        @if($resumeName)
                            <div class="mb-4 space-y-3 rounded-2xl bg-neutral-50 p-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400">File name</p>
                                    <p class="mt-1 text-xs font-bold text-neutral-700 break-all">{{ $resumeName }}</p>
                                </div>

                                @if($resume?->mime_type)
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400">File type</p>
                                        <p class="mt-1 text-xs font-bold text-neutral-700">{{ $resume->mime_type }}</p>
                                    </div>
                                @endif

                                @if($resumeSize)
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400">File size</p>
                                        <p class="mt-1 text-xs font-bold text-neutral-700">{{ $resumeSize }}</p>
                                    </div>
                                @endif

                                @if($resume?->extraction_status)
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400">Text extraction</p>
                                        <p class="mt-1 text-xs font-bold capitalize text-neutral-700">{{ $resume->extraction_status }}</p>
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('employer.jobs.applicants.resume', [$job, $application]) }}" target="_blank"
                               class="block w-full text-center py-4 bg-neutral-950 text-white rounded-2xl font-black hover:bg-neutral-800 transition shadow-lg hover:shadow-xl">
                                Download Resume
                            </a>
                        @else
                            <p class="text-xs font-bold text-neutral-500">No resume attached.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-amber-50 border-2 border-amber-200 rounded-3xl p-6">
                    <h4 class="text-amber-900 font-black text-sm mb-2">Update Status</h4>
                    <p class="text-amber-700 text-xs font-medium mb-4">Change the application status to manage your hiring pipeline.</p>
                    
                    <form action="{{ route('employer.jobs.applicants.status.update', [$job, $application]) }}" method="POST" novalidate>
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="status" class="block text-[10px] font-black uppercase tracking-widest text-amber-900 mb-1">New Status</label>
                            <select id="status" name="status" class="w-full bg-white border border-amber-200 rounded-xl p-2 text-sm font-bold text-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-400">
                                @foreach(['pending' => 'Pending', 'interview' => 'Interview', 'hired' => 'Hired', 'rejected' => 'Rejected'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $application->status) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="note" class="block text-[10px] font-black uppercase tracking-widest text-amber-900 mb-1">Note <span class="font-medium normal-case">(optional)</span></label>
                            <textarea id="note" name="note" rows="2" placeholder="e.g. Technical interview scheduled for Friday" class="w-full bg-white border border-amber-200 rounded-xl px-3 py-2 text-sm text-amber-900 placeholder-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none">{{ old('note') }}</textarea>
                            @error('note')
                                <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-sm transition">
                            Save Status
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if($application->statusLogs->isNotEmpty())
            <div class="bg-white border-2 border-neutral-200 rounded-3xl p-8 shadow-sm mt-6">
                <h3 class="text-sm font-black uppercase text-neutral-400 mb-6 tracking-widest">Status History</h3>

                <ol class="relative border-l border-neutral-200 space-y-6 ml-3">
                    @foreach($application->statusLogs as $log)
                        @php
                            $lc = $statusConfig[$log->new_status] ?? ['label' => ucfirst($log->new_status), 'bg' => 'bg-neutral-100', 'text' => 'text-neutral-700'];
                        @endphp
                        <li class="ml-6">
                            <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full {{ $lc['bg'] }} ring-4 ring-white">
                                <svg class="h-2.5 w-2.5 {{ $lc['text'] }}" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                            </span>

                            <div class="flex items-center gap-2 flex-wrap">
                                @if($log->old_status)
                                    @php
                                        $oc = $statusConfig[$log->old_status] ?? ['label' => ucfirst($log->old_status), 'bg' => 'bg-neutral-100', 'text' => 'text-neutral-700'];
                                    @endphp
                                    <span class="rounded-full {{ $oc['bg'] }} {{ $oc['text'] }} px-2 py-0.5 text-[10px] font-black uppercase">
                                        {{ $oc['label'] }}
                                    </span>
                                    <svg class="w-3 h-3 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                                    </svg>
                                @endif
                                <span class="rounded-full {{ $lc['bg'] }} {{ $lc['text'] }} px-2 py-0.5 text-[10px] font-black uppercase">
                                    {{ $lc['label'] }}
                                </span>

                                <span class="text-xs text-neutral-400 font-medium">
                                    {{ $log->created_at->format('M d, Y · g:i A') }}
                                </span>
                                @if($log->changedByUser)
                                    <span class="text-xs text-neutral-400">
                                        by {{ $log->changedByUser->name }}
                                    </span>
                                @endif
                            </div>

                            @if($log->note)
                                <p class="mt-1 text-xs text-neutral-600 font-medium">{{ $log->note }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
</x-dashboard-shell>