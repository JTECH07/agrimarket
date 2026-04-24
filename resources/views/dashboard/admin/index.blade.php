@extends('layouts.dashboard')

@section('title', 'Administration')
@section('page_title', 'Tour de Contrôle Admin')

@section('content')
<div class="space-y-8">

    <!-- ===== KPI CARDS ===== -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
            $kpis = [
                ['label' => 'Utilisateurs', 'value' => number_format($stats['total_users']), 'icon' => 'users', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
                ['label' => 'Commandes', 'value' => number_format($stats['total_orders']), 'icon' => 'shopping-cart', 'bg' => 'bg-orange-50', 'text' => 'text-orange-600'],
                ['label' => 'Revenu total', 'value' => number_format($stats['total_revenue'], 0, ',', ' ') . ' FCFA', 'icon' => 'trending-up', 'bg' => 'bg-brand-50', 'text' => 'text-brand-600'],
                ['label' => 'En attente', 'value' => number_format($stats['pending_orders']), 'icon' => 'clock', 'bg' => 'bg-yellow-50', 'text' => 'text-yellow-600'],
                ['label' => 'Produits', 'value' => number_format($stats['total_products']), 'icon' => 'package', 'bg' => 'bg-purple-50', 'text' => 'text-purple-600'],
                ['label' => 'Restaurants', 'value' => number_format($stats['total_restaurants']), 'icon' => 'chef-hat', 'bg' => 'bg-red-50', 'text' => 'text-red-500'],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="relative z-10">
                <div class="w-9 h-9 {{ $kpi['bg'] }} rounded-xl flex items-center justify-center mb-3">
                    <i data-lucide="{{ $kpi['icon'] }}" class="w-4 h-4 {{ $kpi['text'] }}"></i>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ $kpi['label'] }}</p>
                <p class="text-xl font-black text-gray-900 leading-tight">{{ $kpi['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- ===== ACTIONS + RECENT ORDERS ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-black text-gray-900">Commandes récentes</h3>
                <a href="{{ route('dashboard.orders') }}" class="text-sm font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                    Tout voir <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            @if($recentOrders->isEmpty())
                <div class="text-center py-10 text-gray-400">
                    <i data-lucide="shopping-cart" class="w-10 h-10 mx-auto mb-3 text-gray-200"></i>
                    <p class="text-sm">Aucune commande pour le moment.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-white border border-transparent hover:border-gray-100 transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-white border border-gray-100 rounded-full flex items-center justify-center text-xs font-black text-gray-500 group-hover:border-brand-200 group-hover:text-brand-600 transition-colors">
                                {{ substr($order->customer->name ?? 'C', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $order->customer->name ?? 'Client' }}</p>
                                <p class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-black text-gray-900">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase
                                {{ $order->status === 'pending' ? 'bg-orange-100 text-orange-700' :
                                   ($order->status === 'delivered' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ $order->status }}
                            </span>
                            <a href="{{ route('dashboard.orders.show', $order->id) }}" class="text-xs font-bold text-gray-400 hover:text-brand-600 transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="space-y-4">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-gray-900 mb-4">Actions rapides</h3>
                <div class="space-y-3">
                    <a href="{{ route('dashboard.admin.users') }}" class="flex items-center gap-3 p-4 rounded-2xl bg-gray-50 hover:bg-brand-50 transition-all border border-transparent hover:border-brand-100 group">
                        <div class="w-9 h-9 bg-white rounded-xl border border-gray-100 flex items-center justify-center group-hover:border-brand-200 transition-colors">
                            <i data-lucide="shield-check" class="w-4 h-4 text-brand-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-900">Vérifier vendeurs</p>
                            <p class="text-xs text-gray-400">Valider nouveaux producteurs</p>
                        </div>
                    </a>
                    <a href="{{ route('dashboard.orders') }}" class="flex items-center gap-3 p-4 rounded-2xl bg-gray-50 hover:bg-orange-50 transition-all border border-transparent hover:border-orange-100 group">
                        <div class="w-9 h-9 bg-white rounded-xl border border-gray-100 flex items-center justify-center group-hover:border-orange-200 transition-colors">
                            <i data-lucide="clipboard-list" class="w-4 h-4 text-orange-500"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-900">Commandes</p>
                            <p class="text-xs text-gray-400">Superviser et gérer</p>
                        </div>
                    </a>
                    <a href="{{ route('catalog') }}" target="_blank" class="flex items-center gap-3 p-4 rounded-2xl bg-gray-50 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100 group">
                        <div class="w-9 h-9 bg-white rounded-xl border border-gray-100 flex items-center justify-center group-hover:border-blue-200 transition-colors">
                            <i data-lucide="globe" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-900">Voir le site public</p>
                            <p class="text-xs text-gray-400">Catalogue & vitrine</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-gray-900 rounded-3xl p-6 text-white">
                <h3 class="text-base font-black mb-4">État du système</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Base de données</span>
                        <span class="flex items-center gap-1.5 text-xs font-black text-green-400">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span> Online
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">FedaPay Gateway</span>
                        <span class="flex items-center gap-1.5 text-xs font-black text-green-400">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span> Actif
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Livraisons (Gozem)</span>
                        <span class="flex items-center gap-1.5 text-xs font-black text-yellow-400">
                            <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full"></span> À configurer
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
