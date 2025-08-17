<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Jangan lupa import Auth

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login DAN rolenya adalah 'admin'
        if (Auth::check() && Auth::user()->role == 'admin') {
            // Jika ya, lanjutkan request ke tujuan
            return $next($request);
        }

        // Jika tidak, kembalikan ke halaman dashboard dengan pesan error
        // abort(403, 'Unauthorized action.'); // Alternatif lain
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman ini.');
    }
}
