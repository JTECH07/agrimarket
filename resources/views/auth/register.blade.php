@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8 bg-white p-10 rounded-3xl shadow-xl border border-gray-100" x-data="{ userType: 'customer' }">
        <div class="text-center">
            <div class="mx-auto h-12 w-12 bg-dynamic-orange/10 text-dynamic-orange rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="user-plus" class="w-6 h-6"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Créer un compte</h2>
            <p class="mt-2 text-sm text-gray-600">
                Rejoignez la révolution de l'agro-alimentaire local.
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations de base -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">Identité</h3>
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nom complet</label>
                        <input id="name" name="name" type="text" required class="appearance-none block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all" placeholder="Jean Dupont">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Adresse Email</label>
                        <input id="email" name="email" type="email" required class="appearance-none block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all" placeholder="jean@exemple.com">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">Téléphone</label>
                        <input id="phone" name="phone" type="text" class="appearance-none block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all" placeholder="+229 ...">
                    </div>
                </div>

                <!-- Type de compte et sécurité -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">Type de compte</h3>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Vous êtes ?</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="relative flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all" :class="userType === 'customer' ? 'border-brand-500 bg-brand-50 ring-1 ring-brand-500' : 'border-gray-200 hover:bg-gray-50'">
                                <input type="radio" name="user_type" value="customer" x-model="userType" class="sr-only">
                                <i data-lucide="user" class="w-5 h-5 mb-1" :class="userType === 'customer' ? 'text-brand-600' : 'text-gray-400'"></i>
                                <span class="text-[10px] font-bold uppercase tracking-wider" :class="userType === 'customer' ? 'text-brand-700' : 'text-gray-500'">Client</span>
                            </label>
                            <label class="relative flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all" :class="userType === 'producer' ? 'border-brand-500 bg-brand-50 ring-1 ring-brand-500' : 'border-gray-200 hover:bg-gray-50'">
                                <input type="radio" name="user_type" value="producer" x-model="userType" class="sr-only">
                                <i data-lucide="tractor" class="w-5 h-5 mb-1" :class="userType === 'producer' ? 'text-brand-600' : 'text-gray-400'"></i>
                                <span class="text-[10px] font-bold uppercase tracking-wider" :class="userType === 'producer' ? 'text-brand-700' : 'text-gray-500'">Fermier</span>
                            </label>
                            <label class="relative flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all" :class="userType === 'restaurant' ? 'border-brand-500 bg-brand-50 ring-1 ring-brand-500' : 'border-gray-200 hover:bg-gray-50'">
                                <input type="radio" name="user_type" value="restaurant" x-model="userType" class="sr-only">
                                <i data-lucide="chef-hat" class="w-5 h-5 mb-1" :class="userType === 'restaurant' ? 'text-brand-600' : 'text-gray-400'"></i>
                                <span class="text-[10px] font-bold uppercase tracking-wider" :class="userType === 'restaurant' ? 'text-brand-700' : 'text-gray-500'">Resto</span>
                            </label>
                        </div>
                    </div>

                    <!-- Champs conditionnels vendeur -->
                    <div x-show="userType === 'producer'" x-transition class="bg-brand-50/50 p-4 rounded-2xl border border-brand-100">
                        <label for="farm_name" class="block text-sm font-semibold text-brand-700 mb-1">Nom de votre Ferme / Exploitation</label>
                        <input id="farm_name" name="farm_name" type="text" class="appearance-none block w-full px-4 py-3 border border-brand-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all" placeholder="Les Vergers du Sud">
                    </div>

                    <div x-show="userType === 'restaurant'" x-transition class="bg-brand-50/50 p-4 rounded-2xl border border-brand-100">
                        <label for="restaurant_name" class="block text-sm font-semibold text-brand-700 mb-1">Nom de votre Restaurant</label>
                        <input id="restaurant_name" name="restaurant_name" type="text" class="appearance-none block w-full px-4 py-3 border border-brand-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all" placeholder="L'Assiette de l'Amitié">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Mot de passe</label>
                        <input id="password" name="password" type="password" required class="appearance-none block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirmer mot de passe</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="appearance-none block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent text-lg font-bold rounded-xl text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all shadow-lg shadow-brand-500/30">
                    Créer mon compte
                </button>
            </div>
        </form>
        
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Déjà membre ? 
                <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:text-brand-500 transition-colors">
                    Se connecter ici
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
