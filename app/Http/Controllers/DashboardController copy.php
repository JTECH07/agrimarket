<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Delivery;
use App\Models\DeliveryAgent;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Producer;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    // =========================================================
    //   INDEX — redirige selon le type d'utilisateur
    // =========================================================
    public function index()
    {
        $user = Auth::user();

        if ($user->user_type === 'producer') {
            return $this->producerDashboard($user);
        }

        if ($user->user_type === 'restaurant') {
            return $this->restaurantDashboard($user);
        }

        if ($user->user_type === 'customer') {
            $orders = Order::where('customer_id', $user->id)
                ->with(['items.product', 'items.menuItem', 'seller'])
                ->latest()
                ->get();
            return view('dashboard.customer.index', compact('orders'));
        }

        if ($user->user_type === 'delivery_agent') {
            $agent      = $user->deliveryAgent;
            $deliveries = collect();
            if ($agent) {
                $deliveries = Delivery::where('delivery_agent_id', $agent->id)
                    ->with('order.deliveryAddress')
                    ->latest()
                    ->get();
            }
            return view('dashboard.delivery.index', compact('deliveries'));
        }

        if ($user->user_type === 'admin') {
            $stats = [
                'total_users'   => User::count(),
                'total_orders'  => Order::count(),
                'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_products' => Product::count(),
                'total_restaurants' => Restaurant::count(),
            ];
            $recentOrders = Order::with(['customer', 'seller'])->latest()->take(10)->get();
            return view('dashboard.admin.index', compact('stats', 'recentOrders'));
        }

        return redirect('/');
    }

    private function producerDashboard(User $user)
    {
        $producer = $this->ensureProducerProfile($user);
        $stats = [
            'total_sales'    => Order::where('seller_id', $user->id)->where('payment_status', 'paid')->sum('total'),
            'pending_orders' => Order::where('seller_id', $user->id)->where('status', 'pending')->count(),
            'total_items'    => Product::where('producer_id', $producer->id)->count(),
        ];
        $recentOrders = Order::where('seller_id', $user->id)->with('customer')->latest()->take(5)->get();
        return view('dashboard.index', compact('stats', 'recentOrders'));
    }

    private function restaurantDashboard(User $user)
    {
        $restaurant = $this->ensureRestaurantProfile($user);
        $stats = [
            'total_sales'    => Order::where('seller_id', $user->id)->where('payment_status', 'paid')->sum('total'),
            'pending_orders' => Order::where('seller_id', $user->id)->where('status', 'pending')->count(),
            'total_items'    => MenuItem::whereHas('menu', fn($q) => $q->where('restaurant_id', $restaurant->id))->count(),
        ];
        $recentOrders = Order::where('seller_id', $user->id)->with('customer')->latest()->take(5)->get();
        return view('dashboard.index', compact('stats', 'recentOrders'));
    }

    // =========================================================
    //   PRODUCTS / CATALOGUE
    // =========================================================
    public function products()
    {
        $user = Auth::user();
        if (!$this->isSeller($user)) {
            return redirect()->route('dashboard.index')->with('error', 'Espace réservé aux vendeurs.');
        }

        $categories = Category::orderBy('name')->get();

        if ($user->user_type === 'producer') {
            $producer = $this->ensureProducerProfile($user);
            $items    = Product::where('producer_id', $producer->id)->with('category')->latest()->get();
        } else {
            $restaurant = $this->ensureRestaurantProfile($user);
            $items      = MenuItem::whereHas('menu', fn($q) => $q->where('restaurant_id', $restaurant->id))
                ->with(['category', 'menu'])->latest()->get();
        }

        return view('dashboard.products', compact('items', 'categories'));
    }

    public function createProduct()
    {
        $user = Auth::user();
        if (!$this->isSeller($user)) {
            return redirect()->route('dashboard.index')->with('error', 'Espace réservé aux vendeurs.');
        }

        $categories = Category::orderBy('name')->get();

        if ($user->user_type === 'restaurant') {
            $restaurant = $this->ensureRestaurantProfile($user);
            $restaurant->menus()->firstOrCreate(
                ['restaurant_id' => $restaurant->id],
                ['name' => 'Menu Principal']
            );
        } else {
            $this->ensureProducerProfile($user);
        }

        return view('dashboard.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        if (!$this->isSeller($user)) abort(403);

        if ($user->user_type === 'producer') {
            $request->validate([
                'name'           => 'required|string|max:255',
                'description'    => 'nullable|string',
                'price'          => 'required|numeric|min:0',
                'category_id'    => 'nullable|exists:categories,id',
                'stock_quantity' => 'nullable|integer|min:0',
                'unit'           => 'nullable|string|max:50',
                'image'          => 'nullable|image|max:2048',
            ]);

            $producer  = $this->ensureProducerProfile($user);
            $imagePath = $request->hasFile('image') ? $request->file('image')->store('products', 'public') : null;

            Product::create([
                'producer_id'    => $producer->id,
                'name'           => $request->name,
                'description'    => $request->description,
                'price'          => $request->price,
                'category_id'    => $request->category_id,
                'stock_quantity' => $request->stock_quantity ?? 0,
                'unit'           => $request->unit ?? 'kg',
                'is_available'   => true,
                'image'          => $imagePath,
            ]);
        } else {
            $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'price'       => 'required|numeric|min:0',
                'category_id' => 'nullable|exists:categories,id',
                'image'       => 'nullable|image|max:2048',
            ]);

            $restaurant = $this->ensureRestaurantProfile($user);
            $menu       = $restaurant->menus()->firstOrCreate(
                ['restaurant_id' => $restaurant->id],
                ['name' => 'Menu Principal']
            );
            $imagePath = $request->hasFile('image') ? $request->file('image')->store('menu_items', 'public') : null;

            MenuItem::create([
                'menu_id'      => $menu->id,
                'name'         => $request->name,
                'description'  => $request->description,
                'price'        => $request->price,
                'category_id'  => $request->category_id,
                'is_available' => true,
                'image'        => $imagePath,
            ]);
        }

        return redirect()->route('dashboard.products')->with('success', 'Article créé avec succès !');
    }

    public function editProduct($id)
    {
        $user       = Auth::user();
        $categories = Category::orderBy('name')->get();

        if ($user->user_type === 'producer') {
            $producer = $this->ensureProducerProfile($user);
            $item     = Product::where('producer_id', $producer->id)->findOrFail($id);
        } else {
            $restaurant = $this->ensureRestaurantProfile($user);
            $item       = MenuItem::whereHas('menu', fn($q) => $q->where('restaurant_id', $restaurant->id))->findOrFail($id);
        }

        return view('dashboard.products.edit', compact('item', 'categories'));
    }

    public function updateProduct(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->user_type === 'producer') {
            $request->validate([
                'name'           => 'required|string|max:255',
                'price'          => 'required|numeric|min:0',
                'stock_quantity' => 'nullable|integer|min:0',
                'image'          => 'nullable|image|max:2048',
            ]);

            $producer = $this->ensureProducerProfile($user);
            $item     = Product::where('producer_id', $producer->id)->findOrFail($id);

            $data = $request->only(['name', 'description', 'price', 'category_id', 'unit', 'stock_quantity']);
            $data['is_available'] = $request->boolean('is_available');
            if ($request->hasFile('image')) {
                if ($item->image) Storage::disk('public')->delete($item->image);
                $data['image'] = $request->file('image')->store('products', 'public');
            }
            $item->update($data);
        } else {
            $request->validate(['name' => 'required|string|max:255', 'price' => 'required|numeric|min:0']);
            $restaurant = $this->ensureRestaurantProfile($user);
            $item       = MenuItem::whereHas('menu', fn($q) => $q->where('restaurant_id', $restaurant->id))->findOrFail($id);

            $data = $request->only(['name', 'description', 'price', 'category_id']);
            $data['is_available'] = $request->boolean('is_available');
            if ($request->hasFile('image')) {
                if ($item->image) Storage::disk('public')->delete($item->image);
                $data['image'] = $request->file('image')->store('menu_items', 'public');
            }
            $item->update($data);
        }

        return redirect()->route('dashboard.products')->with('success', 'Article mis à jour !');
    }

    public function destroyProduct($id)
    {
        $user = Auth::user();

        if ($user->user_type === 'producer') {
            $producer = $this->ensureProducerProfile($user);
            $item     = Product::where('producer_id', $producer->id)->findOrFail($id);
        } else {
            $restaurant = $this->ensureRestaurantProfile($user);
            $item       = MenuItem::whereHas('menu', fn($q) => $q->where('restaurant_id', $restaurant->id))->findOrFail($id);
        }

        if ($item->image) Storage::disk('public')->delete($item->image);
        $item->delete();

        return redirect()->route('dashboard.products')->with('success', 'Article supprimé.');
    }

    // =========================================================
    //   ORDERS
    // =========================================================
    public function orders()
    {
        $user = Auth::user();
        $canManageActions = false;

        if ($user->user_type === 'admin') {
            $orders = Order::with(['customer', 'seller', 'items.product', 'items.menuItem'])->latest()->paginate(20);
            $canManageActions = true;
        } elseif ($this->isSeller($user)) {
            $orders = Order::where('seller_id', $user->id)
                ->with(['customer', 'items.product', 'items.menuItem'])
                ->latest()
                ->paginate(20);
            $canManageActions = true;
        } else {
            $orders = Order::where('customer_id', $user->id)
                ->with(['seller', 'items.product', 'items.menuItem'])
                ->latest()
                ->paginate(20);
        }

        return view('dashboard.orders', compact('orders', 'canManageActions'));
    }

    public function showOrder($id)
    {
        $user  = Auth::user();
        $order = Order::with(['customer', 'seller', 'items.product', 'items.menuItem', 'deliveryAddress', 'delivery.deliveryAgent.user'])
            ->findOrFail($id);

        // Security check
        if ($user->user_type !== 'admin' && $order->customer_id !== $user->id && $order->seller_id !== $user->id) {
            abort(403);
        }

        return view('dashboard.orders.show', compact('order'));
    }

    public function confirmOrder($id)
    {
        $user  = Auth::user();
        $order = Order::findOrFail($id);

        if ($order->seller_id !== $user->id && $user->user_type !== 'admin') abort(403);

        $order->update(['status' => 'confirmed']);

        return redirect()->back()->with('success', 'Commande confirmée. Préparation en cours !');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,preparing,ready,delivering,delivered,cancelled']);
        $user  = Auth::user();
        $order = Order::findOrFail($id);

        if ($order->seller_id !== $user->id && $user->user_type !== 'admin') abort(403);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Statut de la commande mis à jour.');
    }

    // =========================================================
    //   DELIVERIES
    // =========================================================
    public function pickupDelivery($id)
    {
        $agent    = Auth::user()->deliveryAgent;
        $delivery = Delivery::where('delivery_agent_id', $agent?->id)->findOrFail($id);
        $delivery->update(['status' => 'picked_up']);
        return redirect()->back()->with('success', 'Colis récupéré !');
    }

    public function completeDelivery($id)
    {
        $agent    = Auth::user()->deliveryAgent;
        $delivery = Delivery::where('delivery_agent_id', $agent?->id)->findOrFail($id);
        $delivery->update(['status' => 'delivered']);
        if ($delivery->order) {
            $delivery->order->update(['status' => 'delivered', 'payment_status' => $delivery->order->payment_method === 'cash_on_delivery' ? 'paid' : $delivery->order->payment_status]);
        }
        return redirect()->back()->with('success', 'Livraison confirmée ! Bravo.');
    }

    // =========================================================
    //   SETTINGS
    // =========================================================
    public function settings()
    {
        $user = Auth::user();
        if (!$this->isSeller($user)) {
            return redirect()->route('dashboard.index');
        }

        $profile = $user->user_type === 'producer'
            ? $this->ensureProducerProfile($user)
            : $this->ensureRestaurantProfile($user);

        return view('dashboard.settings', compact('profile'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        if (!$this->isSeller($user)) abort(403);

        $request->validate([
            'name_or_farm' => 'required|string|max:255',
            'location'     => 'nullable|string|max:255',
            'description'  => 'nullable|string|max:2000',
        ]);

        if ($user->user_type === 'producer') {
            $profile = $this->ensureProducerProfile($user);
            $profile->update([
                'farm_name'   => $request->name_or_farm,
                'location'    => $request->location,
                'description' => $request->description,
            ]);
        } else {
            $profile = $this->ensureRestaurantProfile($user);
            $profile->update([
                'name'        => $request->name_or_farm,
                'location'    => $request->location,
                'description' => $request->description,
            ]);
        }

        return redirect()->route('dashboard.settings')->with('success', 'Profil mis à jour avec succès !');
    }

    // =========================================================
    //   ADMIN — Utilisateurs
    // =========================================================
    public function adminUsers(Request $request)
    {
        if (Auth::user()->user_type !== 'admin') abort(403);

        $query = User::query()->latest();

        if ($request->filled('type')) {
            $query->where('user_type', $request->type);
        }

        if ($request->filled('verified')) {
            $query->where('is_verified', (bool) $request->verified);
        }

        $users = $query->paginate(20);

        return view('dashboard.admin.users', compact('users'));
    }

    public function verifyUser($id)
    {
        if (Auth::user()->user_type !== 'admin') abort(403);

        $user = User::findOrFail($id);
        $user->update(['is_verified' => true]);

        return redirect()->back()->with('success', "{$user->name} a été vérifié(e) avec succès.");
    }

    public function deleteUser($id)
    {
        if (Auth::user()->user_type !== 'admin') abort(403);
        if ($id == Auth::id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', "Utilisateur supprimé.");
    }

    // =========================================================
    //   HELPERS
    // =========================================================
    private function isSeller(User $user): bool
    {
        return in_array($user->user_type, ['producer', 'restaurant']);
    }

    private function ensureProducerProfile(User $user): Producer
    {
        return Producer::firstOrCreate(
            ['user_id' => $user->id],
            ['farm_name' => $user->name . ' Farm', 'location' => 'À configurer']
        );
    }

    private function ensureRestaurantProfile(User $user): Restaurant
    {
        return Restaurant::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => 'Restaurant ' . $user->name, 'location' => 'À configurer']
        );
    }
}
