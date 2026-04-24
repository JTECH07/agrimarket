@extends('layouts.dashboard')

@section('title', 'Détail commande')
@section('page_title', 'Détail de la commande')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-6">
            <div>
                <p class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Commande</p>
                <h2 class="text-2xl font-black text-gray-900">#{{ $order->order_number ?? $order->id }}</h2>
                <p class="text-sm text-gray-500 mt-1">Créée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-black text-brand-600">{{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
                <span class="inline-block mt-1 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ in_array($order->status, ['pending', 'confirmed', 'preparing']) ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700' }}">
                    {{ $order->status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Client</p>
                <p class="font-bold text-gray-900">{{ $order->customer->name ?? 'Client' }}</p>
                <p class="text-sm text-gray-600">{{ $order->customer->email ?? 'Email non disponible' }}</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Adresse de livraison</p>
                <p class="font-bold text-gray-900">{{ $order->deliveryAddress->city ?? 'Ville non définie' }}</p>
                <p class="text-sm text-gray-600">{{ $order->deliveryAddress->address_line ?? 'Adresse non définie' }}</p>
                @if($order->deliveryAddress?->additional_info)
                    <p class="text-xs text-gray-500 mt-1">{{ $order->deliveryAddress->additional_info }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
        <h3 class="text-xl font-black text-gray-900 mb-4">Articles commandés</h3>
        <div class="space-y-3">
            @foreach($order->items as $item)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <div>
                        <p class="font-bold text-gray-900">{{ $item->product->name ?? $item->menuItem->name ?? 'Article' }}</p>
                        <p class="text-xs text-gray-500">Quantité: {{ $item->quantity }} • Prix unitaire: {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <p class="font-black text-gray-900">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</p>
                </div>
            @endforeach
        </div>
    </div>

    @if($order->delivery)
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
            <h3 class="text-xl font-black text-gray-900 mb-4">Suivi livraison</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Statut</p>
                    <p class="font-bold text-gray-900">{{ $order->delivery->status }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Tracking</p>
                    <p class="font-bold text-gray-900">{{ $order->delivery->tracking_number }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Livreur</p>
                    <p class="font-bold text-gray-900">{{ $order->delivery->deliveryAgent->user->name ?? 'Non assigné' }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
