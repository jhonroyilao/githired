<x-dashboard-shell title="Create Job Listing">

    @php
        $labelClass = 'mb-2 block text-[0.98rem] font-extrabold text-neutral-950';
        $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
        $errorClass = 'mt-2 text-sm font-bold text-signal-red';
        $primaryButtonClass = 'inline-flex min-h-[3.35rem] min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-6 py-3 font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5';
    @endphp

    <div class="max-w-5xl">
        <h1 class="font-display text-4xl font-extrabold tracking-[-0.04em] text-neutral-900">
            Create Job Listing
        </h1>

        <p class="mt-2 text-neutral-600">
            Submit a new job opening for review.
        </p>

        @if ($errors->any())
            <div class="mt-6 rounded-xl border border-signal-red bg-signal-red-100 p-4">
                <ul class="list-disc pl-5 text-sm font-bold text-signal-red">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('employer.jobs.store') }}" class="mt-8">
            @csrf

            <div class="grid gap-5 md:grid-cols-2">

                <div>
                    <label class="{{ $labelClass }}">Job Title</label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        class="{{ $inputClass }}"
                        required>

                    @error('title')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Category</label>

                    <select
                        name="category_id"
                        class="{{ $inputClass }}"
                        required>

                        <option value="">Select Category</option>

                        @foreach($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected(old('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('category_id')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Location</label>

                    <input
                        type="text"
                        name="location"
                        value="{{ old('location') }}"
                        placeholder="Manila, Philippines"
                        class="{{ $inputClass }}"
                        required>

                    @error('location')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Location Type</label>

                    <select
                        name="location_type"
                        class="{{ $inputClass }}"
                        required>

                        <option value="">Select Location Type</option>

                        <option value="remote" @selected(old('location_type') === 'remote')>
                            Remote
                        </option>

                        <option value="onsite" @selected(old('location_type') === 'onsite')>
                            On-site
                        </option>

                        <option value="hybrid" @selected(old('location_type') === 'hybrid')>
                            Hybrid
                        </option>
                    </select>

                    @error('location_type')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Job Type</label>

                    <select
                        name="type"
                        class="{{ $inputClass }}"
                        required>

                        <option value="">Select Job Type</option>

                        @foreach (\App\Enums\JobType::cases() as $type)
                            <option
                                value="{{ $type->value }}"
                                @selected(old('type') === $type->value)>
                                {{ str($type->value)->replace('-', ' ')->title() }}
                            </option>
                        @endforeach
                    </select>

                    @error('type')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Experience Level</label>

                    <select
                        name="experience_level"
                        class="{{ $inputClass }}"
                        required>

                        <option value="">Select Experience Level</option>

                        <option value="entry" @selected(old('experience_level') === 'entry')>
                            Entry
                        </option>

                        <option value="mid" @selected(old('experience_level') === 'mid')>
                            Mid
                        </option>

                        <option value="senior" @selected(old('experience_level') === 'senior')>
                            Senior
                        </option>
                    </select>

                    @error('experience_level')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Minimum Salary</label>

                    <input
                        type="number"
                        name="salary_min"
                        value="{{ old('salary_min') }}"
                        step="0.01"
                        class="{{ $inputClass }}">

                    @error('salary_min')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Maximum Salary</label>

                    <input
                        type="number"
                        name="salary_max"
                        value="{{ old('salary_max') }}"
                        step="0.01"
                        class="{{ $inputClass }}">

                    @error('salary_max')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Salary Currency</label>

                    <input
                        type="text"
                        name="salary_currency"
                        value="{{ old('salary_currency', 'PHP') }}"
                        maxlength="3"
                        class="{{ $inputClass }}">

                    @error('salary_currency')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Application Deadline</label>

                    <input
                        type="date"
                        name="expires_at"
                        value="{{ old('expires_at') }}"
                        class="{{ $inputClass }}">

                    <p class="mt-2 text-sm text-neutral-500">Leave blank if the job has no deadline.</p>

                    @error('expires_at')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="mt-5">
                <label class="{{ $labelClass }}">Skills Required</label>

                <input
                    type="text"
                    name="skills_required"
                    value="{{ old('skills_required') }}"
                    placeholder="Laravel, PHP, PostgreSQL"
                    class="{{ $inputClass }}">
            </div>

            <div class="mt-5">
                <label class="{{ $labelClass }}">Description</label>

                <textarea
                    name="description"
                    rows="6"
                    class="{{ $inputClass }}"
                    required>{{ old('description') }}</textarea>

                @error('description')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-5">
                <label class="{{ $labelClass }}">Requirements</label>

                <textarea
                    name="requirements"
                    rows="6"
                    class="{{ $inputClass }}"
                    required>{{ old('requirements') }}</textarea>

                @error('requirements')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-8">
                <button
                    type="submit"
                    class="{{ $primaryButtonClass }}">
                    Submit Job
                </button>
            </div>
        </form>
    </div>

</x-dashboard-shell>
