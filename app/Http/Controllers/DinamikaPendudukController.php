<?php

namespace App\Http\Controllers;

use App\Models\DinamikaPenduduk;
use Illuminate\Http\Request;

class DinamikaPendudukController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = (int) $request->query('tahun', now()->year);

        $yearRange = DinamikaPenduduk::query()
            ->selectRaw('MIN(tahun) as min_year, MAX(tahun) as max_year')
            ->first();

        $minYear = (int) ($yearRange->min_year ?? (now()->year - 3));
        $maxYear = (int) ($yearRange->max_year ?? now()->year);

        if ($minYear > $maxYear) {
            $minYear = (int) now()->year;
            $maxYear = (int) now()->year;
        }

        $yearOptions = range($maxYear, $minYear);

        if ($selectedYear < $minYear || $selectedYear > $maxYear) {
            $selectedYear = $maxYear;
        }

        $hasDusunSpecificData = DinamikaPenduduk::query()
            ->where('tahun', $selectedYear)
            ->whereNotNull('id_dusun')
            ->exists();

        $scopeQuery = function () use ($selectedYear, $hasDusunSpecificData) {
            $query = DinamikaPenduduk::query()->where('tahun', $selectedYear);

            if ($hasDusunSpecificData) {
                $query->whereNotNull('id_dusun');
            } else {
                $query->whereNull('id_dusun');
            }

            return $query;
        };

        $monthlyRows = $scopeQuery()
            ->select('bulan')
            ->selectRaw('SUM(jumlah_lahir) as jumlah_lahir')
            ->selectRaw('SUM(jumlah_meninggal) as jumlah_meninggal')
            ->selectRaw('SUM(jumlah_masuk) as jumlah_masuk')
            ->selectRaw('SUM(jumlah_keluar) as jumlah_keluar')
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $kelahiranSeries = [];
        $kematianSeries = [];
        $migrasiMasukSeries = [];
        $migrasiKeluarSeries = [];
        $growthSeries = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $row = $monthlyRows->get($bulan);
            $lahir = (int) ($row->jumlah_lahir ?? 0);
            $meninggal = (int) ($row->jumlah_meninggal ?? 0);
            $masuk = (int) ($row->jumlah_masuk ?? 0);
            $keluar = (int) ($row->jumlah_keluar ?? 0);

            $kelahiranSeries[] = $lahir;
            $kematianSeries[] = $meninggal;
            $migrasiMasukSeries[] = $masuk;
            $migrasiKeluarSeries[] = $keluar;
            $growthSeries[] = $lahir + $masuk - $meninggal - $keluar;
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

        $previousYearTotal = DinamikaPenduduk::query()
            ->where('tahun', $previousYear)
            ->when(
                $previousHasDusunSpecificData,
                fn ($query) => $query->whereNotNull('id_dusun'),
                fn ($query) => $query->whereNull('id_dusun')
            )
            ->selectRaw('SUM(jumlah_lahir) as jumlah_lahir')
            ->selectRaw('SUM(jumlah_meninggal) as jumlah_meninggal')
            ->selectRaw('SUM(jumlah_masuk) as jumlah_masuk')
            ->selectRaw('SUM(jumlah_keluar) as jumlah_keluar')
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

        $trendKelahiran = $trendFormatter($totalKelahiran, (int) ($previousYearTotal->jumlah_lahir ?? 0));
        $trendKematian = $trendFormatter($totalKematian, (int) ($previousYearTotal->jumlah_meninggal ?? 0));
        $trendMigrasiMasuk = $trendFormatter($totalMigrasiMasuk, (int) ($previousYearTotal->jumlah_masuk ?? 0));
        $trendMigrasiKeluar = $trendFormatter($totalMigrasiKeluar, (int) ($previousYearTotal->jumlah_keluar ?? 0));

        $yearStart = $selectedYear - 4;
        $yearlyRows = DinamikaPenduduk::query()
            ->whereBetween('tahun', [$yearStart, $selectedYear])
            ->when(
                $hasDusunSpecificData,
                fn ($query) => $query->whereNotNull('id_dusun'),
                fn ($query) => $query->whereNull('id_dusun')
            )
            ->select('tahun')
            ->selectRaw('SUM(jumlah_lahir) as jumlah_lahir')
            ->selectRaw('SUM(jumlah_meninggal) as jumlah_meninggal')
            ->selectRaw('SUM(jumlah_masuk) as jumlah_masuk')
            ->selectRaw('SUM(jumlah_keluar) as jumlah_keluar')
            ->groupBy('tahun')
            ->get()
            ->keyBy('tahun');

        $yearlyLabels = [];
        $yearlyLahir = [];
        $yearlyMeninggal = [];
        $yearlyMasuk = [];
        $yearlyKeluar = [];

        for ($year = $yearStart; $year <= $selectedYear; $year++) {
            $yearlyLabels[] = (string) $year;
            $row = $yearlyRows->get($year);
            $yearlyLahir[] = (int) ($row->jumlah_lahir ?? 0);
            $yearlyMeninggal[] = (int) ($row->jumlah_meninggal ?? 0);
            $yearlyMasuk[] = (int) ($row->jumlah_masuk ?? 0);
            $yearlyKeluar[] = (int) ($row->jumlah_keluar ?? 0);
        }

        return view('kasi.dinamika-penduduk', [
            'selectedYear' => $selectedYear,
            'yearOptions' => $yearOptions,
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
            'tahun' => 'required|integer|min:2000|max:2100',
            'bulan' => 'required|integer|min:1|max:12',
            'jumlah_lahir' => 'required|integer|min:0',
            'jumlah_meninggal' => 'required|integer|min:0',
            'jumlah_masuk' => 'required|integer|min:0',
            'jumlah_keluar' => 'required|integer|min:0',
        ], [
            'tahun.required' => 'Tahun wajib diisi.',
            'bulan.required' => 'Bulan wajib dipilih.',
        ]);

        DinamikaPenduduk::updateOrCreate(
            [
                'tahun' => $validated['tahun'],
                'bulan' => $validated['bulan'],
                'id_dusun' => null,
            ],
            [
                'jumlah_lahir' => $validated['jumlah_lahir'],
                'jumlah_meninggal' => $validated['jumlah_meninggal'],
                'jumlah_masuk' => $validated['jumlah_masuk'],
                'jumlah_keluar' => $validated['jumlah_keluar'],
            ]
        );

        return redirect()
            ->route('kasi.dinamika', ['tahun' => $validated['tahun']])
            ->with('success', 'Rekap dinamika penduduk berhasil disimpan.');
    }
}
