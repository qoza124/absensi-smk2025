<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  ...string $roles  // Ini akan menangkap semua role yang diizinkan
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah pengguna sudah login
        if (!auth()->check()) {
            // Jika belum login, bisa diarahkan ke halaman login
            // Tapi kita ikuti pola lama Anda, redirect ke 'home'
            Alert::error('Akses Ditolak', 'Anda harus login terlebih dahulu.');
            return redirect()->route('home');
        }

        // 2. Dapatkan role pengguna yang sedang login
        $userRole = auth()->user()->role;

        // 3. Cek apakah role pengguna ada di dalam daftar $roles yang diizinkan
        if (in_array($userRole, $roles)) {
            // 4. Jika diizinkan, lanjutkan request
            return $next($request);
        }

        // 5. Jika tidak diizinkan, tolak dan redirect ke 'home'
        // (Saya standarisasi semua redirect ke 'home' agar konsisten)
        Alert::error('Akses Ditolak', 'Anda tidak memiliki wewenang untuk mengakses laman ini.');
        return redirect()->route('home');
    }
}