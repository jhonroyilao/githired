@php
    $labelClass = 'mb-1.5 block text-[0.92rem] font-extrabold text-neutral-950';
    $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-3.5 py-2.5 text-base text-neutral-900 transition placeholder:text-neutral-600/50 focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
    $errorClass = 'mt-2 text-sm font-bold text-signal-red';
    $buttonClass = 'mt-4 inline-flex min-h-12 w-full items-center justify-center rounded-[0.875rem] bg-primarygreen font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5 hover:bg-[#a2d74d] active:translate-y-0';
    $linkClass = 'font-black text-neutral-900 no-underline hover:underline hover:underline-offset-4';
@endphp

<x-auth-shell title="Sign up" subtitle="Create your GitHired account">
    @if ($errors->any())
        <div class="mt-5 rounded-[0.875rem] border border-signal-red bg-signal-red-100 px-4 py-3.5 text-sm text-[#7f2c20]" role="alert">
            <ul class="m-0 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}" class="mt-5" novalidate>
        @csrf

        <div>
            <label for="name" class="{{ $labelClass }}">Full name</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                class="{{ $inputClass }}"
                placeholder="Juan Dela Cruz"
                autocomplete="name"
                required
                autofocus
            >
            @error('name')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4">
            <label for="email" class="{{ $labelClass }}">Email address</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                class="{{ $inputClass }}"
                placeholder="Enter your email"
                autocomplete="email"
                required
            >
            @error('email')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <fieldset class="mt-4">
            <legend class="{{ $labelClass }}">What best describes you?</legend>

            <div class="grid grid-cols-2 gap-4 max-[920px]:grid-cols-1">
                <label class="relative grid min-h-[6.25rem] cursor-pointer place-items-center rounded-[1.125rem] border-2 border-neutral-950/60 bg-white p-3 text-center transition hover:-translate-y-0.5 has-[:checked]:border-neutral-900 has-[:checked]:bg-neutral-900 has-[:checked]:text-white">
                    <input
                        name="role"
                        type="radio"
                        value="applicant"
                        class="peer absolute opacity-0 pointer-events-none"
                        @checked(old('role', 'applicant') === 'applicant')
                        required
                    >
                    <span class="absolute right-4 top-4 hidden size-6 place-items-center rounded-lg bg-primarygreen font-black text-neutral-900 peer-checked:grid">✓</span>
                    <img src="{{ asset('assets/find-white.svg') }}" alt="" class="hidden size-10 object-contain peer-checked:block">
                    <img src="{{ asset('assets/find-green.svg') }}" alt="" class="size-10 object-contain peer-checked:hidden">
                    <span class="mt-2 text-base font-black">Job seeker</span>
                </label>

                <label class="relative grid min-h-[6.25rem] cursor-pointer place-items-center rounded-[1.125rem] border-2 border-neutral-950/60 bg-white p-3 text-center transition hover:-translate-y-0.5 has-[:checked]:border-neutral-900 has-[:checked]:bg-neutral-900 has-[:checked]:text-white">
                    <input
                        name="role"
                        type="radio"
                        value="employer"
                        class="peer absolute opacity-0 pointer-events-none"
                        @checked(old('role') === 'employer')
                        required
                    >
                    <span class="absolute right-4 top-4 hidden size-6 place-items-center rounded-lg bg-primarygreen font-black text-neutral-900 peer-checked:grid">✓</span>
                    <img src="{{ asset('assets/find-white.svg') }}" alt="" class="hidden size-10 object-contain peer-checked:block">
                    <img src="{{ asset('assets/find-green.svg') }}" alt="" class="size-10 object-contain peer-checked:hidden">
                    <span class="mt-2 text-base font-black">Recruiter</span>
                </label>
            </div>

            @error('role')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </fieldset>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="{{ $labelClass }}">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="{{ $inputClass }}"
                    placeholder="Enter your password"
                    autocomplete="new-password"
                    required
                >
                @error('password')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="{{ $labelClass }}">Confirm password</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="{{ $inputClass }}"
                    placeholder="Repeat your password"
                    autocomplete="new-password"
                    required
                >
            </div>
        </div>

        <button type="submit" class="{{ $buttonClass }}">Sign up</button>
    </form>

    <p class="mt-4 text-center text-neutral-600">
        Already have an account?
        <a href="{{ route('login') }}" class="{{ $linkClass }}">Sign in</a>
    </p>
</x-auth-shell>
