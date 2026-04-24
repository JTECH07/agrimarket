@extends('layouts.dashboard')

@section('title', 'Commandes')
@section('page_title', 'Gestion des Commandes')

@section('content')
<div class="space-y-6">

    <!-- Status Filter -->
    <div class="bg-white rounded-2xl p-1.5 border border-gray-100 shadow-sm inline-flex gap-1 flex-wrap">
        @php
            $statuses = ['all' => 'Toutes', 'pending' => 'En attente', 'confirmed' => 'Confirmées', 'delivering' => 'En livraison', 'delivered' => 'Livrées', 'cancelled' => 'Annulées'];
            $current = request('status', 'all');
        @endphp
        @foreach($statuses as $key => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $key]) }}"
           class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all
               {{ $current === $key ? 'bg-brand-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <!-- Orders List -->
    @if($orders->isEmpty())
        <div class="bg-white rounded-3xl p-20 shadow-sm border border-gray-100 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-5">
                <i data-lucide="shopping-bag" class="w-10 h-10 text-gray-200"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Aucune commande</h3>
            <p class="text-gray-400 text-sm">Les commandes apparaîtront ici dès qu'elles seront passées.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-brand-200 transition-all">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 border-b border-gray-50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-50 border border-gray-100 rounded-2xl flex items-center justify-center text-gray-400 font-black text-sm uppercase">
                                {{ substr($order->customer->name ?? 'C', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-black text-gray-900">{{ $order->customer->name ?? 'Client Anonyme' }}</p>
                                <p class="text-xs text-gray-400 font-medium">
                                    #{{ $order->order_number ?? $order->id }} · {{ $order->created_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <p class="text-lg font-black text-gray-900">{{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase
                                    {{ $order->status === 'pending' ? 'bg-orange-100 text-orange-700' :
                                       ($order->status === 'delivered' ? 'bg-green-100 text-green-700' :
                                       ($order->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) }}">
                                    <span class="w-1.5 h-1.5 rounded-full
                                        {{ $order->status === 'pending' ? 'bg-orange-500' :
                                           ($order->status === 'delivered' ? 'bg-green-500' :
                                           ($order->status === 'cancelled' ? 'bg-red-500' : 'bg-blue-500')) }}"></span>
                                    {{ $order->status }}
                                </span>
                            </div>

                            @if(($canManageActions ?? false) && $order->status === 'pending')
                                <form action="{{ route('dashboard.orders.confirm', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-2.5 rounded-xl font-black text-sm shadow-lg shadow-brand-500/20 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                        Confirmer
                                    </button>
                                </form>
                            @endif

                            @if(($canManageActions ?? false) && in_array($order->status, ['confirmed', 'preparing']))
                                <form action="{{ route('dashboard.orders.update', $order->id) }}" method="POST" class="flex gap-2">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-brand-500">
                                        <option>Changer statut...</option>
                                        <option value="preparing">🍳 En préparation</option>
                                        <option value="ready">✅ Prêt à livrer</option>
                                        <option value="delivering">🚚 En livraison</option>
                                        <option value="delivered">🎉 Livré</option>
                                        <option value="cancelled">❌ Annuler</option>
                                    </select>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Items Preview -->
                    <div class="px-6 py-4">
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($order->items->take(4) as $item)
                                <span class="bg-gray-50 border border-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-lg">
                                    {{ $item->quantity }}× {{ $item->product->name ?? $item->menuItem->name ?? 'Article' }}
                                </span>
                            @endforeach
                            @if($order->items->count() > 4)
                                <span class="bg-gray-50 border border-gray-100 text-gray-400 text-xs font-bold px-3 py-1 rounded-lg">
                                    +{{ $order->items->count() - 4 }} autres
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('dashboard.orders.show', $order->id) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 hover:text-brand-700 transition-colors">
                            Voir le détail complet <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="mt-6">
            {{ $orders->withQueryString()->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
