@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Navegación --}}
    <a href="{{ route('home') }}" class="text-blue-500 hover:underline mb-8 inline-block flex items-center gap-1 transition-colors">
        &larr; <span class="text-sm">Volver a la cartelera</span>
    </a>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 items-start">
        {{-- Columna Izquierda: Póster --}}
        <div class="md:col-span-1">
            <div class="sticky top-8">
                <img src="{{ $pelicula->imagen_url ?? 'https://via.placeholder.com/500x750?text=Sin+Poster' }}" 
                     alt="{{ $pelicula->titulo }}" 
                     class="w-full rounded-2xl shadow-2xl border-4 border-white object-cover aspect-[2/3]">
            </div>
        </div>

        {{-- Columna Derecha: Información --}}
        <div class="md:col-span-2 space-y-8">
            <div class="border-b pb-6 text-center md:text-left">
                <h1 class="text-5xl font-extrabold text-gray-950 mb-4 tracking-tighter leading-tight">{{ $pelicula->titulo }}</h1>
                <div class="flex items-center justify-center md:justify-start gap-4 text-sm">
                    <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        {{ $pelicula->genero }}
                    </span>
                    <span class="text-gray-400">|</span>
                    <span class="text-gray-600 font-medium">{{ $pelicula->duracion }} minutos</span>
                </div>
            </div>

            {{-- Sinopsis --}}
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-3 uppercase tracking-wide">Sinopsis</h3>
                <p class="text-gray-700 leading-relaxed text-lg italic bg-gray-50 p-6 rounded-xl border border-dashed border-gray-200">
                    {{ $pelicula->sinopsis ?? 'No hay una descripción disponible para esta película actualmente.' }}
                </p>
            </div>

            {{-- SECCIÓN DE RESERVA: DISEÑO DE ALTA PROFUNDIDAD --}}
            <div class="mt-16 py-16 px-6 bg-gray-100 rounded-[3rem] shadow-[inset_0_2px_10px_rgba(0,0,0,0.05)] border border-gray-200 flex flex-col items-center justify-center text-center gap-10">
                <div class="space-y-2">
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight">¿Estás listo?</h2>
                    <p class="text-gray-500 text-xl">Haz clic abajo para comenzar tu experiencia</p>
                </div>
                
                {{-- 
                    BOTÓN CON PROFUNDIDAD EXTREMA:
                    - Texto en gris oscuro (#1a202c / gray-900) para contraste.
                    - Sombra multinivel (negra para profundidad, azul para resplandor).
                    - Borde interno (ring) para simular biselado.
                --}}
                <button type="button" 
                   class="group relative inline-flex items-center justify-center bg-blue-600 text-gray-900 text-5xl font-black py-10 px-24 rounded-full 
                          shadow-[0_20px_0_0_#1e40af,0_25px_50px_-12px_rgba(0,0,0,0.5)] 
                          transition-all duration-150 transform 
                          hover:translate-y-1 hover:shadow-[0_15px_0_0_#1e40af,0_20px_40px_-12px_rgba(0,0,0,0.4)] 
                          active:translate-y-4 active:shadow-[0_4px_0_0_#1e40af,0_10px_20px_-12px_rgba(0,0,0,0.3)] 
                          focus:outline-none flex items-center gap-8 border-t-4 border-blue-400">
                    <span class="tracking-tighter uppercase">Reservar</span>
                </button>

            </div>
        </div>
    </div>
</div>

<style>
    /* Efecto de cuerpo para que el botón se vea como un bloque sólido */
    button {
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.2);
        -webkit-font-smoothing: antialiased;
    }
    
    /* Pequeña vibración sutil para invitar al clic */
    @keyframes wiggle {
        0%, 100% { transform: rotate(0deg) translateY(4px); }
        50% { transform: rotate(0.5deg) translateY(0px); }
    }
    .group {
        animation: wiggle 3s ease-in-out infinite;
    }
</style>
@endsection