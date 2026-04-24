<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\MenuItem;
use App\Scopes\ProducerScope;
use App\Scopes\RestaurantScope;

class WebController extends Controller
{
    /**
     * Page d'accueil : produits et plats en vedette.
     */
    public function index()
    {
        $featuredProducts = Product::withoutGlobalScope(ProducerScope::class)
            ->with(['producer', 'category'])
            ->where('is_available', true)
            ->latest()
            ->take(4)
            ->get();

        $featuredMenus = MenuItem::withoutGlobalScope(RestaurantScope::class)
            ->with(['menu.restaurant', 'category'])
            ->where('is_available', true)
            ->latest()
            ->take(4)
            ->get();

        return view('welcome', compact('featuredProducts', 'featuredMenus'));
    }

    /**
     * Catalogue universel avec filtrage et recherche.
     */
    public function catalog(Request $request)
    {
        $type   = $request->query('type', 'all');
        $search = $request->query('search', '');

        $products = collect();
        $menus    = collect();

        if ($type === 'all' || $type === 'products') {
            $query = Product::withoutGlobalScope(ProducerScope::class)
                ->with(['producer', 'category'])
                ->where('is_available', true);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('producer', fn($q) => $q->where('farm_name', 'like', "%{$search}%"));
                });
            }

            $products = $query->latest()->get();
        }

        if ($type === 'all' || $type === 'menus') {
            $query = MenuItem::withoutGlobalScope(RestaurantScope::class)
                ->with(['menu.restaurant', 'category'])
                ->where('is_available', true);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('menu.restaurant', fn($q) => $q->where('name', 'like', "%{$search}%"));
                });
            }

            $menus = $query->latest()->get();
        }

        // Fusion et tri
        $items = $products->merge($menus)->sortByDesc('created_at');

        return view('catalog', compact('items', 'type', 'search'));
    }

    /**
     * Fiche produit ou plat.
     */
    public function show($type, $id)
    {
        if ($type === 'product') {
            $item = Product::withoutGlobalScope(ProducerScope::class)
                ->with(['producer', 'category'])
                ->findOrFail($id);
        } else {
            $item = MenuItem::withoutGlobalScope(RestaurantScope::class)
                ->with(['menu.restaurant', 'category'])
                ->findOrFail($id);
        }

        return view('product.show', compact('item', 'type'));
    }
}
