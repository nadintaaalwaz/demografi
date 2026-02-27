<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WilayahController extends Controller
{
    /**
     * Display a listing of wilayah.
     */
    public function index()
    {
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        $wilayah = Wilayah::orderBy('tipe', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        return view('kasi.wilayah.index', compact('wilayah'));
    }

    /**
     * Show the form for creating a new wilayah.
     */
    public function create()
    {
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        return view('kasi.wilayah.create');
    }

    /**
     * Store a newly created wilayah in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:dusun,rt,rw',
            'nomor_rt' => 'nullable|integer|min:1',
            'nomor_rw' => 'nullable|integer|min:1',
            'luas_wilayah' => 'nullable|numeric|min:0.01',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        Wilayah::create($validated);

        return redirect()->route('kasi.wilayah.index')
            ->with('success', 'Wilayah berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified wilayah.
     */
    public function edit($id)
    {
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        $wilayah = Wilayah::findOrFail($id);

        return view('kasi.wilayah.edit', compact('wilayah'));
    }

    /**
     * Update the specified wilayah in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        $wilayah = Wilayah::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:dusun,rt,rw',
            'nomor_rt' => 'nullable|integer|min:1',
            'nomor_rw' => 'nullable|integer|min:1',
            'luas_wilayah' => 'nullable|numeric|min:0.01',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $wilayah->update($validated);

        return redirect()->route('kasi.wilayah.index')
            ->with('success', 'Data wilayah berhasil diperbarui!');
    }

    /**
     * Remove the specified wilayah from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        $wilayah = Wilayah::findOrFail($id);

        // Cek apakah ada penduduk di wilayah ini
        if ($wilayah->tipe === 'dusun') {
            $jumlahPenduduk = User::where('id_dusun', $id)->count();
            
            if ($jumlahPenduduk > 0) {
                return redirect()->route('kasi.wilayah.index')
                    ->with('error', "Tidak bisa hapus wilayah karena masih ada {$jumlahPenduduk} penduduk di {$wilayah->nama}.");
            }
        }

        $wilayah->delete();

        return redirect()->route('kasi.wilayah.index')
            ->with('success', 'Wilayah berhasil dihapus!');
    }
}
