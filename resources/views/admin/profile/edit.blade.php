<x-dashboard-shell
    title="Edit Admin Profile"
    eyebrow="Admin Workspace"
    :user="$user"
>

    @php
        $labelClass = 'mb-2 block text-[0.98rem] font-extrabold text-neutral-950';
        $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
        $errorClass = 'mt-2 text-sm font-bold text-signal-red';
        $primaryButtonClass = 'inline-flex min-h-[3.35rem] min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-6 py-3 font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5';
    @endphp

    <div class="max-w-4xl">

        <h1 class="font-display text-4xl font-extrabold tracking-[-0.04em] text-neutral-900">
            Edit Admin Profile
        </h1>

        <p class="mt-2 text-neutral-600">
            Update your account information.
        </p>

        @if (session('success'))
            <div class="mt-6 rounded-xl border border-green-200 bg-green-50 p-4">
                <p class="font-bold text-green-700">
                    {{ session('success') }}
                </p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-xl border border-signal-red bg-signal-red-100 p-4">
                <ul class="list-disc pl-5 text-sm font-bold text-signal-red">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('admin.profile.update') }}"
            class="mt-8"
        >
            @csrf
            @method('PUT')

            <div class="grid gap-5">

                <div>
                    <label class="{{ $labelClass }}">
                        Full Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        class="{{ $inputClass }}"
                        required
                    >

                    @error('name')
                        <div class="{{ $errorClass }}">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        class="{{ $inputClass }}"
                        required
                    >

                    @error('email')
                        <div class="{{ $errorClass }}">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

            <div class="mt-8 flex gap-3"> 
                <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-[3.35rem] min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-neutral-300 bg-neutral-100 px-6 py-3 font-black text-neutral-700 transition hover:bg-neutral-200" > 
                    Back 
                </a> 
                <button type="submit" class="{{ $primaryButtonClass }}" > 
                    Save Changes 
                </button> 
            </div>

        </form>

    </div>

</x-dashboard-shell>