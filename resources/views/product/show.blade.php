@extends('layouts.app')

@section('title', $item->name)

@section('content')
@php
    $isProduct = ($type === 'product');
    $sellerName = $isProduct ? ($item->producer->farm_name ?? 'Ferme') : ($item->menu->restaurant->name ?? 'Restaurant');
    $accentColor = $isProduct ? 'brand' : 'orange';
@endphp

<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8 text-sm font-medium text-gray-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li><a href="/" class="hover:text-brand-600">Accueil</a></li>
                <li><i data-lucide="chevron-right" class="w-4 h-4"></i></li>
                <li><a href="/catalog" class="hover:text-brand-600">Catalogue</a></li>
                <li><i data-lucide="chevron-right" class="w-4 h-4"></i></li>
                <li class="text-gray-900 font-bold">{{ $item->name }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Image Section -->
            <div class="relative group">
                <div class="aspect-square bg-{{$accentColor}}-50 rounded-[2.5rem] flex items-center justify-center text-{{$accentColor}}-200 overflow-hidden border border-{{$accentColor}}-100 shadow-inner">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    @else
                        <i data-lucide="{{ $isProduct ? 'apple' : 'utensils' }}" class="w-40 h-40 opacity-30 group-hover:scale-110 transition-transform duration-700"></i>
                    @endif
                </div>
                @if($isProduct && $item->is_organic)
                    <div class="absolute top-6 left-6 bg-brand-600 text-white text-sm font-black px-4 py-2 rounded-2xl shadow-lg ring-4 ring-white">BIO</div>
                @endif
            </div>

            <!-- Details Section -->
            <div class="flex flex-col" x-data="{ quantity: 1 }">
                <div class="mb-6">
                    <span class="inline-block bg-{{$accentColor}}-100 text-{{$accentColor}}-700 text-xs font-black px-3 py-1 rounded-full uppercase tracking-widest mb-4">
                        {{ $item->category->name ?? 'Catégorie' }}
                    </span>
                    <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4 tracking-tight leading-tight">{{ $item->name }}</h1>
                    
                    <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 bg-gray-100 rounded-lg text-gray-600">
                                <i data-lucide="map-pin" class="w-4 h-4 text-dynamic-orange text-orange-600"></i>
                            </div>
                            <span class="text-sm font-bold text-gray-700">
                                {{ $isProduct ? ($item->producer->location ?? 'Localisation non définie') : ($item->menu->restaurant->location ?? 'Localisation non définie') }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-8">
                        <p class="text-lg text-gray-600 leading-relaxed font-light">
                            {{ $item->description ?? 'Aucune description détaillée n\'est disponible pour cet article pour le moment.' }}
                        </p>
                    </div>

                    <div class="flex items-baseline gap-2 mb-10">
                        <span class="text-4xl font-black text-{{$isProduct ? 'brand-600' : 'dynamic-orange'}}">{{ number_format($item->price, 0, ',', ' ') }} FCFA</span>
                        @if($isProduct)
                        <span class="text-gray-400 font-medium">/ {{ $item->unit }}</span>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <div class="flex items-center bg-gray-100 p-1.5 rounded-2xl border border-gray-200 w-fit">
                            <button @click="if(quantity > 1) quantity--" class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-white hover:shadow-sm text-gray-600 transition-all font-bold text-xl">-</button>
                            <input type="number" x-model="quantity" readonly class="w-12 bg-transparent text-center font-black text-gray-900 border-none focus:ring-0">
                            <button @click="quantity++" class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-white hover:shadow-sm text-gray-600 transition-all font-bold text-xl">+</button>
                        </div>

                        <button 
                            onclick="addToCart('{{ $item->id }}', '{{ $isProduct ? 'product' : 'menu_item' }}', '{{ addslashes($item->name) }}', '{{ $item->price }}', document.querySelector('[x-model=quantity]').value)"
                            class="flex-grow bg-{{$accentColor}}-600 hover:bg-{{$accentColor}}-700 text-white px-8 py-4 rounded-2xl font-black text-lg shadow-xl shadow-{{$accentColor}}-500/30 hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                            <i data-lucide="shopping-basket" class="w-6 h-6 pointer-events-none"></i>
                            Ajouter au panier
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center gap-3 text-gray-600">
                            <i data-lucide="shield-check" class="w-5 h-5 text-green-500"></i>
                            <span class="text-xs font-bold uppercase tracking-wider">Qualité Garantie</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center gap-3 text-gray-600">
                            <i data-lucide="truck" class="w-5 h-5 text-blue-500"></i>
                            <span class="text-xs font-bold uppercase tracking-wider">Livraison Express</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
