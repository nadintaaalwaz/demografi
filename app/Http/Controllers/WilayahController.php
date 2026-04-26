<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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

        $wilayah = Wilayah::with('dusun')
            ->orderBy('tipe', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        $rwByDusun = Wilayah::query()
            ->where('tipe', 'rw')
            ->whereNotNull('id_dusun')
            ->whereNotNull('nomor_rw')
            ->orderBy('id_dusun')
            ->orderBy('nomor_rw')
            ->get(['id_dusun', 'nomor_rw'])
            ->groupBy('id_dusun')
            ->map(function ($rows) {
                return $rows->pluck('nomor_rw')
                    ->map(fn ($value) => (int) $value)
                    ->unique()
                    ->sort()
                    ->values()
                    ->all();
            })
            ->toArray();

        $rtByDusunRw = Wilayah::query()
            ->where('tipe', 'rt')
            ->whereNotNull('id_dusun')
            ->whereNotNull('nomor_rw')
            ->whereNotNull('nomor_rt')
            ->orderBy('id_dusun')
            ->orderBy('nomor_rw')
            ->orderBy('nomor_rt')
            ->get(['id_dusun', 'nomor_rw', 'nomor_rt'])
            ->groupBy(function ($row) {
                return $row->id_dusun . '-' . $row->nomor_rw;
            })
            ->map(function ($rows) {
                return $rows->pluck('nomor_rt')
                    ->map(fn ($value) => (int) $value)
                    ->unique()
                    ->sort()
                    ->values()
                    ->all();
            })
            ->toArray();

        $dusunSummary = Wilayah::query()
            ->from('wilayah as w')
            ->leftJoin('penduduk as p', 'p.id_dusun', '=', 'w.id')
            ->where('w.tipe', 'dusun')
            ->select('w.id', 'w.nama', 'w.latitude', 'w.longitude', 'w.luas_wilayah')
            ->selectRaw('COUNT(p.nik) as total_penduduk')
            ->groupBy('w.id', 'w.nama', 'w.latitude', 'w.longitude', 'w.luas_wilayah')
            ->orderBy('w.nama')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => (int) $item->id,
                    'nama' => $item->nama,
                    'latitude' => (float) $item->latitude,
                    'longitude' => (float) $item->longitude,
                    'luas_wilayah' => $item->luas_wilayah !== null ? (float) $item->luas_wilayah : null,
                    'total_penduduk' => (int) $item->total_penduduk,
                ];
            })
            ->values()
            ->all();

        $totalLuasDusun = Wilayah::query()
            ->where('tipe', 'dusun')
            ->whereNotNull('luas_wilayah')
            ->sum('luas_wilayah');

        $wilayahCounts = [
            'dusun' => Wilayah::where('tipe', 'dusun')->count(),
            'rt' => Wilayah::where('tipe', 'rt')->count(),
            'rw' => Wilayah::where('tipe', 'rw')->count(),
        ];

        return view('kasi.wilayah.index', [
            'wilayah' => $wilayah,
            'rwByDusun' => $rwByDusun,
            'rtByDusunRw' => $rtByDusunRw,
            'dusunSummary' => $dusunSummary,
            'totalLuasDusun' => (float) $totalLuasDusun,
            'wilayahCounts' => $wilayahCounts,
            'sebalorBoundaryUrl' => asset('data/sebalor-boundary.geojson'),
        ]);
    }

    /**
     * Show the form for creating a new wilayah.
     */
    public function create()
    {
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        $dusunList = Wilayah::where('tipe', 'dusun')
            ->orderBy('nama', 'asc')
            ->get();

        return view('kasi.wilayah.create', compact('dusunList'));
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
            'id_dusun' => [
                'nullable',
                'integer',
                Rule::exists('wilayah', 'id')->where(function ($query) {
                    $query->where('tipe', 'dusun');
                }),
            ],
            'nomor_rt' => 'nullable|integer|min:1|required_if:tipe,rt',
            'nomor_rw' => 'nullable|integer|min:1|required_if:tipe,rt,rw',
            'luas_wilayah' => 'nullable|numeric|min:0.01',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validated['tipe'] === 'rw' && empty($validated['id_dusun'])) {
            return redirect()->back()
                ->withErrors(['id_dusun' => 'Dusun wajib dipilih untuk data RW/RT.'])
                ->withInput();
        }

        if ($validated['tipe'] === 'rw') {
            $existsRw = Wilayah::query()
                ->where('tipe', 'rw')
                ->where('id_dusun', $validated['id_dusun'])
                ->where('nomor_rw', $validated['nomor_rw'])
                ->exists();

            if ($existsRw) {
                return redirect()->back()
                    ->withErrors(['nomor_rw' => 'Nomor RW sudah ada.'])
                    ->withInput();
            }

            $validated['nomor_rt'] = null;
        }

        if ($validated['tipe'] === 'rt') {
            $rwParent = Wilayah::query()
                ->where('tipe', 'rw')
                ->where('nomor_rw', $validated['nomor_rw'])
                ->first();

            if (!$rwParent) {
                return redirect()->back()
                    ->withErrors(['nomor_rw' => 'Nomor RW belum terdaftar. Tambahkan data RW terlebih dahulu.'])
                    ->withInput();
            }

            $validated['id_dusun'] = $rwParent->id_dusun;

            $rwExists = Wilayah::query()
                ->where('tipe', 'rw')
                ->where('nomor_rw', $validated['nomor_rw'])
                ->exists();

            if (!$rwExists) {
                return redirect()->back()
                    ->withErrors(['nomor_rw' => 'Nomor RW belum terdaftar. Tambahkan data RW terlebih dahulu.'])
                    ->withInput();
            }

            $duplicateRtInRw = Wilayah::query()
                ->where('tipe', 'rt')
                ->where('id_dusun', $validated['id_dusun'])
                ->where('nomor_rw', $validated['nomor_rw'])
                ->where('nomor_rt', $validated['nomor_rt'])
                ->exists();

            if ($duplicateRtInRw) {
                return redirect()->back()
                    ->withErrors(['nomor_rt' => 'Nomor RT tersebut sudah ada di RW yang dipilih.'])
                    ->withInput();
            }
        }

        if ($validated['tipe'] === 'dusun') {
            $validated['nomor_rt'] = null;
            $validated['nomor_rw'] = null;
            $validated['id_dusun'] = null;
        }

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
        $dusunList = Wilayah::where('tipe', 'dusun')
            ->orderBy('nama', 'asc')
            ->get();

        return view('kasi.wilayah.edit', compact('wilayah', 'dusunList'));
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
            'id_dusun' => [
                'nullable',
                'integer',
                Rule::exists('wilayah', 'id')->where(function ($query) {
                    $query->where('tipe', 'dusun');
                }),
            ],
            'nomor_rt' => 'nullable|integer|min:1|required_if:tipe,rt',
            'nomor_rw' => 'nullable|integer|min:1|required_if:tipe,rt,rw',
            'luas_wilayah' => 'nullable|numeric|min:0.01',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validated['tipe'] === 'rw' && empty($validated['id_dusun'])) {
            return redirect()->back()
                ->withErrors(['id_dusun' => 'Dusun wajib dipilih untuk data RW/RT.'])
                ->withInput();
        }

        if ($validated['tipe'] === 'rw') {
            $existsRw = Wilayah::query()
                ->where('tipe', 'rw')
                ->where('id_dusun', $validated['id_dusun'])
                ->where('nomor_rw', $validated['nomor_rw'])
                ->where('id', '!=', $wilayah->id)
                ->exists();

            if ($existsRw) {
                return redirect()->back()
                    ->withErrors(['nomor_rw' => 'Nomor RW sudah ada.'])
                    ->withInput();
            }

            $validated['nomor_rt'] = null;
        }

        if ($validated['tipe'] === 'rt') {
            $rwParent = Wilayah::query()
                ->where('tipe', 'rw')
                ->where('nomor_rw', $validated['nomor_rw'])
                ->first();

            if (!$rwParent) {
                return redirect()->back()
                    ->withErrors(['nomor_rw' => 'Nomor RW belum terdaftar. Tambahkan data RW terlebih dahulu.'])
                    ->withInput();
            }

            $validated['id_dusun'] = $rwParent->id_dusun;

            $rwExists = Wilayah::query()
                ->where('tipe', 'rw')
                ->where('nomor_rw', $validated['nomor_rw'])
                ->exists();

            if (!$rwExists) {
                return redirect()->back()
                    ->withErrors(['nomor_rw' => 'Nomor RW belum terdaftar. Tambahkan data RW terlebih dahulu.'])
                    ->withInput();
            }

            $duplicateRtInRw = Wilayah::query()
                ->where('tipe', 'rt')
                ->where('id_dusun', $validated['id_dusun'])
                ->where('nomor_rw', $validated['nomor_rw'])
                ->where('nomor_rt', $validated['nomor_rt'])
                ->where('id', '!=', $wilayah->id)
                ->exists();

            if ($duplicateRtInRw) {
                return redirect()->back()
                    ->withErrors(['nomor_rt' => 'Nomor RT tersebut sudah ada di RW yang dipilih.'])
                    ->withInput();
            }
        }

        if ($validated['tipe'] === 'dusun') {
            $validated['nomor_rt'] = null;
            $validated['nomor_rw'] = null;
            $validated['id_dusun'] = null;
        }

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
            $jumlahAnakWilayah = Wilayah::where('id_dusun', $wilayah->id)->count();
            if ($jumlahAnakWilayah > 0) {
                return redirect()->route('kasi.wilayah.index')
                    ->with('error', "Tidak bisa hapus wilayah karena masih ada {$jumlahAnakWilayah} RW/RT yang terhubung ke {$wilayah->nama}.");
            }

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
