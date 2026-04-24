@extends('layouts.dashboard')

@section('title', 'Vue d\'ensemble')
@section('page_title', 'Tableau de bord')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-shadow">
        <div class="w-14 h-14 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center">
            <i data-lucide="banknote" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 border-b border-gray-50 pb-1 uppercase tracking-widest mb-1">Revenu (Livré)</p>
            <p class="text-2xl font-black text-gray-900">{{ number_format($stats['total_sales'], 0, ',', ' ') }} <span class="text-xs font-bold">FCFA</span></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-shadow">
        <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center">
            <i data-lucide="clock" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 border-b border-gray-50 pb-1 uppercase tracking-widest mb-1">Commandes en attente</p>
            <p class="text-2xl font-black text-gray-900">{{ $stats['pending_orders'] }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-shadow">
        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
            <i data-lucide="package-search" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 border-b border-gray-50 pb-1 uppercase tracking-widest mb-1">Catalogue actif</p>
            <p class="text-2xl font-black text-gray-900">{{ $stats['total_items'] }} {{ Auth::user()->isProducer() ? 'Produits' : 'Plats' }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Activity -->
    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-50">
            <h3 class="text-xl font-black text-gray-900">Activité Récente</h3>
            <a href="{{ route('dashboard.orders') }}" class="text-sm font-bold text-brand-600 hover:text-brand-700">Voir tout</a>
        </div>
        @if($recentOrders->isEmpty())
            <div class="text-center py-10">
                <i data-lucide="inbox" class="w-12 h-12 text-gray-200 mx-auto mb-3"></i>
                <p class="text-gray-400 text-sm font-medium">Aucune commande pour le moment.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($recentOrders as $order)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-white border border-transparent hover:border-gray-100 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center font-bold text-gray-400 group-hover:text-brand-600 group-hover:border-brand-100">
                            {{ substr($order->customer->name ?? 'C', 0, 1) }}
                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-sm truncate max-w-[120px]">{{ $order->customer->name ?? 'Client' }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-black text-gray-900 text-sm mb-1">{{ number_format($order->total, 0, ',', ' ') }} F</p>
                        <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest
                            {{ $order->status === 'pending' ? 'bg-orange-100 text-orange-600' : 'bg-brand-100 text-brand-600' }}">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Promo Card -->
    <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden flex flex-col justify-center">
        @if(Auth::user()->isProducer())
            <i data-lucide="tractor" class="absolute -bottom-10 -right-10 w-48 h-48 opacity-10 rotate-12"></i>
            <h3 class="text-2xl font-black mb-4">Optimisez votre récolte</h3>
        @else
            <i data-lucide="utensils" class="absolute -bottom-10 -right-10 w-48 h-48 opacity-10 -rotate-12"></i>
            <h3 class="text-2xl font-black mb-4">Gérez vos menus</h3>
        @endif
        <p class="text-gray-400 mb-8 font-light leading-relaxed">Boostez votre visibilité et augmentez vos ventes en mettant à jour régulièrement votre catalogue.</p>
        <div class="flex gap-4">
            <a href="{{ route('dashboard.products.create') }}" class="bg-brand-600 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-xl shadow-brand-900/20 hover:bg-brand-500 transition-all">
                Ajouter un article
            </a>
            <a href="{{ route('dashboard.settings') }}" class="bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-xl font-bold text-sm transition-all border border-white/10">
                Paramètres
            </a>
        </div>
    </div>
</div>
@endsection
