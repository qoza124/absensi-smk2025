<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mapel;


class MapelController extends Controller
{

    public function index(Mapel $id){
        $mapel = Mapel::all();
        //dd($kelas->all());
        return view('app.mapel', compact('mapel'));
    }

    public function tambah(Request $request){
        
        $request->validate([
            'name' => 'required|unique:mapel,name|min:3|max:100',
        ]);
        Mapel::create([
            'name' => $request->name,
        ]);

        return redirect('mapel')->with('success', 'Data Mata Pelajaran berhasil ditambahkan!');
    }
    public function edit(Request $request, $id){
        $mapel = Mapel::find($id);
        $mapel->name = $request->name;
        $mapel->update();
        //dd($kelas->all());
        return redirect('mapel')->with('success', 'Data Mata Pelajaran berhasil diubah!');
    }

    public function hapus(String $id){
        $mapel = Mapel::find($id);
        $mapel->delete();
        return redirect('mapel')->with('success', 'Data Mata Pelajaran berhasil dihapus!');
    }
}
