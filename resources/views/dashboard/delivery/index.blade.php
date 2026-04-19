@extends('layouts.dashboard')

@section('title', 'Mes Livraisons')
@section('page_title', 'Espace Livreur')

@section('content')
<div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase text-gray-400">À récupérer</p>
                <p class="text-xl font-black text-gray-900">{{ $deliveries->where('status', 'pending')->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center">
                <i data-lucide="truck" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase text-gray-400">En transit</p>
                <p class="text-xl font-black text-gray-900">{{ $deliveries->where('status', 'picked_up')->count() }}</p>
            </div>
        </div>
    </div>

    <h3 class="text-xl font-black text-gray-900">Missions assignées</h3>

    @if($deliveries->isEmpty())
        <div class="bg-white rounded-[2.5rem] p-20 shadow-sm border border-gray-100 text-center">
            <i data-lucide="bike" class="w-16 h-16 text-gray-200 mx-auto mb-4"></i>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Pas de missions pour le moment</h3>
            <p class="text-gray-400">Vous recevrez des notifications dès qu'une commande sera prête à être livrée.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($deliveries as $delivery)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6 hover:border-blue-200 transition-all">
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <div class="w-12 h-12 bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center border border-gray-100">
                            <i data-lucide="map-pin" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-900">{{ $delivery->order->deliveryAddress->city ?? 'Quartier non défini' }}</p>
                            <p class="text-xs text-gray-500 font-medium">{{ $delivery->order->deliveryAddress->address_line1 ?? 'Adresse de livraison' }}</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 rounded-2xl border border-gray-100 text-center w-full md:w-auto">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Détails Colis</p>
                        <p class="text-sm font-bold text-gray-900">Commande #{{ $delivery->order->order_number }}</p>
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto">
                        @if($delivery->status === 'pending')
                            <button class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 transition">Enlever le colis</button>
                        @elseif($delivery->status === 'picked_up')
                            <button class="w-full md:w-auto bg-brand-600 hover:bg-brand-700 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-brand-500/20 transition">Confirmer Livraison</button>
                        @else
                            <span class="px-4 py-2 bg-green-50 text-green-600 rounded-xl text-xs font-black uppercase tracking-wider">Livraison Terminée</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
