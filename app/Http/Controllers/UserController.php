<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\QueryException;


class UserController extends Controller
{

    public function index(User $id)
    {
        $users = User::all();
        //dd($kelas->all());
        return view('app.user', compact('users'));
    }

    public function tambah(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'name' => 'required',
            'password' => 'required',
            'role' => 'required',
        ]);
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password,
            'role' => $request->role,
        ]);

        return redirect('user')->with('sukses', 'Pengguna berhasil ditambahkan!');
    }
    public function edit(Request $request, $id)
    {
        $users = User::find($id);
        $users->name = $request->name;
        $users->username = $request->username;
        $users->role = $request->role;
        $users->update();
        //dd($kelas->all());
        return redirect('user')->with('sukses', 'Pengguna berhasil diubah!');
    }

    public function reset(string $id)
    {
        $users = User::find($id);
        $users->password = '123456*';
        $users->save();
        //dd($kelas->all());
        return redirect('user')->with('sukses', ' Password Pengguna berhasil direset!');
    }

    public function hapus(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return redirect('user')->with('sukses', 'Data pengguna berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect('user')->with('gagal', 'Pengguna terkoneksi dengan data lain');
        }
    }
}
