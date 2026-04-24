<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Producer;
use App\Models\Restaurant;
use App\Models\DeliveryAgent;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:customer,producer,restaurant,delivery_agent',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'is_active' => true,
            'is_verified' => false,
        ]);

        // Créer l'entité spécifique selon le rôle
        if ($user->user_type === 'producer') {
            Producer::create([
                'user_id' => $user->id,
                'farm_name' => $request->input('farm_name', 'Ferme de ' . $user->name),
            ]);
        } elseif ($user->user_type === 'restaurant') {
            Restaurant::create([
                'user_id' => $user->id,
                'name' => $request->input('restaurant_name', 'Restaurant de ' . $user->name),
            ]);
        } elseif ($user->user_type === 'delivery_agent') {
            DeliveryAgent::create([
                'user_id' => $user->id,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $relation = match ($user->user_type) {
            'producer' => 'producer',
            'restaurant' => 'restaurant',
            'delivery_agent' => 'deliveryAgent',
            default => null,
        };

        if ($relation) {
            $user->load($relation);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone|string|email',
            'phone' => 'required_without:email|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('password');
        if ($request->has('email')) {
            $credentials['email'] = $request->email;
        } else {
            $credentials['phone'] = $request->phone;
        }

        if (!auth()->attempt($credentials)) {
            throw ValidationException::withMessages([
                'auth' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        $user = User::where($request->has('email') ? 'email' : 'phone', $request->has('email') ? $request->email : $request->phone)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        if ($user->user_type === 'producer') {
            $user->load('producer');
        } elseif ($user->user_type === 'restaurant') {
            $user->load('restaurant');
        } elseif ($user->user_type === 'delivery_agent') {
            $user->load('deliveryAgent');
        }

        return response()->json($user);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }
}
