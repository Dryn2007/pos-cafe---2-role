<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow p-6">
            <h1 class="text-2xl font-semibold mb-1">Login</h1>
            <p class="text-sm text-gray-500 mb-6">Masuk untuk akses Admin / Kasir</p>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                    Remember me
                </label>

                <button type="submit"
                    class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-white font-medium hover:bg-indigo-700">
                    Masuk
                </button>
            </form>

            <div class="text-sm text-gray-600 mt-6">
                Belum punya akun?
                <a class="text-indigo-600 hover:underline" href="{{ route('register') }}">Register</a>
            </div>

            <div class="text-xs text-gray-500 mt-6">
                <div>Default akun (seed):</div>
                <div>Admin: <code>admin@cafe.test</code> / <code>password</code></div>
                <div>Kasir: <code>kasir@cafe.test</code> / <code>password</code></div>
            </div>
        </div>
    </div>
</body>

</html>