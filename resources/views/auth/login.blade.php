<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · SIKAT Bank Papua</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="grid min-h-screen place-items-center px-4 py-10">
    <div class="w-full max-w-[420px] rounded-2xl border border-slate-200 bg-white px-8 py-12 shadow-xl shadow-slate-200/60 sm:px-12">
        <p class="text-center text-3xl font-black tracking-wider text-navy-800" style="font-family: 'Cinzel Decorative', fantasy;">SIKAT</p>
        <h1 class="mt-6 text-center text-[22px] font-extrabold tracking-tight">Sistem Kelola ATK</h1>

        @if (session('info'))
            <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                {{ session('info') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
            @csrf
            <div>
                <label for="username" class="form-label">Username</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username"
                    required autofocus class="form-input py-3">
                @error('username')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="form-label">Password</label>
                <div class="relative">
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        placeholder=" " class="peer form-input py-3 pr-12">
                    <button type="button" data-toggle-password="password" title="Lihat password" aria-label="Lihat password"
                        class="absolute inset-y-0 right-0 grid w-12 cursor-pointer place-items-center text-slate-400 peer-placeholder-shown:hidden hover:text-navy-800">
                        <x-icon name="eye" class="size-5" data-icon="show" />
                        <x-icon name="eye-off" class="hidden size-5" data-icon="hide" />
                    </button>
                </div>
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))
                        class="size-[18px] rounded accent-navy-800">
                    Ingat saya
                </label>
                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-brand-500 hover:text-brand-600">
                    Lupa sandi?
                </a>
            </div>

            <button type="submit" class="btn btn-primary mt-2 w-full justify-center px-6 py-3">Masuk</button>
        </form>
    </div>
</body>

</html>
