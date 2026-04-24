<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Agrimarket – @yield('title', 'Tableau de bord')</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 500: '#22c55e', 600: '#16a34a', 700: '#15803d' },
                        dynamic: { orange: '#f97316' }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm font-semibold; }
        .sidebar-link.active { @apply bg-brand-600 text-white shadow-lg; }
        .sidebar-link:not(.active) { @apply text-gray-400 hover:bg-gray-800 hover:text-white; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/60 z-40 md:hidden backdrop-blur-sm"></div>

    <div class="flex min-h-screen">
        <!-- ===== SIDEBAR ===== -->
        <aside class="fixed md:relative w-72 md:w-64 bg-gray-900 text-white flex-shrink-0 flex flex-col z-50 h-full md:h-auto min-h-screen transition-transform duration-300 ease-in-out"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

            <!-- Logo -->
            <div class="p-6 border-b border-gray-800 flex items-center justify-between">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="bg-brand-600 p-1.5 rounded-lg group-hover:bg-brand-500 transition-colors">
                        <i data-lucide="wheat" class="w-5 h-5 text-white"></i>
                    </div>
                    <span class="text-xl font-black text-white">Agrimarket</span>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- User info -->
            <div class="px-4 py-4 border-b border-gray-800">
                <div class="flex items-center gap-3 px-2">
                    <div class="w-10 h-10 bg-brand-600 text-white rounded-full flex items-center justify-center font-black text-base uppercase flex-shrink-0">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ Auth::user()->user_type }}</p>
                    </div>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-grow p-4 space-y-1 overflow-y-auto mt-2">

                <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest px-4 mb-2">Navigation</p>

                <a href="{{ route('dashboard.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm font-semibold {{ request()->routeIs('dashboard.index') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Tableau de bord</span>
                </a>

                @if(in_array(Auth::user()->user_type, ['producer', 'restaurant']))
                    <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest px-4 pt-4 mb-2">Mon activité</p>

                    <a href="{{ route('dashboard.products') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm font-semibold {{ request()->routeIs('dashboard.products*') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i data-lucide="package" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Mon Catalogue</span>
                    </a>

                    <a href="{{ route('dashboard.orders') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm font-semibold {{ request()->routeIs('dashboard.orders*') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i data-lucide="clipboard-list" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Commandes Reçues</span>
                    </a>

                    <a href="{{ route('dashboard.settings') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm font-semibold {{ request()->routeIs('dashboard.settings') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i data-lucide="settings" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Profil & Paramètres</span>
                    </a>
                @endif

                @if(Auth::user()->user_type === 'customer')
                    <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest px-4 pt-4 mb-2">Mes achats</p>
                    <a href="{{ route('dashboard.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition-all text-sm font-semibold">
                        <i data-lucide="shopping-bag" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Mes Commandes</span>
                    </a>
                    <a href="{{ route('catalog') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition-all text-sm font-semibold">
                        <i data-lucide="grid" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Catalogue</span>
                    </a>
                @endif

                @if(Auth::user()->user_type === 'delivery_agent')
                    <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest px-4 pt-4 mb-2">Livraisons</p>
                    <a href="{{ route('dashboard.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('dashboard.index') ? 'bg-brand-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-all text-sm font-semibold">
                        <i data-lucide="truck" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Mes Tournées</span>
                    </a>
                @endif

                @if(Auth::user()->user_type === 'admin')
                    <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest px-4 pt-4 mb-2">Administration</p>
                    <a href="{{ route('dashboard.admin.users') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm font-semibold {{ request()->routeIs('dashboard.admin.users') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i data-lucide="users" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Utilisateurs</span>
                    </a>
                    <a href="{{ route('dashboard.orders') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm font-semibold {{ request()->routeIs('dashboard.orders*') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i data-lucide="shield-check" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Toutes Commandes</span>
                    </a>
                @endif

                <!-- Back to site -->
                <div class="pt-4 mt-4 border-t border-gray-800">
                    <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition-all text-sm font-semibold">
                        <i data-lucide="home" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Retour au site</span>
                    </a>
                </div>
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-gray-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full rounded-xl text-red-400 hover:bg-red-400/10 hover:text-red-300 transition-all text-sm font-semibold">
                        <i data-lucide="log-out" class="w-5 h-5 flex-shrink-0"></i>
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>

        <!-- ===== MAIN ===== -->
        <div class="flex-grow flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="sticky top-0 z-30 h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <!-- Mobile hamburger -->
                    <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition-colors">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <h1 class="text-lg font-bold text-gray-900">@yield('page_title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Quick add button for sellers -->
                    @if(in_array(Auth::user()->user_type, ['producer', 'restaurant']))
                        <a href="{{ route('dashboard.products.create') }}"
                           class="hidden sm:flex items-center gap-2 bg-brand-600 text-white px-4 py-2 rounded-xl font-bold text-sm hover:bg-brand-700 transition-colors shadow-sm">
                            <i data-lucide="plus" class="w-4 h-4"></i> Nouveau
                        </a>
                    @endif

                    <!-- User avatar -->
                    <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2 border border-gray-100">
                        <div class="w-7 h-7 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center font-black text-xs uppercase">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-bold text-gray-700 hidden sm:block">{{ explode(' ', Auth::user()->name)[0] }}</span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-grow p-4 sm:p-6 lg:p-8">
                <!-- Flash messages -->
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl mb-6 font-medium text-sm flex items-center gap-3 shadow-sm">
                        <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0 text-green-500"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl mb-6 font-medium text-sm flex items-center gap-3 shadow-sm">
                        <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 text-red-500"></i>
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl mb-6 text-sm shadow-sm">
                        <div class="flex items-center gap-2 font-bold mb-2">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i> Erreurs à corriger :
                        </div>
                        <ul class="list-disc list-inside space-y-1">
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
    @stack('scripts')
</body>
</html>
