<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agrimarket - @yield('title', 'Accueil')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        window.tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 500: '#22c55e', 600: '#16a34a', 700: '#15803d', 900: '#14532d' },
                        dynamic: { orange: '#f97316', yellow: '#eab308' }
                    },
                    animation: {
                        'slide-up': 'slideUp 0.3s ease-out',
                        'fade-in': 'fadeIn 0.2s ease-out',
                    },
                    keyframes: {
                        slideUp: { '0%': { transform: 'translateY(100%)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } },
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                    }
                }
            }
        };

        // === CART GLOBAL FUNCTION ===
        function addToCart(id, type, name, price, quantity = 1) {
            if (window.Alpine && window.Alpine.store('cart')) {
                window.Alpine.store('cart').add({ id: parseInt(id), type, name, price: parseFloat(price), quantity: parseInt(quantity) });
                showToast('✅ ' + name + ' ajouté au panier !', 'success');
            }
        }

        // === TOAST SYSTEM ===
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container');
            if (!toastContainer) return;
            const toast = document.createElement('div');
            const colors = { success: 'bg-brand-600', error: 'bg-red-500', info: 'bg-gray-800', warning: 'bg-orange-500' };
            toast.className = `${colors[type] || colors.info} text-white px-5 py-3 rounded-2xl shadow-2xl font-bold text-sm flex items-center gap-3 transform transition-all duration-300 translate-y-4 opacity-0 max-w-sm`;
            toast.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" class="ml-auto text-white/70 hover:text-white text-lg leading-none">&times;</button>`;
            toastContainer.appendChild(toast);
            requestAnimationFrame(() => { toast.classList.remove('translate-y-4', 'opacity-0'); });
            setTimeout(() => {
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
        window.showToast = showToast;

        // === ALPINE CART STORE ===
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                items: Alpine.$persist([]).as('agri_cart'),
                
                add(item) {
                    const existing = this.items.find(i => i.id === item.id && i.type === item.type);
                    if (existing) {
                        existing.quantity += item.quantity || 1;
                    } else {
                        this.items.push({ ...item, quantity: item.quantity || 1 });
                    }
                    this.items = [...this.items]; // trigger reactivity
                    this.pulseCart();
                },
                
                remove(itemId, type) {
                    this.items = this.items.filter(i => !(i.id === itemId && i.type === type));
                },

                updateQty(itemId, type, qty) {
                    if (qty < 1) { this.remove(itemId, type); return; }
                    const item = this.items.find(i => i.id === itemId && i.type === type);
                    if (item) { item.quantity = qty; this.items = [...this.items]; }
                },

                clear() { this.items = []; },
                
                get count() { return this.items.reduce((total, item) => total + item.quantity, 0); },
                get total() { return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0); },

                pulseCart() {
                    const badge = document.getElementById('cart-badge');
                    if (badge) { badge.classList.remove('scale-125'); void badge.offsetWidth; badge.classList.add('scale-125'); setTimeout(() => badge.classList.remove('scale-125'), 300); }
                }
            });
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .glass-nav { background: rgba(255,255,255,0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-bottom: 1px solid rgba(0,0,0,0.06); }
        .bg-pattern { background-color: #f0fdf4; background-image: radial-gradient(#22c55e 0.5px, transparent 0.5px); background-size: 20px 20px; position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: -1; }
        .hover-lift { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.12); }
        #toast-container { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem; }
        .mobile-menu-open { overflow: hidden; }
        .badge-pulse { transition: transform 0.2s cubic-bezier(.36,.07,.19,.97); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased" x-data="{ mobileMenuOpen: false }">

<!-- Toast Container -->
<div id="toast-container"></div>

<!-- Navigation -->
<nav class="fixed w-full z-50 glass-nav" :class="{ 'shadow-sm': mobileMenuOpen }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-2.5 group flex-shrink-0">
                <div class="bg-gradient-to-tr from-brand-600 to-dynamic-yellow p-2 rounded-xl shadow-lg group-hover:shadow-brand-500/30 transition-shadow">
                    <i data-lucide="wheat" class="text-white w-6 h-6"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight text-gray-900">Agrimarket</span>
            </a>
            
            <!-- Desktop Nav -->
            <div class="hidden md:flex space-x-8 items-center">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-brand-600 font-medium transition-colors {{ request()->routeIs('home') ? 'text-brand-600 font-bold' : '' }}">Accueil</a>
                <a href="{{ route('catalog') }}" class="text-gray-600 hover:text-brand-600 font-medium transition-colors flex items-center gap-1.5 {{ request()->routeIs('catalog') ? 'text-brand-600 font-bold' : '' }}">
                    Catalogue
                    <span class="bg-brand-100 text-brand-700 px-2 py-0.5 rounded-full text-xs font-bold">Nouveau</span>
                </a>
                <a href="{{ route('catalog') }}?type=products" class="text-gray-600 hover:text-green-600 font-medium transition-colors">🌿 Produits</a>
                <a href="{{ route('catalog') }}?type=menus" class="text-gray-600 hover:text-orange-500 font-medium transition-colors">🍽️ Plats</a>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <!-- Cart -->
                <a href="{{ route('checkout') }}" class="relative p-2.5 text-gray-600 hover:text-brand-600 transition-colors rounded-xl hover:bg-brand-50">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    <span id="cart-badge"
                          class="badge-pulse absolute -top-1 -right-1 bg-dynamic-orange text-white text-[10px] font-black px-1.5 py-0.5 rounded-full border-2 border-white min-w-[20px] text-center hidden"
                          x-init="$watch('$store.cart.count', val => { $el.textContent = val; $el.classList.toggle('hidden', val === 0); })"
                          x-text="$store.cart.count">
                    </span>
                </a>

                @auth
                    <a href="{{ route('dashboard.index') }}" class="hidden md:flex bg-gray-900 text-white px-5 py-2.5 rounded-full font-semibold text-sm shadow-md items-center gap-2 hover:bg-gray-800 transition-colors">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Mon espace
                    </a>
                    <!-- User dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                            <div class="w-9 h-9 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center font-black text-sm uppercase">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                            <div class="px-4 py-2 border-b border-gray-50 mb-1">
                                <p class="text-sm font-black text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400 uppercase font-bold">{{ Auth::user()->user_type }}</p>
                            </div>
                            <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-brand-600 transition-colors">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Tableau de bord
                            </a>
                            @if(in_array(Auth::user()->user_type, ['producer', 'restaurant']))
                                <a href="{{ route('dashboard.settings') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-brand-600 transition-colors">
                                    <i data-lucide="settings" class="w-4 h-4"></i> Paramètres profil
                                </a>
                            @endif
                            <hr class="my-1 border-gray-50">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                    <i data-lucide="log-out" class="w-4 h-4"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-brand-600 font-bold text-sm px-3 py-2 hidden md:block">Connexion</a>
                    <a href="{{ route('register') }}" class="bg-gray-900 text-white px-5 py-2.5 rounded-full font-bold text-sm shadow-md hover:bg-gray-800 transition-colors">S'inscrire</a>
                @endauth

                <!-- Mobile hamburger -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors">
                    <i data-lucide="menu" class="w-6 h-6" x-show="!mobileMenuOpen"></i>
                    <i data-lucide="x" class="w-6 h-6" x-show="mobileMenuOpen" x-cloak></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
         class="md:hidden bg-white border-t border-gray-100 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 py-6 space-y-1">
            <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-brand-50 hover:text-brand-600 font-medium transition-colors">
                <i data-lucide="home" class="w-5 h-5"></i> Accueil
            </a>
            <a href="{{ route('catalog') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-brand-50 hover:text-brand-600 font-medium transition-colors">
                <i data-lucide="grid" class="w-5 h-5"></i> Catalogue Universel
            </a>
            <a href="{{ route('catalog') }}?type=products" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-brand-50 hover:text-brand-600 font-medium transition-colors">
                <i data-lucide="tractor" class="w-5 h-5"></i> Produits Agricoles
            </a>
            <a href="{{ route('catalog') }}?type=menus" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-orange-500 font-medium transition-colors">
                <i data-lucide="utensils" class="w-5 h-5"></i> Plats Préparés
            </a>
            <hr class="border-gray-100 my-2">
            @auth
                <a href="{{ route('dashboard.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gray-900 text-white font-bold transition-colors">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Mon Espace
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 font-medium transition-colors">
                        <i data-lucide="log-out" class="w-5 h-5"></i> Déconnexion
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    <i data-lucide="log-in" class="w-5 h-5"></i> Connexion
                </a>
                <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-brand-600 text-white font-bold transition-colors">
                    <i data-lucide="user-plus" class="w-5 h-5"></i> Créer un compte
                </a>
            @endauth
        </div>
    </div>
</nav>

<main class="min-h-screen pt-20">
    @if(session('success'))
        <script>document.addEventListener('DOMContentLoaded', () => showToast('✅ {{ addslashes(session('success')) }}', 'success'));</script>
    @endif
    @if(session('error'))
        <script>document.addEventListener('DOMContentLoaded', () => showToast('❌ {{ addslashes(session('error')) }}', 'error'));</script>
    @endif

    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-20 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            <!-- Brand -->
            <div class="lg:col-span-1">
                <div class="flex items-center gap-2.5 mb-5">
                    <div class="bg-gradient-to-tr from-brand-600 to-dynamic-yellow p-2 rounded-xl">
                        <i data-lucide="wheat" class="text-white w-5 h-5"></i>
                    </div>
                    <span class="text-xl font-extrabold">Agrimarket</span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed mb-5">La marketplace qui connecte producteurs locaux, restaurants et particuliers pour une alimentation saine et locale.</p>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 bg-gray-800 hover:bg-brand-600 rounded-xl flex items-center justify-center transition-colors text-gray-400 hover:text-white">
                        <i data-lucide="facebook" class="w-4 h-4"></i>
                    </a>
                    <a href="#" class="w-9 h-9 bg-gray-800 hover:bg-brand-600 rounded-xl flex items-center justify-center transition-colors text-gray-400 hover:text-white">
                        <i data-lucide="instagram" class="w-4 h-4"></i>
                    </a>
                    <a href="#" class="w-9 h-9 bg-gray-800 hover:bg-brand-600 rounded-xl flex items-center justify-center transition-colors text-gray-400 hover:text-white">
                        <i data-lucide="twitter" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Marketplace -->
            <div>
                <h4 class="font-black text-white mb-5 text-sm uppercase tracking-widest">Marketplace</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="{{ route('catalog') }}?type=products" class="hover:text-brand-400 transition-colors flex items-center gap-2"><i data-lucide="tractor" class="w-4 h-4"></i> Produits agricoles</a></li>
                    <li><a href="{{ route('catalog') }}?type=menus" class="hover:text-brand-400 transition-colors flex items-center gap-2"><i data-lucide="chef-hat" class="w-4 h-4"></i> Plats de restaurants</a></li>
                    <li><a href="{{ route('catalog') }}" class="hover:text-brand-400 transition-colors flex items-center gap-2"><i data-lucide="grid" class="w-4 h-4"></i> Tout le catalogue</a></li>
                    <li><a href="{{ route('checkout') }}" class="hover:text-brand-400 transition-colors flex items-center gap-2"><i data-lucide="shopping-cart" class="w-4 h-4"></i> Mon panier</a></li>
                </ul>
            </div>

            <!-- Rejoindre -->
            <div>
                <h4 class="font-black text-white mb-5 text-sm uppercase tracking-widest">Rejoignez-nous</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="{{ route('register') }}?type=producer" class="hover:text-brand-400 transition-colors flex items-center gap-2"><i data-lucide="leaf" class="w-4 h-4"></i> Je suis producteur</a></li>
                    <li><a href="{{ route('register') }}?type=restaurant" class="hover:text-brand-400 transition-colors flex items-center gap-2"><i data-lucide="utensils" class="w-4 h-4"></i> Je suis restaurateur</a></li>
                    <li><a href="{{ route('register') }}?type=customer" class="hover:text-brand-400 transition-colors flex items-center gap-2"><i data-lucide="user" class="w-4 h-4"></i> Je suis client</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-brand-400 transition-colors flex items-center gap-2"><i data-lucide="log-in" class="w-4 h-4"></i> Se connecter</a></li>
                </ul>
            </div>

            <!-- Paiement & Contact -->
            <div>
                <h4 class="font-black text-white mb-5 text-sm uppercase tracking-widest">Paiements acceptés</h4>
                <div class="grid grid-cols-2 gap-2 mb-6">
                    <div class="bg-gray-800 rounded-xl px-3 py-2 text-xs font-bold text-gray-300 flex items-center gap-1.5">
                        <i data-lucide="smartphone" class="w-3.5 h-3.5 text-yellow-400"></i> MTN MoMo
                    </div>
                    <div class="bg-gray-800 rounded-xl px-3 py-2 text-xs font-bold text-gray-300 flex items-center gap-1.5">
                        <i data-lucide="smartphone" class="w-3.5 h-3.5 text-orange-400"></i> Moov Money
                    </div>
                    <div class="bg-gray-800 rounded-xl px-3 py-2 text-xs font-bold text-gray-300 flex items-center gap-1.5">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5 text-blue-400"></i> FedaPay
                    </div>
                    <div class="bg-gray-800 rounded-xl px-3 py-2 text-xs font-bold text-gray-300 flex items-center gap-1.5">
                        <i data-lucide="banknote" class="w-3.5 h-3.5 text-green-400"></i> Cash livraison
                    </div>
                </div>
                <p class="text-xs text-gray-500">Livraison partenaire Gozem &amp; Yango disponible</p>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-gray-500">&copy; 2026 Agrimarket. Tous droits réservés. Bénin 🇧🇯</p>
            <div class="flex items-center gap-6 text-xs text-gray-500">
                <a href="#" class="hover:text-gray-300 transition-colors">Confidentialité</a>
                <a href="#" class="hover:text-gray-300 transition-colors">CGU</a>
                <a href="#" class="hover:text-gray-300 transition-colors">Aide</a>
            </div>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        // Show initial cart count
        document.addEventListener('alpine:initialized', () => {
            const count = Alpine.store('cart')?.count || 0;
            const badge = document.getElementById('cart-badge');
            if (badge && count > 0) { badge.textContent = count; badge.classList.remove('hidden'); }
        });
    });
</script>

@stack('scripts')
</body>
</html>
