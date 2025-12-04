<?php

namespace App\Http\Controllers;

use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }
    public function postlogin(Request $request)
    {
        //dd($request->all());
        if (Auth::attempt($request->only('username', 'password'))) {
            $user = Auth::user();
            if ($user->role == 'Admin') {
                return redirect('dashboard')->with('sukses', 'Login Sukses!');
            } elseif ($user->role == 'Wali Kelas') {
                return redirect('absensi')->with('sukses', 'Login Sukses!');
            }
            elseif ($user->role == 'Kesiswaan') {
                return redirect('absensi')->with('sukses', 'Login Sukses!');
            }
            elseif ($user->role == 'Guru') {
                return redirect('absensi')->with('sukses', 'Login Sukses!');
            }
        } else {
            Alert::error('Username atau Password Salah', 'Silahkan Coba lagi atau silahkan menghubungi admin');
            return back();
        }
    }
    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }
}
