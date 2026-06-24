@php
    $secondaryButtonClass = 'inline-flex min-h-11 items-center justify-center rounded-xl border-2 border-neutral-900 bg-white px-4 py-2 text-sm font-black text-neutral-900 no-underline transition hover:-translate-y-0.5';
    $primaryButtonClass = 'inline-flex min-h-11 items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-4 py-2 text-sm font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5';
    $dangerButtonClass = 'inline-flex min-h-11 items-center justify-center rounded-xl border-2 border-signal-red bg-white px-4 py-2 text-sm font-black text-signal-red transition hover:-translate-y-0.5';
@endphp

<x-dashboard-shell title="My resume" eyebrow="Job seeker workspace" :user="$user">
    <p class="mt-5 max-w-2xl text-lg font-bold leading-8 text-neutral-600">
        Upload a PDF resume and choose which version should be used for applications and future resume parsing.
    </p>

    @if (session('status'))
        <div class="mt-5 rounded-2xl border-2 border-primarygreen bg-primarygreen-100 px-4 py-3 font-black text-neutral-900">
            {{ session('status') }}
        </div>
    @endif

    <section class="mt-7 rounded-2xl border border-neutral-200 bg-white p-5">
        <h2 class="text-xl font-black text-neutral-900">Upload PDF</h2>

        <form method="POST" action="{{ route('applicant.resume.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4" data-resume-upload-form>
            @csrf

            <div>
                <label for="resume" class="mb-1.5 block text-base font-extrabold text-neutral-950">Resume file</label>
                <input id="resume" name="resume" type="file" accept="application/pdf" required class="w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition file:mr-4 file:rounded-lg file:border-0 file:bg-primarygreen file:px-4 file:py-2 file:text-sm file:font-black file:text-neutral-900 focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25">
                <div class="mt-1.5 flex flex-wrap items-center gap-3">
                    <p class="text-xs font-bold text-neutral-600/70">PDF only, 5 MB maximum.</p>
                    <button type="button" class="hidden text-xs font-black text-signal-red underline decoration-2 underline-offset-4" data-clear-resume-file>
                        Remove selected file
                    </button>
                </div>
                @error('resume')
                    <div class="mt-1.5 text-sm font-bold text-signal-red">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="{{ $primaryButtonClass }}">Upload resume</button>
        </form>
    </section>

    @php use Illuminate\Support\Str; @endphp

    <section class="mt-5 rounded-2xl border border-neutral-200 bg-white p-5">
        <h2 class="text-xl font-black text-neutral-900">Current resume</h2>

        @if ($currentResume)
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-neutral-100 p-4">
                <div>
                    <p class="font-black text-neutral-900">{{ $currentResume->original_name ?? 'Resume PDF' }}</p>
                    <p class="text-sm font-bold text-neutral-600">{{ $currentResume->created_at->format('M d, Y') }}</p>

                    {{-- Extraction status badge --}}
                    <p class="mt-1 text-xs font-bold">
                        @if ($currentResume->extraction_status === 'ready')
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-green-800">
                                ✓ Text extracted
                            </span>
                        @elseif ($currentResume->extraction_status === 'failed')
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-red-800">
                                ✗ Extraction failed
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2 py-0.5 text-yellow-800">
                                ⏳ Extraction pending
                            </span>
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('applicant.resume.show', $currentResume) }}" class="{{ $secondaryButtonClass }}">Download</a>
                    <form method="POST" action="{{ route('applicant.resume.destroy', $currentResume) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="{{ $dangerButtonClass }}">Delete</button>
                    </form>
                </div>
            </div>

            {{-- Extracted text preview --}}
            @if ($currentResume->extraction_status === 'ready' && $currentResume->extracted_text)
                <div class="mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
                    <p class="mb-2 text-xs font-black uppercase tracking-widest text-neutral-500">Extracted Text Preview</p>
                    <pre class="max-h-64 overflow-y-auto whitespace-pre-wrap break-words font-sans text-sm text-neutral-700">{{ Str::limit($currentResume->extracted_text, 1500) }}</pre>
                    @if (strlen($currentResume->extracted_text) > 1500)
                        <p class="mt-2 text-xs font-bold text-neutral-400">
                            Showing first 1,500 of {{ number_format(strlen($currentResume->extracted_text)) }} characters.
                        </p>
                    @endif
                </div>

            @elseif ($currentResume->extraction_status === 'failed')
                <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-800">
                    Text extraction failed. Job matching will use your profile skills instead.
                    @if ($currentResume->extraction_error)
                        <p class="mt-1 text-xs font-semibold opacity-75">{{ $currentResume->extraction_error }}</p>
                    @endif
                </div>

            @elseif ($currentResume->extraction_status === 'pending')
                <div class="mt-4 rounded-2xl border border-yellow-200 bg-yellow-50 p-4 text-sm font-bold text-yellow-800">
                    Text extraction is in progress. Refresh the page in a moment.
                </div>
            @endif

        @else
            <p class="mt-4 rounded-2xl bg-neutral-100 p-4 font-bold text-neutral-600">No current resume uploaded.</p>
        @endif
    </section>

    @if ($resumeHistory->isNotEmpty())
        <section class="mt-5 rounded-2xl border border-neutral-200 bg-white p-5">
            <h2 class="text-xl font-black text-neutral-900">Resume history</h2>

            <div class="mt-4 space-y-3">
                @foreach ($resumeHistory as $oldResume)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-neutral-100 p-4">
                        <div>
                            <p class="font-black text-neutral-900">{{ $oldResume->original_name ?? 'Resume PDF' }}</p>
                            <p class="text-sm font-bold text-neutral-600">{{ $oldResume->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('applicant.resume.show', $oldResume) }}" class="{{ $secondaryButtonClass }}">Download</a>
                            <form method="POST" action="{{ route('applicant.resume.set-current', $oldResume) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="{{ $primaryButtonClass }}">Set current</button>
                            </form>
                            <form method="POST" action="{{ route('applicant.resume.destroy', $oldResume) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="{{ $dangerButtonClass }}">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <script>
        document.querySelectorAll('[data-resume-upload-form]').forEach((form) => {
            const input = form.querySelector('input[type="file"]');
            const clearButton = form.querySelector('[data-clear-resume-file]');

            if (! input || ! clearButton) {
                return;
            }

            input.addEventListener('change', () => {
                clearButton.classList.toggle('hidden', input.files.length === 0);
            });

            clearButton.addEventListener('click', () => {
                input.value = '';
                clearButton.classList.add('hidden');
                input.focus();
            });
        });
    </script>
</x-dashboard-shell>
