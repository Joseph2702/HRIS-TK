@extends('layouts.app')
@section('title', 'Presensi — Aluna')
@section('content')
<div x-data="presensiPage()" x-init="init()">

    <div class="page-banner"><h1>Presensi Kehadiran Murid</h1></div>

    <div class="flex items-center justify-between mb-5">
        <div class="relative">
            <input type="text" x-model="search" class="input-search" placeholder="Search here">
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <button @click="openModal()" class="btn-pink">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah
        </button>
    </div>

    {{-- Tabel Kelas --}}
    <div class="overflow-x-auto">
        <table class="aluna-table">
            <thead>
                <tr>
                    <th class="text-left">No.</th>
                    <th class="text-left">Nama Kelas</th>
                    <th class="text-center">Jumlah Murid</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="loading">
                    <tr><td colspan="4" class="text-center py-8 text-gray-400 text-sm" style="background:transparent">Memuat data...</td></tr>
                </template>
                <template x-if="!loading && filtered.length===0">
                    <tr><td colspan="4" class="text-center py-8 text-gray-400 text-sm" style="background:transparent">Belum ada kelas</td></tr>
                </template>
                <template x-for="(item,idx) in filtered" :key="item.id_kelas">
                    <tr>
                        <td x-text="idx+1"></td>
                        <td class="font-medium" x-text="item.nama_kelas"></td>
                        <td class="text-center text-gray-600" x-text="(item.murid_count||0)+' orang'"></td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-3">

                                {{-- Icon Report: Riwayat Presensi --}}
                                <button @click="window.location.href='/presensi/history?kelas_id='+item.id_kelas"
                                        class="text-indigo-400 hover:text-indigo-600 transition-colors"
                                        title="Riwayat Presensi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </button>

                                {{-- Icon Edit: ke halaman presensi per kelas --}}
                                <button @click="window.location.href='/presensi/detail?kelas_id='+item.id_kelas"
                                        class="text-teal-500 hover:text-teal-700 transition-colors"
                                        title="Input Presensi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                {{-- Icon Hapus --}}
                                <button @click="confirmDelete(item)"
                                        class="text-red-400 hover:text-red-600 transition-colors"
                                        title="Hapus Kelas">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah Kelas (hanya nama, jumlah murid auto) --}}
    <div x-show="showModal" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showModal=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6" @click.stop>
            <h3 class="font-bold text-gray-800 mb-5">Tambah Kelas</h3>
            <form @submit.prevent="save()" class="space-y-4">
                <div>
                    <label class="label">Nama Kelas</label>
                    <input type="text" x-model="form.nama_kelas" class="input" required
                           placeholder="Contoh: Kuncup Anyelir">
                </div>
                <p class="text-xs text-gray-400">
                    Jumlah murid akan otomatis dihitung berdasarkan data murid yang diinput admin.
                </p>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showModal=false" class="btn-salmon">Batal</button>
                    <button type="submit" class="btn-green" :disabled="saving"
                            x-text="saving?'Menyimpan...':'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Confirm Hapus Kelas --}}
    <div x-show="showDelete" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showDelete=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center" @click.stop>
            <p class="text-gray-700 text-sm mb-5">Hapus kelas ini? Semua jadwal dan presensi terkait juga akan terhapus.</p>
            <div class="flex justify-center gap-3">
                <button @click="showDelete=false" class="btn-secondary">Batal</button>
                <button @click="deleteKelas()" class="btn-salmon">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
function presensiPage() {
    return {
        items: [], loading: true, search: '', saving: false,
        showModal: false, showDelete: false,
        deleteId: null,
        form: { nama_kelas: '' },

        get filtered() {
            return this.items.filter(i => i.nama_kelas?.toLowerCase().includes(this.search.toLowerCase()));
        },

        async init() {
            const r = await api.get('/kelas');
            this.loading = false;
            if (r?.ok) this.items = r.data.data?.data || [];
        },

        openModal() {
            this.form = { nama_kelas: '' };
            this.showModal = true;
        },

        confirmDelete(item) { this.deleteId = item.id_kelas; this.showDelete = true; },

        async save() {
            this.saving = true;
            const r = await api.post('/kelas', { nama_kelas: this.form.nama_kelas });
            this.saving = false;
            if (r?.ok) {
                this.showModal = false;
                Alpine.store('notif').success('Kelas berhasil ditambahkan');
                await this.init();
            } else {
                Alpine.store('notif').error(r?.data?.message || 'Gagal menyimpan');
            }
        },

        async deleteKelas() {
            const r = await api.del(`/kelas/${this.deleteId}`);
            this.showDelete = false;
            if (r?.ok) {
                Alpine.store('notif').success('Kelas berhasil dihapus');
                await this.init();
            } else {
                Alpine.store('notif').error(r?.data?.message || 'Gagal menghapus');
            }
        },

        formatTanggal(d) {
            return d ? new Date(d).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) : '-';
        },
    };
}
</script>
@endsection
