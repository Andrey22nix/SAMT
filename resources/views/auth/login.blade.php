@extends('layouts.guest')

@section('content')
<div class="card">
    <h1 class="text-3xl font-bold text-center mb-2 text-gray-800">Iniciar Sesión</h1>
    <p class="text-center text-gray-500 mb-8">Accede a tu cuenta</p>

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

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email">Correo Electrónico</label>
            <input id="email" class="input" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="tu@email.com">
        </div>

        <div>
            <label for="password">Contraseña</label>
            <input id="password" class="input" type="password" name="password" required placeholder="••••••••">
        </div>

        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-600">Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:text-indigo-900" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <button type="submit" class="btn-primary">Iniciar Sesión</button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:text-indigo-800 hover:underline">
            Crear cuenta
        </a>
    </p>
</div>
@endsection
