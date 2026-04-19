@extends('layouts.dashboard')

@section('title', 'Administration')
@section('page_title', 'Tour de Contrôle')

@section('content')
<div class="space-y-8">
    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Utilisateurs</p>
                <p class="text-4xl font-black text-gray-900">{{ number_format($stats['total_users']) }}</p>
            </div>
            <i data-lucide="users" class="absolute -bottom-4 -right-4 w-24 h-24 text-gray-50 group-hover:text-brand-50 transition-colors"></i>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Commandes Totales</p>
                <p class="text-4xl font-black text-gray-900">{{ number_format($stats['total_orders']) }}</p>
            </div>
            <i data-lucide="shopping-cart" class="absolute -bottom-4 -right-4 w-24 h-24 text-gray-50 group-hover:text-orange-50 transition-colors"></i>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Chiffre d'Affaires</p>
                <p class="text-4xl font-black text-brand-600">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
            </div>
            <i data-lucide="trending-up" class="absolute -bottom-4 -right-4 w-24 h-24 text-gray-50 group-hover:text-green-50 transition-colors"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
            <h3 class="text-xl font-black text-gray-900 mb-6">Actions Administratives</h3>
            <div class="grid grid-cols-2 gap-4">
                <button class="p-4 rounded-2xl bg-gray-50 hover:bg-brand-50 text-left transition-all border border-transparent hover:border-brand-100 group">
                    <i data-lucide="shield-check" class="w-6 h-6 text-brand-600 mb-3"></i>
                    <p class="text-sm font-black text-gray-900">Vérifier Vendeurs</p>
                    <p class="text-xs text-gray-500 font-medium">12 profils en attente</p>
                </button>
                <button class="p-4 rounded-2xl bg-gray-50 hover:bg-orange-50 text-left transition-all border border-transparent hover:border-orange-100 group">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-orange-600 mb-3"></i>
                    <p class="text-sm font-black text-gray-900">Signalements</p>
                    <p class="text-xs text-gray-500 font-medium">3 litiges clients</p>
                </button>
            </div>
        </div>

        <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white">
            <h3 class="text-xl font-black mb-4">Maintenance Système</h3>
            <p class="text-gray-400 text-sm mb-6 leading-relaxed">Dernière sauvegarde effectuée il y a 2 heures. Tous les services (FedaPay, Gozem API) sont opérationnels.</p>
            <div class="flex gap-3">
                <div class="px-3 py-1 bg-green-500/10 text-green-500 rounded-lg text-[10px] font-black uppercase tracking-widest border border-green-500/20">DB Online</div>
                <div class="px-3 py-1 bg-green-500/10 text-green-500 rounded-lg text-[10px] font-black uppercase tracking-widest border border-green-500/20">Payment GW UP</div>
            </div>
        </div>
    </div>
</div>
@endsection
