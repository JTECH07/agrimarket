<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Producer;
use App\Models\Restaurant;

class WebAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect based on user type
            if (Auth::user()->user_type === 'producer' || Auth::user()->user_type === 'restaurant') {
                return redirect()->intended('/dashboard');
            }
            
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'user_type' => 'required|in:customer,producer,restaurant',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'phone' => $request->phone ?? null,
        ]);

        // Création du profil associé en fonction du user_type
        if ($user->user_type === 'producer') {
            Producer::create([
                'user_id' => $user->id,
                'farm_name' => $request->farm_name ?? ($user->name . " Farm"),
                'location' => "À configurer",
            ]);
        } elseif ($user->user_type === 'restaurant') {
            Restaurant::create([
                'user_id' => $user->id,
                'name' => $request->restaurant_name ?? ("Restaurant " . $user->name),
                'location' => "À configurer",
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->user_type === 'producer' || $user->user_type === 'restaurant') {
            return redirect('/dashboard');
        }

        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
