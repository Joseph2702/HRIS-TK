@extends('layouts.app')
@section('title', 'Manajemen Kelas — Aluna Monitoring')
@section('page-title', 'Manajemen Kelas')

@section('content')
<div x-data="kelasPage()" x-init="init()">
    <div class="flex justify-end mb-6">
        <button @click="openModal()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kelas
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <template x-if="loading">
            <div class="card text-center text-gray-400 col-span-3 py-8">Memuat data...</div>
        </template>
        <template x-if="!loading && items.length === 0">
            <div class="card text-center text-gray-400 col-span-3 py-8">Belum ada kelas</div>
        </template>
        <template x-for="item in items" :key="item.id_kelas">
            <div class="card hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="flex gap-2">
                        <button @click="editItem(item)" class="text-indigo-600 hover:text-indigo-800 text-sm">Edit</button>
                        <button @click="confirmDelete(item)" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                    </div>
                </div>
                <h3 class="font-semibold text-gray-800" x-text="item.nama_kelas"></h3>
                <p class="text-sm text-gray-400 mt-1 line-clamp-2" x-text="item.deskripsi || 'Tidak ada deskripsi'"></p>
                <div class="mt-3 flex items-center gap-2">
                    <span class="badge-blue" x-text="`${item.murid_count || 0} Murid`"></span>
                </div>
            </div>
        </template>
    </div>

    {{-- Modal Form --}}
    <div x-show="showModal" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
            <h3 class="text-lg font-semibold text-gray-800 mb-5" x-text="editId ? 'Edit Kelas' : 'Tambah Kelas'"></h3>
            <form @submit.prevent="save()" class="space-y-4">
                <div>
                    <label class="label">Nama Kelas</label>
                    <input type="text" x-model="form.nama_kelas" class="input" required>
                </div>
                <div>
                    <label class="label">Deskripsi</label>
                    <textarea x-model="form.deskripsi" class="input h-24 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary" :disabled="saving">
                        <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Confirm Delete --}}
    <div x-show="showDelete" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showDelete = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6" @click.stop>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Hapus Kelas</h3>
            <p class="text-gray-500 text-sm mb-5">Apakah Anda yakin ingin menghapus kelas ini?</p>
            <div class="flex justify-end gap-3">
                <button @click="showDelete = false" class="btn-secondary">Batal</button>
                <button @click="deleteItem()" class="btn-danger">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
function kelasPage() {
    return {
        items: [], loading: true, showModal: false, showDelete: false,
        editId: null, deleteId: null, saving: false,
        form: { nama_kelas: '', deskripsi: '' },

        async init() { await this.load(); },
        async load() {
            this.loading = true;
            const res = await api.get('/kelas');
            this.loading = false;
            if (res?.ok) this.items = res.data.data?.data || [];
        },
        openModal() { this.editId = null; this.form = { nama_kelas: '', deskripsi: '' }; this.showModal = true; },
        editItem(item) { this.editId = item.id_kelas; this.form = { nama_kelas: item.nama_kelas, deskripsi: item.deskripsi || '' }; this.showModal = true; },
        confirmDelete(item) { this.deleteId = item.id_kelas; this.showDelete = true; },
        async save() {
            this.saving = true;
            const res = this.editId
                ? await api.put(`/kelas/${this.editId}`, this.form)
                : await api.post('/kelas', this.form);
            this.saving = false;
            if (res?.ok) { this.showModal = false; Alpine.store('notif').success('Kelas berhasil disimpan'); await this.load(); }
            else { Alpine.store('notif').error(res?.data?.message || 'Gagal menyimpan'); }
        },
        async deleteItem() {
            const res = await api.del(`/kelas/${this.deleteId}`);
            this.showDelete = false;
            if (res?.ok) { Alpine.store('notif').success('Kelas berhasil dihapus'); await this.load(); }
            else { Alpine.store('notif').error(res?.data?.message || 'Gagal menghapus'); }
        },
    };
}
</script>
@endsection
