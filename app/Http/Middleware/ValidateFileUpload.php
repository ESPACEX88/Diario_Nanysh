<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateFileUpload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validar archivos si existen en la request
        if ($request->hasFile('image') || $request->hasFile('photo') || $request->hasFile('file')) {
            $file = $request->file('image') ?? $request->file('photo') ?? $request->file('file');
            
            // Validar tipo MIME
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return back()->withErrors([
                    'file' => 'Tipo de archivo no permitido. Solo se permiten imágenes (JPEG, PNG, GIF, WebP).'
                ]);
            }
            
            // Validar tamaño (5MB máximo)
            $maxSize = 5 * 1024 * 1024; // 5MB en bytes
            if ($file->getSize() > $maxSize) {
                return back()->withErrors([
                    'file' => 'El archivo es demasiado grande. El tamaño máximo es 5MB.'
                ]);
            }
        }
        
        return $next($request);
    }
}

