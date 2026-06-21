<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | GitHired</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="mb-4">
                            <h1 class="h3 mb-2">Create an account</h1>
                            <p class="text-secondary mb-0">Join GitHired as an applicant or employer.</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register.store') }}" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    autocomplete="name"
                                    required
                                    autofocus
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    autocomplete="email"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <fieldset class="mb-3">
                                <legend class="form-label fs-6">Account type</legend>

                                <div class="form-check">
                                    <input
                                        id="role_applicant"
                                        name="role"
                                        type="radio"
                                        value="applicant"
                                        class="form-check-input @error('role') is-invalid @enderror"
                                        @checked(old('role', 'applicant') === 'applicant')
                                        required
                                    >
                                    <label for="role_applicant" class="form-check-label">Applicant</label>
                                </div>

                                <div class="form-check">
                                    <input
                                        id="role_employer"
                                        name="role"
                                        type="radio"
                                        value="employer"
                                        class="form-check-input @error('role') is-invalid @enderror"
                                        @checked(old('role') === 'employer')
                                        required
                                    >
                                    <label for="role_employer" class="form-check-label">Employer</label>
                                </div>

                                @error('role')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </fieldset>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    autocomplete="new-password"
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm password</label>
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    class="form-control"
                                    autocomplete="new-password"
                                    required
                                >
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>

                        <p class="text-center text-secondary mt-4 mb-0">
                            Already have an account?
                            <a href="{{ route('login') }}">Log in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
