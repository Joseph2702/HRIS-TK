@extends('layouts.app')
@section('title', 'Riwayat Presensi — Aluna')
@section('content')
<div x-data="presensiHistoryPage()" x-init="init()">

    <div class="page-banner"><h1>Riwayat Presensi</h1></div>

    <div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-5 mb-6">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-700 mb-3">Pilih Kelas</p>
            <select x-model="selectedClassId" @change="loadHistory()" class="input w-full">
                <option value="">Pilih kelas</option>
                <template x-for="kelas in kelasList" :key="kelas.id_kelas">
                    <option :value="kelas.id_kelas" x-text="kelas.nama_kelas"></option>
                </template>
            </select>
            <template x-if="selectedKelas">
                <div class="mt-4 rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                    <p class="font-semibold text-gray-800" x-text="selectedKelas.nama_kelas"></p>
                    <p x-text="selectedKelas.deskripsi || 'Tidak ada deskripsi kelas'" class="mt-1"></p>
                </div>
            </template>
        </div>

        <div class="space-y-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <input type="text" x-model="search" class="input-search w-full" placeholder="Cari topik, tanggal, atau status">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="flex items-center gap-2">
                    <input type="date" x-model="fromDate" class="input" style="width:170px;" />
                    <span class="text-sm text-gray-500">s/d</span>
                    <input type="date" x-model="toDate" class="input" style="width:170px;" />
                </div>
                <button type="button" @click="loadHistory()" class="btn-pink">Refresh</button>
                <button type="button" @click="exportCsv()" class="btn-green ml-auto" :disabled="!selectedClassId || filteredHistory.length === 0">Export CSV</button>
            </div>

            <div class="rounded-3xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3">
                    <template x-if="loading">
                        <div class="text-center text-gray-400 py-10">Memuat riwayat...</div>
                    </template>
                    <template x-if="!loading && !selectedClassId">
                        <div class="text-center text-gray-400 py-10">Silakan pilih kelas untuk melihat riwayat presensi.</div>
                    </template>
                    <template x-if="!loading && selectedClassId && filteredHistory.length === 0">
                        <div class="text-center text-gray-400 py-10">Tidak ditemukan riwayat presensi untuk kelas ini.</div>
                    </template>

                    <template x-if="!loading && selectedClassId && filteredHistory.length > 0">
                        <div class="overflow-x-auto">
                            <table class="aluna-table w-full">
                                <thead>
                                    <tr>
                                        <th class="text-left">Tanggal</th>
                                        <th class="text-left">Topik</th>
                                        <th class="text-left">Jam</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="jadwal in filteredHistory" :key="jadwal.id_jadwal">
                                        <tr>
                                            <td class="font-medium" x-text="formatTanggal(jadwal.tanggal)"></td>
                                            <td x-text="jadwal.topik || 'Tanpa topik'"></td>
                                            <td x-text="(jadwal.jam_mulai || '-') + ' – ' + (jadwal.jam_selesai || '-')" class="text-sm text-gray-600"></td>
                                            <td class="text-center">
                                                <button @click="showDetail(jadwal)" class="btn-blue py-1 px-3 text-xs">Detail</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div x-show="showDetailModal" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/30" @click="showDetailModal=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-3xl p-6 overflow-y-auto max-h-[90vh]" @click.stop>
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800" x-text="detailJadwal ? detailJadwal.topik || 'Detail Presensi' : 'Detail Presensi'"></h3>
                    <p class="text-xs text-gray-500" x-text="detailJadwal ? formatTanggal(detailJadwal.tanggal) + ' · ' + (detailJadwal.jam_mulai || '-') + ' – ' + (detailJadwal.jam_selesai || '-') : ''"></p>
                </div>
                <button @click="showDetailModal=false" class="text-gray-500 hover:text-gray-700">Tutup</button>
            </div>
            <div class="overflow-x-auto">
                <table class="aluna-table w-full">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Murid</th>
                            <th class="text-center">Status Kehadiran</th>
                            <th class="text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="detailRows.length === 0">
                            <tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada data presensi tersedia.</td></tr>
                        </template>
                        <template x-for="(row, idx) in detailRows" :key="row.id_murid">
                            <tr>
                                <td x-text="idx + 1"></td>
                                <td x-text="row.nama_murid"></td>
                                <td class="text-center">
                                    <span :class="row.presensi?.status_kehadiran === 'hadir' ? 'badge-green' : 'badge-red'" x-text="row.presensi?.status_kehadiran || 'tidak hadir'"></span>
                                </td>
                                <td x-text="row.presensi?.keterangan || '-'" class="text-left"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function presensiHistoryPage() {
    return {
        kelasList: [], selectedClassId: null, selectedKelas: null,
        jadwalList: [], loading: true, search: '', fromDate: '', toDate: '',
        showDetailModal: false,
        detailRows: [],
        detailJadwal: null,

        get filteredHistory() {
            const keyword = this.search.toLowerCase();
            const from = this.fromDate ? new Date(this.fromDate) : null;
            const to = this.toDate ? new Date(this.toDate) : null;

            return this.jadwalList.filter(jadwal => {
                const tanggal = this.formatTanggal(jadwal.tanggal).toLowerCase();
                const topik = (jadwal.topik || '').toLowerCase();
                const jam = `${jadwal.jam_mulai || ''} ${jadwal.jam_selesai || ''}`.toLowerCase();
                const tanggalValue = new Date(jadwal.tanggal);

                if (from && tanggalValue < from) {
                    return false;
                }
                if (to && tanggalValue > to) {
                    return false;
                }

                return tanggal.includes(keyword) || topik.includes(keyword) || jam.includes(keyword);
            });
        },

        async init() {
            const params = new URLSearchParams(window.location.search);
            const preselected = params.get('kelas_id');
            const r = await api.get('/kelas');
            this.loading = false;
            if (r?.ok) {
                this.kelasList = r.data.data?.data || [];
                if (preselected) {
                    this.selectedClassId = preselected;
                    await this.loadHistory();
                }
            }
        },

        async loadHistory() {
            if (!this.selectedClassId) {
                this.selectedKelas = null;
                this.jadwalList = [];
                return;
            }

            this.loading = true;
            this.selectedKelas = this.kelasList.find(k => String(k.id_kelas) === String(this.selectedClassId)) || null;
            const r = await api.get(`/kelas/${this.selectedClassId}/jadwal`);
            if (!r?.ok) {
                this.jadwalList = [];
                this.loading = false;
                return;
            }

            const jadwalList = r.data.data || [];
            const result = [];
            for (const jadwal of jadwalList) {
                const pr = await api.get(`/jadwal/${jadwal.id_jadwal}/presensi`);
                const presensiRows = (pr?.data?.data || []).map(p => p.presensi ? {
                    id_presensi:       p.presensi.id_presensi,
                    status_kehadiran:  p.presensi.status_kehadiran,
                } : null).filter(Boolean);

                result.push({
                    ...jadwal,
                    hadir_count:       presensiRows.filter(p => p.status_kehadiran === 'hadir').length,
                    tidak_hadir_count: presensiRows.filter(p => p.status_kehadiran === 'tidak_hadir').length,
                });
            }

            this.jadwalList = result.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));
            this.loading = false;
        },

        async showDetail(jadwal) {
            this.detailJadwal = jadwal;
            this.showDetailModal = true;
            this.detailRows = [];

            const pr = await api.get(`/jadwal/${jadwal.id_jadwal}/presensi`);
            if (pr?.ok) {
                this.detailRows = pr.data.data || [];
            }
        },

        formatTanggal(d) {
            return d ? new Date(d).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) : '-';
        },

        exportCsv() {
            if (!this.selectedClassId || this.filteredHistory.length === 0) {
                return;
            }

            const rows = [
                ['Tanggal', 'Topik', 'Jam'],
                ...this.filteredHistory.map(jadwal => [
                    this.formatTanggal(jadwal.tanggal),
                    jadwal.topik || 'Tanpa topik',
                    `${jadwal.jam_mulai || '-'} – ${jadwal.jam_selesai || '-'}`,
                ]),
            ];

            const csvContent = rows.map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            const kelasName = this.selectedKelas?.nama_kelas ? this.selectedKelas.nama_kelas.replace(/\s+/g, '-').toLowerCase() : 'kelas';
            const rangeLabel = this.fromDate || this.toDate ? `_${this.fromDate || 'awal'}-${this.toDate || 'akhir'}` : '';
            link.download = `riwayat-presensi-${kelasName}${rangeLabel}.csv`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        },
    };
}
</script>
@endsection