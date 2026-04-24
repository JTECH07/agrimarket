@extends('layouts.app')

@section('title', 'Catalogue Universel')

@section('content')
<div class="bg-white min-h-screen" x-data="{
    search: '',
    get filtered() {
        if (!this.search.trim()) return true;
        return true; // Server-side search handles this
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">Catalogue Universel</h1>
            <p class="text-gray-400 font-light max-w-2xl">Produits frais de nos fermes et plats préparés par nos restaurants partenaires.</p>
        </div>

        <!-- Search & Filters Bar -->
        <div class="flex flex-col md:flex-row gap-4 mb-8">
            <!-- Search -->
            <form method="GET" action="{{ route('catalog') }}" class="flex-grow flex gap-3">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="relative flex-grow">
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Chercher un produit, un plat, un vendeur..."
                           class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all font-medium text-gray-700 placeholder-gray-400">
                </div>
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-colors flex-shrink-0 flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Rechercher</span>
                </button>
            </form>

            <!-- Type Filters -->
            <div class="flex bg-gray-100 p-1 rounded-2xl gap-1 flex-shrink-0">
                <a href="{{ route('catalog') }}?type=all{{ request('search') ? '&search='.urlencode(request('search')) : '' }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $type === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">
                    Tout
                </a>
                <a href="{{ route('catalog') }}?type=products{{ request('search') ? '&search='.urlencode(request('search')) : '' }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-1.5 {{ $type === 'products' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-brand-600' }}">
                    <i data-lucide="tractor" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Produits</span>
                </a>
                <a href="{{ route('catalog') }}?type=menus{{ request('search') ? '&search='.urlencode(request('search')) : '' }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-1.5 {{ $type === 'menus' ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-orange-500' }}">
                    <i data-lucide="utensils" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Plats</span>
                </a>
            </div>
        </div>

        <!-- Search result info -->
        @if(request('search'))
        <div class="mb-6 flex items-center gap-3">
            <p class="text-sm text-gray-500 font-medium">
                {{ $items->count() }} résultat(s) pour "<span class="font-bold text-gray-900">{{ request('search') }}</span>"
            </p>
            <a href="{{ route('catalog') }}?type={{ $type }}" class="text-xs bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-500 px-3 py-1 rounded-full font-bold transition-colors flex items-center gap-1">
                <i data-lucide="x" class="w-3 h-3"></i> Effacer
            </a>
        </div>
        @endif

        <!-- Grid -->
        @if($items->isEmpty())
        <div class="text-center py-24 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
            <i data-lucide="search" class="w-14 h-14 text-gray-300 mx-auto mb-4"></i>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Aucun article trouvé</h3>
            <p class="text-gray-400 mb-6">{{ request('search') ? 'Essayez un autre terme de recherche.' : 'Revenez bientôt, de nouveaux produits arrivent.' }}</p>
            <a href="{{ route('catalog') }}" class="inline-flex bg-brand-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-brand-700 transition">Voir tout le catalogue</a>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($items as $item)
                @php
                    $isProduct = ($item instanceof \App\Models\Product);
                    $seller = $isProduct ? $item->producer : $item->menu?->restaurant;
                    $sellerName = $isProduct ? ($seller->farm_name ?? 'Ferme') : ($seller->name ?? 'Restaurant');
                    $location = $seller->location ?? 'Bénin';
                    $typeKey = $isProduct ? 'product' : 'menu_item';
                    $imageUrl = $item->image ? asset('storage/' . $item->image) : null;
                @endphp

                <div class="group bg-white border border-gray-100 rounded-3xl p-4 flex flex-col h-full shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <a href="{{ route('item.show', ['type' => $typeKey, 'id' => $item->id]) }}" class="block mb-4">
                        <div class="aspect-square rounded-2xl flex items-center justify-center overflow-hidden relative
                            {{ $isProduct ? 'bg-brand-50' : 'bg-orange-50' }}">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                @if($isProduct)
                                    <i data-lucide="apple" class="w-16 h-16 text-brand-200"></i>
                                @else
                                    <i data-lucide="utensils" class="w-16 h-16 text-orange-200"></i>
                                @endif
                            @endif
                            <!-- Type badge -->
                            <div class="absolute top-3 right-3">
                                @if($isProduct)
                                    <span class="bg-brand-600/90 text-white text-[9px] font-black px-2 py-0.5 rounded-lg uppercase tracking-wider backdrop-blur-sm">Ferme</span>
                                @else
                                    <span class="bg-orange-500/90 text-white text-[9px] font-black px-2 py-0.5 rounded-lg uppercase tracking-wider backdrop-blur-sm">Resto</span>
                                @endif
                            </div>
                        </div>
                    </a>

                    <div class="flex-grow">
                        <p class="text-[10px] font-black text-gray-400 mb-1 uppercase tracking-widest">{{ $item->category->name ?? 'Général' }}</p>
                        <h3 class="font-black text-gray-900 mb-1 line-clamp-1">{{ $item->name }}</h3>
                        <div class="flex items-center gap-1 text-gray-400 mb-2">
                            <i data-lucide="map-pin" class="w-3 h-3 text-orange-400 flex-shrink-0"></i>
                            <span class="text-[10px] font-bold uppercase tracking-wider truncate">{{ $location }}</span>
                        </div>
                    </div>

                    <div class="mt-auto pt-3 border-t border-gray-50 flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase truncate max-w-[100px]">{{ $sellerName }}</p>
                            <p class="text-lg font-black {{ $isProduct ? 'text-brand-600' : 'text-orange-500' }}">
                                {{ number_format($item->price, 0, ',', ' ') }} <span class="text-xs">FCFA</span>
                            </p>
                        </div>
                        <button onclick="addToCart('{{ $item->id }}', '{{ $typeKey }}', '{{ addslashes($item->name) }}', '{{ $item->price }}')"
                            class="w-11 h-11 rounded-2xl flex items-center justify-center transition-all shadow-sm
                                {{ $isProduct ? 'bg-brand-50 text-brand-700 hover:bg-brand-600 hover:text-white' : 'bg-orange-50 text-orange-500 hover:bg-orange-500 hover:text-white' }}">
                            <i data-lucide="shopping-basket" class="w-5 h-5 pointer-events-none"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Item count -->
        <div class="mt-8 text-center text-sm text-gray-400 font-medium">
            {{ $items->count() }} article(s) affiché(s)
            @if($type !== 'all')
                · <a href="{{ route('catalog') }}" class="text-brand-600 hover:underline">Voir tout le catalogue</a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
