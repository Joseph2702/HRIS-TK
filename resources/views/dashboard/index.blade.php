@extends('layouts.app')
@section('title', 'Dashboard — Aluna Monitoring')

@section('content')
<div x-data="dashboard()" x-init="init()">

    {{-- ── ORANG TUA: Grafik indikator (filter: anak + periode) ────────────────── --}}
    <template x-if="role === 'orang_tua'">
        <div>
            <div class="page-banner mb-6">
                <h1>Selamat datang, <span x-text="userName"></span>!</h1>
                <p class="text-sm text-gray-600 mt-1" x-text="'Hari ini: ' + tanggalHariIni"></p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h3 class="font-semibold text-gray-800 text-sm">Grafik Perkembangan Anak (Indikator)</h3>
                    <div class="flex items-center gap-2 flex-wrap">
                        <select x-model="parentFilter.murid_id" @change="loadParentTrend()" class="px-3 py-1 rounded-full border text-sm">
                            <option value="">Pilih Anak</option>
                            <template x-for="m in muridList" :key="m.id_murid">
                                <option :value="m.id_murid" x-text="m.nama_murid"></option>
                            </template>
                        </select>

                        <input type="date" x-model="parentFilter.from" @change="loadParentTrend()" class="px-3 py-1 rounded-full border text-sm">
                        <input type="date" x-model="parentFilter.to" @change="loadParentTrend()" class="px-3 py-1 rounded-full border text-sm">
                        <button @click="resetParentTrendFilters()" class="btn-salmon py-1 px-3">Reset</button>
                    </div>
                </div>

                <div>
                    <canvas id="parentIndikatorChart" width="900" height="320" class="w-full"></canvas>
                    <p class="text-xs text-gray-500 mt-3">Keterangan: BB = Belum Berkembang · MB = Mulai Berkembang · BSH = Berkembang Sesuai Harapan · BSB = Berkembang Sangat Baik</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-center gap-3 mt-6">
                <a href="/laporan" class="btn-pink">Lihat Laporan Kegiatan</a>
                <a href="/notifikasi" class="btn-blue">Notifikasi</a>
            </div>
        </div>
    </template>


    {{-- ── GURU DASHBOARD ──────────────────────────────────── --}}
    <template x-if="role === 'guru'">
        <div>
            <div class="page-banner mb-6">
                <h1>Selamat datang, <span x-text="userName"></span>!</h1>
                <p class="text-sm text-gray-600 mt-1" x-text="'Hari ini: ' + tanggalHariIni"></p>
            </div>

            {{-- Stats guru --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#C2DFF4">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Jadwal Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="loading ? '...' : stats.jadwalHariIni"></p>
                    </div>
                </div>
                {{-- Indikator Penilaian Trend Chart (Admin) --}}
                <!-- <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 text-sm">Grafik Perkembangan Anak (Indikator)</h3>
                        <div class="flex items-center gap-2">
                            <select x-model="filter.murid_id" @change="loadTrend()" class="px-3 py-1 rounded-full border text-sm">
                                <option value="">Semua Anak</option>
                                <template x-for="m in muridList" :key="m.id_murid"><option :value="m.id_murid" x-text="m.nama_murid"></option></template>
                            </select>
                            <select x-model="filter.kelas_id" @change="loadTrend()" class="px-3 py-1 rounded-full border text-sm">
                                <option value="">Semua Kelas</option>
                                <template x-for="k in kelasList" :key="k.id_kelas"><option :value="k.id_kelas" x-text="k.nama_kelas"></option></template>
                            </select>
                            <input type="date" x-model="filter.from" @change="loadTrend()" class="px-3 py-1 rounded-full border text-sm">
                            <input type="date" x-model="filter.to" @change="loadTrend()" class="px-3 py-1 rounded-full border text-sm">
                            <button @click="resetTrendFilters()" class="btn-salmon py-1 px-3">Reset</button>
                        </div>
                    </div>
                    <div>
                        <canvas id="indikatorChartAdmin" width="900" height="320" class="w-full"></canvas>
                        <p class="text-xs text-gray-500 mt-3">Keterangan: BB = Belum Berkembang · MB = Mulai Berkembang · BSH = Berkembang Sesuai Harapan · BSB = Berkembang Sangat Baik</p>
                    </div>
                </div> -->
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#EFC9EA">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Total Murid Diajar</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="loading ? '...' : stats.totalMurid"></p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#9FD4A0">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Laporan Dibuat</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="loading ? '...' : stats.totalLaporan"></p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#F4A9A8">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Artikel Ditulis</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="loading ? '...' : stats.totalArtikel"></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                {{-- Jadwal hari ini --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 text-sm">Jadwal Mengajar Hari Ini</h3>
                        <a href="/presensi" class="text-xs text-gray-400 hover:text-gray-600">Lihat Semua →</a>
                    </div>
                    <template x-if="jadwalHariIni.length === 0 && !loading">
                        <p class="text-gray-400 text-sm text-center py-6">Tidak ada jadwal hari ini</p>
                    </template>
                    <div class="space-y-3">
                        <template x-for="j in jadwalHariIni" :key="j.id_jadwal">
                            <div class="rounded-xl p-3 flex items-center gap-3" style="background:rgba(194,223,244,0.2)">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0" style="background:#C2DFF4">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-800" x-text="j.kelas?.nama_kelas"></p>
                                    <p class="text-xs text-gray-400" x-text="j.jam_mulai + ' – ' + j.jam_selesai + ' · ' + (j.topik || 'Tanpa topik')"></p>
                                </div>
                                <a :href="'/presensi/detail?kelas_id=' + j.id_kelas" class="text-xs btn-blue py-1 px-3">Absen</a>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Laporan terbaru --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 text-sm">Laporan Kegiatan Terbaru</h3>
                        <a href="/laporan" class="text-xs text-gray-400 hover:text-gray-600">Lihat Semua →</a>
                    </div>
                    <template x-if="laporanTerbaru.length === 0 && !loading">
                        <p class="text-gray-400 text-sm text-center py-6">Belum ada laporan</p>
                    </template>
                    <div class="space-y-3">
                        <template x-for="l in laporanTerbaru" :key="l.id_laporan">
                            <div class="rounded-xl p-3 flex items-start gap-3" style="background:rgba(239,201,234,0.2)">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0" style="background:#EFC9EA">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate" x-text="l.judul_laporan"></p>
                                    <p class="text-xs text-gray-400" x-text="(l.murid?.nama_murid || '-') + ' · ' + formatDate(l.created_at)"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            {{-- Indikator Penilaian Trend Chart (Guru) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800 text-sm">Grafik Perkembangan Anak (Indikator)</h3>
                    <div class="flex items-center gap-2">
                        <select x-model="filter.murid_id" @change="loadTrend()" class="px-3 py-1 rounded-full border text-sm">
                            <option value="">Semua Anak</option>
                            <template x-for="m in muridList" :key="m.id_murid"><option :value="m.id_murid" x-text="m.nama_murid"></option></template>
                        </select>
                        <select x-model="filter.kelas_id" @change="loadTrend()" class="px-3 py-1 rounded-full border text-sm">
                            <option value="">Semua Kelas</option>
                            <template x-for="k in kelasList" :key="k.id_kelas"><option :value="k.id_kelas" x-text="k.nama_kelas"></option></template>
                        </select>
                        <input type="date" x-model="filter.from" @change="loadTrend()" class="px-3 py-1 rounded-full border text-sm">
                        <input type="date" x-model="filter.to" @change="loadTrend()" class="px-3 py-1 rounded-full border text-sm">
                        <button @click="resetTrendFilters()" class="btn-salmon py-1 px-3">Reset</button>
                    </div>
                </div>
                <div>
                    <canvas id="indikatorChart" width="900" height="320" class="w-full"></canvas>
                    <p class="text-xs text-gray-500 mt-3">Keterangan: BB = Belum Berkembang · MB = Mulai Berkembang · BSH = Berkembang Sesuai Harapan · BSB = Berkembang Sangat Baik</p>
                </div>
            </div>
        </div>
    </template>

    {{-- ── ADMIN DASHBOARD ─────────────────────────────────── --}}
    <template x-if="role === 'admin'">
        <div>
            <div class="page-banner mb-6">
                <h1>Selamat datang, <span x-text="userName"></span>!</h1>
                <p class="text-sm text-gray-600 mt-1" x-text="'Hari ini: ' + tanggalHariIni"></p>
            </div>

            {{-- Stats 4 kolom --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <template x-for="s in adminStats" :key="s.label">
                    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0"
                             :style="'background:' + s.color">
                            <span class="text-xl" x-text="s.icon"></span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400" x-text="s.label"></p>
                            <p class="text-2xl font-bold text-gray-800" x-text="loading ? '...' : s.value"></p>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Indikator Trend Chart (Admin) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-6">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h3 class="font-semibold text-gray-800 text-sm">Grafik Perkembangan Anak (Indikator)</h3>
                    <div class="flex items-center gap-2 flex-wrap">
                        <select x-model="filter.murid_id" @change="loadTrend()" class="px-3 py-1 rounded-full border text-sm">
                            <option value="">Semua Anak</option>
                            <template x-for="m in muridList" :key="m.id_murid">
                                <option :value="m.id_murid" x-text="m.nama_murid"></option>
                            </template>
                        </select>
                        <select x-model="filter.kelas_id" @change="loadTrend()" class="px-3 py-1 rounded-full border text-sm">
                            <option value="">Semua Kelas</option>
                            <template x-for="k in kelasList" :key="k.id_kelas">
                                <option :value="k.id_kelas" x-text="k.nama_kelas"></option>
                            </template>
                        </select>
                        <input type="date" x-model="filter.from" @change="loadTrend()" class="px-3 py-1 rounded-full border text-sm">
                        <input type="date" x-model="filter.to" @change="loadTrend()" class="px-3 py-1 rounded-full border text-sm">
                        <button @click="resetTrendFilters()" class="btn-salmon py-1 px-3">Reset</button>
                    </div>
                </div>

                <div>
                    <canvas id="indikatorChartAdmin" width="900" height="320" class="w-full"></canvas>
                    <p class="text-xs text-gray-500 mt-3">Keterangan: BB = Belum Berkembang · MB = Mulai Berkembang · BSH = Berkembang Sesuai Harapan · BSB = Berkembang Sangat Baik</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                {{-- Kelas & Murid --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 text-sm">Kelas Aktif</h3>
                        <a href="/presensi" class="text-xs text-gray-400 hover:text-gray-600">Kelola →</a>
                    </div>
                    <div class="space-y-2">
                        <template x-for="k in kelasList" :key="k.id_kelas">
                            <div class="flex items-center justify-between rounded-full px-4 py-2.5" style="background:rgba(194,223,244,0.25)">
                                <span class="text-sm font-medium text-gray-700" x-text="k.nama_kelas"></span>
                                <span class="badge-blue text-xs" x-text="(k.murid_count || 0) + ' murid'"></span>
                            </div>
                        </template>
                        <template x-if="kelasList.length === 0 && !loading">
                            <p class="text-gray-400 text-xs text-center py-3">Belum ada kelas</p>
                        </template>
                    </div>
                </div>

                {{-- Artikel terbaru --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 text-sm">Artikel Terbaru</h3>
                        <a href="/artikel" class="text-xs text-gray-400 hover:text-gray-600">Lihat Semua →</a>
                    </div>
                    <div class="space-y-3">
                        <template x-if="artikelTerbaru.length === 0 && !loading">
                            <p class="text-gray-400 text-sm text-center py-4">Belum ada artikel</p>
                        </template>
                        <template x-for="a in artikelTerbaru" :key="a.id_artikel">
                            <div class="flex items-start gap-2">
                                <div class="w-2 h-2 rounded-full mt-1.5 shrink-0" style="background:#EFC9EA"></div>
                                <div class="min-w-0">
                                    <p class="text-sm text-gray-800 truncate" x-text="a.judul_artikel"></p>
                                    <p class="text-xs text-gray-400" x-text="a.pembuat?.nama + ' · ' + formatDate(a.tanggal_publish)"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Laporan & Presensi terbaru --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 text-sm">Laporan Terbaru</h3>
                        <a href="/laporan" class="text-xs text-gray-400 hover:text-gray-600">Lihat Semua →</a>
                    </div>
                    <div class="space-y-3">
                        <template x-if="laporanTerbaru.length === 0 && !loading">
                            <p class="text-gray-400 text-sm text-center py-4">Belum ada laporan</p>
                        </template>
                        <template x-for="l in laporanTerbaru" :key="l.id_laporan">
                            <div class="rounded-xl p-2.5" style="background:rgba(239,201,234,0.2)">
                                <p class="text-sm font-medium text-gray-800 truncate" x-text="l.judul_laporan"></p>
                                <p class="text-xs text-gray-400" x-text="(l.murid?.nama_murid || '-') + ' · ' + formatDate(l.created_at)"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function dashboard() {
    const user = api.getUser() || {};
    const roles = user.roles || [];
    const role  = roles.includes('admin') ? 'admin' : roles.includes('guru') ? 'guru' : 'orang_tua';

    return {
        role, loading: true,
        userName: user.nama || 'Pengguna',
        tanggalHariIni: new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }),

        // Admin
        adminStats: [
            { label: 'Total Murid', value: 0, icon: '👨‍🎓', color: '#EFC9EA' },
            { label: 'Total Kelas', value: 0, icon: '🏫',   color: '#C2DFF4' },
            { label: 'Total Guru',  value: 0, icon: '👩‍🏫', color: '#9FD4A0' },
        ],
        kelasList: [], artikelTerbaru: [], laporanTerbaru: [], muridList: [],

        // Trend chart filters & data
        filter: { murid_id: '', kelas_id: '', from: '', to: '' },
        parentFilter: { from: '', to: '' },
        trendData: [],


        // Guru
        stats: { jadwalHariIni: 0, totalMurid: 0, totalLaporan: 0, totalArtikel: 0 },
        jadwalHariIni: [],

        async init() {
            if (this.role === 'orang_tua') {
                // Default: 7 hari terakhir (eligibility window)
                const today = new Date();
                const to = today.toISOString().split('T')[0];
                const fromDate = new Date(today);
                fromDate.setDate(fromDate.getDate() - 6);
                const from = fromDate.toISOString().split('T')[0];

                this.parentFilter.from = from;
                this.parentFilter.to = to;

                // Load anak untuk dropdown (akan ditampilkan sesuai hasil endpoint /murid)
                try {
                    const mr = await api.get('/murid');
                    if (mr?.ok) {
                        const list = mr.data.data?.data || mr.data.data || mr.data?.data?.data || [];
                        this.muridList = Array.isArray(list) ? list : [];

                        if (!this.parentFilter.murid_id && this.muridList.length > 0) {
                            this.parentFilter.murid_id = this.muridList[0].id_murid;
                        }
                    }
                } catch (e) {}

                this.loading = false;
                try { await this.loadParentTrend(); } catch(e) {}
                return;
            }


            if (this.role === 'admin') {
                const [mr, kr, lr, ar] = await Promise.all([
                    api.get('/murid'),
                    api.get('/kelas'),
                    api.get('/laporan'),
                    api.get('/artikel'),
                ]);
                this.loading = false;

                // Default: 7 hari terakhir supaya chart tidak kosong/tergantung backend
                const today = new Date();
                const to = today.toISOString().split('T')[0];
                const fromDate = new Date(today);
                fromDate.setDate(fromDate.getDate() - 6);
                const from = fromDate.toISOString().split('T')[0];
                if (!this.filter.from) this.filter.from = from;
                if (!this.filter.to) this.filter.to = to;
                if (mr?.ok) { this.adminStats[0].value = mr.data.data?.total || 0; this.muridList = mr.data.data?.data || []; }
                if (kr?.ok) { this.adminStats[1].value = kr.data.data?.total || 0; this.kelasList = kr.data.data?.data?.slice(0,5) || []; }
                if (lr?.ok) { this.adminStats[3].value = lr.data.data?.total || 0; this.laporanTerbaru = lr.data.data?.data?.slice(0,4) || []; }
                if (ar?.ok) { this.artikelTerbaru = ar.data.data?.data?.slice(0,4) || []; }
                // Count guru from users (approximate)
                const ur = await api.get('/users');
                if (ur?.ok) {
                    const users = ur.data.data?.data || [];
                    this.adminStats[2].value = users.filter(u => u.roles?.some(r => r.nama_role === 'guru')).length;
                }

                // load initial trend data for admin
                try { await this.loadTrend(); } catch(e){}

            }

            if (this.role === 'guru') {
                const today = new Date().toISOString().split('T')[0];
                const [kr, lr, ar] = await Promise.all([
                    api.get('/kelas'),
                    api.get('/laporan'),
                    api.get('/artikel'),
                ]);
                this.loading = false;

                // Jadwal hari ini — get all kelas then their jadwal
                if (kr?.ok) {
                    const kelasList = kr.data.data?.data || [];
                    let allJadwal = [];
                    for (const k of kelasList) {
                        const jr = await api.get(`/kelas/${k.id_kelas}/jadwal`);
                        if (jr?.ok) allJadwal.push(...(jr.data.data || []));
                    }
                    this.jadwalHariIni = allJadwal.filter(j => j.tanggal === today);
                    this.stats.jadwalHariIni = this.jadwalHariIni.length;
                    // Total murid from all kelas
                    this.stats.totalMurid = kelasList.reduce((s, k) => s + (k.murid_count || 0), 0);
                    // set kelas list
                    this.kelasList = kelasList;
                    // load murid list for filters
                    const mr2 = await api.get('/murid');
                    if (mr2?.ok) this.muridList = mr2.data.data?.data || [];
                }
                if (lr?.ok) { this.laporanTerbaru = lr.data.data?.data?.slice(0,4) || []; this.stats.totalLaporan = lr.data.data?.total || 0; }
                if (ar?.ok) this.stats.totalArtikel = ar.data.data?.total || 0;
            }
            // load initial trend data after fetching lists
            try { if (this.role !== 'orang_tua') await this.loadTrend(); } catch(e){}
        },

        async loadTrend() {
            // Fetch from server-side aggregation endpoint
            // (admin & guru: 3 filter anak/kelas/periode)
            const params = new URLSearchParams();

            if (this.filter.murid_id) params.append('murid_id', this.filter.murid_id);
            if (this.filter.kelas_id) params.append('kelas_id', this.filter.kelas_id);
            if (this.filter.from) params.append('from', this.filter.from);
            if (this.filter.to) params.append('to', this.filter.to);

            const r = await api.get(`/laporan/trend/data?${params.toString()}`);
            if (!r?.ok) {
                this.trendData = [];
                this.drawCanvasChart('indikatorChart', [], {});
                this.drawCanvasChart('indikatorChartAdmin', [], {});
                return;
            }

            // Response is array of { date, value }
            const points = r.data.data || [];
            this.trendData = points;

            // draw on both canvases
            this.drawCanvasChart('indikatorChart', points, { showHover: true });
            this.drawCanvasChart('indikatorChartAdmin', points, { showHover: true });
        },

        resetTrendFilters() {
            this.filter = { murid_id: '', kelas_id: '', from: '', to: '' };
            this.loadTrend();
        },

        resetParentTrendFilters() {
            this.parentFilter = { murid_id: this.parentFilter.murid_id, from: '', to: '' };
            this.loadParentTrend();
        },

        async loadParentTrend() {
            const params = new URLSearchParams();
            if (this.parentFilter.murid_id) params.append('murid_id', this.parentFilter.murid_id);
            if (this.parentFilter.from) params.append('from', this.parentFilter.from);
            if (this.parentFilter.to) params.append('to', this.parentFilter.to);

            const r = await api.get(`/laporan/trend/data?${params.toString()}`);
            if (!r?.ok) {
                this.drawCanvasChart('parentIndikatorChart', [], {});
                return;
            }

            const points = r.data.data || [];
            this.trendData = points;
            this.drawCanvasChart('parentIndikatorChart', points, { showHover: true });
        },


        mapIndicator(ind) {
            if (!ind) return 0;
            return ind === 'BB' ? 1 : ind === 'MB' ? 2 : ind === 'BSH' ? 3 : ind === 'BSB' ? 4 : 0;
        },

        drawCanvasChart(canvasId, points, opts = {}) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const dpr = window.devicePixelRatio || 1;
            const w = canvas.width = canvas.clientWidth * dpr;
            const h = canvas.height = 320 * dpr;
            ctx.clearRect(0, 0, w, h);

            // Background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, w, h);

            // Margins and dimensions
            const left = 60 * dpr;
            const right = w - 20 * dpr;
            const top = 20 * dpr;
            const bottom = 55 * dpr; // slightly larger to keep labels inside
            const plotW = right - left;
            const plotH = h - top - bottom;

            // Draw Y-axis labels and grid (1-4)
            const labels = ['', 'BB', 'MB', 'BSH', 'BSB'];
            ctx.font = `${12 * dpr}px sans-serif`;
            ctx.fillStyle = '#6b7280';
            ctx.textAlign = 'right';

            for (let i = 1; i <= 4; i++) {
                const y = h - bottom - (i / 4) * plotH;
                ctx.fillText(labels[i], left - 8 * dpr, y + 4 * dpr);

                // Grid line
                ctx.strokeStyle = 'rgba(0, 0, 0, 0.05)';
                ctx.lineWidth = 1 * dpr;
                ctx.beginPath();
                ctx.moveTo(left, y);
                ctx.lineTo(right, y);
                ctx.stroke();
            }

            if (!points || points.length === 0) {
                ctx.fillStyle = '#9ca3af';
                ctx.textAlign = 'center';
                ctx.fillText('Tidak ada data indikator', w / 2, h / 2);
                return;
            }

            // Plot area border
            ctx.strokeStyle = '#e5e7eb';
            ctx.lineWidth = 1 * dpr;
            ctx.strokeRect(left, top, plotW, plotH);

            // Helper to robustly read y + date from API shape
            const getYVal = (p) => {
                if (p == null) return null;
                const y = p.value ?? p.y ?? p.indikator ?? p.indeks ?? p.score;
                const num = Number(y);
                return Number.isFinite(num) ? num : null;
            };
            const getDateStr = (p) => {
                return p?.date ?? p?.x ?? p?.waktu ?? null;
            };
            const formatDateLabel = (dateStr, idx) => {
                if (!dateStr) return `#${idx + 1}`;

                // timestamp (ms/sec)
                if (typeof dateStr === 'number') {
                    const ms = dateStr > 1e12 ? dateStr : dateStr * 1000;
                    const d = new Date(ms);
                    return d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' });
                }

                if (typeof dateStr !== 'string') return `#${idx + 1}`;

                // YYYY-MM-DD or YYYY-MM-DDTHH:mm:ss
                if (dateStr.includes('T')) dateStr = dateStr.split('T')[0];
                if (dateStr.includes('-')) {
                    const parts = dateStr.split('-');
                    if (parts.length >= 3) return `${parts[2]}/${parts[1]}`; // DD/MM
                }

                return dateStr; // fallback
            };

            // Draw line
            ctx.strokeStyle = '#3b82f6';
            ctx.lineWidth = 2.5 * dpr;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';

            const positions = points.map((p, idx) => {
                const yVal = getYVal(p);
                const dateStr = getDateStr(p);
                const x = left + (idx / Math.max(1, points.length - 1)) * plotW;
                const y = h - bottom - ((yVal ?? 0) / 4) * plotH;
                return { x, y, date: dateStr, val: yVal };
            });

            // Draw smooth curve using quadratic curves
            ctx.beginPath();
            ctx.moveTo(positions[0].x, positions[0].y);
            if (positions.length > 1) {
                for (let i = 1; i < positions.length; i++) {
                    const prev = positions[i - 1];
                    const curr = positions[i];
                    const cpx = (prev.x + curr.x) / 2;
                    const cpy = (prev.y + curr.y) / 2;
                    ctx.quadraticCurveTo(prev.x, prev.y, cpx, cpy);
                }
            }
            ctx.stroke();

            // Draw points and X-axis labels
            positions.forEach((p, idx) => {
                // Point circle
                ctx.fillStyle = '#3b82f6';
                ctx.beginPath();
                ctx.arc(p.x, p.y, 4 * dpr, 0, Math.PI * 2);
                ctx.fill();

                // X-axis label (inside canvas)
                ctx.fillStyle = '#9ca3af';
                ctx.textAlign = 'center';
                ctx.font = `${11 * dpr}px sans-serif`;
                const dateLabel = formatDateLabel(p.date, idx);
                ctx.fillText(dateLabel, p.x, h - bottom + 28 * dpr);
            });

            // Hover tooltip (if showHover)
            if (opts.showHover) {
                canvas.onmousemove = (e) => {
                    const rect = canvas.getBoundingClientRect();
                    const mx = (e.clientX - rect.left) * dpr;
                    const my = (e.clientY - rect.top) * dpr;

                    // Find closest point
                    let closest = null;
                    let minDist = 20 * dpr;
                    for (const p of positions) {
                        const dist = Math.hypot(p.x - mx, p.y - my);
                        if (dist < minDist) {
                            minDist = dist;
                            closest = p;
                        }
                    }

                    if (closest) {
                        // Highlight point
                        ctx.fillStyle = '#1f2937';
                        ctx.beginPath();
                        ctx.arc(closest.x, closest.y, 6 * dpr, 0, Math.PI * 2);
                        ctx.fill();

                        // Tooltip
                        const indLabel = ['', 'BB', 'MB', 'BSH', 'BSB'][Math.round(closest.val)];
                        const tooltipW = 100 * dpr;
                        const tooltipH = 32 * dpr;
                        const tooltipX = Math.max(left, Math.min(closest.x - tooltipW / 2, right - tooltipW));
                        const tooltipY = Math.max(top, closest.y - tooltipH - 10 * dpr);

                        ctx.fillStyle = 'rgba(0, 0, 0, 0.8)';
                        ctx.fillRect(tooltipX, tooltipY, tooltipW, tooltipH);

                        ctx.fillStyle = '#ffffff';
                        ctx.font = `${11 * dpr}px sans-serif`;
                        ctx.textAlign = 'center';
                        ctx.fillText(closest.date, tooltipX + tooltipW / 2, tooltipY + 10 * dpr);
                        ctx.fillText(indLabel + ' (' + closest.val.toFixed(2) + ')', tooltipX + tooltipW / 2, tooltipY + 22 * dpr);
                    }
                };
                canvas.onmouseleave = () => {
                    canvas.onmousemove = null;
                };
            }
        },

        formatDate(dt) {
            return dt ? new Date(dt).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '-';
        },
    };
}
</script>
@endsection
