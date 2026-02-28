<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
        
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
            Přihlášení
        </h2>

        @error('email')
            <h2>{{ $message }}</h2>
        @enderror

        <form method="POST" action="/login" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">
                    Email
                </label>
                <input type="email" 
                       name="email" 
                       placeholder="Email"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                       required>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">
                    Heslo
                </label>
                <input type="password" 
                       name="password" 
                       placeholder="••••••••"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                       required>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="/register" 
                   class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                    Registrace
                </a>

                <button type="submit" 
                        class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition-all shadow-md active:scale-95">
                    Přihlásit se
                </button>
            </div>

        </form>

    </div>

</body>
</html>