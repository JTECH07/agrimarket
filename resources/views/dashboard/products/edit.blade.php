@extends('layouts.dashboard')

@section('title', 'Modifier l\'article')
@section('page_title', 'Édition d\'article')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center">
                <i data-lucide="edit-3" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-gray-900">Modifier l'article</h3>
                <p class="text-sm text-gray-500 font-medium tracking-tight">Mettez à jour les informations de <span class="text-brand-600 font-bold">{{ $item->name }}</span>.</p>
            </div>
        </div>

        <form action="{{ route('dashboard.products.update', $item->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Photo de l'article</label>
                    <div class="flex gap-4 items-end">
                        @if($item->image)
                            <div class="w-32 h-32 rounded-2xl overflow-hidden border border-gray-200">
                                <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <label class="flex-grow flex flex-col items-center justify-center h-32 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-400 mb-2"></i>
                                <p class="text-[10px] text-gray-400 font-black uppercase">Changer la photo</p>
                            </div>
                            <input type="file" name="image" class="hidden" accept="image/*" />
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Nom de l'article</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $item->name) }}"
                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium">
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Catégorie</label>
                        <select name="category_id" id="category_id" required
                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Prix (FCFA)</label>
                        <input type="number" name="price" id="price" required min="0" value="{{ old('price', $item->price) }}"
                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-black text-lg text-brand-600">
                    </div>

                    @if(Auth::user()->user_type === 'producer')
                    <div>
                        <label for="unit" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Unité de mesure</label>
                        <input type="text" name="unit" id="unit" required value="{{ old('unit', $item->unit) }}"
                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium">
                    </div>

                    <div>
                        <label for="stock_quantity" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Quantité en stock</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" required min="0" value="{{ old('stock_quantity', $item->stock_quantity) }}"
                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium">
                    </div>
                    @endif
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Description</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full bg-gray-50 border border-gray-100 rounded-[1.5rem] px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium leading-relaxed">{{ old('description', $item->description) }}</textarea>
                </div>

                <div class="flex items-center gap-4 pt-6">
                    <a href="{{ route('dashboard.products') }}" class="flex-grow text-center text-gray-500 font-bold py-4 hover:bg-gray-50 rounded-xl transition-all">
                        Annuler
                    </a>
                    <button type="submit" class="flex-[2] bg-brand-600 hover:bg-brand-700 text-white font-black py-4 rounded-xl shadow-xl shadow-brand-500/20 transition-all hover:-translate-y-1">
                        Mettre à jour l'article
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
