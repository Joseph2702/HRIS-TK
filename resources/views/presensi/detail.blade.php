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
            <button x-show="isAdmin" @click="openTambahMurid()" class="btn-pink">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah
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
                <div class="relative">
                    <label class="label">Cari Orang Tua</label>
                    <input type="text"
                           x-model="parentSearch"
                           @focus="showParentDropdown = true"
                           @input="tambahForm.id_orang_tua = ''; tambahForm.id_murid = ''; showParentDropdown = true"
                           class="input"
                           placeholder="Cari nama orang tua..."
                           autocomplete="off">
                    <div x-show="showParentDropdown && filteredParents.length > 0"
                         @click.away="showParentDropdown = false"
                         class="absolute z-20 mt-1 w-full overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg max-h-60 overflow-y-auto">
                        <template x-for="parent in filteredParents" :key="parent.id_orang_tua">
                            <button type="button"
                                    @click="selectParent(parent)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                <span x-text="parent.nama_orang_tua"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="label">Nama Anak</label>
                    <select x-model="tambahForm.id_murid" class="input" :disabled="!tambahForm.id_orang_tua">
                        <option value="">-- Pilih Anak --</option>
                        <template x-for="child in childOptions" :key="child.id_murid">
                            <option :value="child.id_murid"
                                    x-text="child.nama_murid + (child.kelas?.nama_kelas ? ' (' + child.kelas.nama_kelas + ')' : '')"></option>
                        </template>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hanya anak yang belum terdaftar di kelas manapun yang muncul di sini.</p>
                </div>
                <template x-if="tambahForm.id_orang_tua && childOptions.length === 0">
                    <p class="text-sm text-gray-500">Orang tua ini tidak memiliki anak yang belum terdaftar di kelas manapun.</p>
                </template>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showTambahMurid=false" class="btn-salmon">Batal</button>
                    <button type="submit" class="btn-green" :disabled="savingMurid || !tambahForm.id_murid" x-text="savingMurid?'Menyimpan...':'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function presensiDetail() {
    const kelasId = new URLSearchParams(location.search).get('kelas_id') || location.pathname.split('/').pop();
    const user = api.getUser() || {};
    return {
        kelasId: kelasId,
        namaKelas: '',
        tanggal: new Date().toISOString().split('T')[0],
        jadwalId: null,
        muridList: [],
        loading: true,
        saving: false,
        showEditMurid: false,
        showTambahMurid: false,
        showParentDropdown: false,
        savingMurid: false,
        editMuridId: null,
        editMuridForm: { nama_murid: '', jenis_kelamin: 'laki-laki', tanggal_lahir: '', status_murid: 'aktif' },
        tambahForm: { id_orang_tua: '', id_murid: '' },
        parentSearch: '',
        parents: [],
        allMurid: [],
        get isAdmin() { return (user.roles || []).includes('admin'); },
        get filteredParents() {
            const search = this.parentSearch.toLowerCase();
            return this.parents.filter(p => p.nama_orang_tua.toLowerCase().includes(search));
        },
        get eligibleChildren() {
            return this.allMurid.filter(m =>
                m.id_kelas == null || m.id_kelas === '' || m.id_kelas === 0 || m.id_kelas === '0'
            );
        },
        get childOptions() {
            if (!this.tambahForm.id_orang_tua) return [];
            return this.eligibleChildren.filter(m => String(m.id_orang_tua) === String(this.tambahForm.id_orang_tua));
        },

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
            this.parentSearch = '';
            this.tambahForm = { id_orang_tua: '', id_murid: '' };
            this.showTambahMurid = true;
            if (this.parents.length === 0) {
                this.loadParentOptions();
            }
        },

        async loadParentOptions() {
            const r = await api.get('/murid');
            if (!r?.ok) {
                this.parents = [];
                this.allMurid = [];
                return;
            }

            const result = r.data.data;
            this.allMurid = Array.isArray(result)
                ? result
                : (result?.data || []);

            const map = new Map();
            for (const m of this.allMurid) {
                const orangTua = m.orang_tua;
                const user = orangTua?.user;
                if (!orangTua || !user) continue;
                const id = orangTua.id_orang_tua;
                if (!map.has(id)) {
                    map.set(id, { id_orang_tua: id, nama_orang_tua: user.nama });
                }
            }
            this.parents = Array.from(map.values()).sort((a, b) => a.nama_orang_tua.localeCompare(b.nama_orang_tua));
        },

        selectParent(parent) {
            this.tambahForm.id_orang_tua = parent.id_orang_tua;
            this.parentSearch = parent.nama_orang_tua;
            this.tambahForm.id_murid = '';
            this.showParentDropdown = false;
        },

        async saveTambahMurid() {
            this.savingMurid = true;
            if (!this.tambahForm.id_orang_tua || !this.tambahForm.id_murid) {
                Alpine.store('notif').error('Pilih orang tua dan anak terlebih dahulu');
                this.savingMurid = false;
                return;
            }

            const r = await api.post(`/murid/${this.tambahForm.id_murid}/kelas`, { id_kelas: parseInt(kelasId) });
            this.savingMurid = false;
            if (r?.ok) {
                this.showTambahMurid = false;
                Alpine.store('notif').success('Murid berhasil ditambahkan ke kelas');
                await this.loadMurid();
            } else {
                Alpine.store('notif').error(r?.data?.message || 'Gagal menambahkan murid');
            }
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
