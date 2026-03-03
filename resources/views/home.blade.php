<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }}</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased">

    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Vítejte,</span>
                    <span class="font-semibold text-indigo-600">{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit"
                        class="text-sm font-medium text-red-600 hover:text-red-800 transition-colors cursor-pointer">
                        Odhlásit se
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- LEFT COLUMN -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 sticky top-8">
                    <h2 class="text-lg font-bold mb-4 text-gray-800">Nový záznam</h2>
                    <form method="POST" action="/entry" class="space-y-4" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Titulek</label>
                            <input type="text" name="title" placeholder="Jaký byl tvůj den?"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                                required />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Hodnocení (1-5)</label>
                            <input type="number" min="1" max="5" name="rating" placeholder="1 = Nejlepší"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                                required />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Popis</label>
                            <textarea rows="4" name="description" placeholder="Podrobnosti..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                                required></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Soubor</label>
                            <input type="file" name="uploaded_file" accept="image/png,image/jpg,image/jpeg"/>
                        </div>

                        <button type="submit"
                            class="w-full bg-indigo-600 text-white font-bold py-2 rounded-lg hover:bg-indigo-700 transition-all shadow-md active:scale-95">
                            Vložit záznam
                        </button>
                    </form>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Záznamy</h2>

                    <div class="flex gap-3">
                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-medium">
                            Celkem: {{ count($entries) }}
                        </span>
                        @if($average > 0)
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            {{ $average <= 2
                                ? 'bg-green-100 text-green-700'
                                : 'bg-orange-100 text-orange-700' }}">
                            Průměrné hodnocení: {{ $average }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                    <!-- MOBILE CARDS -->
                    <div class="md:hidden divide-y divide-gray-200">
                        @forelse($entries as $entry)
                            <div class="p-4 space-y-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="text-sm text-gray-500">
                                            {{ $entry->created_at->format('d.m.Y') }}
                                        </div>
                                        <div class="font-semibold text-gray-900">
                                            {{ Crypt::decryptString($entry->title) }}
                                        </div>
                                    </div>

                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                        {{ Crypt::decryptString($entry->rating) <= 2
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-orange-100 text-orange-700' }}
                                        font-bold text-sm">
                                        {{ Crypt::decryptString($entry->rating) }}
                                    </span>
                                </div>

                                <div class="flex justify-end gap-4 pt-2 border-t border-gray-100">
                                    <button onclick="location.href='/entry/{{ $entry->id }}'"
                                        class="text-indigo-600 font-medium text-sm">
                                        Upravit
                                    </button>

                                    <form method="POST" action="/entry/{{ $entry->id }}">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 font-medium text-sm"
                                            onclick="return confirm('Opravdu smazat?')">
                                            Smazat
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-gray-500 italic">
                                Zatím zde nejsou žádné záznamy.
                            </div>
                        @endforelse
                    </div>

                    <!-- DESKTOP TABLE -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Datum</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Titulek</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500 text-center">Hodnocení</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500 text-right">Akce</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($entries as $entry)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $entry->created_at->format('d.m.Y') }}
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ Crypt::decryptString($entry->title) }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                                {{ Crypt::decryptString($entry->rating) <= 2
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-orange-100 text-orange-700' }}
                                                font-bold text-sm">
                                                {{ Crypt::decryptString($entry->rating) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button onclick="location.href='/entry/{{ $entry->id }}'"
                                                    class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                                    Upravit
                                                </button>

                                                <form method="POST" action="/entry/{{ $entry->id }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 font-medium text-sm"
                                                        onclick="return confirm('Opravdu smazat?')">
                                                        Smazat
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if(count($entries) == 0)
                            <div class="p-8 text-center text-gray-500 italic">
                                Zatím zde nejsou žádné záznamy.
                            </div>
                        @endif
                    </div>
                </div>
                <p>@if($entries->currentPage() > 1)<a class="text-left" href="/page/{{ $entries->currentPage() - 1 }}">Předchozí týden</a> @endif @if($entries->currentPage() < $entries->lastPage())<a class="text-right" href="/page/{{ $entries->currentPage() + 1 }}">Další týden</a>@endif</p>
            </div>

        </div>
    </main>

</body>
</html>