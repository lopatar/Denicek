<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upravit záznam</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex flex-col">
                <span class="text-sm text-gray-500">Úprava záznamu</span>
                <span class="font-semibold text-indigo-600">{{ auth()->user()->name }}</span>
            </div>
            <button onclick="location.href='/'" 
                class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">
                ← Zpět na přehled
            </button>
        </div>
    </div>
</nav>

<main class="max-w-3xl mx-auto px-4 py-10 sm:px-6 lg:px-8">

    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Upravit záznam</h2>

        <form method="POST" action="/entry/{{ $entry->id }}/edit" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">
                    Titulek
                </label>
                <input type="text" 
                       name="title" 
                       value="{{ Crypt::decryptString($entry->title) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                       required>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">
                    Hodnocení (1 = nejlepší, 5 = nejhorší)
                </label>
                <input type="number" 
                       min="1" 
                       max="5" 
                       name="rating" 
                       value="{{ $entry->rating }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                       required>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">
                    Popis
                </label>
                <textarea rows="6" 
                          name="description"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                          required>{{ Crypt::decryptString($entry->description) }}</textarea>
            </div>

            <div class="flex justify-end gap-4 pt-4">
                <button type="button"
                        onclick="location.href='/'"
                        class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                    Zrušit
                </button>

                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition shadow-md active:scale-95">
                    Uložit změny
                </button>
            </div>

        </form>
        @foreach($uploadedFiles as $file)
        <a href="/file/{{ $file->id }}">Přiložený soubor</a>
        @endforeach
    </div>

</main>

</body>
</html>