<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - @yield('title', 'Vendeur')</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 600: '#16a34a', 700: '#15803d' },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="p-6 border-b border-gray-800 flex items-center gap-3">
                <div class="bg-brand-600 p-1.5 rounded-lg"><i data-lucide="wheat" class="w-5 h-5"></i></div>
                <span class="text-xl font-black">Agrimarket</span>
            </div>
            <nav class="flex-grow p-4 space-y-2 mt-4">
                <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('dashboard.index') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>

                @if(in_array(Auth::user()->user_type, ['producer', 'restaurant']))
                    <a href="{{ route('dashboard.products') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('dashboard.products*') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i data-lucide="package" class="w-5 h-5"></i> Mon Catalogue
                    </a>
                    <a href="{{ route('dashboard.orders') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('dashboard.orders') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i data-lucide="clipboard-list" class="w-5 h-5"></i> Commandes Reçues
                    </a>
                    <a href="{{ route('dashboard.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('dashboard.settings') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i data-lucide="settings" class="w-5 h-5"></i> Paramètres
                    </a>
                @endif

                @if(Auth::user()->user_type === 'delivery_agent')
                    <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-gray-400 hover:bg-gray-800 hover:text-white">
                        <i data-lucide="truck" class="w-5 h-5"></i> Mes Tournées
                    </a>
                @endif

                @if(Auth::user()->user_type === 'customer')
                    <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-gray-400 hover:bg-gray-800 hover:text-white">
                        <i data-lucide="shopping-bag" class="w-5 h-5"></i> Mes Achats
                    </a>
                @endif

                @if(Auth::user()->user_type === 'admin')
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition">
                        <i data-lucide="users" class="w-5 h-5"></i> Utilisateurs
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition">
                        <i data-lucide="shield-check" class="w-5 h-5"></i> Validations
                    </a>
                @endif

                <div class="pt-4 mt-4 border-t border-gray-800">
                    <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition">
                        <i data-lucide="home" class="w-5 h-5"></i> Retour au site
                    </a>
                </div>
            </nav>
            <div class="p-4 border-t border-gray-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full rounded-xl text-red-400 hover:bg-red-400/10 transition">
                        <i data-lucide="log-out" class="w-5 h-5"></i> Déconnexion
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex-grow flex flex-col">
            <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-8">
                <h1 class="text-xl font-bold text-gray-900">@yield('page_title')</h1>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs font-medium text-gray-500 uppercase">{{ Auth::user()->user_type }}</p>
                    </div>
                    <div class="w-10 h-10 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </header>
            <main class="p-8">
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-xl mb-6 font-medium text-sm flex items-center gap-3 shadow-sm">
                        <i data-lucide="check-circle" class="w-5 h-5"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 font-medium text-sm flex items-center gap-3 shadow-sm">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i> {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 font-medium text-sm shadow-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
