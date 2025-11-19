<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIMIT') }} - {{ $title ?? 'Acceso' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                font-family: 'Inter', sans-serif;
            }

            .card {
                background: white;
                border-radius: 16px;
                padding: 40px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            }

            .input {
                width: 100%;
                padding: 14px 16px;
                border-radius: 10px;
                border: 2px solid #e5e7eb;
                margin-top: 8px;
                margin-bottom: 20px;
                transition: all 0.3s;
                font-size: 15px;
            }

            .input:focus {
                border-color: #6366f1;
                outline: none;
                box-shadow: 0 0 0 4px rgba(99,102,241,0.1);
            }

            .btn-primary {
                width: 100%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 14px;
                border-radius: 10px;
                border: none;
                cursor: pointer;
                transition: all 0.3s;
                font-weight: 600;
                font-size: 16px;
                margin-top: 10px;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
            }

            label {
                font-weight: 500;
                color: #374151;
                font-size: 14px;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white text-center mb-2">SIMIT</h1>
                <p class="text-white/80 text-center">Sistema de Gestión de Multas</p>
            </div>

            <div class="w-full sm:max-w-md">
                @yield('content')
            </div>
        </div>
    </body>
</html>
