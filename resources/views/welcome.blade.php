@extends('layouts.app')

@section('title', 'La Marketplace Agricole & Restaurant')

@section('content')
<div class="relative overflow-hidden">
    <!-- Décoration de fond -->
    <div class="bg-pattern opacity-50"></div>
    <div class="absolute inset-x-0 top-0 h-96 bg-gradient-to-b from-brand-50 to-transparent z-[-1]"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-32">
        <!-- Hero Section -->
        <div class="text-center max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 mb-8 tracking-tight leading-tight">
                Connectez <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-dynamic-yellow">Producteurs</span> et <span class="text-dynamic-orange">Gourmands</span> sans intermédiaire.
            </h1>
            <p class="text-lg md:text-xl text-gray-600 mb-10 leading-relaxed font-light">
                Achetez vos légumes directement à la ferme, ou commandez les plats préparés par les meilleurs restaurants locaux. Agrimarket unifie toute la chaîne alimentaire sur une seule plateforme puissante.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="/catalog?type=products" class="bg-brand-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-brand-700 hover:-translate-y-1 transition-all shadow-lg shadow-brand-500/30 flex items-center justify-center gap-2">
                    <i data-lucide="tractor" class="w-5 h-5"></i>
                    Acheter aux producteurs
                </a>
                <a href="/catalog?type=menus" class="bg-white text-gray-900 border border-gray-200 px-8 py-4 rounded-xl font-bold text-lg hover:border-dynamic-orange hover:text-dynamic-orange hover:-translate-y-1 transition-all shadow-sm flex items-center justify-center gap-2">
                    <i data-lucide="chef-hat" class="w-5 h-5"></i>
                    Commander un plat
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Nouveautés -->
<div class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Les pépites de nos fermes</h2>
                <p class="text-gray-500">Produits frais et bio, récoltés aujourd'hui.</p>
            </div>
            <a href="/catalog?type=products" class="hidden sm:flex text-brand-600 font-semibold items-center gap-1 hover:text-brand-800 transition">
                Tout voir <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($featuredProducts as $item)
            <div class="group bg-white border border-gray-100 rounded-3xl overflow-hidden hover-lift p-4 flex flex-col h-full bg-gradient-to-b from-white to-gray-50/50">
                <div class="aspect-square bg-brand-50 rounded-2xl mb-4 flex items-center justify-center text-brand-200 group-hover:scale-105 transition-transform duration-500 relative">
                    <i data-lucide="apple" class="w-20 h-20 opacity-50"></i>
                    @if($item->is_organic)
                        <span class="absolute top-3 right-3 bg-brand-100 text-brand-700 text-xs font-bold px-2 py-1 rounded-full">BIO</span>
                    @endif
                </div>
                <div class="flex-grow">
                    <p class="text-xs font-bold text-gray-400 mb-1 uppercase tracking-wider">{{ $item->category->name ?? 'Catégorie' }}</p>
                    <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1">{{ $item->name }}</h3>
                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $item->description }}</p>
                </div>
                <div class="mt-auto flex justify-between items-end pt-4 border-t border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500">Par {{ $item->producer->farm_name ?? 'Ferme Anonyme' }}</p>
                        <p class="text-xl font-black text-brand-600">{{ number_format($item->price, 0, ',', ' ') }} FCFA <span class="text-xs font-normal text-gray-500">/ {{ $item->unit }}</span></p>
                    </div>
                    <button 
                        x-data
                        @click="$store.cart.add({ id: {{ $item->id }}, type: 'product', name: '{{ addslashes($item->name) }}', price: {{ $item->price }} })"
                        class="bg-gray-100 p-2 rounded-full text-gray-900 hover:bg-brand-600 hover:text-white transition-colors">
                        <i data-lucide="plus" class="w-5 h-5 pointer-events-none"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="bg-gray-50 py-24 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Les meilleurs plats préparés</h2>
                <p class="text-gray-500">Cuisinés avec amour par nos restaurants partenaires.</p>
            </div>
            <a href="/catalog?type=menus" class="hidden sm:flex text-dynamic-orange font-semibold items-center gap-1 hover:text-orange-700 transition">
                Tout goûter <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($featuredMenus as $item)
            <div class="group bg-white border border-gray-100 rounded-3xl overflow-hidden hover-lift p-4 flex flex-col h-full">
                <div class="aspect-square bg-orange-50 rounded-2xl mb-4 flex items-center justify-center text-orange-200 group-hover:scale-105 transition-transform duration-500">
                    <i data-lucide="utensils" class="w-20 h-20 opacity-50"></i>
                </div>
                <div class="flex-grow">
                    <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1">{{ $item->name }}</h3>
                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $item->description }}</p>
                </div>
                <div class="mt-auto flex justify-between items-end pt-4 border-t border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500">Par {{ $item->menu->restaurant->name ?? 'Restaurant' }}</p>
                        <p class="text-xl font-black text-dynamic-orange">{{ number_format($item->price, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <button 
                        x-data
                        @click="$store.cart.add({ id: {{ $item->id }}, type: 'menu_item', name: '{{ addslashes($item->name) }}', price: {{ $item->price }} })"
                        class="bg-gray-100 p-2 rounded-full text-gray-900 hover:bg-dynamic-orange hover:text-white transition-colors">
                        <i data-lucide="plus" class="w-5 h-5 pointer-events-none"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
