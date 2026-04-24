@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-xl border border-gray-100">
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Réinitialiser le mot de passe</h1>
        <p class="text-sm text-gray-600 mb-6">Entrez votre adresse email. Si un compte existe, vous recevrez un lien de réinitialisation.</p>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl p-3 mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-3 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Adresse Email</label>
                <input id="email" name="email" type="email" required value="{{ old('email') }}"
                    class="appearance-none block w-full px-4 py-3 border border-gray-200 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all"
                    placeholder="exemple@agrimarket.com">
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl text-white bg-brand-600 hover:bg-brand-700 font-bold transition-all">
                Envoyer le lien
            </button>
        </form>

        <p class="text-sm text-gray-600 mt-6 text-center">
            <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:text-brand-500">Retour à la connexion</a>
        </p>
    </div>
</div>
@endsection
