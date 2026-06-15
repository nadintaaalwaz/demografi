<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPenduduk;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PengajuanPendudukController extends Controller
{
    /**
     * Halaman daftar pengajuan Kasun
     */
    public function index()
    {
        $idDusun = Auth::user()->id_dusun;

        $pengajuan = PengajuanPenduduk::with([
            'pengaju.dusun'
        ])
            ->whereHas('pengaju.dusun', function ($q) use ($idDusun) {
                $q->where('id', $idDusun);
            })
            ->latest()
            ->paginate(10);


        return view('kasun.pengajuan', compact('pengajuan'));

    }

    /**
     * Halaman approval pengajuan untuk Kasi
     */
    public function approvalIndex(Request $request)
    {
        $query = PengajuanPenduduk::query()->with(['pengaju.dusun']);

        // Filter status
        $query->whereIn('status', ['menunggu', 'disetujui', 'ditolak']);

        // Filter nama pengaju (via relasi)
        $nama = trim((string) $request->query('nama'));
        if ($nama !== '') {
            $query->whereHas('pengaju', function ($q) use ($nama) {
                $q->where('nama', 'like', '%' . $nama . '%');
            });
        }

        // Filter dusun
        $dusunId = $request->query('dusun');
        if (!empty($dusunId)) {
            $query->whereHas('pengaju.dusun', function ($q) use ($dusunId) {
                $q->where('id', $dusunId);
            });
        }

        // Sort berdasarkan waktu pengajuan
        $query->orderByDesc('created_at');

        // Pagination size
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $pengajuan = $query->paginate($perPage)->withQueryString();

        // Ambil daftar dusun untuk filter dropdown
        $dusunList = \App\Models\Wilayah::query()
            ->where('tipe', 'dusun')
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return view('kasi.pengajuan.index', compact('pengajuan', 'dusunList'));
    }
    /**
     * Form tambah pengajuan
     */
    public function create()
    {
        $penduduk = Penduduk::orderBy('nama_lengkap')->get();

        return view('kasun.pengajuan_create', compact('penduduk'));

    }

    /**
     * Simpan pengajuan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_pengajuan' => 'required',
            'nik' => 'nullable',
            'catatan' => 'nullable',
            'lampiran.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:10240',
        ]);

        $lampiran = [];
        if ($request->hasFile('lampiran')) {
            foreach ((array) $request->file('lampiran') as $file) {
                if (!$file) {
                    continue;
                }
                $lampiran[] = $file->store('lampiran_pengajuan', 'public');
            }
        }
        
        // Ambil semua input tambahan yang dinamis di form
        $extraData = $request->except([
            '_token',
            'jenis_pengajuan',
            'nik',
            'catatan',
            'lampiran',
        ]);

        // Filter data tambahan agar tidak menyimpan string kosong dari input tersembunyi
        $extraDataFiltered = array_filter($extraData, function($value) {
            return $value !== null && $value !== '';
        });

        $extraDataFiltered['lampiran'] = $lampiran;

        PengajuanPenduduk::create([
            'id_pengaju' => Auth::user()->id,
            'nik' => $request->nik,
            'kode_pengajuan' => 'PGJ-' . now()->format('YmdHis'),
            'jenis_pengajuan' => $request->jenis_pengajuan,
            'status' => 'menunggu',
            'data_pengajuan' => $extraDataFiltered, // Menyimpan array bersih ke kolom JSON
            'catatan' => $request->catatan,
        ]);

        return redirect()
            ->route('kasun.pengajuan.index')
            ->with('success', 'Pengajuan berhasil dikirim.');
    }
    
    /**
     * Detail pengajuan
     */
    public function show(PengajuanPenduduk $pengajuan)
    {
        // Pastikan Kasun hanya bisa lihat pengajuan milik dusunnya sendiri
        $idDusun = Auth::user()->id_dusun;

        $valid = PengajuanPenduduk::query()
            ->where('id', $pengajuan->id)
            ->whereHas('pengaju.dusun', function ($q) use ($idDusun) {
                $q->where('id', $idDusun);
            })
            ->exists();

        abort_unless($valid, 403);

        return view('kasun.pengajuan', compact('pengajuan'));
    }
    /**
     * Setujui pengajuan
     */
    public function approve(PengajuanPenduduk $pengajuan)
    {
        $pengajuan->update([
            'status' => 'disetujui',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan disetujui.');
    }

    /**
     * Tolak pengajuan
     */
    public function reject(Request $request, PengajuanPenduduk $pengajuan)
    {
        $request->validate([
            'alasan_penolakan' => 'required'
        ]);

        $pengajuan->update([
            'status' => 'ditolak',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        return back()->with('success', 'Pengajuan ditolak.');
    }
}