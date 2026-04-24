@extends('layouts.app')

@section('title', 'La Marketplace Agricole & Restaurant du Bénin')

@section('content')
<!-- ===== HERO ===== -->
<div class="relative overflow-hidden">
    <div class="bg-pattern opacity-40"></div>
    <div class="absolute inset-x-0 top-0 h-[600px] bg-gradient-to-b from-brand-50/80 to-transparent z-[-1]"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-28 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 bg-white border border-brand-200 text-brand-700 px-4 py-2 rounded-full text-sm font-bold mb-8 shadow-sm">
                <span class="w-2 h-2 bg-brand-500 rounded-full animate-pulse"></span>
                Plateforme #1 Bénin · Produits frais & Livraison
            </div>

            <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 mb-8 tracking-tight leading-[1.05]">
                Du champ <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-dynamic-yellow">à votre table</span>,<br>
                <span class="text-dynamic-orange">directement.</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-500 mb-10 leading-relaxed font-light max-w-2xl mx-auto">
                Achetez vos légumes, fruits et viandes directement aux producteurs. 
                Commandez vos plats préférés et faites-les livrer à domicile.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="/catalog?type=products"
                   class="bg-brand-600 text-white px-8 py-4 rounded-2xl font-bold text-base hover:bg-brand-700 shadow-xl shadow-brand-500/30 flex items-center justify-center gap-2 transition-all hover:-translate-y-1">
                    <i data-lucide="tractor" class="w-5 h-5"></i>
                    Acheter aux producteurs
                </a>
                <a href="/catalog?type=menus"
                   class="bg-white text-gray-900 border border-gray-200 px-8 py-4 rounded-2xl font-bold text-base hover:border-dynamic-orange hover:text-dynamic-orange flex items-center justify-center gap-2 transition-all hover:-translate-y-1 shadow-sm">
                    <i data-lucide="chef-hat" class="w-5 h-5"></i>
                    Commander un plat
                </a>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-6">
            @php
                $stats = [
                    ['icon' => 'tractor', 'value' => '200+', 'label' => 'Producteurs', 'color' => 'text-brand-600'],
                    ['icon' => 'chef-hat', 'value' => '85+', 'label' => 'Restaurants', 'color' => 'text-dynamic-orange'],
                    ['icon' => 'users', 'value' => '5 000+', 'label' => 'Clients actifs', 'color' => 'text-blue-600'],
                    ['icon' => 'truck', 'value' => '1 200+', 'label' => 'Livraisons/mois', 'color' => 'text-purple-600'],
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="bg-white rounded-2xl p-5 text-center shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <i data-lucide="{{ $stat['icon'] }}" class="w-6 h-6 {{ $stat['color'] }} mx-auto mb-2"></i>
                <p class="text-2xl font-black text-gray-900">{{ $stat['value'] }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $stat['label'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ===== FEATURED PRODUCTS ===== -->
<div class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <span class="text-xs font-black text-brand-600 uppercase tracking-widest mb-2 block">🌿 Producteurs locaux</span>
                <h2 class="text-3xl font-black text-gray-900 mb-1">Pépites de nos fermes</h2>
                <p class="text-gray-400 font-medium">Frais, naturel, récolté pour vous.</p>
            </div>
            <a href="/catalog?type=products" class="hidden sm:flex bg-brand-50 text-brand-700 font-bold px-5 py-2.5 rounded-xl items-center gap-2 hover:bg-brand-100 transition-colors text-sm">
                Tout voir <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        @if($featuredProducts->isEmpty())
            <div class="text-center py-16 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                <i data-lucide="package-search" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                <p class="text-gray-400 font-medium">Aucun produit disponible pour le moment.</p>
                @auth
                    @if(Auth::user()->user_type === 'producer')
                        <a href="{{ route('dashboard.products.create') }}" class="mt-4 inline-block bg-brand-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-brand-700 transition">Ajouter mon premier produit</a>
                    @endif
                @endauth
            </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $item)
                <div class="group bg-white border border-gray-100 rounded-3xl p-4 flex flex-col h-full shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <a href="{{ route('item.show', ['type' => 'product', 'id' => $item->id]) }}" class="block mb-4">
                        <div class="aspect-square bg-brand-50 rounded-2xl flex items-center justify-center overflow-hidden relative">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <i data-lucide="apple" class="w-16 h-16 text-brand-200"></i>
                            @endif
                            @if($item->is_organic ?? false)
                                <span class="absolute top-3 left-3 bg-brand-600 text-white text-[10px] font-black px-2 py-1 rounded-lg">BIO</span>
                            @endif
                        </div>
                    </a>
                    <div class="flex-grow">
                        <p class="text-[10px] font-black text-gray-400 mb-1 uppercase tracking-widest">{{ $item->category->name ?? 'Catégorie' }}</p>
                        <h3 class="text-base font-bold text-gray-900 mb-1 truncate">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3 h-3 text-orange-400 flex-shrink-0"></i>
                            <span class="truncate">{{ $item->producer->location ?? 'Bénin' }}</span>
                        </p>
                    </div>
                    <div class="mt-auto flex justify-between items-center pt-4 border-t border-gray-50">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold">{{ $item->producer->farm_name ?? 'Ferme' }}</p>
                            <p class="text-lg font-black text-brand-600">{{ number_format($item->price, 0, ',', ' ') }} <span class="text-xs font-bold">FCFA</span></p>
                        </div>
                        <button onclick="addToCart('{{ $item->id }}', 'product', '{{ addslashes($item->name) }}', '{{ $item->price }}')"
                            class="w-10 h-10 bg-brand-50 text-brand-700 rounded-xl flex items-center justify-center hover:bg-brand-600 hover:text-white transition-all shadow-sm">
                            <i data-lucide="plus" class="w-5 h-5 pointer-events-none"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-8 sm:hidden">
            <a href="/catalog?type=products" class="inline-flex items-center gap-2 text-brand-600 font-bold">Voir tous les produits <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
        </div>
        @endif
    </div>
</div>

<!-- ===== HOW IT WORKS ===== -->
<div class="bg-gray-50 py-24 border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-xs font-black text-dynamic-orange uppercase tracking-widest mb-2 block">Simple & rapide</span>
            <h2 class="text-3xl font-black text-gray-900 mb-3">Comment ça marche ?</h2>
            <p class="text-gray-400 max-w-xl mx-auto">Commander sur Agrimarket est aussi simple que d'appeler un ami.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
                $steps = [
                    ['num' => '01', 'icon' => 'search', 'title' => 'Parcourez le catalogue', 'desc' => 'Explorez les produits frais de nos fermes partenaires ou les plats de vos restaurants favoris.', 'color' => 'brand'],
                    ['num' => '02', 'icon' => 'shopping-basket', 'title' => 'Composez votre panier', 'desc' => 'Ajoutez plusieurs articles de différents vendeurs en un seul panier. Choisissez vos quantités.', 'color' => 'orange'],
                    ['num' => '03', 'icon' => 'truck', 'title' => 'Recevez chez vous', 'desc' => 'Payez par Mobile Money, FedaPay ou à la livraison. Votre commande arrive sous 24–48h.', 'color' => 'blue'],
                ];
            @endphp
            @foreach($steps as $step)
            <div class="relative bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="text-6xl font-black text-gray-100 absolute top-6 right-6">{{ $step['num'] }}</div>
                <div class="w-14 h-14 bg-{{ $step['color'] === 'brand' ? 'brand-50' : ($step['color'] === 'orange' ? 'orange-50' : 'blue-50') }} rounded-2xl flex items-center justify-center mb-5">
                    <i data-lucide="{{ $step['icon'] }}" class="w-7 h-7 {{ $step['color'] === 'brand' ? 'text-brand-600' : ($step['color'] === 'orange' ? 'text-orange-500' : 'text-blue-600') }}"></i>
                </div>
                <h3 class="text-lg font-black text-gray-900 mb-2">{{ $step['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ===== FEATURED MENUS ===== -->
<div class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <span class="text-xs font-black text-dynamic-orange uppercase tracking-widest mb-2 block">🍽️ Restaurants partenaires</span>
                <h2 class="text-3xl font-black text-gray-900 mb-1">Plats du moment</h2>
                <p class="text-gray-400 font-medium">Cuisinés par nos meilleurs restaurants.</p>
            </div>
            <a href="/catalog?type=menus" class="hidden sm:flex bg-orange-50 text-orange-600 font-bold px-5 py-2.5 rounded-xl items-center gap-2 hover:bg-orange-100 transition-colors text-sm">
                Tout goûter <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        @if($featuredMenus->isEmpty())
            <div class="text-center py-16 bg-orange-50/50 rounded-3xl border border-dashed border-orange-200">
                <i data-lucide="utensils" class="w-12 h-12 text-orange-200 mx-auto mb-3"></i>
                <p class="text-orange-400 font-medium">Aucun plat disponible pour le moment.</p>
                @auth
                    @if(Auth::user()->user_type === 'restaurant')
                        <a href="{{ route('dashboard.products.create') }}" class="mt-4 inline-block bg-dynamic-orange text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-orange-600 transition">Ajouter mon premier plat</a>
                    @endif
                @endauth
            </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredMenus as $item)
                <div class="group bg-white border border-gray-100 rounded-3xl p-4 flex flex-col h-full shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <a href="{{ route('item.show', ['type' => 'menu_item', 'id' => $item->id]) }}" class="block mb-4">
                        <div class="aspect-square bg-orange-50 rounded-2xl flex items-center justify-center overflow-hidden">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <i data-lucide="utensils" class="w-16 h-16 text-orange-200"></i>
                            @endif
                        </div>
                    </a>
                    <div class="flex-grow">
                        <p class="text-[10px] font-black text-gray-400 mb-1 uppercase tracking-widest">{{ $item->category->name ?? 'Plat' }}</p>
                        <h3 class="text-base font-bold text-gray-900 mb-1 truncate">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3 h-3 text-orange-400 flex-shrink-0"></i>
                            <span class="truncate">{{ $item->menu->restaurant->location ?? 'Cotonou' }}</span>
                        </p>
                    </div>
                    <div class="mt-auto flex justify-between items-center pt-4 border-t border-gray-50">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold truncate max-w-[100px]">{{ $item->menu->restaurant->name ?? 'Restaurant' }}</p>
                            <p class="text-lg font-black text-dynamic-orange">{{ number_format($item->price, 0, ',', ' ') }} <span class="text-xs font-bold">FCFA</span></p>
                        </div>
                        <button onclick="addToCart('{{ $item->id }}', 'menu_item', '{{ addslashes($item->name) }}', '{{ $item->price }}')"
                            class="w-10 h-10 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all shadow-sm">
                            <i data-lucide="plus" class="w-5 h-5 pointer-events-none"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- ===== CTA VENDEURS ===== -->
<div class="bg-gray-900 py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Producteurs -->
            <div class="bg-gradient-to-br from-brand-600 to-brand-700 rounded-3xl p-10 relative overflow-hidden">
                <i data-lucide="tractor" class="absolute -bottom-8 -right-8 w-40 h-40 text-white/10"></i>
                <div class="relative z-10">
                    <span class="inline-block bg-white/20 text-white text-xs font-black px-3 py-1 rounded-full mb-5 uppercase tracking-widest">Pour les producteurs</span>
                    <h3 class="text-2xl font-black text-white mb-3">Vendez vos récoltes directement</h3>
                    <p class="text-brand-100 text-sm leading-relaxed mb-6">Inscrivez-vous gratuitement, publiez vos produits et recevez vos paiements directement sur votre Mobile Money.</p>
                    <ul class="space-y-2 mb-8 text-sm text-brand-100">
                        <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-white flex-shrink-0"></i> Inscription gratuite</li>
                        <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-white flex-shrink-0"></i> Paiement sécurisé Mobile Money</li>
                        <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-white flex-shrink-0"></i> Gestion stock & commandes intégrée</li>
                    </ul>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-brand-700 font-black px-6 py-3 rounded-xl shadow-xl hover:shadow-2xl hover:-translate-y-0.5 transition-all text-sm">
                        Devenir producteur <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
            <!-- Restaurants -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-3xl p-10 relative overflow-hidden">
                <i data-lucide="chef-hat" class="absolute -bottom-8 -right-8 w-40 h-40 text-white/10"></i>
                <div class="relative z-10">
                    <span class="inline-block bg-white/20 text-white text-xs font-black px-3 py-1 rounded-full mb-5 uppercase tracking-widest">Pour les restaurants</span>
                    <h3 class="text-2xl font-black text-white mb-3">Développez votre clientèle</h3>
                    <p class="text-orange-100 text-sm leading-relaxed mb-6">Publiez votre menu en ligne, recevez des commandes B2B (producteurs) et B2C (particuliers) depuis une seule plateforme.</p>
                    <ul class="space-y-2 mb-8 text-sm text-orange-100">
                        <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-white flex-shrink-0"></i> Menu digital en ligne 24h/24</li>
                        <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-white flex-shrink-0"></i> Commandes B2B & B2C centralisées</li>
                        <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-white flex-shrink-0"></i> Système de livraison partenaire</li>
                    </ul>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-orange-600 font-black px-6 py-3 rounded-xl shadow-xl hover:shadow-2xl hover:-translate-y-0.5 transition-all text-sm">
                        Référencer mon restaurant <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== PAYMENT METHODS ===== -->
<div class="bg-white py-16 border-t border-gray-100">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Paiements acceptés & Partenaires livraison</p>
        <div class="flex flex-wrap justify-center gap-4">
            @foreach(['MTN Mobile Money', 'Moov Money', 'FedaPay', 'Cash à la livraison', 'Gozem', 'Yango'] as $method)
            <div class="bg-gray-50 border border-gray-200 rounded-xl px-5 py-2.5 text-sm font-bold text-gray-600">{{ $method }}</div>
            @endforeach
        </div>
    </div>
</div>
@endsection
