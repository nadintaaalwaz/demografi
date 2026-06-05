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

        $dusunLabels = $dusunRows->pluck('nama')->map(fn($v) => (string) $v)->all();
        $dusunData = $dusunRows->pluck('total')->map(fn($v) => (int) $v)->all();

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

        $rwLabels = [];
        $rwData = [];
        foreach ($rwRows as $r) {
            $dusunName = $dusunMap[$r->id_dusun] ?? 'Dusun';
            $label = $scopeDusun ? sprintf('RW %s', $r->rw) : sprintf('%s - RW %s', $dusunName, $r->rw);
            $rwLabels[] = $label;
            $rwData[] = (int) $r->total;
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

        $rtLabels = [];
        $rtData = [];
        foreach ($rtRows as $r) {
            $dusunName = $dusunMap[$r->id_dusun] ?? 'Dusun';
            $label = $scopeDusun ? sprintf('RW %s - RT %s', $r->rw, $r->rt) : sprintf('%s - RW %s - RT %s', $dusunName, $r->rw, $r->rt);
            $rtLabels[] = $label;
            $rtData[] = (int) $r->total;
        }

        return response()->json([
            'dusun' => ['labels' => $dusunLabels, 'data' => $dusunData],
            'rw' => ['labels' => $rwLabels, 'data' => $rwData],
            'rt' => ['labels' => $rtLabels, 'data' => $rtData],
        ]);
    }
}
