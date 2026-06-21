@php
    $labelClass = 'mb-1.5 block text-[0.92rem] font-extrabold text-neutral-950';
    $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-3.5 py-2.5 text-base text-neutral-900 transition placeholder:text-neutral-600/50 focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
    $errorClass = 'mt-2 text-sm font-bold text-signal-red';
    $buttonClass = 'mt-6 inline-flex min-h-12 w-full items-center justify-center rounded-[0.875rem] bg-primarygreen font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5 hover:bg-[#a2d74d] active:translate-y-0';
    $linkClass = 'font-black text-neutral-900 no-underline hover:underline hover:underline-offset-4';
@endphp

<x-auth-shell title="Sign in" subtitle="Please enter your details" card-width="narrow">
    @if ($errors->any())
        <div class="mt-5 rounded-[0.875rem] border border-signal-red bg-signal-red-100 px-4 py-3.5 text-sm text-[#7f2c20]" role="alert">
            <ul class="m-0 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="mt-5" novalidate>
        @csrf

        <div>
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
                autofocus
            >
            @error('email')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-6">
            <label for="password" class="{{ $labelClass }}">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                class="{{ $inputClass }}"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
            >
            @error('password')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-5 flex items-center justify-between gap-4">
            <label for="remember" class="flex items-center gap-2 font-bold text-neutral-600">
                <input
                    id="remember"
                    name="remember"
                    type="checkbox"
                    value="1"
                    class="size-[1.15rem] accent-primarygreen"
                    @checked(old('remember'))
                >
                <span>Remember me</span>
            </label>
        </div>

        <button type="submit" class="{{ $buttonClass }}">Sign in</button>
    </form>

    <p class="mt-7 text-center text-neutral-600">
        Do not have an account?
        <a href="{{ route('register') }}" class="{{ $linkClass }}">Sign up</a>
    </p>
</x-auth-shell>
