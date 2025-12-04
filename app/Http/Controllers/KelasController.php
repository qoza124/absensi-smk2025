<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
// JANGAN LUPA TAMBAHKAN INI JIKA ANDA INGIN MENGGUNAKAN RULE
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;


class KelasController extends Controller
{

    public function index(Kelas $id)
    {
        $kelas = Kelas::all();
        $rolesToDisplay = ['Wali Kelas'];

        // 2. Gunakan 'whereIn' untuk memfilter dan 'get' untuk mengambil data
        $users = User::whereIn('role', $rolesToDisplay)->get();
        //dd($kelas->all());
        return view('app.kelas', compact('kelas', 'users'));
    }

    public function tambah(Request $request)
    {

        $request->validate([
            'name' => 'required|unique:kelas,name|min:3|max:10',
            'users_id' => 'required',
        ]);
        Kelas::create([
            'name' => $request->name,
            'users_id' => $request->users_id,
        ]);

        // Fungsi tambah tetap redirect, karena modalnya beda
        return redirect('kelas')->with('sukses', 'Kelas Berhasil ditambahkan.');
    }


    // ==========================================================
    // PERUBAHAN BESAR PADA FUNGSI EDIT DI BAWAH INI
    // ==========================================================
    public function edit(Request $request, $id)
    {

        // 1. Buat Validator secara manual
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'min:3',
                'max:10',
                // Rule unique yang mengabaikan ID saat ini
                Rule::unique('kelas')->ignore($id),
            ],
            'users_id' => 'required',
        ], [
            // Pesan custom Anda
            'name.required' => 'Nama kelas wajib diisi.',
            'name.unique' => 'Nama kelas tersebut sudah digunakan.',
            'name.min' => 'Nama kelas minimal 3 karakter.',
            'users_id.required' => 'Wali kelas wajib dipilih.',
        ]);

        // 2. Cek jika validasi GAGAL
        if ($validator->fails()) {
            // Kembalikan response JSON berisi errors dengan status 422
            return response()->json([
                'errors' => $validator->errors()
            ], 422); // 422 = Unprocessable Entity
        }

        // 3. Jika validasi LOLOS, update data
        $kelas = Kelas::find($id);
        $kelas->name = $request->name;
        $kelas->users_id = $request->users_id;
        $kelas->update();

        // 4. Kembalikan response SUKSES dalam format JSON
        return response()->json([
            'success' => 'Kelas berhasil diubah!'
        ], 200); // 200 = OK
    }
    // ==========================================================
    // BATAS PERUBAHAN FUNGSI EDIT
    // ==========================================================


    public function hapus(string $id)
    {
        $kelas = Kelas::find($id);
        $kelas->delete();
        // Fungsi hapus tetap redirect
        return redirect('kelas');
    }
}