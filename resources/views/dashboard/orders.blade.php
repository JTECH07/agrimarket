@extends('layouts.dashboard')

@section('title', 'Gestion des Commandes')
@section('page_title', 'Commandes reçues')

@section('content')
<div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
    <div class="mb-10">
        <h3 class="text-2xl font-black text-gray-900 mb-1">Commandes en cours</h3>
        <p class="text-sm font-medium text-gray-500">Gérez les demandes de vos clients et lancez les livraisons.</p>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-gray-50 text-gray-200 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i data-lucide="shopping-bag" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Aucune commande pour le moment</h3>
            <p class="text-gray-500">Les commandes de vos clients apparaîtront ici dès qu'elles seront passées.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="border border-gray-100 rounded-3xl p-6 hover:border-brand-200 transition-all bg-gray-50/30">
                    <div class="flex flex-col md:flex-row justify-between gap-6">
                        <div class="flex gap-4">
                            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center border border-gray-100 shadow-sm">
                                <i data-lucide="user" class="w-6 h-6 text-brand-600"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900">{{ $order->customer->name ?? 'Client Anonyme' }}</h4>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Commande #{{ $order->id }} • {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-lg font-black text-gray-900">{{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
                                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider 
                                    {{ $order->status === 'pending' ? 'bg-orange-100 text-orange-700' : 
                                       ($order->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $order->status }}
                                </span>
                            </div>

                            @if($order->status === 'pending')
                                <form action="{{ route('dashboard.orders.confirm', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-3 rounded-2xl font-black text-sm shadow-lg shadow-brand-500/20 transition-all hover:-translate-y-1">
                                        Valider & Livrer
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Articles commandés :</p>
                        <ul class="space-y-2">
                            @foreach($order->items as $item)
                                <li class="text-sm font-bold text-gray-700 flex justify-between">
                                    <span>{{ $item->quantity }}x {{ $item->product ? $item->product->name : ($item->menuItem ? $item->menuItem->name : 'Article') }}</span>
                                    <span>{{ number_format($item->price * $item->quantity, 0, ',', ' ') }} FCFA</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
