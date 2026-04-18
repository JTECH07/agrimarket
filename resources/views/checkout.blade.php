@extends('layouts.app')

@section('title', 'Finaliser la commande')

@section('content')
<div class="bg-gray-50 min-h-screen py-12" x-data="checkoutForm()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Votre Panier</h1>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Liste du Panier -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <template x-if="$store.cart.items.length === 0">
                        <div class="text-center py-12">
                            <i data-lucide="shopping-cart" class="w-16 h-16 text-gray-200 mx-auto mb-4"></i>
                            <h3 class="text-xl font-bold text-gray-500">Votre panier est vide</h3>
                            <a href="/catalog" class="text-brand-600 font-semibold mt-4 inline-block hover:underline">Découvrir le catalogue</a>
                        </div>
                    </template>

                    <template x-for="item in $store.cart.items" :key="item.type + item.id">
                        <div class="flex items-center justify-between py-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 rounded-xl px-2 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400">
                                    <i data-lucide="package" x-show="item.type === 'product'"></i>
                                    <i data-lucide="utensils" x-show="item.type === 'menu_item'"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-lg" x-text="item.name"></h4>
                                    <p class="text-sm text-gray-500 font-medium" x-text="item.price + ' FCFA / unité'"></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-6">
                                <!-- Contrôleur de quantite -->
                                <div class="flex items-center bg-gray-100 rounded-full">
                                    <button @click="updateQuantity(item, -1)" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-200 text-gray-600 transition">-</button>
                                    <span class="w-8 text-center font-bold text-sm text-gray-800" x-text="item.quantity"></span>
                                    <button @click="updateQuantity(item, 1)" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-200 text-gray-600 transition">+</button>
                                </div>
                                <p class="font-black text-gray-900 w-24 text-right" x-text="(item.price * item.quantity).toLocaleString() + ' FCFA'"></p>
                                <button @click="$store.cart.remove(item.id, item.type)" class="text-red-400 hover:text-red-600 p-2 rounded-full hover:bg-red-50 transition">
                                    <i data-lucide="trash-2" class="w-5 h-5 pointer-events-none"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Résumé & Paiement -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 sticky top-24">
                    <h3 class="text-xl font-extrabold text-gray-900 mb-6">Récapitulatif</h3>
                    
                    <div class="space-y-4 mb-6 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Sous-total (<span x-text="$store.cart.count"></span> articles)</span>
                            <span class="font-bold text-gray-900" x-text="$store.cart.total.toLocaleString() + ' FCFA'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Frais de livraison</span>
                            <span class="text-gray-400 italic">Calculé plus tard</span>
                        </div>
                        <hr class="border-gray-100">
                        <div class="flex justify-between text-lg">
                            <span class="font-black text-gray-900">Total à payer</span>
                            <span class="font-black text-brand-600" x-text="$store.cart.total.toLocaleString() + ' FCFA'"></span>
                        </div>
                    </div>

                    @auth
                        <form @submit.prevent="submitCheckout">
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Méthode de Paiement</label>
                                <select x-model="paymentMethod" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-medium outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all">
                                    <option value="mobile_money">Mobile Money (FedaPay)</option>
                                    <option value="card">Carte Bancaire (FedaPay)</option>
                                    <option value="cash_on_delivery">Paiement à la livraison</option>
                                </select>
                            </div>

                            <!-- Hardcoded address for demonstration, needs a proper address picker in production -->
                            <input type="hidden" x-model="addressId" value="1" />

                            <template x-if="errorMsg">
                                <div class="bg-red-50 text-red-600 p-3 rounded-xl mb-4 text-sm font-medium" x-text="errorMsg"></div>
                            </template>

                            <button 
                                type="submit" 
                                :disabled="$store.cart.items.length === 0 || isProcessing"
                                class="w-full bg-brand-600 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-4 rounded-xl shadow-lg transition-transform hover:-translate-y-1 flex items-center justify-center gap-2">
                                <i data-lucide="credit-card" class="w-5 h-5" x-show="!isProcessing"></i>
                                <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin" x-show="isProcessing"></div>
                                <span x-text="isProcessing ? 'Génération...' : 'Valider la commande'"></span>
                            </button>
                        </form>
                    @else
                        <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 mb-4 text-center">
                            <p class="text-sm text-orange-800 font-medium mb-3">Vous devez être connecté(e) pour finaliser votre commande.</p>
                            <a href="/login" class="inline-block bg-dynamic-orange text-white px-6 py-2 rounded-lg font-bold text-sm shadow hover:bg-orange-600 transition">Se connecter</a>
                            <p class="text-xs text-orange-600 mt-2">ou <a href="/register" class="underline">Créer un compte</a></p>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function checkoutForm() {
        return {
            paymentMethod: 'mobile_money',
            addressId: 1, // Fixe pour l'instant
            isProcessing: false,
            errorMsg: '',

            updateQuantity(item, change) {
                if(item.quantity + change < 1) {
                    Alpine.store('cart').remove(item.id, item.type);
                } else {
                    item.quantity += change;
                    // Trigger reactivity manually for nested object since persist can sometimes lose deep track
                    Alpine.store('cart').items = [...Alpine.store('cart').items];
                }
            },

            async submitCheckout() {
                this.isProcessing = true;
                this.errorMsg = '';

                // Formater les items pour l'API
                const apiItems = Alpine.store('cart').items.map(i => ({
                    type: i.type,
                    id: i.id,
                    quantity: i.quantity
                }));

                try {
                    const response = await fetch('/api/v1/checkout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            // En production sans Sanctum SPA, utiliser Axios ou injecter le Bearer.
                            // Laravel gère l'auth de session sur /api/ routes pour le même domaine via le middleware EnsureFrontendRequestsAreStateful
                        },
                        body: JSON.stringify({
                            items: apiItems,
                            delivery_address_id: this.addressId,
                            payment_method: this.paymentMethod
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        this.errorMsg = data.message || 'Erreur lors du checkout';
                        this.isProcessing = false;
                        return;
                    }

                    // Succès ! Vider le panier
                    Alpine.store('cart').items = [];

                    if (data.payment_link) {
                        window.location.href = data.payment_link;
                    } else {
                        alert("Votre commande a été confirmée ! Préparez " + data.orders.reduce((t, o)=>t+o.total, 0) + " FCFA pour la livraison.");
                        window.location.href = "/";
                    }

                } catch (e) {
                    this.errorMsg = "Erreur de connexion au serveur.";
                    this.isProcessing = false;
                }
            }
        }
    }
</script>
@endpush
@endsection
