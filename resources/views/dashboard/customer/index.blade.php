@extends('layouts.dashboard')

@section('title', 'Mes Commandes')
@section('page_title', 'Mon Espace Client')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-brand-600 to-brand-700 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-3xl font-black mb-2">Ravi de vous revoir, {{ Auth::user()->name }} !</h2>
            <p class="text-brand-100 font-medium">Suivez vos commandes en cours et retrouvez vos produits préférés.</p>
        </div>
        <i data-lucide="shopping-bag" class="absolute -bottom-8 -right-8 w-48 h-48 opacity-10 -rotate-12"></i>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white rounded-[2.5rem] p-20 shadow-sm border border-gray-100 text-center">
            <i data-lucide="package-open" class="w-16 h-16 text-gray-200 mx-auto mb-4"></i>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Vous n'avez pas encore passé de commande</h3>
            <p class="text-gray-400 mb-6">Explorez notre catalogue pour trouver les meilleurs produits locaux.</p>
            <a href="/catalog" class="bg-brand-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-brand-700 transition shadow-lg shadow-brand-500/20">Explorer le catalogue</a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:border-brand-200 transition-all group">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-6 border-b border-gray-50">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-gray-100 text-gray-600 text-[10px] font-black px-2 py-0.5 rounded-lg uppercase tracking-widest border border-gray-200">#{{ $order->order_number }}</span>
                                <span class="text-[10px] font-bold text-gray-400">{{ $order->created_at->format('d M Y à H:i') }}</span>
                            </div>
                            <h4 class="text-lg font-black text-gray-900">Commande auprès de <span class="text-brand-600">{{ $order->seller->name ?? 'Vendeur Agrimarket' }}</span></h4>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-2xl font-black text-gray-900">{{ number_format($order->total, 0, ',', ' ') }} <span class="text-xs">FCFA</span></p>
                                <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                    {{ $order->status === 'delivered' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-end gap-6">
                        <div class="flex -space-x-3 overflow-hidden">
                            @foreach($order->items->take(5) as $item)
                                <div class="inline-block h-10 w-10 rounded-full ring-2 ring-white bg-gray-100 flex items-center justify-center text-gray-400" title="{{ $item->product->name ?? $item->menuItem->name }}">
                                    <i data-lucide="{{ $item->product_id ? 'apple' : 'utensils' }}" class="w-4 h-4"></i>
                                </div>
                            @endforeach
                            @if($order->items->count() > 5)
                                <div class="inline-block h-10 w-10 rounded-full ring-2 ring-white bg-gray-900 border border-white flex items-center justify-center text-white text-[10px] font-bold">
                                    +{{ $order->items->count() - 5 }}
                                </div>
                            @endif
                        </div>
                        <button class="bg-gray-50 hover:bg-brand-50 text-gray-600 hover:text-brand-600 px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 border border-gray-100">
                            Détails <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
