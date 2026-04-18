<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Scopes\ProducerScope;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource (Marketplace public).
     */
    public function index(Request $request)
    {
        // Enlève le global scope pour que tout le monde (même un producteur) puisse voir le catalogue complet
        $query = Product::withoutGlobalScope(ProducerScope::class)
                    ->where('is_available', true)
                    ->with('category', 'producer');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->paginate(15));
    }

    /**
     * Store a newly created resource in storage (Only for Producer).
     */
    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->user_type !== 'producer') {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string',
            'stock_quantity' => 'required|integer|min:0',
            'min_order_quantity' => 'integer|min:1',
            'is_organic' => 'boolean',
            'origin' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();

        // L'assignation de producer_id est gérée par le Trait MultitenantProducer
        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::withoutGlobalScope(ProducerScope::class)
                    ->with('category', 'producer', 'images', 'reviews')
                    ->findOrFail($id);

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id); // Global scope appliqué, donc il faut être LE producteur propriétaire

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'unit' => 'string',
            'stock_quantity' => 'integer|min:0',
            'is_available' => 'boolean',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Produit supprimé avec succès']);
    }

    /**
     * Obtenir les propres produits du producteur connecté.
     */
    public function myProducts()
    {
        if (!auth()->check() || auth()->user()->user_type !== 'producer') {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        // Ici, le GlobalScope s'applique par défaut et limite aux produits de ce producteur.
        return response()->json(Product::with('category')->paginate(15));
    }
}
