<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\Usuario;
use App\Models\Reserva;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsuariosController extends Controller
{
    public function mostrarLogin()
    {
        if (!session()->has('url.intended')) {
            session(['url.intended' => url()->previous()]);
        }
        return view('usuarios.login');
    }

    public function mostrarRegistro() 
    {
        return view('usuarios.registro');
    }

    public function almacenar(Request $request)
    {
        $request->validate([
            'correo'   => 'required|email|unique:usuarios,correo',
            'password' => ['required', 'min:8', 'max:20'],
            'nombre'   => 'required|string',
        ], [
            'correo.unique' => 'Este correo ya está registrado.',
            'correo.required' => 'La dirección de correo es imprescindible para crear la cuenta.',
            'correo.email'    => 'El formato de correo no es válido.',
            'password.required' => 'Indica tu contraseña.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max'      => 'La contraseña no puede tener más de 20 caracteres.',
            'nombre.required' => 'Indica tu nombre.'
        ]);

        // CORRECCIÓN: Se cambia 'User::create' por 'Usuario::create' para coincidir con tu modelo
        Usuario::create([
            'nombre'    => $request->nombre,
            'apellidos' => $request->apellidos, 
            'correo'    => $request->correo,
            'password'  => Hash::make($request->password),
            'tipo'      => 'cliente',
        ]);

        return redirect('/login')->with('success', '¡Usuario dado de alta!'); 
    }

    public function acceder(Request $request)
    {
        $request->validate([
            'correo'   => 'required|email',
            'password' => 'required', 
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

        // Intentamos el login usando las credenciales personalizadas
        if (Auth::attempt($credenciales, $remember)) {
            $request->session()->regenerate();

            //nuevo-ini
            $urlIntended = redirect()->intended('/')->getTargetUrl();
            if (str_contains($urlIntended, '/admin') && Auth::user()->tipo !== 'admin') {
                return redirect('/');
            }
            //nuevo-fin
            return redirect()->intended('/');
        }

        return back()->withErrors(['error' => 'El correo o la contraseña no coinciden.']);
    }

    public function editar()
    {
        $usuario = Auth::user();
        return view('usuarios.editar', compact('usuario'));
    }

    public function actualizar(Request $request)
    {
        $request->validate([
            'password' => ['nullable', 'sometimes', Password::min(8)->max(8)->letters()->numbers()->symbols()->mixedCase()]
        ], [
            'password' => 'La contraseña debe tener exactamente 8 caracteres, incluir mayúsculas, minúsculas, números y símbolos.',
        ]);
    
        $usuario = Auth::user();
        $usuario->nombre = $request->nombre;
        $usuario->apellidos = $request->apellidos;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return back()->with('success', '¡Perfil actualizado correctamente!');
    }

    public function historial()
    {
        $reservas = DB::table('reservas')
            ->join('eventos', 'reservas.id_evento', '=', 'eventos.id_eventos')
            ->join('salas', 'eventos.id_sala', '=', 'salas.id_sala')
            ->join('peliculas', 'eventos.id_pelicula', '=', 'peliculas.id_pelicula')
            ->where('reservas.id_usuario', Auth::id())
            ->select(
                'reservas.id_reserva',
                'reservas.fila',
                'reservas.asiento',
                'reservas.fecha_reserva',
                'reservas.fecha_sesion',
                'eventos.id_eventos',
                'eventos.nombre as evento_nombre',
                'eventos.precio',
                'eventos.fecha_estreno',
                'reservas.hora_sesion',
                'salas.nombre as sala_nombre',
                'peliculas.titulo as pelicula_titulo',
                'peliculas.imagen as pelicula_imagen',
                'peliculas.genero',
                'peliculas.duracion'
            )
            ->orderByDesc('reservas.fecha_reserva')
            ->get()
            ->groupBy(fn($r) => $r->id_eventos . '_' . $r->fecha_sesion);

        return view('usuarios.historial', compact('reservas'));
    }

    public function salir(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}