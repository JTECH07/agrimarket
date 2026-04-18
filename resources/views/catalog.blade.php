@extends('layouts.app')

@section('title', 'Catalogue Universel')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-8 border-b border-gray-100 pb-8">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Catalogue Universel</h1>
                <p class="text-lg text-gray-500 max-w-2xl">Découvrez les meilleurs ressources de nos producteurs et les plats préparés de nos restaurants partenaires.</p>
            </div>
            
            <div class="flex bg-gray-100 p-1 rounded-xl">
                <a href="/catalog?type=all" class="px-6 py-2 rounded-lg text-sm font-semibold {{ $type === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }} transition-all">Tout voir</a>
                <a href="/catalog?type=products" class="px-6 py-2 rounded-lg text-sm font-semibold {{ $type === 'products' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-brand-600' }} transition-all">Produits Agricoles</a>
                <a href="/catalog?type=menus" class="px-6 py-2 rounded-lg text-sm font-semibold {{ $type === 'menus' ? 'bg-white text-dynamic-orange shadow-sm' : 'text-gray-500 hover:text-dynamic-orange' }} transition-all">Plats Préparés</a>
            </div>
        </div>

        @if($items->isEmpty())
        <div class="text-center py-20 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
            <i data-lucide="search" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Aucun article trouvé</h3>
            <p class="text-gray-500">Essayez de changer de catégorie.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($items as $item)
                @php 
                    $isProduct = isset($item->producer_id);
                    $sellerName = $isProduct ? ($item->producer->farm_name ?? 'Ferme') : ($item->menu->restaurant->name ?? 'Restaurant');
                    $accentColor = $isProduct ? 'brand' : 'orange';
                @endphp
                
                <div class="group bg-white border border-gray-100 rounded-3xl overflow-hidden hover-lift p-4 flex flex-col h-full">
                    <div class="aspect-square bg-{{$accentColor}}-50 rounded-2xl mb-4 flex items-center justify-center text-{{$accentColor}}-200 group-hover:scale-105 transition-transform duration-500 relative">
                        @if($isProduct)
                            <i data-lucide="apple" class="w-20 h-20 opacity-50"></i>
                            @if($item->is_organic)
                                <span class="absolute top-3 right-3 bg-brand-100 text-brand-700 text-xs font-bold px-2 py-1 rounded-full">BIO</span>
                            @endif
                        @else
                            <i data-lucide="utensils" class="w-20 h-20 opacity-50"></i>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <p class="text-xs font-bold text-gray-400 mb-1 uppercase tracking-wider">{{ $item->category->name ?? 'General' }}</p>
                        <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1">{{ $item->name }}</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $item->description }}</p>
                    </div>
                    <div class="mt-auto flex justify-between items-end pt-4 border-t border-gray-100">
                        <div>
                            <p class="text-xs text-gray-500">Par {{ $sellerName }}</p>
                            <p class="text-xl font-black text-{{$isProduct ? 'brand-600' : 'dynamic-orange'}}">{{ number_format($item->price, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <button 
                            x-data
                            @click="$store.cart.add({ id: {{ $item->id }}, type: '{{ $isProduct ? 'product' : 'menu_item' }}', name: '{{ addslashes($item->name) }}', price: {{ $item->price }} })"
                            class="bg-gray-100 p-2 rounded-full text-gray-900 hover:bg-{{$isProduct ? 'brand' : 'orange'}}-600 hover:text-white transition-colors">
                            <i data-lucide="shopping-cart" class="w-5 h-5 pointer-events-none"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
