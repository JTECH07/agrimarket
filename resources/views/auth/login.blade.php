@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-xl border border-gray-100">
        <div class="text-center">
            <div class="mx-auto h-12 w-12 bg-brand-100 text-brand-600 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="lock" class="w-6 h-6"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Bon retour !</h2>
            <p class="mt-2 text-sm text-gray-600">
                Connectez-vous pour gérer vos commandes ou votre boutique.
            </p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl p-3">
                {{ session('success') }}
            </div>
        @endif
        
        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Adresse Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                        class="appearance-none block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 focus:z-10 sm:text-sm transition-all @error('email') border-red-500 @enderror" 
                        placeholder="exemple@agrimarket.com" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Mot de passe</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                        class="appearance-none block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 focus:z-10 sm:text-sm transition-all">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded transition-all">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-700"> Se souvenir de moi </label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-brand-600 hover:text-brand-500 transition-colors"> Mot de passe oublié ? </a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all shadow-lg shadow-brand-500/30">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i data-lucide="log-in" class="h-5 w-5 text-brand-300 group-hover:text-brand-200"></i>
                    </span>
                    Se connecter
                </button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <p class="text-sm text-gray-600">
                Pas encore de compte ? 
                <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-brand-500 transition-colors">
                    S'inscrire gratuitement
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
