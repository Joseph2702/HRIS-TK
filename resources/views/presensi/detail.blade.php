@extends('layouts.app')
@section('title', 'Presensi Kelas — Aluna')
@section('content')
<div x-data="presensiDetail()" x-init="init()">

    {{-- Banner dengan tombol back --}}
    <div class="page-banner flex items-center gap-3">
        <a href="/presensi" class="text-gray-600 hover:text-gray-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 x-text="namaKelas || 'Memuat...'"></h1>
    </div>

    {{-- Filter tanggal + aksi --}}
    <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
        <div>
            <p class="text-xs text-gray-500 mb-1 font-medium">Masukan tanggal hari ini</p>
            <div class="relative">
                <input type="date" x-model="tanggal" @change="loadMurid()"
                       class="input" style="width:200px;border-radius:9999px;padding:0.5rem 1rem">
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openTambahMurid()" class="btn-pink">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah
            </button>
            <button @click="editMode = !editMode" class="btn-blue">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </button>
            <button @click="simpanPresensi()" class="btn-green" :disabled="saving">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
            </button>
        </div>
    </div>

    {{-- Tabel murid --}}
    <div class="overflow-x-auto">
        <table class="aluna-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Murid</th>
                    <th class="text-center">Jenis Kelamin</th>
                    <th class="text-center">Umur</th>
                    <th class="text-center">ABK/Non ABK</th>
                    <th class="text-center">Kehadiran</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="loading">
                    <tr><td colspan="7" class="text-center py-8 text-gray-400 text-sm" style="background:transparent">Memuat data murid...</td></tr>
                </template>
                <template x-if="!loading && muridList.length === 0">
                    <tr><td colspan="7" class="text-center py-8 text-gray-400 text-sm" style="background:transparent">Belum ada murid di kelas ini</td></tr>
                </template>
                <template x-for="(m, idx) in muridList" :key="m.id_murid">
                    <tr>
                        <td x-text="idx + 1"></td>
                        <td class="font-medium" x-text="m.nama_murid"></td>
                        <td class="text-center" x-text="m.jenis_kelamin === 'laki-laki' ? 'L' : 'P'"></td>
                        <td class="text-center" x-text="hitungUmur(m.tanggal_lahir)"></td>
                        <td class="text-center">
                            <span class="text-xs text-gray-500">Non ABK</span>
                        </td>
                        <td class="text-center">
                            {{-- Toggle kehadiran --}}
                            <button @click="toggleKehadiran(m)"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none"
                                    :style="m.status_kehadiran === 'hadir' ? 'background:#9FD4A0' : 'background:#D1D5DB'">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200"
                                      :class="m.status_kehadiran === 'hadir' ? 'translate-x-6' : 'translate-x-1'"></span>
                            </button>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Description laporan kegiatan --}}
                                <button @click="openLaporan(m)"
                                        :disabled="!jadwalId"
                                        class="rounded-full border border-indigo-200 px-3 py-1 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        title="Description laporan kegiatan">
                                    Description
                                </button>

                                {{-- Edit murid --}}
                                <template x-if="editMode">
                                    <button @click="openEditMurid(m)" class="text-teal-500 hover:text-teal-700" title="Edit Murid">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- Modal Edit Murid --}}
    <div x-show="showEditMurid" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showEditMurid=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
            <h3 class="font-bold text-gray-800 mb-5">Edit Data Murid</h3>
            <form @submit.prevent="saveEditMurid()" class="space-y-4">
                <div><label class="label">Nama Murid</label><input type="text" x-model="editMuridForm.nama_murid" class="input" required></div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Jenis Kelamin</label>
                        <select x-model="editMuridForm.jenis_kelamin" class="input">
                            <option value="laki-laki">Laki-laki</option>
                            <option value="perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div><label class="label">Tanggal Lahir</label><input type="date" x-model="editMuridForm.tanggal_lahir" class="input"></div>
                </div>
                <div>
                    <label class="label">Status</label>
                    <select x-model="editMuridForm.status_murid" class="input">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showEditMurid=false" class="btn-salmon">Batal</button>
                    <button type="submit" class="btn-green" :disabled="savingMurid" x-text="savingMurid?'Menyimpan...':'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tambah Murid --}}
    <div x-show="showTambahMurid" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showTambahMurid=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
            <h3 class="font-bold text-gray-800 mb-5">Tambah Murid ke Kelas</h3>
            <form @submit.prevent="saveTambahMurid()" class="space-y-4">
                <div><label class="label">Nama Murid</label><input type="text" x-model="tambahForm.nama_murid" class="input" required></div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Jenis Kelamin</label>
                        <select x-model="tambahForm.jenis_kelamin" class="input">
                            <option value="laki-laki">Laki-laki</option>
                            <option value="perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div><label class="label">Tanggal Lahir</label><input type="date" x-model="tambahForm.tanggal_lahir" class="input"></div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showTambahMurid=false" class="btn-salmon">Batal</button>
                    <button type="submit" class="btn-green" :disabled="savingMurid" x-text="savingMurid?'Menyimpan...':'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function presensiDetail() {
    const kelasId = new URLSearchParams(location.search).get('kelas_id') || location.pathname.split('/').pop();
    return {
        kelasId: kelasId,
        namaKelas: '',
        tanggal: new Date().toISOString().split('T')[0],
        jadwalId: null,
        muridList: [],
        loading: true,
        saving: false,
        editMode: false,
        showEditMurid: false,
        showTambahMurid: false,
        savingMurid: false,
        editMuridId: null,
        editMuridForm: { nama_murid: '', jenis_kelamin: 'laki-laki', tanggal_lahir: '', status_murid: 'aktif' },
        tambahForm: { nama_murid: '', jenis_kelamin: 'laki-laki', tanggal_lahir: '' },

        async init() {
            // Load kelas info
            const kr = await api.get(`/kelas/${kelasId}`);
            if (kr?.ok) this.namaKelas = kr.data.data?.nama_kelas || '';
            await this.loadMurid();
        },

        async loadMurid() {
            this.loading = true;
            // Get jadwal by kelas + tanggal
            const jr = await api.get(`/kelas/${kelasId}/jadwal`);
            const jadwalList = jr?.data?.data || [];
            const jadwal = jadwalList.find(j => j.tanggal === this.tanggal) || jadwalList[0];
            this.jadwalId = jadwal?.id_jadwal || null;

            if (!this.jadwalId) {
                // No jadwal, just load murid without presensi
                const mr = await api.get(`/murid?kelas_id=${kelasId}`);
                const allMurid = mr?.data?.data || [];
                this.muridList = allMurid.filter(m => m.id_kelas == kelasId).map(m => ({
                    ...m, status_kehadiran: 'hadir', keterangan: ''
                }));
                this.loading = false;
                return;
            }

            // Load presensi for jadwal
            const pr = await api.get(`/jadwal/${this.jadwalId}/presensi`);
            if (pr?.ok) {
                this.muridList = (pr.data.data || []).map(p => ({
                    id_murid: p.id_murid,
                    nama_murid: p.nama_murid,
                    jenis_kelamin: p.jenis_kelamin || 'laki-laki',
                    tanggal_lahir: p.tanggal_lahir,
                    id_kelas: parseInt(kelasId),
                    status_kehadiran: p.presensi?.status_kehadiran || 'hadir',
                    keterangan: p.presensi?.keterangan || '',
                }));
            }
            this.loading = false;
        },

        toggleKehadiran(m) {
            m.status_kehadiran = m.status_kehadiran === 'hadir' ? 'tidak_hadir' : 'hadir';
        },

        async simpanPresensi() {
            if (!this.jadwalId) {
                Alpine.store('notif').error('Pilih tanggal yang memiliki jadwal kelas');
                return;
            }
            this.saving = true;
            const r = await api.post(`/jadwal/${this.jadwalId}/presensi`, {
                presensi: this.muridList.map(m => ({
                    id_murid: m.id_murid,
                    status_kehadiran: m.status_kehadiran,
                    keterangan: m.keterangan || '',
                }))
            });
            this.saving = false;
            if (r?.ok) Alpine.store('notif').success('Presensi berhasil disimpan');
            else Alpine.store('notif').error(r?.data?.message || 'Gagal menyimpan presensi');
        },

        openEditMurid(m) {
            this.editMuridId = m.id_murid;
            this.editMuridForm = { nama_murid: m.nama_murid, jenis_kelamin: m.jenis_kelamin || 'laki-laki', tanggal_lahir: m.tanggal_lahir || '', status_murid: m.status_murid || 'aktif' };
            this.showEditMurid = true;
        },

        async saveEditMurid() {
            this.savingMurid = true;
            const r = await api.post(`/murid/${this.editMuridId}`, this.editMuridForm);
            this.savingMurid = false;
            if (r?.ok) {
                this.showEditMurid = false;
                Alpine.store('notif').success('Data murid berhasil diperbarui');
                await this.loadMurid();
            } else Alpine.store('notif').error(r?.data?.message || 'Gagal menyimpan');
        },

        openTambahMurid() {
            this.tambahForm = { nama_murid: '', jenis_kelamin: 'laki-laki', tanggal_lahir: '' };
            this.showTambahMurid = true;
        },

        async saveTambahMurid() {
            this.savingMurid = true;
            const r = await api.post('/murid', { ...this.tambahForm, id_kelas: parseInt(kelasId) });
            this.savingMurid = false;
            if (r?.ok) {
                this.showTambahMurid = false;
                Alpine.store('notif').success('Murid berhasil ditambahkan');
                await this.loadMurid();
            } else Alpine.store('notif').error(r?.data?.message || 'Gagal menambahkan murid');
        },

        openLaporan(m) {
            if (!this.jadwalId) {
                Alpine.store('notif').error('Pilih tanggal dengan jadwal kelas terlebih dahulu');
                return;
            }

            const url = new URL(window.location.origin + '/presensi/laporan');
            url.searchParams.set('murid_id', m.id_murid);
            url.searchParams.set('nama', m.nama_murid);
            url.searchParams.set('kelas_id', this.kelasId);
            url.searchParams.set('jadwal_id', this.jadwalId);
            url.searchParams.set('tanggal', this.tanggal);
            window.location.href = url.toString();
        },

        hitungUmur(tgl) {
            if (!tgl) return '-';
            const diff = Date.now() - new Date(tgl).getTime();
            return Math.floor(diff / (1000 * 60 * 60 * 24 * 365));
        },
    };
}
</script>
@endsection
