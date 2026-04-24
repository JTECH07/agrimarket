@extends('layouts.app')

@section('title', 'Finaliser la commande')

@section('content')
<div class="bg-gray-50 min-h-screen py-10" x-data="checkoutForm()">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
            <a href="/catalog" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-white rounded-xl transition-all">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Votre Panier</h1>
                <p class="text-sm text-gray-400 font-medium" x-text="`${$store.cart.count} article(s) · ${$store.cart.total.toLocaleString()} FCFA`"></p>
            </div>
        </div>

        <!-- Empty State -->
        <template x-if="$store.cart.items.length === 0">
            <div class="bg-white rounded-3xl p-16 text-center shadow-sm border border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-5">
                    <i data-lucide="shopping-cart" class="w-10 h-10 text-gray-300"></i>
                </div>
                <h3 class="text-xl font-black text-gray-700 mb-2">Votre panier est vide</h3>
                <p class="text-gray-400 mb-6">Ajoutez des produits ou des plats pour passer une commande.</p>
                <a href="/catalog" class="inline-flex items-center gap-2 bg-brand-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-brand-700 transition">
                    <i data-lucide="grid" class="w-4 h-4"></i> Découvrir le catalogue
                </a>
            </div>
        </template>

        <!-- Cart Content -->
        <template x-if="$store.cart.items.length > 0">
            <div class="flex flex-col lg:flex-row gap-6">

                <!-- ===== CART ITEMS ===== -->
                <div class="w-full lg:w-[60%]">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50">
                            <h2 class="font-black text-gray-900">Articles</h2>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <template x-for="item in $store.cart.items" :key="item.type + item.id">
                                <div class="flex items-center gap-4 p-5 hover:bg-gray-50/50 transition-colors">
                                    <!-- Icon -->
                                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0"
                                         :class="item.type === 'product' ? 'bg-brand-50 text-brand-400' : 'bg-orange-50 text-orange-400'">
                                        <template x-if="item.type === 'product'">
                                            <i data-lucide="apple" class="w-6 h-6"></i>
                                        </template>
                                        <template x-if="item.type === 'menu_item'">
                                            <i data-lucide="utensils" class="w-6 h-6"></i>
                                        </template>
                                    </div>

                                    <!-- Info -->
                                    <div class="flex-grow min-w-0">
                                        <h4 class="font-bold text-gray-900 text-sm truncate" x-text="item.name"></h4>
                                        <p class="text-xs text-gray-400 font-medium" x-text="item.price.toLocaleString() + ' FCFA / unité'"></p>
                                    </div>

                                    <!-- Quantity control -->
                                    <div class="flex items-center bg-gray-100 rounded-xl border border-gray-200 flex-shrink-0">
                                        <button @click="updateQuantity(item, -1)"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-red-500 hover:bg-red-50 rounded-l-xl transition-all font-bold">−</button>
                                        <span class="w-8 text-center text-sm font-black text-gray-800" x-text="item.quantity"></span>
                                        <button @click="updateQuantity(item, 1)"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-brand-600 hover:bg-brand-50 rounded-r-xl transition-all font-bold">+</button>
                                    </div>

                                    <!-- Subtotal -->
                                    <p class="font-black text-gray-900 text-sm w-24 text-right flex-shrink-0"
                                       x-text="(item.price * item.quantity).toLocaleString() + ' FCFA'"></p>

                                    <!-- Remove -->
                                    <button @click="$store.cart.remove(item.id, item.type)"
                                            class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all flex-shrink-0">
                                        <i data-lucide="trash-2" class="w-4 h-4 pointer-events-none"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Clear Cart -->
                        <div class="p-4 border-t border-gray-50 flex justify-end">
                            <button @click="if(confirm('Vider le panier ?')) $store.cart.clear()"
                                    class="text-xs text-gray-400 hover:text-red-500 font-bold transition-colors flex items-center gap-1">
                                <i data-lucide="trash" class="w-3.5 h-3.5"></i> Vider le panier
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ===== SUMMARY & PAYMENT ===== -->
                <div class="w-full lg:w-[40%]">
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 sticky top-24">
                        <h3 class="text-lg font-extrabold text-gray-900 mb-5">Récapitulatif</h3>

                        <!-- Totals -->
                        <div class="space-y-3 mb-6 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Sous-total (<span x-text="$store.cart.count"></span> articles)</span>
                                <span class="font-bold text-gray-900" x-text="$store.cart.total.toLocaleString() + ' FCFA'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Frais de livraison</span>
                                <span class="text-gray-400 italic text-xs">Calculés à la validation</span>
                            </div>
                            <hr class="border-gray-100">
                            <div class="flex justify-between text-base">
                                <span class="font-black text-gray-900">Total estimé</span>
                                <span class="font-black text-brand-600" x-text="$store.cart.total.toLocaleString() + ' FCFA'"></span>
                            </div>
                        </div>

                        @auth
                            <!-- Delivery Address -->
                            <div class="space-y-3 mb-5">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest">Adresse de livraison</label>
                                <input x-model="addressLine" type="text" placeholder="Quartier, rue, numéro..."
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all">
                                <input x-model="city" type="text" placeholder="Ville (ex: Cotonou)"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all">
                                <textarea x-model="additionalInfo" rows="2" placeholder="Repère, étage, instructions..."
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all resize-none"></textarea>
                            </div>

                            <!-- Payment Method -->
                            <div class="mb-5">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Méthode de paiement</label>
                                <div class="space-y-2">
                                    <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                                           :class="paymentMethod === 'mobile_money' ? 'border-brand-500 bg-brand-50' : 'border-gray-200 hover:bg-gray-50'">
                                        <input type="radio" x-model="paymentMethod" value="mobile_money" class="text-brand-600">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="smartphone" class="w-4 h-4 text-yellow-500"></i>
                                            <span class="text-sm font-bold">Mobile Money (MTN / Moov)</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                                           :class="paymentMethod === 'card' ? 'border-brand-500 bg-brand-50' : 'border-gray-200 hover:bg-gray-50'">
                                        <input type="radio" x-model="paymentMethod" value="card" class="text-brand-600">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="credit-card" class="w-4 h-4 text-blue-500"></i>
                                            <span class="text-sm font-bold">Carte Bancaire (FedaPay)</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                                           :class="paymentMethod === 'cash_on_delivery' ? 'border-brand-500 bg-brand-50' : 'border-gray-200 hover:bg-gray-50'">
                                        <input type="radio" x-model="paymentMethod" value="cash_on_delivery" class="text-brand-600">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="banknote" class="w-4 h-4 text-green-500"></i>
                                            <span class="text-sm font-bold">Paiement à la livraison</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Error Message -->
                            <template x-if="errorMsg">
                                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm font-medium flex items-center gap-2">
                                    <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                                    <span x-text="errorMsg"></span>
                                </div>
                            </template>

                            <!-- Submit Button -->
                            <button type="button" @click="submitCheckout()"
                                :disabled="$store.cart.items.length === 0 || isProcessing"
                                class="w-full bg-brand-600 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-black py-4 rounded-2xl shadow-lg shadow-brand-500/20 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2 text-base">
                                <template x-if="!isProcessing">
                                    <span class="flex items-center gap-2">
                                        <i data-lucide="credit-card" class="w-5 h-5 pointer-events-none"></i>
                                        Valider la commande
                                    </span>
                                </template>
                                <template x-if="isProcessing">
                                    <span class="flex items-center gap-2">
                                        <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                        Traitement en cours...
                                    </span>
                                </template>
                            </button>

                            <p class="text-center text-xs text-gray-400 mt-3 flex items-center justify-center gap-1">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-green-500"></i>
                                Paiement sécurisé · Données protégées
                            </p>
                        @else
                            <div class="bg-orange-50 border border-orange-100 rounded-2xl p-5 text-center">
                                <i data-lucide="log-in" class="w-8 h-8 text-orange-400 mx-auto mb-2"></i>
                                <p class="text-sm text-orange-800 font-bold mb-3">Connexion requise pour finaliser</p>
                                <a href="/login" class="inline-block w-full bg-dynamic-orange text-white px-6 py-3 rounded-xl font-bold text-sm shadow hover:bg-orange-600 transition">
                                    Se connecter
                                </a>
                                <p class="text-xs text-orange-500 mt-2">ou <a href="/register" class="underline font-bold">Créer un compte gratuit</a></p>
                            </div>
                        @endauth
                    </div>
                </div>

            </div>
        </template>
    </div>
</div>

@push('scripts')
<script>
function checkoutForm() {
    return {
        paymentMethod: 'mobile_money',
        addressTitle: 'Domicile',
        addressLine: '',
        city: '',
        additionalInfo: '',
        isProcessing: false,
        errorMsg: '',

        updateQuantity(item, change) {
            const newQty = item.quantity + change;
            if (newQty < 1) {
                Alpine.store('cart').remove(item.id, item.type);
            } else {
                item.quantity = newQty;
                Alpine.store('cart').items = [...Alpine.store('cart').items];
            }
        },

        async submitCheckout() {
            this.errorMsg = '';

            if (!this.addressLine.trim() || !this.city.trim()) {
                this.errorMsg = 'Veuillez renseigner votre adresse et votre ville de livraison.';
                return;
            }

            this.isProcessing = true;

            const apiItems = Alpine.store('cart').items.map(i => ({
                type: i.type,
                id: i.id,
                quantity: i.quantity
            }));

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch('{{ route('checkout.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        items: apiItems,
                        delivery_address: {
                            title: this.addressTitle,
                            address_line: this.addressLine,
                            city: this.city,
                            additional_info: this.additionalInfo
                        },
                        payment_method: this.paymentMethod
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    this.errorMsg = data.message || 'Erreur lors de la validation de la commande.';
                    this.isProcessing = false;
                    return;
                }

                // Succès
                Alpine.store('cart').clear();

                if (data.payment_link) {
                    // Redirect to payment gateway
                    window.location.href = data.payment_link;
                } else {
                    // Show success toast and redirect
                    if (window.showToast) {
                        window.showToast('🎉 Commande confirmée ! Préparez votre paiement à la livraison.', 'success');
                    }
                    setTimeout(() => { window.location.href = '/'; }, 2000);
                }

            } catch (e) {
                this.errorMsg = 'Erreur de connexion. Vérifiez votre connexion internet et réessayez.';
                this.isProcessing = false;
            }
        }
    }
}
</script>
@endpush
@endsection
