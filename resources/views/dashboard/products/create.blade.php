@extends('layouts.dashboard')

@section('title', 'Ajouter un article')
@section('page_title', 'Nouvel article')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center">
                <i data-lucide="plus-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-gray-900">Mettre en vente</h3>
                <p class="text-sm text-gray-500 font-medium">Ajoutez un nouvel article à votre boutique sur Agrimarket.</p>
            </div>
        </div>

        <form action="{{ route('dashboard.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Photo de l'article</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i data-lucide="image" class="w-8 h-8 text-gray-400 mb-2"></i>
                                <p class="text-xs text-gray-500 font-bold">Cliquez pour ajouter une photo (JPG, PNG)</p>
                            </div>
                            <input type="file" name="image_file" class="hidden" accept="image/*" />
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nom de l'article</label>
                        <input type="text" name="name" id="name" required placeholder="Ex: Tomates cerises fraîches"
                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium">
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-bold text-gray-700 mb-2">Catégorie</label>
                        <select name="category_id" id="category_id" required
                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-bold text-gray-700 mb-2">Prix (FCFA)</label>
                        <input type="number" name="price" id="price" required min="0" placeholder="0"
                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium text-lg text-brand-600 font-black">
                    </div>

                    @if(Auth::user()->user_type === 'producer')
                    <div>
                        <label for="unit" class="block text-sm font-bold text-gray-700 mb-2">Unité de mesure</label>
                        <input type="text" name="unit" id="unit" required placeholder="Ex: kg, sac, panier"
                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium">
                    </div>

                    <div>
                        <label for="stock_quantity" class="block text-sm font-bold text-gray-700 mb-2">Quantité en stock</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" required min="0" placeholder="0"
                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium">
                    </div>
                    @endif
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4" placeholder="Détaillez les caractéristiques de votre produit..."
                        class="w-full bg-gray-50 border border-gray-100 rounded-[1.5rem] px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium"></textarea>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <a href="{{ route('dashboard.products') }}" class="flex-grow text-center text-gray-500 font-bold py-4 hover:bg-gray-50 rounded-xl transition">
                        Annuler
                    </a>
                    <button type="submit" class="flex-[2] bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-brand-500/30 transition-transform hover:-translate-y-1">
                        Publier l'article
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
