<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agrimarket - @yield('title', 'Accueil')</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Scripts Core (ORDRE CRITIQUE) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        // Configuration Tailwind
        window.tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 600: '#16a34a', 700: '#15803d', 900: '#14532d' },
                        dynamic: { orange: '#f97316', yellow: '#eab308' }
                    }
                }
            }
        };

        // Fonction Globale pour le Panier (évite les erreurs JSON dans le HTML)
        function addToCart(id, type, name, price, quantity = 1) {
            if (window.Alpine && window.Alpine.store('cart')) {
                window.Alpine.store('cart').add({
                    id: parseInt(id),
                    type: type,
                    name: name,
                    price: parseFloat(price),
                    quantity: parseInt(quantity)
                });
            } else {
                console.error('Alpine Cart Store not initialized');
            }
        }

        // Initialisation du Store
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
                    this.pulseCart();
                },
                
                remove(itemId, type) {
                    this.items = this.items.filter(i => !(i.id === itemId && i.type === type));
                },
                
                get count() {
                    return this.items.reduce((total, item) => total + item.quantity, 0);
                },

                get total() {
                    return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                pulseCart() {
                    const badge = document.getElementById('cart-badge');
                    if(badge) {
                        badge.classList.remove('animate-bounce');
                        void badge.offsetWidth;
                        badge.classList.add('animate-bounce');
                    }
                }
            });
        });
    </script>

    <style>
        .glass-nav { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.3); }
        .hover-lift { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .bg-pattern { background-color: #f0fdf4; background-image: radial-gradient(#22c55e 0.5px, transparent 0.5px); background-size: 20px 20px; opacity: 0.8; position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: -1; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="/" class="flex items-center gap-2 group">
                    <div class="bg-gradient-to-tr from-brand-600 to-dynamic-yellow p-2 rounded-xl shadow-lg">
                        <i data-lucide="wheat" class="text-white w-6 h-6"></i>
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight text-gray-900">Agrimarket</span>
                </a>
                
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="/" class="text-gray-600 hover:text-brand-600 font-medium">Accueil</a>
                    <a href="/catalog" class="text-gray-600 hover:text-brand-600 font-medium flex items-center gap-1">
                        Catalogue Universel
                        <span class="bg-brand-100 text-brand-700 px-2 py-0.5 rounded-full text-xs font-bold">Live</span>
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    <a href="/checkout" class="relative p-2 text-gray-600 hover:text-brand-600 transition-colors group">
                        <i data-lucide="shopping-cart"></i>
                        <template x-if="$store.cart">
                            <span id="cart-badge" 
                                  class="absolute top-0 right-0 -mt-2 -mr-2 bg-dynamic-orange text-white text-[11px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white"
                                  x-show="$store.cart.count > 0"
                                  x-text="$store.cart.count">
                            </span>
                        </template>
                    </a>
                    
                    @auth
                        <div class="flex items-center gap-4">
                            @if(Auth::user()->user_type !== 'customer')
                                <a href="/dashboard" class="hidden md:flex bg-gray-900 text-white px-6 py-2.5 rounded-full font-semibold shadow-md items-center gap-2">
                                    Tableau de Bord <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                                </a>
                            @endif
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                                    <div class="w-10 h-10 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center font-bold">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-600 hover:bg-brand-50">Profil</a>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50">Déconnexion</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-brand-600 font-bold text-sm px-4 py-2">Connexion</a>
                            <a href="{{ route('register') }}" class="bg-gray-900 text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-md">S'inscrire</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="min-h-screen pt-20">
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-white py-16 mt-20 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex justify-center items-center gap-2 mb-6">
                <i data-lucide="wheat" class="text-brand-500 w-6 h-6"></i>
                <span class="text-2xl font-bold text-white">Agrimarket</span>
            </div>
            <p class="text-sm border-t border-gray-800 pt-8 text-gray-500">&copy; 2026 Agrimarket Foundation. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    
    @stack('scripts')
</body>
</html>
