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
        $pengajuan = PengajuanPenduduk::latest()->paginate(10);

        return view('kasun.pengajuan', compact('pengajuan'));

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
            // Lampiran bersifat opsional untuk semua jenis.
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

        PengajuanPenduduk::create([
            'id_pengaju' => Auth::id(),
            'nik' => $request->nik,
            'kode_pengajuan' => 'PGJ-' . now()->format('YmdHis'),
            'jenis_pengajuan' => $request->jenis_pengajuan,
            'status' => 'menunggu',
            'data_pengajuan' => array_filter([
                // Jika tidak ada field tambahan, tetap simpan data yang ada.
                // Semua field selain token dan kolom pengajuan akan ikut tersimpan.
                ...$request->except([
                    '_token',
                    'jenis_pengajuan',
                    'nik',
                    'catatan',
                    'lampiran',
                ]),
                'lampiran' => $lampiran,
            ]),
            'catatan' => $request->catatan,
        ]);

        return redirect()
            ->route('pengajuan.index')
            ->with('success', 'Pengajuan berhasil dikirim.');
    }


    /**
     * Detail pengajuan
     */
    public function show(PengajuanPenduduk $pengajuan)
    {
        // Untuk sementara, render menggunakan tampilan yang disiapkan untuk Kasun.
        // Jika Anda memiliki halaman detail versi Kasi, bisa dibuat terpisah.
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