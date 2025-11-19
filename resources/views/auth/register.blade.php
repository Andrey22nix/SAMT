@extends('layouts.guest')

@section('content')
<div class="card">
    <h1 class="text-3xl font-bold text-center mb-2 text-gray-800">Crear Cuenta</h1>
    <p class="text-center text-gray-500 mb-8">Regístrate para comenzar</p>

    <!-- Mensajes de error -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="name">Nombre</label>
            <input id="name" class="input" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Tu nombre completo">
        </div>

        <div>
            <label for="email">Correo Electrónico</label>
            <input id="email" class="input" type="email" name="email" value="{{ old('email') }}" required placeholder="tu@email.com">
        </div>

        <div>
            <label for="password">Contraseña</label>
            <input id="password" class="input" type="password" name="password" required placeholder="Mínimo 8 caracteres">
        </div>

        <div>
            <label for="password_confirmation">Confirmar Contraseña</label>
            <input id="password_confirmation" class="input" type="password" name="password_confirmation" required placeholder="Repite tu contraseña">
        </div>

        <button type="submit" class="btn-primary">Registrarse</button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:text-indigo-800 hover:underline">
            Iniciar sesión
        </a>
    </p>
</div>
@endsection
