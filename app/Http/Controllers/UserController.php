<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;//Para las validaciones de la contraseña
use App\Models\User; 
use Illuminate\Support\Facades\Hash; //Esto permite encriptar la clave
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login()
        {
            if (!session()->has('url.intended')) {
                session(['url.intended' => url()->previous()]);
            }
            return view('users.login');
        }

    public function create() 
        {
            return view('users.create');
        }

    public function store(Request $request)
        {
        //dd($request->all());
        $request->validate([
            'correo'   => 'required|email|unique:usuarios,correo',
            'password' => ['required', Password::min(8)->max(8)->letters()->numbers()->symbols()->mixedCase(),'confirmed'],
            'nombre'     => 'required|string',
            ], [
                'correo.unique' => 'Este correo ya está registrado.',
                'correo.required' => 'La dirección de correo es imprescindible para crear la cuenta.',
                'correo.email'    => 'El formato de correo no es válido.',
                'password' => 'La contraseña debe tener exactamente 8 caracteres, incluir mayúsculas, minúsculas, números y símbolos.',
                'password.required' => 'Indica tu contraseña.',
                'nombre.required' => 'Indica tu nombre.'
                ]);

        User::create([
                'nombre'    => $request->nombre,
                'apellidos' => $request->apellidos, 
                'correo'    => $request->correo,
                'password'  => Hash::make($request->password),
                'tipo'      => 'cliente',
            ]);

        return redirect('/login')->with('success', '¡Usuario dado de alta!'); 
        }

    public function authenticate(Request $request)
        {
       $request->validate([
            'correo'   => 'required|email',
            'password' => 'required', 
//            'password' => ['required', Password::min(8)->max(8)->letters()->numbers()->symbols()->mixedCase()],
            ], [
                'correo.required' => 'La dirección de correo es imprescindible para entrar en la cuenta.',
                'correo.email'    => 'El formato de correo no es válido.',
                'password.required' => 'Indica tu contraseña.'
        ]);

        $remember = $request->has('remember');

        $credenciales = [
            'correo'   => $request->correo,
            'password' => $request->password,
        ];

        //Intentamos el login
        if (Auth::attempt($credenciales,$remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard'); // Cámbialo por tu ruta de bienvenida
        }

        //Si falla, volvemos atrás con error
        return back()->withErrors(['error' => 'El correo o la contraseña no coinciden.',
        ]);
    }

    public function edit()
        {
            // Obtenemos los datos del usuario identificado
            $usuario = Auth::user();
            return view('users.edit', compact('usuario'));
        }

public function update(Request $request)
    {
        $request->validate([
            'password' => ['nullable', 'sometimes', Password::min(8)->max(8)->letters()->numbers()->symbols()->mixedCase()],
            'password_confirmation' => 'same:password'
        ], [
            'password_confirmation.same' => 'Las contraseñas no coinciden.',
            'password' => 'La contraseña debe tener exactamente 8 caracteres, incluir mayúsculas, minúsculas, números y símbolos.',
        ]);
    
        // 1. Identificamos al usuario
        $usuario = Auth::user();

        // 2. Actualizamos los campos de texto
        $usuario->nombre = $request->nombre;
        $usuario->apellidos = $request->apellidos;

        // 3. Lógica de la contraseña: 
        // Solo si el usuario ha escrito algo en el campo password, la cambiamos.
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        // 4. Guardamos los cambios en la base de datos
        $usuario->save();

        // 5. Redirigimos atrás con un mensaje de éxito
        return back()->with('success', '¡Perfil actualizado correctamente!');
    }

    public function logout(Request $request)
        {
            // 1. Cierra la sesión en el sistema Auth
            Auth::logout();

            // 2. Invalida la sesión actual del usuario
            $request->session()->invalidate();

            // 3. Regenera el token CSRF para el próximo que use el PC
            $request->session()->regenerateToken();

            // 4. Redirige a donde quieras (normalmente al login o inicio)
            return redirect('/login');
        }
}