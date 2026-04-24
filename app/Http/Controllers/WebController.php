<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class WebController extends Controller
{
    /**
     * Affiche la page d'accueil.
     */
    public function index()
    {
        // On récupère les deux types d'articles récents pour la page d'accueil
        $featuredProducts = Product::with(['category', 'producer'])
            ->latest()
            ->take(8)
            ->get();

        $featuredMenus = \App\Models\MenuItem::with(['category', 'menu.restaurant'])
            ->latest()
            ->take(4)
            ->get();

        return view('welcome', compact('featuredProducts', 'featuredMenus'));
    }

    /**
     * Affiche le catalogue.
     */
    public function catalog(Request $request)
    {
        $categories = Category::all();
        $type = $request->query('type', 'all');
        $search = $request->query('search');

        $products = collect();
        $menuItems = collect();

        // 1. Récupération des Produits (Agrimarket)
        if ($type === 'all' || $type === 'products') {
            $query = Product::with(['category', 'producer']);
            if ($search) {
                $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
            }
            $products = $query->latest()->get();
        }

        // 2. Récupération des Menus (Resto)
        if ($type === 'all' || $type === 'menus') {
            $query = \App\Models\MenuItem::with(['category', 'menu.restaurant']);
            if ($search) {
                $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
            }
            $menuItems = $query->latest()->get();
        }

        // 3. Fusion et Pagination manuelle (ou affichage simple)
        $allItems = $products->merge($menuItems)->sortByDesc('created_at');
        
        // Pour la pagination manuelle simple sur collection
        $page = $request->query('page', 1);
        $perPage = 12;
        $items = new \Illuminate\Pagination\LengthAwarePaginator(
            $allItems->forPage($page, $perPage),
            $allItems->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('catalog', compact('items', 'categories', 'type'));
    }

    /**
     * Affiche un produit ou un menu spécifique.
     */
    public function show($type, $id)
    {
        if ($type === 'product') {
            $item = Product::with(['category', 'producer'])->findOrFail($id);
        } else {
            $item = \App\Models\MenuItem::with(['category', 'menu.restaurant'])->findOrFail($id);
        }

        return view('product.show', compact('type', 'item'));
    }
}