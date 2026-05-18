<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wilayah;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    /**
     * Normalisasi teks untuk perbandingan sederhana.
     */
    private function normalizeText(?string $value): string
    {
        $value = trim((string) $value);
        $value = mb_strtolower($value);
        $value = preg_replace('/\s+/', ' ', $value);

        return $value ?? '';
    }

    /**
     * Pastikan nama kasun memang berada di dusun yang dipilih.
     */
    private function validateKasunDusunMatch(string $nama, ?int $idDusun, callable $fail): void
    {
        $nama = trim($nama);

        $penduduk = Penduduk::query()
            ->whereRaw('LOWER(TRIM(nama_lengkap)) = ?', [mb_strtolower($nama)])
            ->first();

        if (!$penduduk) {
            $fail('Nama kasun tidak ditemukan pada data penduduk. Pastikan nama benar dan ada.');
            return;
        }

        if (!$idDusun) {
            return;
        }

        $dusunDipilih = Wilayah::find($idDusun);
        $namaDusunDipilih = $dusunDipilih?->nama ?? 'dusun yang dipilih';
        $dusunPenduduk = Wilayah::find($penduduk->id_dusun);
        $namaDusunPenduduk = $dusunPenduduk?->nama ?? 'dusun lain';

        if ((int) $penduduk->id_dusun !== (int) $idDusun) {
            $fail("Nama tersebut tidak berada pada dusun {$namaDusunDipilih}. Data penduduk terdaftar di dusun {$namaDusunPenduduk}.");
            return;
        }

        $alamatPenduduk = $this->normalizeText($penduduk->alamat ?? '');
        $namaDusunNorm = $this->normalizeText($namaDusunDipilih);

        if ($alamatPenduduk !== '' && $namaDusunNorm !== '' && !str_contains($alamatPenduduk, $namaDusunNorm)) {
            $fail("Alamat penduduk tidak sesuai dengan dusun {$namaDusunDipilih}. Pastikan yang diampu juga beralamat di dusun tersebut.");
        }
    }

    /**
     * Display a listing of kasun users.
     */
    public function index()
    {
        // Pastikan hanya kasi yang bisa akses
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        $users = User::where('role', 'kasun')
            ->orderBy('nama', 'asc')
            ->get();

        return view('kasi.manajemen-user.index', compact('users'));
    }

    /**
     * Show the form for creating a new kasun user.
     */
    public function create()
    {
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        $dusunList = Wilayah::where('tipe', 'dusun')
            ->orderBy('nama', 'asc')
            ->get();

        return view('kasi.manajemen-user.create', compact('dusunList'));
    }

    /**
     * Store a newly created kasun user in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'nama' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $this->validateKasunDusunMatch(
                        (string) $value,
                        $request->filled('id_dusun') ? (int) $request->input('id_dusun') : null,
                        $fail
                    );
                },
            ],
            'id_dusun' => 'nullable|integer',
        ]);

        User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'nama' => $validated['nama'],
            'role' => 'kasun',
            'id_dusun' => $validated['id_dusun'] ?? null,
        ]);

        return redirect()->route('kasi.users.index')
            ->with('success', 'Akun kasun berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified kasun user.
     */
    public function edit($id)
    {
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('role', 'kasun')->findOrFail($id);
        $dusunList = Wilayah::where('tipe', 'dusun')
            ->orderBy('nama', 'asc')
            ->get();

        return view('kasi.manajemen-user.edit', compact('user', 'dusunList'));
    }

    /**
     * Update the specified kasun user in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('role', 'kasun')->findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'nama' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $this->validateKasunDusunMatch(
                        (string) $value,
                        $request->filled('id_dusun') ? (int) $request->input('id_dusun') : null,
                        $fail
                    );
                },
            ],
            'id_dusun' => 'nullable|integer',
        ]);

        $user->username = $validated['username'];
        $user->nama = $validated['nama'];
        $user->id_dusun = $validated['id_dusun'] ?? null;

        // Update password hanya jika diisi
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('kasi.users.index')
            ->with('success', 'Data kasun berhasil diperbarui!');
    }

    /**
     * Remove the specified kasun user from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'kasi') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('role', 'kasun')->findOrFail($id);
        $user->delete();

        return redirect()->route('kasi.users.index')
            ->with('success', 'Akun kasun berhasil dihapus!');
    }
}
