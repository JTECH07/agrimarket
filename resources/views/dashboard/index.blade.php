@extends('layouts.dashboard')

@section('title', 'Vue d\'ensemble')
@section('page_title', 'Tableau de bord')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5">
        <div class="w-14 h-14 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center">
            <i data-lucide="banknote" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 border-b border-gray-50 pb-1 uppercase tracking-widest mb-1">Ventes payées</p>
            <p class="text-2xl font-black text-gray-900">{{ number_format($stats['total_sales'], 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5">
        <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center">
            <i data-lucide="clock" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 border-b border-gray-50 pb-1 uppercase tracking-widest mb-1">En attente</p>
            <p class="text-2xl font-black text-gray-900">{{ $stats['pending_orders'] }} Commandes</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5">
        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
            <i data-lucide="package-search" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 border-b border-gray-50 pb-1 uppercase tracking-widest mb-1">Articles actifs</p>
            <p class="text-2xl font-black text-gray-900">{{ $stats['total_items'] }} Produits</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-50">
            <h3 class="text-xl font-black text-gray-900">Ventes Récentes</h3>
            <a href="{{ route('dashboard.orders') }}" class="text-sm font-bold text-brand-600 hover:text-brand-700">Tout voir</a>
        </div>
        @if($recentOrders->isEmpty())
            <div class="text-center py-10">
                <p class="text-gray-400 text-sm font-medium">Aucune commande reçue pour le moment.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($recentOrders as $order)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-white border border-transparent hover:border-gray-100 transition-all group">
                    <div class="flex items-center gap-4 text-xs">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-gray-400 font-bold border border-gray-100 group-hover:bg-brand-50 group-hover:text-brand-600">
                            {{ substr($order->user->name ?? 'C', 0, 1) }}
                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-sm">{{ $order->user->name ?? 'Client' }}</p>
                            <p class="text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-black text-gray-900 mb-1">{{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $order->status === 'pending' ? 'bg-orange-100 text-orange-600' : 'bg-brand-100 text-brand-600' }}">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-brand-600 rounded-[2.5rem] p-8 text-white relative overflow-hidden flex flex-col justify-center">
        <i data-lucide="tractor" class="absolute -bottom-10 -right-10 w-48 h-48 opacity-10 rotate-12"></i>
        <h3 class="text-2xl font-black mb-4">Gagnez du temps avec Agrimarket</h3>
        <p class="text-brand-100 mb-8 font-light leading-relaxed">Gérez vos stocks, suivez vos livraisons et discutez avec vos clients depuis une interface unique.</p>
        <div class="flex gap-4">
            <a href="{{ route('dashboard.products') }}" class="bg-white text-brand-700 px-6 py-3 rounded-xl font-bold text-sm shadow-xl shadow-brand-900/20 hover:scale-105 transition-transform">Nouveau produit</a>
        </div>
    </div>
</div>
@endsection
