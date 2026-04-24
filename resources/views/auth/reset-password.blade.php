@extends('layouts.app')

@section('title', 'Nouveau mot de passe')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-xl border border-gray-100">
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Définir un nouveau mot de passe</h1>
        <p class="text-sm text-gray-600 mb-6">Choisissez un mot de passe sécurisé pour votre compte.</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-3 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Adresse Email</label>
                <input id="email" name="email" type="email" required value="{{ old('email', $email) }}"
                    class="appearance-none block w-full px-4 py-3 border border-gray-200 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all">
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Nouveau mot de passe</label>
                <input id="password" name="password" type="password" required
                    class="appearance-none block w-full px-4 py-3 border border-gray-200 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirmer le mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="appearance-none block w-full px-4 py-3 border border-gray-200 text-gray-900 rounded-xl focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all">
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl text-white bg-brand-600 hover:bg-brand-700 font-bold transition-all">
                Mettre à jour le mot de passe
            </button>
        </form>
    </div>
</div>
@endsection
