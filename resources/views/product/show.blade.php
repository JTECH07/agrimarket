@extends('layouts.app')

@section('title', $item->name)

@section('content')
@php
    $isProduct = ($type === 'product');
    $sellerName = $isProduct ? ($item->producer->farm_name ?? 'Ferme') : ($item->menu->restaurant->name ?? 'Restaurant');
    $sellerLocation = $isProduct ? ($item->producer->location ?? 'Localisation non définie') : ($item->menu->restaurant->location ?? 'Localisation non définie');
@endphp

<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- Breadcrumb -->
        <nav class="flex mb-8 text-sm font-medium text-gray-400" aria-label="Breadcrumb">
            <ol class="inline-flex items-center flex-wrap gap-1">
                <li><a href="/" class="hover:text-brand-600 transition-colors">Accueil</a></li>
                <li><i data-lucide="chevron-right" class="w-4 h-4 inline"></i></li>
                <li><a href="/catalog" class="hover:text-brand-600 transition-colors">Catalogue</a></li>
                <li><i data-lucide="chevron-right" class="w-4 h-4 inline"></i></li>
                @if($isProduct)
                <li><a href="/catalog?type=products" class="hover:text-brand-600 transition-colors">Produits</a></li>
                @else
                <li><a href="/catalog?type=menus" class="hover:text-orange-500 transition-colors">Plats</a></li>
                @endif
                <li><i data-lucide="chevron-right" class="w-4 h-4 inline"></i></li>
                <li class="text-gray-900 font-bold truncate max-w-[200px]">{{ $item->name }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
            <!-- ===== IMAGE ===== -->
            <div class="relative" x-data="{ zoom: false }">
                <div @click="zoom = !zoom"
                     class="aspect-square rounded-3xl flex items-center justify-center overflow-hidden border shadow-inner cursor-zoom-in
                        {{ $isProduct ? 'bg-brand-50 border-brand-100' : 'bg-orange-50 border-orange-100' }}">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                             class="w-full h-full object-cover transition-transform duration-700"
                             :class="zoom ? 'scale-125' : 'scale-100'">
                    @else
                        @if($isProduct)
                            <i data-lucide="apple" class="w-40 h-40 text-brand-200"></i>
                        @else
                            <i data-lucide="utensils" class="w-40 h-40 text-orange-200"></i>
                        @endif
                    @endif
                </div>

                <!-- Badges -->
                <div class="absolute top-5 left-5 flex gap-2">
                    @if($isProduct && ($item->is_organic ?? false))
                        <span class="bg-brand-600 text-white text-xs font-black px-3 py-1.5 rounded-xl shadow-lg ring-2 ring-white">🌿 BIO</span>
                    @endif
                    @if($item->is_available)
                        <span class="bg-white/90 text-green-700 text-xs font-black px-3 py-1.5 rounded-xl shadow-sm border border-green-200 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> En stock
                        </span>
                    @else
                        <span class="bg-white/90 text-red-600 text-xs font-black px-3 py-1.5 rounded-xl shadow-sm border border-red-200 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Rupture
                        </span>
                    @endif
                </div>

                <p class="text-center text-xs text-gray-400 mt-2">Cliquez sur l'image pour zoomer</p>
            </div>

            <!-- ===== DETAILS ===== -->
            <div class="flex flex-col" x-data="{ quantity: 1, added: false }">
                <!-- Category -->
                <div class="mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-widest
                        {{ $isProduct ? 'bg-brand-100 text-brand-700' : 'bg-orange-100 text-orange-600' }}">
                        @if($isProduct)
                            <i data-lucide="tractor" class="w-3.5 h-3.5"></i>
                        @else
                            <i data-lucide="chef-hat" class="w-3.5 h-3.5"></i>
                        @endif
                        {{ $item->category->name ?? 'Catégorie' }}
                    </span>
                </div>

                <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-4 leading-tight">{{ $item->name }}</h1>

                <!-- Seller & Location -->
                <div class="flex flex-wrap gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="flex items-center gap-2 text-sm">
                        <div class="w-8 h-8 {{ $isProduct ? 'bg-brand-100 text-brand-700' : 'bg-orange-100 text-orange-600' }} rounded-lg flex items-center justify-center">
                            @if($isProduct)
                                <i data-lucide="tractor" class="w-4 h-4"></i>
                            @else
                                <i data-lucide="chef-hat" class="w-4 h-4"></i>
                            @endif
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $isProduct ? 'Ferme' : 'Restaurant' }}</p>
                            <p class="font-bold text-gray-800">{{ $sellerName }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <div class="w-8 h-8 bg-gray-100 text-gray-500 rounded-lg flex items-center justify-center">
                            <i data-lucide="map-pin" class="w-4 h-4 text-orange-500"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Localisation</p>
                            <p class="font-bold text-gray-800">{{ $sellerLocation }}</p>
                        </div>
                    </div>
                    @if($isProduct && $item->unit)
                    <div class="flex items-center gap-2 text-sm">
                        <div class="w-8 h-8 bg-gray-100 text-gray-500 rounded-lg flex items-center justify-center">
                            <i data-lucide="package" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Unité</p>
                            <p class="font-bold text-gray-800">{{ $item->unit }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Description -->
                @if($item->description)
                <div class="mb-6">
                    <p class="text-gray-600 leading-relaxed">{{ $item->description }}</p>
                </div>
                @endif

                <!-- Price -->
                <div class="flex items-baseline gap-2 mb-8">
                    <span class="text-4xl font-black {{ $isProduct ? 'text-brand-600' : 'text-orange-500' }}">
                        {{ number_format($item->price, 0, ',', ' ') }}
                    </span>
                    <span class="text-xl font-black text-gray-400">FCFA</span>
                    @if($isProduct && $item->unit)
                        <span class="text-base text-gray-400 font-medium">/ {{ $item->unit }}</span>
                    @endif
                </div>

                @if($item->is_available)
                <!-- Add to Cart -->
                <div class="flex flex-col sm:flex-row gap-3 mb-8">
                    <!-- Quantity -->
                    <div class="flex items-center bg-gray-100 rounded-2xl p-1 border border-gray-200">
                        <button @click="if(quantity > 1) quantity--"
                                class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-white hover:shadow-sm text-gray-600 font-bold transition-all">−</button>
                        <span x-text="quantity" class="w-10 text-center font-black text-gray-900 text-lg"></span>
                        <button @click="quantity++"
                                class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-white hover:shadow-sm text-gray-600 font-bold transition-all">+</button>
                    </div>

                    <button
                        @click="addToCart('{{ $item->id }}', '{{ $isProduct ? 'product' : 'menu_item' }}', '{{ addslashes($item->name) }}', '{{ $item->price }}', quantity); added = true; setTimeout(() => added = false, 2500)"
                        class="flex-grow py-4 rounded-2xl font-black text-base transition-all flex items-center justify-center gap-2 shadow-lg
                            {{ $isProduct ? 'bg-brand-600 hover:bg-brand-700 text-white shadow-brand-500/30' : 'bg-orange-500 hover:bg-orange-600 text-white shadow-orange-500/30' }}"
                        :class="added ? 'scale-95' : 'hover:-translate-y-0.5'">
                        <template x-if="!added">
                            <span class="flex items-center gap-2">
                                <i data-lucide="shopping-basket" class="w-5 h-5 pointer-events-none"></i>
                                Ajouter au panier
                            </span>
                        </template>
                        <template x-if="added">
                            <span class="flex items-center gap-2">
                                <i data-lucide="check" class="w-5 h-5 pointer-events-none"></i>
                                Ajouté !
                            </span>
                        </template>
                    </button>
                </div>

                <!-- Total preview -->
                <p class="text-sm text-gray-400 font-medium mb-8" x-show="quantity > 1">
                    Total : <span class="font-black text-gray-700" x-text="({{ $item->price }} * quantity).toLocaleString() + ' FCFA'"></span>
                </p>
                @else
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-8 text-center">
                    <p class="text-red-600 font-bold text-sm">❌ Ce produit est actuellement en rupture de stock.</p>
                </div>
                @endif

                <!-- Guarantees -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center gap-3">
                        <i data-lucide="shield-check" class="w-5 h-5 text-green-500 flex-shrink-0"></i>
                        <span class="text-xs font-bold text-gray-600">Qualité Garantie</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center gap-3">
                        <i data-lucide="truck" class="w-5 h-5 text-blue-500 flex-shrink-0"></i>
                        <span class="text-xs font-bold text-gray-600">Livraison Express</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center gap-3">
                        <i data-lucide="smartphone" class="w-5 h-5 text-yellow-500 flex-shrink-0"></i>
                        <span class="text-xs font-bold text-gray-600">Paiement Mobile Money</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center gap-3">
                        <i data-lucide="headphones" class="w-5 h-5 text-purple-500 flex-shrink-0"></i>
                        <span class="text-xs font-bold text-gray-600">Support 7j/7</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== SELLER INFO ===== -->
        <div class="bg-gray-50 rounded-3xl p-8 mb-12 border border-gray-100">
            <h2 class="text-xl font-black text-gray-900 mb-6">À propos du {{ $isProduct ? 'producteur' : 'restaurant' }}</h2>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="w-16 h-16 {{ $isProduct ? 'bg-brand-100 text-brand-700' : 'bg-orange-100 text-orange-600' }} rounded-2xl flex items-center justify-center flex-shrink-0">
                    @if($isProduct)
                        <i data-lucide="tractor" class="w-8 h-8"></i>
                    @else
                        <i data-lucide="chef-hat" class="w-8 h-8"></i>
                    @endif
                </div>
                <div class="flex-grow">
                    <h3 class="text-lg font-black text-gray-900 mb-1">{{ $sellerName }}</h3>
                    <p class="text-sm text-gray-500 flex items-center gap-1.5 mb-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-orange-400"></i>
                        {{ $sellerLocation }}
                    </p>
                    @php
                        $sellerDesc = $isProduct ? ($item->producer->description ?? null) : ($item->menu->restaurant->description ?? null);
                    @endphp
                    @if($sellerDesc)
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $sellerDesc }}</p>
                    @else
                        <p class="text-gray-400 text-sm italic">Aucune description disponible.</p>
                    @endif
                </div>
                <a href="{{ route('catalog') }}?type={{ $isProduct ? 'products' : 'menus' }}"
                   class="flex-shrink-0 bg-white border border-gray-200 hover:border-brand-300 text-gray-700 hover:text-brand-600 px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                    Voir plus d'articles <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        <!-- ===== REVIEWS PLACEHOLDER ===== -->
        <div class="mb-12">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-black text-gray-900">Avis clients</h2>
                @auth
                    <button class="text-sm font-bold text-brand-600 hover:underline flex items-center gap-1" onclick="showToast('Fonctionnalité bientôt disponible !', 'info')">
                        <i data-lucide="star" class="w-4 h-4"></i> Laisser un avis
                    </button>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-gray-400 hover:text-brand-600 transition-colors">Connectez-vous pour noter</a>
                @endauth
            </div>
            <div class="bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-10 text-center">
                <i data-lucide="star" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                <p class="text-gray-400 font-medium text-sm">Soyez le premier à laisser un avis sur ce produit.</p>
            </div>
        </div>

        <!-- ===== BACK BUTTON ===== -->
        <div class="flex gap-4">
            <a href="{{ route('catalog') }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-900 font-bold text-sm transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Retour au catalogue
            </a>
            <span class="text-gray-200">|</span>
            <a href="{{ route('checkout') }}" class="flex items-center gap-2 text-brand-600 hover:text-brand-700 font-bold text-sm transition-colors">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i> Voir mon panier
            </a>
        </div>
    </div>
</div>
@endsection
