@extends('layouts.app')

@section('title', 'Catalogue Universel')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-8 border-b border-gray-100 pb-8">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Catalogue Universel</h1>
                <p class="text-lg text-gray-500 max-w-2xl font-light">Découvrez toutes les ressources de nos producteurs et les plats de nos restaurants partenaires.</p>
            </div>
            
            <div class="flex bg-gray-100 p-1 rounded-xl shadow-inner">
                <a href="/catalog?type=all" class="px-6 py-2 rounded-lg text-sm font-bold {{ $type === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }} transition-all">Tout voir</a>
                <a href="/catalog?type=products" class="px-6 py-2 rounded-lg text-sm font-bold {{ $type === 'products' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-brand-600' }} transition-all">Produits Agricoles</a>
                <a href="/catalog?type=menus" class="px-6 py-2 rounded-lg text-sm font-bold {{ $type === 'menus' ? 'bg-white text-dynamic-orange shadow-sm' : 'text-gray-500 hover:text-dynamic-orange' }} transition-all">Plats Préparés</a>
            </div>
        </div>

        @if($items->isEmpty())
        <div class="text-center py-20 bg-gray-50 rounded-[2.5rem] border border-dashed border-gray-200">
            <i data-lucide="search" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
            <h3 class="text-xl font-bold text-gray-900 mb-1">Aucun article trouvé</h3>
            <p class="text-gray-500">Réessayez avec un autre filtre.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($items as $item)
                @php 
                    $isProduct = ($item instanceof \App\Models\Product);
                    $seller = $isProduct ? $item->producer : $item->menu->restaurant;
                    $sellerName = $isProduct ? ($seller->farm_name ?? 'Ferme') : ($seller->name ?? 'Restaurant');
                    $location = $seller->location ?? 'Localisation inconnue';
                    $accentColor = $isProduct ? 'brand' : 'orange';
                    $typeKey = $isProduct ? 'product' : 'menu_item';
                    $imageUrl = $item->image ? asset('storage/' . $item->image) : null;
                @endphp
                
                <div class="group bg-white border border-gray-100 rounded-[2rem] p-4 flex flex-col h-full shadow-sm hover:shadow-xl transition-all duration-300">
                    <a href="{{ route('item.show', ['type' => $typeKey, 'id' => $item->id]) }}" class="block mb-4">
                        <div class="aspect-square bg-{{$accentColor}}-50 rounded-[1.5rem] flex items-center justify-center text-{{$accentColor}}-200 overflow-hidden relative">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            @else
                                <i data-lucide="{{ $isProduct ? 'apple' : 'utensils' }}" class="w-16 h-16 opacity-50"></i>
                            @endif
                        </div>
                    </a>

                    <div class="flex-grow">
                        <p class="text-[10px] font-black text-gray-400 mb-1 uppercase tracking-widest">{{ $item->category->name ?? 'Général' }}</p>
                        <h3 class="text-lg font-black text-gray-900 mb-1 line-clamp-1 truncate">{{ $item->name }}</h3>
                        <div class="flex items-center gap-1.5 text-gray-400 mb-3">
                            <i data-lucide="map-pin" class="w-3 h-3 text-orange-500"></i>
                            <span class="text-[10px] font-bold uppercase tracking-wider truncate">{{ $location }}</span>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-gray-50 flex justify-between items-end">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Par {{ $sellerName }}</p>
                            <p class="text-xl font-black text-gray-900">{{ number_format($item->price, 0, ',', ' ') }} <span class="text-xs">FCFA</span></p>
                        </div>
                        <button 
                            onclick="addToCart('{{ $item->id }}', '{{ $typeKey }}', '{{ addslashes($item->name) }}', '{{ $item->price }}')"
                            class="w-12 h-12 bg-gray-50 text-gray-900 rounded-2xl flex items-center justify-center hover:bg-{{$isProduct ? 'brand-600' : 'orange-600'}} hover:text-white transition-all shadow-sm">
                            <i data-lucide="shopping-basket" class="w-5 h-5 pointer-events-none"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
