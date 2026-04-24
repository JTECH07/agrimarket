<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\MenuItem;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use FedaPay\FedaPay;
use FedaPay\Transaction;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:product,menu_item',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_address_id' => 'nullable|integer|exists:addresses,id',
            'delivery_address' => 'nullable|array',
            'delivery_address.title' => 'nullable|string|max:255',
            'delivery_address.address_line' => 'nullable|string|max:255',
            'delivery_address.city' => 'nullable|string|max:255',
            'delivery_address.additional_info' => 'nullable|string|max:255',
            'payment_method' => 'required|in:mobile_money,card,cash_on_delivery',
        ]);

        $deliveryAddressId = $this->resolveDeliveryAddressId($validated);
        if (!$deliveryAddressId) {
            return response()->json([
                'message' => 'Adresse de livraison invalide. Merci de renseigner une adresse complète (ville + adresse).',
            ], 422);
        }

        $groupedItems = [];
        $totalAmount = 0;

        foreach ($request->items as $reqItem) {
            if ($reqItem['type'] === 'product') {
                $product = Product::withoutGlobalScope(\App\Scopes\ProducerScope::class)->with('producer')->findOrFail($reqItem['id']);
                
                if ($product->stock_quantity < $reqItem['quantity']) {
                    return response()->json(['message' => 'Stock insuffisant pour ' . $product->name], 400);
                }

                $seller_id = $product->producer->user_id;
                $price = $product->discount_price ?? $product->price;
                
                $groupedItems[$seller_id][] = [
                    'model' => $product,
                    'quantity' => $reqItem['quantity'],
                    'price' => $price,
                    'type' => 'product'
                ];
                $totalAmount += $price * $reqItem['quantity'];
            } else {
                $menuItem = MenuItem::withoutGlobalScope(\App\Scopes\RestaurantScope::class)->with('menu.restaurant')->findOrFail($reqItem['id']);
                
                if (!$menuItem->is_available) {
                    return response()->json(['message' => $menuItem->name . ' est indisponible'], 400);
                }

                $seller_id = $menuItem->menu->restaurant->user_id;
                $price = $menuItem->price;

                $groupedItems[$seller_id][] = [
                    'model' => $menuItem,
                    'quantity' => $reqItem['quantity'],
                    'price' => $price,
                    'type' => 'menu_item'
                ];
                $totalAmount += $price * $reqItem['quantity'];
            }
        }

        DB::beginTransaction();

        try {
            $createdOrders = [];

            foreach ($groupedItems as $sellerId => $items) {
                $sellerSubtotal = 0;
                foreach ($items as $item) {
                    $sellerSubtotal += $item['price'] * $item['quantity'];
                }

                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'customer_id' => auth()->id(),
                    'order_type' => 'b2c',
                    'seller_id' => $sellerId,
                    'subtotal' => $sellerSubtotal,
                    'delivery_fee' => 0, // Logique de livraison à intégrer (ex: API Gozem)
                    'tax' => 0,
                    'discount' => 0,
                    'total' => $sellerSubtotal,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'delivery_address_id' => $deliveryAddressId,
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['type'] === 'product' ? $item['model']->id : null,
                        'menu_item_id' => $item['type'] === 'menu_item' ? $item['model']->id : null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'total_price' => $item['price'] * $item['quantity'],
                    ]);

                    if ($item['type'] === 'product') {
                        $item['model']->decrement('stock_quantity', $item['quantity']);
                    }
                }

                $createdOrders[] = $order;
            }

            // --- FEDAPAY INTEGRATION ---
            if (in_array($request->payment_method, ['mobile_money', 'card'])) {
                FedaPay::setApiKey(env('FEDAPAY_SECRET_KEY', 'sk_sandbox_test')); // Valeur par défaut pour dev
                FedaPay::setEnvironment(env('FEDAPAY_ENVIRONMENT', 'sandbox'));

                $transaction = Transaction::create([
                    "description" => "Achat Agrimarket - " . count($createdOrders) . " panier(s)",
                    "amount" => $totalAmount,
                    "currency" => ["iso" => "XOF"],
                    "callback_url" => url('/api/v1/fedapay/callback'),
                    "customer" => [
                        "firstname" => auth()->user()->name,
                        "email" => auth()->user()->email,
                        "phone_number" => [
                            "number" => auth()->user()->phone ?? '00000000',
                            "country" => "bj" // Bénin par défaut
                        ]
                    ]
                ]);

                $payToken = $transaction->generateToken();

                foreach ($createdOrders as $order) {
                    Payment::create([
                        'order_id' => $order->id,
                        'reference' => $transaction->id,
                        'amount' => $order->total,
                        'method' => $request->payment_method,
                        'status' => 'pending',
                        'gateway' => 'fedapay'
                    ]);
                }

                DB::commit();

                return response()->json([
                    'message' => 'Commandes créées. Redirection vers FedaPay.',
                    'payment_link' => $payToken->url,
                    'orders' => $createdOrders,
                ], 201);
            }

            DB::commit();

            return response()->json([
                'message' => 'Commandes à la livraison créées avec succès.',
                'orders' => $createdOrders
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erreur lors du checkout', 'error' => $e->getMessage()], 500);
        }
    }

    private function resolveDeliveryAddressId(array $validated): ?int
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if (!empty($validated['delivery_address_id'])) {
            $address = Address::where('id', $validated['delivery_address_id'])
                ->where('user_id', $user->id)
                ->first();

            return $address?->id;
        }

        $payload = $validated['delivery_address'] ?? [];
        if (!empty($payload['address_line']) && !empty($payload['city'])) {
            $address = Address::create([
                'user_id' => $user->id,
                'title' => $payload['title'] ?? 'Livraison',
                'address_line' => $payload['address_line'],
                'city' => $payload['city'],
                'additional_info' => $payload['additional_info'] ?? null,
                'is_default' => $user->addresses()->count() === 0,
            ]);

            return $address->id;
        }

        return $user->addresses()->value('id');
    }

    public function fedapayWebhook(Request $request) 
    {
        // En prod, vérifier la signature X-FedaPay-Signature
        $eventId = $request->input('entity.id'); // Transaction ID
        $status = $request->input('entity.status');
        
        $payments = Payment::where('reference', $eventId)->get();
        
        if ($payments->isNotEmpty()) {
            foreach($payments as $payment) {
                if ($status === 'approved') {
                    $payment->update(['status' => 'successful']);
                    $payment->order->update(['payment_status' => 'paid']);
                } elseif ($status === 'canceled' || $status === 'declined') {
                    $payment->update(['status' => 'failed']);
                    $payment->order->update(['payment_status' => 'failed']);
                }
            }
        }
        
        return response()->json(['status' => 'success']);
    }
}
