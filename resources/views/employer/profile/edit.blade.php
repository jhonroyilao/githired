@php
    $labelClass = 'mb-2 block text-[0.98rem] font-extrabold text-neutral-950';
    $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
    $primaryButtonClass = 'inline-flex min-h-[3.35rem] min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-6 py-3 font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5 max-sm:w-full';
    
    $logoMarkedForRemoval = old('remove_logo') === '1';
    $hasLogo = filled($company?->logo_path) && ! $logoMarkedForRemoval;
    $logoUrl = $hasLogo ? \App\Support\StorageUrl::image($company->logo_path) : null;
    $logoPlaceholder = asset('assets/avatar.svg');
@endphp

<x-dashboard-shell title="Edit Company Profile">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white border-2 border-neutral-200 rounded-2xl p-8 shadow-[4px_4px_0px_0px_rgba(26,35,21,0.06)]">
            
            <div class="mb-8">
                <h1 class="text-2xl font-black text-neutral-950">Company Profile</h1>
                <p class="text-neutral-500 font-medium">Update your company details to help attract top talent.</p>
            </div>

            <form method="POST" action="{{ route('employer.company.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT') 

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="{{ $labelClass }}">Company Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $company?->name) }}" class="{{ $inputClass }}" required>
                    </div>

                    <div>
                        <label for="slug" class="{{ $labelClass }}">Company Slug</label>
                        <input id="slug" name="slug" type="text" value="{{ old('slug', $company?->slug) }}" class="{{ $inputClass }}">
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="industry" class="{{ $labelClass }}">Industry</label>
                        <input id="industry" name="industry" type="text" value="{{ old('industry', $company?->industry) }}" class="{{ $inputClass }}" required>
                    </div>

                    <div>
                        <label for="size" class="{{ $labelClass }}">Company Size</label>
                        <input id="size" name="size" type="text" value="{{ old('size', $company?->size) }}" class="{{ $inputClass }}" required>
                    </div>
                </div>

                <div>
                    <label for="location" class="{{ $labelClass }}">Location</label>
                    <input id="location" name="location" type="text" value="{{ old('location', $company?->location) }}" class="{{ $inputClass }}" required>
                </div>

                <div>
                    <label for="website" class="{{ $labelClass }}">Website</label>
                    <input id="website" name="website" type="url" value="{{ old('website', $company?->website) }}" class="{{ $inputClass }}">
                </div>

                {{-- Logo Section (Keep logic the same) --}}
                <div class="border-t border-neutral-100 pt-6">
                    <label class="{{ $labelClass }}">Company Logo</label>
                    <div class="flex items-center gap-6">
                        <div class="size-24 rounded-2xl overflow-hidden border-2 border-neutral-200">
                            <img id="logo-preview" src="{{ $logoUrl ?? $logoPlaceholder }}" class="h-full w-full object-cover">
                        </div>
                        <label class="cursor-pointer bg-neutral-900 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-neutral-800">
                            Change Logo
                            <input type="file" name="logo" class="hidden" data-preview-target="logo-preview">
                        </label>
                    </div>
                </div>

                <div>
                    <label for="description" class="{{ $labelClass }}">Company Description</label>
                    <textarea id="description" name="description" rows="5" class="{{ $inputClass }}">{{ old('description', $company?->description) }}</textarea>
                </div>

                <div class="pt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('employer.dashboard') }}" class="px-6 py-3 font-black text-neutral-600 hover:text-neutral-900">Cancel</a>
                    <button type="submit" class="{{ $primaryButtonClass }}">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-shell>