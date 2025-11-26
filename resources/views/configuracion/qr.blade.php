@extends('layouts.app')

@section('content')
<div class="min-h-full p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Configuración de Código QR</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <strong class="font-bold">¡Error!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @if(!$qrImageUrl)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6">
                    <p class="font-semibold">⚠️ Advertencia</p>
                    <p class="text-sm mt-1">No hay una imagen de código QR configurada. Por favor, suba una imagen para que los usuarios puedan generar códigos QR durante el proceso de pago.</p>
                </div>
            @endif

            <form action="{{ route('configuracion.qr.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Imagen del Código QR
                    </label>
                    <input type="file" 
                           name="qr_image" 
                           accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-2 text-sm text-gray-500">
                        Formatos aceptados: PNG, JPG, JPEG. Tamaño máximo: 5MB
                    </p>
                </div>

                @if($qrImageUrl)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Imagen Actual
                        </label>
                        <div class="border border-gray-200 rounded-lg p-4 inline-block">
                            <img src="{{ $qrImageUrl }}" 
                                 alt="Código QR actual" 
                                 class="max-w-xs max-h-64 object-contain"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <p class="text-red-600 text-sm mt-2" style="display: none;">Error al cargar la imagen. Por favor, vuelva a subirla.</p>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            URL: <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $qrImageUrl }}</code>
                        </p>
                    </div>
                @endif

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('dashboard') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Guardar Imagen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

