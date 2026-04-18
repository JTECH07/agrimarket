<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Menu;
use App\Scopes\RestaurantScope;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    /**
     * Marketplace public: liste de tous les plats
     */
    public function index(Request $request)
    {
        $query = MenuItem::where('is_available', true)->with('menu.restaurant', 'category');

        if ($request->has('restaurant_id')) {
            $query->whereHas('menu', function($q) use ($request) {
                $q->where('restaurant_id', $request->restaurant_id);
            });
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->paginate(15));
    }

    /**
     * Store (Créer un plat pour le restaurant)
     */
    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->user_type !== 'restaurant') {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        // Vérifier que le menu appartient bien au restaurant
        $menu = Menu::findOrFail($validated['menu_id']); // Global scope s'applique sur Menu

        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        $validated['is_available'] = true;

        $menuItem = MenuItem::create($validated);

        return response()->json($menuItem, 201);
    }

    public function show($id)
    {
        $menuItem = MenuItem::with('menu.restaurant', 'category')->findOrFail($id);
        return response()->json($menuItem);
    }

    public function update(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->user_type !== 'restaurant') {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $menuItem = MenuItem::findOrFail($id);
        // Vérification de sécurité: Le plat appartient bien au restaurant?
        $menu = Menu::findOrFail($menuItem->menu_id); 

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $menuItem->update($validated);

        return response()->json($menuItem);
    }

    public function destroy($id)
    {
        // Seuls les restaurants peuvent supprimer leurs plats. Vérification simple.
        $menuItem = MenuItem::findOrFail($id);
        $menu = Menu::findOrFail($menuItem->menu_id); // Échoue si le menu n'est pas au restaurant (GlobalScope)
        
        $menuItem->delete();
        return response()->json(['message' => 'Plat supprimé.']);
    }
}
