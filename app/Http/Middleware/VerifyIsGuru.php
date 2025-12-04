<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class VerifyIsGuru
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //$role_name = $request->user()->role;
        //$admin_role = User::where('role', 'Admin')->first()->role;
        if (auth()->check() && auth()->user()->role === 'Guru') {
            return $next($request);
        }elseif (auth()->user()->role === 'Admin' || auth()->user()->role === 'Wali Kelas'|| auth()->user()->role === 'Kesiswaan') {
            Alert::error('Akses Ditolak', 'Anda tidak bisa mengakses laman ini');
            return redirect()->route('homeguru');
        }else{

        return $next($request);

        }
    }
}
