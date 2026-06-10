<?php

namespace App\Http\Controllers;

use App\Models\DinamikaPenduduk;
use App\Models\Penduduk;
use App\Models\Wilayah;
use App\Models\RiwayatDinamika;
use Illuminate\Http\Request;

class DinamikaPendudukController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = (int) $request->query('tahun', now()->year);
        $selectedMonth = $request->query('bulan');
        $selectedMonth = $selectedMonth !== null && $selectedMonth !== '' ? (int) $selectedMonth : null;

        if ($selectedMonth !== null && ($selectedMonth < 1 || $selectedMonth > 12)) {
            $selectedMonth = null;
        }

        $yearRange = DinamikaPenduduk::query()
            ->selectRaw('MIN(tahun) as min_year, MAX(tahun) as max_year')
            ->first();

        // Kalau tabel rekap masih kosong (misalnya setelah truncate), tetap tampilkan range mulai dari 2020.
        $minYear = (int) ($yearRange->min_year ?? now()->year);
        $minYear = min($minYear, 2020);
        $maxYear = (int) ($yearRange->max_year ?? now()->year);


        if ($minYear > $maxYear) {
            $minYear = (int) now()->year;
            $maxYear = (int) now()->year;
        }

        $yearOptions = range($maxYear, $minYear);

        if ($selectedYear < $minYear || $selectedYear > $maxYear) {
            $selectedYear = $maxYear;
        }

        $monthOptions = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        // Dipakai untuk konsistensi filter dusun pada rekap Lahir/Masuk (sumber manual)
        $hasDusunSpecificData = DinamikaPenduduk::query()
            ->where('tahun', $selectedYear)
            ->whereNotNull('id_dusun')
            ->exists();

        // Dipakai untuk konsistensi filter dusun pada rekap Meninggal/Keluar (sumber master penduduk)
        $hasDusunSpecificRiwayat = Penduduk::query()
            ->whereYear('tanggal_status', $selectedYear)
            ->where('status', '!=', 'Aktif')
            ->whereNotNull('id_dusun')
            ->exists();


        $scopeQuery = function () use ($selectedYear, $selectedMonth, $hasDusunSpecificData) {
            $query = DinamikaPenduduk::query()->where('tahun', $selectedYear);

            if ($selectedMonth !== null) {
                $query->where('bulan', $selectedMonth);
            }

            if ($hasDusunSpecificData) {
                $query->whereNotNull('id_dusun');
            } else {
                $query->whereNull('id_dusun');
            }

            return $query;
        };

        $monthlyRowsLahirMasuk = $scopeQuery()
            ->select('bulan')
            ->selectRaw('SUM(jumlah_lahir) as jumlah_lahir')
            ->selectRaw('SUM(jumlah_masuk) as jumlah_masuk')
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $monthlyRowsMeninggalKeluar = Penduduk::query()
            ->selectRaw('MONTH(tanggal_status) as bulan')
            ->selectRaw("SUM(CASE WHEN status = 'Meninggal' THEN 1 ELSE 0 END) as jumlah_meninggal")
            ->selectRaw("SUM(CASE WHEN status = 'Keluar' THEN 1 ELSE 0 END) as jumlah_keluar")
            ->whereYear('tanggal_status', $selectedYear)
            ->whereNotNull('tanggal_status')
            ->when(
                $selectedMonth !== null,
                fn ($q) => $q->whereMonth('tanggal_status', $selectedMonth)
            )
            ->when(
                $hasDusunSpecificRiwayat,
                function ($q) {
                    $q->whereNotNull('id_dusun');
                },
                function ($q) {
                    $q->whereNull('id_dusun');
                }
            )
            ->groupByRaw('MONTH(tanggal_status)')
            ->get()
            ->keyBy('bulan');

        $kelahiranSeries = [];
        $kematianSeries = [];
        $migrasiMasukSeries = [];
        $migrasiKeluarSeries = [];
        $growthSeries = [];

        $meninggalDetailsByMonth = [];
        $keluarDetailsByMonth = [];

        // Ambil daftar nama penduduk (meninggal/keluar) per bulan sesuai filter
        $eventBaseQuery = Penduduk::query()
            ->leftJoin('wilayah as dusun', 'penduduk.id_dusun', '=', 'dusun.id')
            ->select([
                'penduduk.nik',
                'penduduk.nama_lengkap',
                'penduduk.rt',
                'penduduk.rw',
                'penduduk.status',
                'penduduk.tanggal_status',
                'dusun.nama as nama_dusun',
            ])
            ->whereIn('penduduk.status', ['Meninggal', 'Keluar'])
            ->whereNotNull('penduduk.tanggal_status')
            ->whereYear('penduduk.tanggal_status', $selectedYear);


        if ($selectedMonth !== null) {
            $eventBaseQuery->whereMonth('penduduk.tanggal_status', $selectedMonth);
        }

        $eventBaseQuery->when(
            $hasDusunSpecificRiwayat,
            fn ($q) => $q->whereNotNull('penduduk.id_dusun'),
            fn ($q) => $q->whereNull('penduduk.id_dusun')
        );

        // index per bulan (1-12)
        $eventRows = collect();
        foreach ($eventBaseQuery->get() as $row) {
            if (empty($row->tanggal_status)) {
                continue;
            }
            $bulanKey = (int) date('n', strtotime($row->tanggal_status));
            $eventRows[$bulanKey] = $eventRows[$bulanKey] ?? collect();
            $eventRows[$bulanKey]->push($row);
        }


        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $rowLahirMasuk = $monthlyRowsLahirMasuk->get($bulan);
            $lahir = (int) ($rowLahirMasuk->jumlah_lahir ?? 0);
            $masuk = (int) ($rowLahirMasuk->jumlah_masuk ?? 0);

            $rowMeninggalKeluar = $monthlyRowsMeninggalKeluar->get($bulan);
            $meninggal = (int) ($rowMeninggalKeluar->jumlah_meninggal ?? 0);
            $keluar = (int) ($rowMeninggalKeluar->jumlah_keluar ?? 0);

            // isi series
            $kelahiranSeries[] = $lahir;
            $kematianSeries[] = $meninggal;
            $migrasiMasukSeries[] = $masuk;
            $migrasiKeluarSeries[] = $keluar;
            $growthSeries[] = $lahir + $masuk - $meninggal - $keluar;

            // daftar nama untuk tabel
            $eventRowsForMonth = $eventRows->get($bulan) ?? collect();
            $meninggalDetailsByMonth[$bulan] = $eventRowsForMonth
                ->where('status', 'Meninggal')
                ->map(function ($item) {
                    return [
                        'nama' => $item->nama_lengkap,
                        'dusun' => $item->nama_dusun,
                        'rt' => $item->rt,
                        'rw' => $item->rw,
                    ];
                })
                ->values()
                ->all();

            $keluarDetailsByMonth[$bulan] = $eventRowsForMonth
                ->where('status', 'Keluar')
                ->map(function ($item) {
                    return [
                        'nama' => $item->nama_lengkap,
                        'dusun' => $item->nama_dusun,
                        'rt' => $item->rt,
                        'rw' => $item->rw,
                    ];
                })
                ->values()
                ->all();
        }

        $totalKelahiran = array_sum($kelahiranSeries);
        $totalKematian = array_sum($kematianSeries);
        $totalMigrasiMasuk = array_sum($migrasiMasukSeries);
        $totalMigrasiKeluar = array_sum($migrasiKeluarSeries);

        $previousYear = $selectedYear - 1;
        $previousHasDusunSpecificData = DinamikaPenduduk::query()
            ->where('tahun', $previousYear)
            ->whereNotNull('id_dusun')
            ->exists();

        $previousHasDusunSpecificRiwayat = Penduduk::query()
            ->whereYear('tanggal_status', $previousYear)
            ->where('status', '!=', 'Aktif')
            ->whereNotNull('id_dusun')
            ->exists();

        $previousYearTotalLahirMasuk = DinamikaPenduduk::query()
            ->where('tahun', $previousYear)
            ->when(
                $selectedMonth !== null,
                fn ($query) => $query->where('bulan', $selectedMonth)
            )
            ->when(
                $previousHasDusunSpecificData,
                fn ($query) => $query->whereNotNull('id_dusun'),
                fn ($query) => $query->whereNull('id_dusun')
            )
            ->selectRaw('SUM(jumlah_lahir) as jumlah_lahir')
            ->selectRaw('SUM(jumlah_masuk) as jumlah_masuk')
            ->first();

        $previousYearTotalMeninggalKeluar = Penduduk::query()
            ->whereYear('tanggal_status', $previousYear)
            ->where('status', '!=', 'Aktif')
            ->whereNotNull('tanggal_status')
            ->when(
                $selectedMonth !== null,
                fn ($q) => $q->whereMonth('tanggal_status', $selectedMonth)
            )
            ->when(
                $previousHasDusunSpecificRiwayat,
                function ($q) {
                    $q->whereNotNull('id_dusun');
                },
                function ($q) {
                    $q->whereNull('id_dusun');
                }
            )
            ->selectRaw("SUM(CASE WHEN status = 'Meninggal' THEN 1 ELSE 0 END) as jumlah_meninggal")
            ->selectRaw("SUM(CASE WHEN status = 'Keluar' THEN 1 ELSE 0 END) as jumlah_keluar")
            ->first();

        $trendFormatter = function (int $current, int $previous): array {
            if ($previous <= 0) {
                return ['label' => '+0%', 'down' => false];
            }

            $percentage = round((($current - $previous) / $previous) * 100);

            return [
                'label' => ($percentage >= 0 ? '+' : '') . $percentage . '%',
                'down' => $percentage < 0,
            ];
        };

        $trendKelahiran = $trendFormatter($totalKelahiran, (int) ($previousYearTotalLahirMasuk->jumlah_lahir ?? 0));
        $trendKematian = $trendFormatter($totalKematian, (int) ($previousYearTotalMeninggalKeluar->jumlah_meninggal ?? 0));
        $trendMigrasiMasuk = $trendFormatter($totalMigrasiMasuk, (int) ($previousYearTotalLahirMasuk->jumlah_masuk ?? 0));
        $trendMigrasiKeluar = $trendFormatter($totalMigrasiKeluar, (int) ($previousYearTotalMeninggalKeluar->jumlah_keluar ?? 0));

        $yearStart = $selectedYear - 4;

        $yearlyRowsLahirMasuk = DinamikaPenduduk::query()
            ->whereBetween('tahun', [$yearStart, $selectedYear])
            ->when(
                $hasDusunSpecificData,
                fn ($query) => $query->whereNotNull('id_dusun'),
                fn ($query) => $query->whereNull('id_dusun')
            )
            ->select('tahun')
            ->selectRaw('SUM(jumlah_lahir) as jumlah_lahir')
            ->selectRaw('SUM(jumlah_masuk) as jumlah_masuk')
            ->groupBy('tahun')
            ->get()
            ->keyBy('tahun');

        $yearlyRowsMeninggalKeluar = Penduduk::query()
            ->whereYear('tanggal_status', '>=', $yearStart)
            ->whereYear('tanggal_status', '<=', $selectedYear)
            ->where('status', '!=', 'Aktif')
            ->whereNotNull('tanggal_status')
            ->when(
                $hasDusunSpecificRiwayat,
                function ($q) {
                    $q->whereNotNull('id_dusun');
                },
                function ($q) {
                    $q->whereNull('id_dusun');
                }
            )
            ->selectRaw('YEAR(tanggal_status) as tahun')
            ->selectRaw("SUM(CASE WHEN status = 'Meninggal' THEN 1 ELSE 0 END) as jumlah_meninggal")
            ->selectRaw("SUM(CASE WHEN status = 'Keluar' THEN 1 ELSE 0 END) as jumlah_keluar")
            ->groupByRaw('YEAR(tanggal_status)')
            ->get()
            ->keyBy('tahun');

        $yearlyLabels = [];
        $yearlyLahir = [];
        $yearlyMeninggal = [];
        $yearlyMasuk = [];
        $yearlyKeluar = [];

        for ($year = $yearStart; $year <= $selectedYear; $year++) {
            $yearlyLabels[] = (string) $year;
            $rowLahirMasuk = $yearlyRowsLahirMasuk->get($year);
            $yearlyLahir[] = (int) ($rowLahirMasuk->jumlah_lahir ?? 0);
            $yearlyMasuk[] = (int) ($rowLahirMasuk->jumlah_masuk ?? 0);

            $rowMeninggalKeluar = $yearlyRowsMeninggalKeluar->get($year);
            $yearlyMeninggal[] = (int) ($rowMeninggalKeluar->jumlah_meninggal ?? 0);
            $yearlyKeluar[] = (int) ($rowMeninggalKeluar->jumlah_keluar ?? 0);
        }

        // Tabel: tepat 1 baris per bulan sesuai filter
        if ($selectedMonth === null) {
            $bulanToShow = collect(range(1, 12));
        } else {
            $bulanToShow = collect([$selectedMonth]);
        }

        $rekapLahirMasuk = DinamikaPenduduk::query()
            ->where('tahun', $selectedYear)
            ->when(
                $selectedMonth !== null,
                fn ($q) => $q->where('bulan', $selectedMonth)
            )
            ->select('bulan')
            ->selectRaw('SUM(jumlah_lahir) as jumlah_lahir')
            ->selectRaw('SUM(jumlah_masuk) as jumlah_masuk')
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $editableRecords = collect();
        foreach ($bulanToShow as $bulan) {
            $row = $rekapLahirMasuk->get($bulan);

            $editableRecords->push((object)[
                'id' => null,
                'tahun' => $selectedYear,
                'bulan' => $bulan,
                'id_dusun' => null,
                'jumlah_lahir' => (int) ($row?->jumlah_lahir ?? 0),
                'jumlah_masuk' => (int) ($row?->jumlah_masuk ?? 0),
            ]);
        }


        // Load dusun list untuk dropdown form
        $dusunList = Wilayah::where('tipe', 'dusun')
            ->orderBy('nama')
            ->pluck('nama', 'id');

        return view('kasi.dinamika-penduduk', [
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'monthOptions' => $monthOptions,
            'yearOptions' => $yearOptions,
            'editableRecords' => $editableRecords,
            'dusunList' => $dusunList,
            'totalKelahiran' => $totalKelahiran,
            'totalKematian' => $totalKematian,
            'totalMigrasiMasuk' => $totalMigrasiMasuk,
            'totalMigrasiKeluar' => $totalMigrasiKeluar,
            'trendKelahiran' => $trendKelahiran,
            'trendKematian' => $trendKematian,
            'trendMigrasiMasuk' => $trendMigrasiMasuk,
            'trendMigrasiKeluar' => $trendMigrasiKeluar,
            'kelahiranSeries' => $kelahiranSeries,
            'kematianSeries' => $kematianSeries,
            'migrasiMasukSeries' => $migrasiMasukSeries,
            'migrasiKeluarSeries' => $migrasiKeluarSeries,
            'growthSeries' => $growthSeries,
            'meninggalDetailsByMonth' => $meninggalDetailsByMonth,
            'keluarDetailsByMonth' => $keluarDetailsByMonth,
            'yearlyLabels' => $yearlyLabels,
            'yearlyLahir' => $yearlyLahir,
            'yearlyMeninggal' => $yearlyMeninggal,
            'yearlyMasuk' => $yearlyMasuk,
            'yearlyKeluar' => $yearlyKeluar,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'record_id' => 'nullable|integer|exists:dinamika_penduduk,id',
            'tahun' => 'required|integer|min:2000|max:2100',
            'bulan' => 'required|integer|min:1|max:12',
            'id_dusun' => 'nullable|integer|exists:wilayah,id',
            'jumlah_lahir' => 'required|integer|min:0',
            'jumlah_masuk' => 'required|integer|min:0',
        ]);

        $recordId = $validated['record_id'] ?? null;

        $flashType = 'success';

        if ($recordId) {
            $record = DinamikaPenduduk::findOrFail($recordId);

            $conflictExists = DinamikaPenduduk::query()
                ->where('tahun', $validated['tahun'])
                ->where('bulan', $validated['bulan'])
                ->where('id_dusun', $validated['id_dusun'] ?? null)
                ->where('id', '!=', $record->id)
                ->exists();

            if ($conflictExists) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        'bulan' => 'Data dinamika untuk kombinasi tahun, bulan, dan dusun tersebut sudah ada. Silakan pilih data yang sudah ada untuk diubah.',
                    ]);
            }

            $record->fill([
                'tahun' => $validated['tahun'],
                'bulan' => $validated['bulan'],
                'id_dusun' => $validated['id_dusun'] ?? null,
                'jumlah_lahir' => $validated['jumlah_lahir'],
                'jumlah_masuk' => $validated['jumlah_masuk'],
            ]);

            $hasChanges = $record->isDirty();
            $record->save();

            $flashMessage = $hasChanges
                ? 'Data dinamika penduduk berhasil diperbarui.'
                : 'Tidak ada perubahan pada data dinamika penduduk.';

            if (!$hasChanges) {
                $flashType = 'warning';
            }
        } else {
            $record = DinamikaPenduduk::firstOrNew([
                'tahun' => $validated['tahun'],
                'bulan' => $validated['bulan'],
                'id_dusun' => $validated['id_dusun'] ?? null,
            ]);

            $isNewRecord = !$record->exists;

            $record->fill([
                'jumlah_lahir' => $validated['jumlah_lahir'],
                'jumlah_masuk' => $validated['jumlah_masuk'],
            ]);

            $hasChanges = $record->isDirty();
            $record->save();

            if ($isNewRecord) {
                $flashMessage = 'Rekap dinamika penduduk berhasil disimpan.';
            } else {
                $flashMessage = $hasChanges
                    ? 'Data dinamika penduduk berhasil diperbarui.'
                    : 'Tidak ada perubahan pada data dinamika penduduk.';

                if (!$hasChanges) {
                    $flashType = 'warning';
                }
            }
        }

        return redirect()
            ->route('kasi.dinamika', ['tahun' => $validated['tahun'], 'bulan' => $validated['bulan']])
            ->with($flashType, $flashMessage);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $record = DinamikaPenduduk::findOrFail($id);
            $tahun = (int) ($request->input('tahun') ?: $record->tahun ?: now()->year);
            $bulan = $request->input('bulan');

            $record->delete();

            $params = ['tahun' => $tahun];
            if ($bulan !== null && $bulan !== '') {
                $params['bulan'] = (int) $bulan;
            }

            return redirect()
                ->route('kasi.dinamika', $params)
                ->with('success', 'Data dinamika penduduk berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', 'Data dinamika penduduk gagal dihapus. Silakan coba lagi.');
        }
    }
}
