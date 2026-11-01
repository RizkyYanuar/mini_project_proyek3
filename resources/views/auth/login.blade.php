<!DOCTYPE html>
<html lang="id">

<head>
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen bg-gray-100 flex justify-center items-center">
    <main class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-4">Login</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="flex flex-col gap-4">
            @csrf
            
            <div class="flex flex-col gap-1">
                <label class="font-medium text-gray-700">Email atau Username:</label>
                <input type="text" name="login" value="{{ old('login') }}" required
                    class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex flex-col gap-1">
                <label class="font-medium text-gray-700">Password:</label>
                <input type="password" name="password" required
                    class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 font-semibold mt-2 transition-colors">
                Login
            </button>
        </form>

        <p class="mt-4 text-sm text-center text-gray-600">
            Belum punya akun? <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Daftar di sini</a>
        </p>
    </main>
</body>

</html>
