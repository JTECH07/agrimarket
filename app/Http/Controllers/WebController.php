<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\MenuItem;
use App\Scopes\ProducerScope;
use App\Scopes\RestaurantScope;

class WebController extends Controller
{
    public function index()
    {
        // Pour la page d'accueil, prenons 4 produits récents et 4 plats
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

    public function catalog(Request $request)
    {
        $type = $request->query('type', 'all'); // 'products', 'menus' ou 'all'
        
        $products = collect();
        $menus = collect();

        if ($type === 'all' || $type === 'products') {
            $products = Product::withoutGlobalScope(ProducerScope::class)
                ->with(['producer', 'category'])
                ->where('is_available', true)
                ->latest()
                ->get();
        }

        if ($type === 'all' || $type === 'menus') {
            $menus = MenuItem::withoutGlobalScope(RestaurantScope::class)
                ->with(['menu.restaurant', 'category'])
                ->where('is_available', true)
                ->latest()
                ->get();
        }

        // On fusionne les deux collections pour le catalogue universel
        $items = $products->merge($menus)->sortByDesc('created_at');

        return view('catalog', compact('items', 'type'));
    }
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
