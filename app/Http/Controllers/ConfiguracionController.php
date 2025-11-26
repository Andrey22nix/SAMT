<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    /**
     * Show QR configuration page.
     */
    public function qr()
    {
        $qrImageUrl = $this->getQRImageUrl();
        
        return view('configuracion.qr', compact('qrImageUrl'));
    }

    /**
     * Upload QR image.
     */
    public function uploadQR(Request $request)
    {
        $request->validate([
            'qr_image' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:5120'], // 5MB max
        ]);

        try {
            // Asegurar que el directorio existe
            if (!Storage::disk('public')->exists('qr')) {
                Storage::disk('public')->makeDirectory('qr');
            }

            // Eliminar imagen anterior si existe
            $oldPath = DB::table('configuraciones')
                ->where('clave', 'qr_image_path')
                ->value('valor');

            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            // Guardar nueva imagen
            $path = $request->file('qr_image')->store('qr', 'public');

            // Guardar o actualizar en configuración
            DB::table('configuraciones')->updateOrInsert(
                ['clave' => 'qr_image_path'],
                [
                    'valor' => $path,
                    'descripcion' => 'Ruta de la imagen del código QR para pagos',
                    'updated_at' => now(),
                    'created_at' => DB::table('configuraciones')->where('clave', 'qr_image_path')->exists() 
                        ? DB::table('configuraciones')->where('clave', 'qr_image_path')->value('created_at')
                        : now()
                ]
            );

            return redirect()->route('configuracion.qr')->with('success', 'Imagen del código QR actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al subir la imagen: ' . $e->getMessage()]);
        }
    }

    /**
     * Get QR image URL from configuration.
     */
    private function getQRImageUrl()
    {
        $qrPath = DB::table('configuraciones')
            ->where('clave', 'qr_image_path')
            ->value('valor');

        if ($qrPath && Storage::disk('public')->exists($qrPath)) {
            // Usar asset() directamente para generar la URL correcta
            // Esto asegura que funcione independientemente de la configuración del servidor
            return asset('storage/' . $qrPath);
        }

        return null;
    }
}
