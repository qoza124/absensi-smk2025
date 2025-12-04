<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        return view('app.myprofil');
    }

    public function updateData(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id), // Username harus unik, kecuali milik sendiri
            ],
        ], [
            'username.unique' => 'Username sudah digunakan oleh pengguna lain.',
        ]);

        /** @var \App\Models\User $user */
        $user->name = $request->name;
        $user->username = $request->username;
        $user->save();

        return redirect()->back()->with('sukses', 'Profil berhasil diperbarui!');
    }

    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'foto.required' => 'Silakan pilih foto terlebih dahulu.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $user = Auth::user();

        if ($request->hasFile('foto')) {
            // 1. Hapus foto lama jika ada
            // Kita gunakan disk 'public' agar targetnya tepat
            if ($user->foto && Storage::disk('public')->exists('profil/' . $user->foto)) {
                Storage::disk('public')->delete('profil/' . $user->foto);
            }

            // 2. Simpan foto baru
            $filename = $request->file('foto')->hashName();

            // PERUBAHAN PENTING DI SINI:
            // Parameter ke-3 adalah 'public', memaksa file masuk ke storage/app/public/profil
            $request->file('foto')->storeAs('profil', $filename, 'public');

            // 3. Update database
            /** @var \App\Models\User $user */
            $user->foto = $filename;
            $user->save();

            return redirect()->back()->with('sukses', 'Foto profil berhasil diperbarui!');
        }

        return redirect()->back()->with('error', 'Gagal mengupload foto.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed', // field konfirmasi harus bernama 'new_password_confirmation'
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();

        // Cek apakah password lama benar
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Password saat ini salah!');
        }

        // Update password
        /** @var \App\Models\User $user */
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('sukses', 'Password berhasil diubah!');
    }
}