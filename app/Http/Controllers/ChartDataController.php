<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Penduduk;
use App\Models\Wilayah;

class ChartDataController extends Controller
{
    /**
     * Return aggregated penduduk counts per dusun, rw and rt.
     * Response structure:
     * {
     *   "dusun": { labels: [...], data: [...] },
     *   "rw": { labels: [...], data: [...] },
     *   "rt": { labels: [...], data: [...] }
     * }
     */
    public function pendudukDistribution(Request $request)
    {
        $user = Auth::user();
        $isKasun = $user && method_exists($user, 'isKasun') && $user->isKasun();
        $scopeDusun = $isKasun ? $user->id_dusun : null;

        // Dusun aggregation (left join to include dusun with zero)
        $dusunQuery = Wilayah::from('wilayah as w')
            ->leftJoin('penduduk as p', function ($join) use ($scopeDusun) {
                $join->on('p.id_dusun', '=', 'w.id')
                    ->where('p.status', '=', 'Aktif');
                if ($scopeDusun) {
                    $join->where('p.id_dusun', $scopeDusun);
                }
            })
            ->where('w.tipe', 'dusun')
            ->select('w.id', 'w.nama')
            ->selectRaw('COUNT(p.nik) as total')
            ->groupBy('w.id', 'w.nama')
            ->orderBy('w.nama');

        if ($scopeDusun) {
            $dusunQuery->where('w.id', $scopeDusun);
        }

        $dusunRows = $dusunQuery->get();

        $totalAktif = (int) $dusunRows->sum('total');

        $dusunRows = $dusunRows
            ->sortByDesc('total')
            ->values();

        $dusunLabels = $dusunRows->pluck('nama')->map(fn($v) => (string) $v)->all();
        $dusunData = $dusunRows->pluck('total')->map(fn($v) => (int) $v)->all();
        $dusunPersen = array_map(function ($value) use ($totalAktif) {
            if ($totalAktif <= 0) return 0;
            return round((($value ?: 0) / $totalAktif) * 100, 1);
        }, $dusunData);


        // RW aggregation grouped by id_dusun + rw
        $rwQuery = Penduduk::query()
            ->where('status', 'Aktif')
            ->when($scopeDusun, fn($q) => $q->where('id_dusun', $scopeDusun))
            ->select('id_dusun', 'rw')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('id_dusun', 'rw')
            ->orderBy('rw');

        $rwRows = $rwQuery->get();

        // Build map of dusun names
        $dusunMap = Wilayah::where('tipe', 'dusun')
            ->when($scopeDusun, fn($q) => $q->where('id', $scopeDusun))
            ->pluck('nama', 'id')
            ->all();

        $rwRows = $rwRows
            ->sortByDesc('total')
            ->values();

        $rwLabels = [];
        $rwData = [];
        $rwPersen = [];
        foreach ($rwRows as $r) {
            $dusunName = $dusunMap[$r->id_dusun] ?? 'Dusun';
            $label = $scopeDusun ? sprintf('RW %s', $r->rw) : sprintf('%s - RW %s', $dusunName, $r->rw);
            $rwLabels[] = $label;
            $value = (int) $r->total;
            $rwData[] = $value;
            $rwPersen[] = $totalAktif > 0 ? round(($value / $totalAktif) * 100, 1) : 0;
        }


        // RT aggregation grouped by id_dusun + rw + rt
        $rtQuery = Penduduk::query()
            ->where('status', 'Aktif')
            ->when($scopeDusun, fn($q) => $q->where('id_dusun', $scopeDusun))
            ->select('id_dusun', 'rw', 'rt')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('id_dusun', 'rw', 'rt')
            ->orderBy('rw')
            ->orderBy('rt');

        $rtRows = $rtQuery->get();

        // Sort by total desc so chart is ordered correctly
        $rtRows = $rtRows
            ->sortByDesc('total')
            ->values();

        $rtLabels = [];
        $rtData = [];
        $rtPersen = [];
        foreach ($rtRows as $r) {
            $dusunName = $dusunMap[$r->id_dusun] ?? 'Dusun';
            $label = $scopeDusun ? sprintf('RW %s - RT %s', $r->rw, $r->rt) : sprintf('%s - RW %s - RT %s', $dusunName, $r->rw, $r->rt);
            $rtLabels[] = $label;
            $value = (int) $r->total;
            $rtData[] = $value;
            $rtPersen[] = $totalAktif > 0 ? round(($value / $totalAktif) * 100, 1) : 0;
        }


        // Build detail arrays for RW and RT (structure without relying on label parsing)
        $rwDetails = [];
        foreach ($rwRows as $r) {
            $rwDetails[] = [
                'id_dusun' => (int) $r->id_dusun,
                'rw' => (string) $r->rw,
                'count' => (int) $r->total,
            ];
        }

        $rtDetails = [];
        foreach ($rtRows as $r) {
            $rtDetails[] = [
                'id_dusun' => (int) $r->id_dusun,
                'rw' => (string) $r->rw,
                'rt' => (string) $r->rt,
                'count' => (int) $r->total,
            ];
        }

        return response()->json([
            'dusun' => ['labels' => $dusunLabels, 'data' => $dusunData, 'persen' => $dusunPersen ?? []],
            'rw' => ['labels' => $rwLabels, 'data' => $rwData, 'persen' => $rwPersen ?? [], 'details' => $rwDetails],
            'rt' => ['labels' => $rtLabels, 'data' => $rtData, 'persen' => $rtPersen ?? [], 'details' => $rtDetails],
        ]);



    }
}
