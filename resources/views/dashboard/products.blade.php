@extends('layouts.dashboard')

@section('title', 'Mon Catalogue')
@section('page_title', 'Gestion du Catalogue')

@section('content')
<div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10 pb-8 border-b border-gray-50">
        <div>
            <h3 class="text-2xl font-black text-gray-900 mb-1">Mes Articles</h3>
            <p class="text-sm font-medium text-gray-500">Gérez vos produits, prix et stocks en temps réel.</p>
        </div>
        <a href="{{ route('dashboard.products.create') }}" class="bg-brand-600 text-white px-8 py-3.5 rounded-2xl font-black text-sm shadow-xl shadow-brand-500/20 flex items-center gap-3 hover:bg-brand-700 transition-all hover:-translate-y-1">
            <i data-lucide="plus-circle" class="w-5 h-5"></i> Mettre en vente
        </a>
    </div>

    @if($items->isEmpty())
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-gray-50 text-gray-200 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i data-lucide="package-search" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Votre catalogue est vide</h3>
            <p class="text-gray-500 mb-8">Commencez par ajouter votre premier produit pour qu'il apparaisse sur Agrimarket.</p>
            <a href="{{ route('dashboard.products.create') }}" class="text-brand-600 font-black flex items-center justify-center gap-2 hover:underline">
                Créer un article maintenant <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    @else
        <div class="overflow-x-auto -mx-8 px-8">
            <table class="w-full text-left border-separate border-spacing-y-4">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                        <th class="pb-4 px-4 font-black">Article</th>
                        <th class="pb-4 px-4 font-black text-center">Catégorie</th>
                        <th class="pb-4 px-4 font-black text-center">Prix</th>
                        <th class="pb-4 px-4 font-black text-center">Disponibilité</th>
                        <th class="pb-4 px-4 font-black text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    @php 
                        $isProduct = isset($item->producer_id);
                        $editUrl = route('dashboard.products.edit', $item->id);
                        $deleteUrl = route('dashboard.products.destroy', $item->id);
                        $viewUrl = route('item.show', ['type' => $isProduct ? 'product' : 'menu_item', 'id' => $item->id]);
                    @endphp
                    <tr class="bg-white group hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 px-4 rounded-l-2xl border-y border-l border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 group-hover:bg-white transition-colors">
                                    <i data-lucide="{{ $isProduct ? 'apple' : 'utensils' }}" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 group-hover:text-brand-600 transition-colors">{{ $item->name }}</p>
                                    @if($isProduct)
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $item->unit }} • Stock: {{ $item->stock_quantity }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 border-y border-gray-100 text-center">
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-gray-200">
                                {{ $item->category->name ?? 'Général' }}
                            </span>
                        </td>
                        <td class="py-4 px-4 border-y border-gray-100 text-center">
                            <p class="text-sm font-black text-gray-900">{{ number_format($item->price, 0, ',', ' ') }} <span class="text-[10px]">FCFA</span></p>
                        </td>
                        <td class="py-4 px-4 border-y border-gray-100 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $item->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $item->is_available ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $item->is_available ? 'En stock' : 'Rupture' }}
                            </span>
                        </td>
                        <td class="py-4 px-4 rounded-r-2xl border-y border-r border-gray-100 text-right">
                            <div class="flex items-center justify-end gap-2 px-2">
                                <a href="{{ $viewUrl }}" target="_blank" class="p-2.5 text-blue-500 hover:bg-blue-50 rounded-xl transition-all" title="Voir sur le site">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </a>
                                <a href="{{ $editUrl }}" class="p-2.5 text-orange-500 hover:bg-orange-50 rounded-xl transition-all" title="Modifier">
                                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                                </a>
                                <form action="{{ $deleteUrl }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2.5 text-red-500 hover:bg-red-50 rounded-xl transition-all" title="Supprimer">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
