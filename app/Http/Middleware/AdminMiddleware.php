<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi akses panel admin hanya untuk user dengan role 'admin'
 * (Owner Jukase Project), sesuai Tabel 1.4 (Definisi Aktor) dan
 * NF-02 (Keamanan: autentikasi & otorisasi).
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Admin (Owner Jukase Project).');
        }

        return $next($request);
    }
}
