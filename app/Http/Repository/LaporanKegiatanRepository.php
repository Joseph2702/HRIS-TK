<?php

namespace App\Http\Repository;

use App\Domain\Entity\LaporanKegiatan;
use Illuminate\Pagination\LengthAwarePaginator;

class LaporanKegiatanRepository
{
    public function findAll(int $perPage = 15): LengthAwarePaginator
    {
        return LaporanKegiatan::with(['murid', 'guru.user', 'jadwal.kelas'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findById(int $id): ?LaporanKegiatan
    {
        return LaporanKegiatan::with(['murid', 'guru.user', 'jadwal.kelas', 'balasan.user'])
            ->find($id);
    }

    public function findByMurid(int $muridId, int $perPage = 15): LengthAwarePaginator
    {
        return LaporanKegiatan::with(['guru.user', 'jadwal.kelas', 'balasan'])
            ->where('id_murid', $muridId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findByOrangTua(int $orangTuaId, int $perPage = 15, ?int $muridId = null): LengthAwarePaginator
    {
        $query = LaporanKegiatan::with(['murid', 'guru.user', 'jadwal.kelas', 'balasan'])
            ->whereHas('murid', function ($q) use ($orangTuaId, $muridId) {
                $q->where('id_orang_tua', $orangTuaId);
                if ($muridId !== null) {
                    $q->where('id_murid', $muridId);
                }
            })
            ->orderByDesc('created_at');

        return $query->paginate($perPage);
    }

    public function create(array $data): LaporanKegiatan
    {
        return LaporanKegiatan::create($data);
    }

    public function update(LaporanKegiatan $laporan, array $data): LaporanKegiatan
    {
        $laporan->update($data);
        return $laporan->fresh(['murid', 'guru.user', 'jadwal.kelas']);
    }

    public function delete(LaporanKegiatan $laporan): void
    {
        $laporan->delete();
    }

    public function aggregateByDate(?int $klasId = null, ?int $muridId = null, ?string $fromDate = null, ?string $toDate = null): array
    {
        return $this->aggregateByDateForMuridIds($klasId, $muridId ? [$muridId] : null, $fromDate, $toDate);
    }

    public function aggregateByDateForMuridIds(?int $klasId = null, ?array $muridIds = null, ?string $fromDate = null, ?string $toDate = null): array
    {
        $query = LaporanKegiatan::query()
            ->whereNotNull('indikator');

        if ($klasId) {
            $query->whereHas('jadwal', function ($q) use ($klasId) {
                $q->where('id_kelas', $klasId);
            });
        }

        if (! empty($muridIds)) {
            $query->whereIn('id_murid', $muridIds);
        }

        if ($fromDate) {
            $query->where('created_at', '>=', $fromDate . ' 00:00:00');
        }

        if ($toDate) {
            $query->where('created_at', '<=', $toDate . ' 23:59:59');
        }

        $results = $query->get(['indikator', 'created_at'])->toArray();

        // Group by date and compute average
        $grouped = [];
        foreach ($results as $row) {
            $date = substr($row['created_at'], 0, 10); // YYYY-MM-DD
            $val = $this->mapIndicatorValue($row['indikator']);
            if (! isset($grouped[$date])) {
                $grouped[$date] = ['sum' => 0, 'count' => 0];
            }
            $grouped[$date]['sum'] += $val;
            $grouped[$date]['count'] += 1;
        }

        // Transform to array of { date, value }
        $output = [];
        foreach ($grouped as $date => $data) {
            $output[] = [
                'date' => $date,
                'value' => round($data['sum'] / $data['count'], 2),
            ];
        }

        // Sort by date
        usort($output, fn ($a, $b) => strcmp($a['date'], $b['date']));
        return $output;
    }


    private function mapIndicatorValue(string $indicator): int
    {
        return match ($indicator) {
            'BB' => 1,
            'MB' => 2,
            'BSH' => 3,
            'BSB' => 4,
            default => 0,
        };
    }
}

