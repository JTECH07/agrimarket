@extends('layouts.dashboard')

@section('title', 'Paramètres du Profil')
@section('page_title', 'Mon Profil Vendeur')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
        <div class="flex items-center gap-4 mb-10">
            <div class="w-14 h-14 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center">
                <i data-lucide="settings" class="w-7 h-7"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-gray-900">Paramètres Généraux</h3>
                <p class="text-sm text-gray-500 font-medium font-medium">Configurez les informations visibles par vos clients sur la plateforme.</p>
            </div>
        </div>

        <form action="{{ route('dashboard.settings.update') }}" method="POST">
            @csrf
             @method('PATCH')
            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Nom public (Ferme ou Restaurant)</label>
                        <input type="text" name="name_or_farm" required 
                            value="{{ old('name_or_farm', Auth::user()->user_type === 'producer' ? $profile->farm_name : $profile->name) }}"
                            class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-bold text-gray-900 shadow-inner">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Localisation (Ville, Quartier, Adresse)</label>
                        <div class="relative">
                            <i data-lucide="map-pin" class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                            <input type="text" name="location" placeholder="Ex: Lomé, Quartier Adidogomé"
                                value="{{ old('location', $profile->location) }}"
                                class="w-full bg-gray-50 border border-gray-100 rounded-2xl pl-16 pr-6 py-4 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-bold text-gray-900 shadow-inner">
                        </div>
                        <p class="mt-2 text-[10px] text-gray-400 font-bold uppercase tracking-wider">Cette adresse sera affichée sur tous vos articles.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Description de votre activité</label>
                        <textarea name="description" rows="5"
                            class="w-full bg-gray-50 border border-gray-100 rounded-[2rem] px-6 py-4 outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium text-gray-700 shadow-inner leading-relaxed">{{ old('description', $profile->description) }}</textarea>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-50">
                    <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-10 py-4 rounded-2xl font-black text-sm shadow-xl transition-all transform hover:-translate-y-1 active:scale-95">
                        Sauvegarder les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
