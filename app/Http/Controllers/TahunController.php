<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tahun;


class TahunController extends Controller
{

    public function index(Tahun $id){
        $tahun = Tahun::all();
        //dd($kelas->all());
        return view('app.tahun', compact('tahun'));
    }

    public function tambah(Request $request){
        
        $request->validate([
            'tahun' => 'required|unique:tahun,tahun|min:6|max:20',
            'mulai' => 'required',
            'selesai' => 'required',
        ]);
        Tahun::create([
            'tahun' => $request->tahun,
            'mulai' => $request->mulai,
            'selesai' => $request->selesai,
        ]);

        return redirect('tahun')->with('success', 'Data Mata Pelajaran berhasil ditambahkan!');
    }
    public function edit(Request $request, $id){
        $tahun = Tahun::find($id);
        $tahun->name = $request->name;
        $tahun->update();
        //dd($kelas->all());
        return redirect('tahun')->with('success', 'Data Mata Pelajaran berhasil diubah!');
    }

    public function hapus(String $id){
        $tahun = Tahun::find($id);
        $tahun->delete();
        return redirect('tahun')->with('success', 'Data Mata Pelajaran berhasil dihapus!');
    }
    // Tambahkan method ini di dalam class TahunController

    public function setAktif($id)
    {
        // 1. Nonaktifkan semua tahun
        Tahun::query()->update(['is_active' => 0]);

        // 2. Aktifkan tahun yang dipilih
        $tahun = Tahun::find($id);
        $tahun->is_active = 1;
        $tahun->save();

        return redirect()->back()->with('sukses', 'Tahun Ajaran ' . $tahun->tahun . ' berhasil diaktifkan!');
    }
}
