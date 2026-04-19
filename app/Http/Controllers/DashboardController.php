<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\Product;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use App\Models\Delivery;
use App\Models\DeliveryAgent;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stats = [
            'total_sales' => 0,
            'pending_orders' => 0,
            'total_items' => 0,
        ];

        if ($user->user_type === 'producer') {
            $producer = $user->producer;
            if ($producer) {
                $stats['total_items'] = Product::where('producer_id', $producer->id)->count();
            }
            $stats['pending_orders'] = Order::where('seller_id', $user->id)->where('status', 'pending')->count();
            $stats['total_sales'] = Order::where('seller_id', $user->id)->where('payment_status', 'paid')->sum('total');
            $recentOrders = Order::where('seller_id', $user->id)->latest()->take(5)->get();
            return view('dashboard.index', compact('stats', 'recentOrders'));

        } elseif ($user->user_type === 'restaurant') {
            $restaurant = $user->restaurant;
            if ($restaurant) {
                $stats['total_items'] = MenuItem::whereHas('menu', function($q) use ($restaurant) {
                    $q->where('restaurant_id', $restaurant->id);
                })->count();
            }
            $stats['pending_orders'] = Order::where('seller_id', $user->id)->where('status', 'pending')->count();
            $stats['total_sales'] = Order::where('seller_id', $user->id)->where('payment_status', 'paid')->sum('total');
            $recentOrders = Order::where('seller_id', $user->id)->latest()->take(5)->get();
            return view('dashboard.index', compact('stats', 'recentOrders'));

        } elseif ($user->user_type === 'customer') {
            $orders = Order::where('customer_id', $user->id)->with('items')->latest()->get();
            return view('dashboard.customer.index', compact('orders'));

        } elseif ($user->user_type === 'delivery_agent') {
            $agent = $user->deliveryAgent;
            $deliveries = collect();
            if ($agent) {
                $deliveries = Delivery::where('delivery_agent_id', $agent->id)->with('order.deliveryAddress')->latest()->get();
            }
            return view('dashboard.delivery.index', compact('deliveries'));

        } elseif ($user->user_type === 'admin') {
            $stats = [
                'total_users' => User::count(),
                'total_orders' => Order::count(),
                'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            ];
            return view('dashboard.admin.index', compact('stats'));
        }

        return redirect('/');
    }

    public function products()
    {
        $user = Auth::user();
        $categories = Category::all();

        if ($user->user_type === 'producer') {
            $items = Product::where('producer_id', $user->producer->id)->latest()->get();
        } else {
            $items = MenuItem::whereHas('menu', function($q) use ($user) {
                $q->where('restaurant_id', $user->restaurant->id);
            })->latest()->get();
        }

        return view('dashboard.products', compact('items', 'categories'));
    }

    public function createProduct()
    {
        $categories = Category::all();
        $user = Auth::user();

        if ($user->user_type === 'restaurant') {
            $menu = $user->restaurant->menus()->first();
            if (!$menu) {
                $menu = Menu::create(['restaurant_id' => $user->restaurant->id, 'name' => 'Menu Principal']);
            }
        }

        return view('dashboard.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($user->user_type === 'producer') {
            $rules['unit'] = 'required|string|max:50';
            $rules['stock_quantity'] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('articles', 'public');
        }

        if ($user->user_type === 'producer') {
            Product::create(array_merge($validated, [
                'producer_id' => $user->producer->id,
                'image' => $imagePath,
                'is_available' => true,
            ]));
        } elseif ($user->user_type === 'restaurant') {
            $menu = $user->restaurant->menus()->first();
            MenuItem::create(array_merge($validated, [
                'menu_id' => $menu->id,
                'image' => $imagePath,
                'is_available' => true,
            ]));
        }

        return redirect()->route('dashboard.products')->with('success', 'Article ajouté avec succès !');
    }

    public function editProduct($id)
    {
        $user = Auth::user();
        $categories = Category::all();
        
        if ($user->user_type === 'producer') {
            $item = Product::where('id', $id)->where('producer_id', $user->producer->id)->firstOrFail();
        } else {
            $item = MenuItem::where('id', $id)->whereHas('menu', function($q) use ($user) {
                $q->where('restaurant_id', $user->restaurant->id);
            })->firstOrFail();
        }

        return view('dashboard.products.edit', compact('item', 'categories'));
    }

    public function updateProduct(Request $request, $id)
    {
        $user = Auth::user();
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($user->user_type === 'producer') {
            $rules['unit'] = 'required|string|max:50';
            $rules['stock_quantity'] = 'required|integer|min:0';
            $item = Product::where('id', $id)->where('producer_id', $user->producer->id)->firstOrFail();
        } else {
            $item = MenuItem::where('id', $id)->whereHas('menu', function($q) use ($user) {
                $q->where('restaurant_id', $user->restaurant->id);
            })->firstOrFail();
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('image_file')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $validated['image'] = $request->file('image_file')->store('articles', 'public');
        }

        $item->update($validated);

        return redirect()->route('dashboard.products')->with('success', 'Article mis à jour !');
    }

    public function destroyProduct($id)
    {
        $user = Auth::user();
        if ($user->user_type === 'producer') {
            $item = Product::where('id', $id)->where('producer_id', $user->producer->id)->firstOrFail();
        } else {
            $item = MenuItem::where('id', $id)->whereHas('menu', function($q) use ($user) {
                $q->where('restaurant_id', $user->restaurant->id);
            })->firstOrFail();
        }

        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();
        return back()->with('success', 'Article supprimé !');
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = Order::where('seller_id', $user->id)->with('customer')->latest()->get();
        return view('dashboard.orders', compact('orders'));
    }

    public function confirmOrder($id)
    {
        try {
            $user = Auth::user();
            $order = Order::where('id', $id)->where('seller_id', $user->id)->firstOrFail();
            
            if ($order->status !== 'pending') {
                return back()->with('error', 'Cette commande a déjà été traitée.');
            }

            $order->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Trouver un agent disponible
            $agent = DeliveryAgent::where('is_available', true)->first();
            
            // Créer la livraison
            Delivery::create([
                'order_id' => $order->id,
                'delivery_agent_id' => $agent ? $agent->id : null,
                'status' => $agent ? 'assigned' : 'pending',
                'tracking_number' => 'TRK-' . strtoupper(bin2hex(random_bytes(4))),
            ]);

            return back()->with('success', 'Commande validée ! ' . ($agent ? 'Un livreur a été assigné.' : 'En attente d\'un livreur disponible.'));

        } catch (\Exception $e) {
            \Log::error('Erreur confirmation commande: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la validation : ' . $e->getMessage());
        }
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $user = Auth::user();
        $order = Order::where('id', $id)->where('seller_id', $user->id)->firstOrFail();
        $request->validate(['status' => 'required|in:preparing,shipping,completed,cancelled']);
        $order->update(['status' => $request->status]);
        return back()->with('success', 'Statut mis à jour.');
    }

    public function settings()
    {
        $user = Auth::user();
        $profile = $user->user_type === 'producer' ? $user->producer : $user->restaurant;
        return view('dashboard.settings', compact('user', 'profile'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $profile = $user->user_type === 'producer' ? $user->producer : $user->restaurant;

        $request->validate([
            'name_or_farm' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($user->user_type === 'producer') {
            $profile->update([
                'farm_name' => $request->name_or_farm,
                'location' => $request->location,
                'description' => $request->description,
            ]);
        } else {
            $profile->update([
                'name' => $request->name_or_farm,
                'location' => $request->location,
                'description' => $request->description,
            ]);
        }

        return back()->with('success', 'Profil mis à jour avec succès !');
    }
}
