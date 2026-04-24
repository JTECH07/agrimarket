<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Delivery;
use App\Models\DeliveryAgent;
use App\Models\Producer;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Vue d'ensemble du tableau de bord adaptée à chaque rôle.
     */
    public function index()
    {
        $user = Auth::user();
        $stats = [
            'total_sales' => 0,
            'pending_orders' => 0,
            'total_items' => 0,
        ];
        $recentOrders = collect();

        if ($user->isProducer()) {
            $stats['total_items'] = Product::where('producer_id', $user->id)->count();
            $stats['pending_orders'] = Order::forSeller($user->id)->pending()->count();
            $stats['total_sales'] = Order::forSeller($user->id)->where('status', 'delivered')->sum('total');
            $recentOrders = Order::forSeller($user->id)->latest()->take(5)->get();
            return view('dashboard.index', compact('stats', 'recentOrders'));

        } elseif ($user->isRestaurant()) {
            $stats['total_items'] = MenuItem::whereHas('menu', function($q) use ($user) {
                $q->where('restaurant_id', $user->id);
            })->count();
            $stats['pending_orders'] = Order::forSeller($user->id)->pending()->count();
            $stats['total_sales'] = Order::forSeller($user->id)->where('status', 'delivered')->sum('total');
            $recentOrders = Order::forSeller($user->id)->latest()->take(5)->get();
            return view('dashboard.index', compact('stats', 'recentOrders'));

        } elseif ($user->isDeliveryAgent()) {
            $deliveries = Delivery::where('delivery_agent_id', $user->id)
                ->with('order.deliveryAddress')
                ->latest()
                ->get();
            return view('dashboard.delivery.index', compact('deliveries'));

        } elseif ($user->isCustomer()) {
            $orders = Order::forCustomer($user->id)->with('items.product', 'items.menuItem')->latest()->get();
            return view('dashboard.customer.index', compact('orders'));

        } elseif ($user->isAdmin()) {
            $stats = [
                'total_users' => User::count(),
                'total_orders' => Order::count(),
                'total_revenue' => Order::where('status', 'delivered')->sum('total'),
                'pending_orders' => Order::pending()->count(),
                'total_products' => Product::count(),
                'total_restaurants' => User::where('user_type', 'restaurant')->count(),
            ];
            $recentOrders = Order::with('customer')->latest()->take(10)->get();
            return view('dashboard.admin.index', compact('stats', 'recentOrders'));
        }

        return redirect('/');
    }

    /**
     * Liste des articles (Produits ou Plats) selon le vendeur.
     */
    public function products()
    {
        $user = Auth::user();
        if ($user->isProducer()) {
            $items = Product::where('producer_id', $user->id)->with('category')->latest()->paginate(10);
        } else {
            $items = MenuItem::whereHas('menu', fn($q) => $q->where('restaurant_id', $user->id))
                ->with('category')->latest()->paginate(10);
        }
            
        return view('dashboard.products', compact('items'));
    }

    public function editProduct($id)
    {
        $user = Auth::user();
        if ($user->isProducer()) {
            $item = Product::where('producer_id', $user->id)->findOrFail($id);
        } else {
            $item = MenuItem::whereHas('menu', fn($q) => $q->where('restaurant_id', $user->id))->findOrFail($id);
        }
        $categories = Category::all();
        return view('dashboard.products.edit', compact('item', 'categories'));
    }

    public function updateProduct(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isProducer()) {
            $item = Product::where('producer_id', $user->id)->findOrFail($id);
        } else {
            $item = MenuItem::whereHas('menu', fn($q) => $q->where('restaurant_id', $user->id))->findOrFail($id);
        }
        
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ];

        if ($user->isProducer()) {
            $rules['stock_quantity'] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('image')) {
            if ($item->image) Storage::disk('public')->delete($item->image);
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        $item->update($validated);
        return redirect()->route('dashboard.products')->with('success', 'Article mis à jour.');
    }

    public function destroyProduct($id)
    {
        $user = Auth::user();
        if ($user->isProducer()) {
            $item = Product::where('producer_id', $user->id)->findOrFail($id);
        } else {
            $item = MenuItem::whereHas('menu', fn($q) => $q->where('restaurant_id', $user->id))->findOrFail($id);
        }

        if ($item->image) Storage::disk('public')->delete($item->image);
        $item->delete();
        return back()->with('success', 'Article supprimé.');
    }

    public function createProduct()
    {
        $categories = Category::all();
        return view('dashboard.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ];

        if ($user->isProducer()) {
            $rules['stock_quantity'] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        if ($user->isProducer()) {
            Product::create(array_merge($validated, ['producer_id' => $user->id]));
        } else {
            $menu = $user->restaurant->menus()->firstOrCreate(['name' => 'Menu Principal']);
            MenuItem::create(array_merge($validated, ['menu_id' => $menu->id]));
        }

        return redirect()->route('dashboard.products')->with('success', 'Article ajouté avec succès.');
    }

    /**
     * Gestion des commandes.
     */
    public function orders()
    {
        $user = Auth::user();
        $query = $user->isAdmin() ? Order::query() : ($user->isCustomer() ? Order::forCustomer($user->id) : Order::forSeller($user->id));
        $orders = $query->with('customer', 'seller')->latest()->paginate(10);
        
        $canManageActions = in_array($user->user_type, ['producer', 'restaurant', 'admin']);
        
        return view('dashboard.orders', compact('orders', 'canManageActions'));
    }

    public function showOrder($id)
    {
        $order = Order::with(['items.product', 'items.menuItem', 'customer', 'deliveryAddress', 'delivery.deliveryAgent.user'])->findOrFail($id);
        return view('dashboard.orders.show', compact('order'));
    }

    /**
     * Workflow : Confirmation de commande -> Création Livraison
     */
    public function confirmOrder($id)
    {
        $order = Order::forSeller(Auth::id())->findOrFail($id);
        
        if ($order->status !== 'pending') {
            return back()->with('error', 'Cette commande a déjà été traitée.');
        }

        DB::transaction(function() use ($order) {
            $order->markAsConfirmed();
            
            // Trouver un livreur disponible
            $agent = DeliveryAgent::where('is_available', true)->first();
            
            Delivery::create([
                'order_id' => $order->id,
                'delivery_agent_id' => $agent?->id,
                'status' => $agent ? 'assigned' : 'pending',
                'tracking_number' => 'TRK-' . strtoupper(uniqid()),
            ]);
        });

        return back()->with('success', 'Commande confirmée ! Un livreur a été sollicité.');
    }

    /**
     * Étapes Livreur
     */
    public function pickupDelivery($id)
    {
        $delivery = Delivery::where('delivery_agent_id', Auth::id())->findOrFail($id);
        $delivery->update(['status' => 'picked_up', 'picked_up_at' => now()]);
        $delivery->order->update(['status' => 'in_delivery']);
        
        return back()->with('success', 'Colis récupéré. En route pour la livraison !');
    }

    public function completeDelivery($id)
    {
        $delivery = Delivery::where('delivery_agent_id', Auth::id())->findOrFail($id);
        $delivery->update(['status' => 'delivered', 'delivered_at' => now()]);
        $delivery->order->markAsDelivered();
        
        return back()->with('success', 'Livraison confirmée. Beau travail !');
    }

    /**
     * Paramètres & Profil
     */
    public function settings()
    {
        $user = Auth::user();
        if ($user->isProducer()) {
            $profile = $user->producer;
        } elseif ($user->isRestaurant()) {
            $profile = Restaurant::where('user_id', $user->id)->first();
        } else {
            $profile = null;
        }

        return view('dashboard.settings', compact('user', 'profile'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_or_farm' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        if ($user->isProducer()) {
            $user->producer()->update([
                'farm_name' => $validated['name_or_farm'],
                'location' => $validated['location'],
                'description' => $validated['description'],
            ]);
        } elseif ($user->isRestaurant()) {
            Restaurant::where('user_id', $user->id)->update([
                'name' => $validated['name_or_farm'],
                'location' => $validated['location'],
                'description' => $validated['description'],
            ]);
        }

        return back()->with('success', 'Profil mis à jour.');
    }

    /**
     * Admin Actions
     */
    public function verifyUser($id)
    {
        User::findOrFail($id)->update(['email_verified_at' => now()]);
        return back()->with('success', 'Utilisateur vérifié.');
    }

    public function deleteUser($id)
    {
        if (Auth::id() == $id) return back()->with('error', 'Auto-destruction impossible.');
        User::findOrFail($id)->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }
}