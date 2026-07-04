@extends('layouts.app')
@section('title', 'Profil Anak — Aluna Monitoring')
@section('page-title', 'Profil Anak')

@section('content')
<div x-data="muridPage()" x-init="init()">
    <div class="flex justify-end mb-6">
        <button @click="openModal()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Profil Anak
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <template x-if="loading">
            <div class="card text-center text-gray-400 col-span-3 py-8">Memuat data...</div>
        </template>
        <template x-if="!loading && items.length === 0">
            <div class="card text-center text-gray-400 col-span-3 py-8">Belum ada profil anak</div>
        </template>
        <template x-for="item in items" :key="item.id_murid">
            <div class="card hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 overflow-hidden flex items-center justify-center shrink-0">
                        <template x-if="item.foto_murid">
                            <img :src="'/storage/' + item.foto_murid" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!item.foto_murid">
                            <span class="text-xl font-bold text-indigo-400" x-text="item.nama_murid.charAt(0)"></span>
                        </template>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800" x-text="item.nama_murid"></h3>
                        <p class="text-sm text-gray-400" x-text="item.kelas?.nama_kelas || 'Belum di kelas'"></p>
                    </div>
                </div>
                <div class="space-y-1 text-sm text-gray-500">
                    <p><span class="font-medium text-gray-700">Jenis Kelamin:</span> <span x-text="item.jenis_kelamin || '-'"></span></p>
                    <p><span class="font-medium text-gray-700">Tgl. Lahir:</span> <span x-text="formatDate(item.tanggal_lahir)"></span></p>
                    <p>
                        <span :class="item.status_murid === 'aktif' ? 'badge-green' : 'badge-red'" x-text="item.status_murid"></span>
                    </p>
                </div>
                <div class="mt-4 flex gap-2">
                    <button @click="editItem(item)" class="btn-secondary text-xs flex-1">Edit</button>
                    <button @click="confirmDelete(item)" class="btn-danger text-xs">Hapus</button>
                </div>
            </div>
        </template>
    </div>

    {{-- Modal Form --}}
    <div x-show="showModal" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6" @click.stop>
            <h3 class="text-lg font-semibold text-gray-800 mb-5" x-text="editId ? 'Edit Profil Anak' : 'Tambah Profil Anak'"></h3>
            <form @submit.prevent="save()" class="space-y-4">
                <div>
                    <label class="label">Nama Murid</label>
                    <input type="text" x-model="form.nama_murid" class="input" required>
                </div>
                <div>
                    <label class="label">Kelas</label>
                    <select x-model="form.id_kelas" class="input" required>
                        <option value="">-- Pilih Kelas --</option>
                        <template x-for="k in kelasList" :key="k.id_kelas">
                            <option :value="k.id_kelas" x-text="k.nama_kelas"></option>
                        </template>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Jenis Kelamin</label>
                        <select x-model="form.jenis_kelamin" class="input">
                            <option value="">-- Pilih --</option>
                            <option value="laki-laki">Laki-laki</option>
                            <option value="perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Tanggal Lahir</label>
                        <input type="date" x-model="form.tanggal_lahir" class="input">
                    </div>
                </div>
                <div>
                    <label class="label">Foto</label>
                    <input type="file" @change="form.foto = $event.target.files[0]" class="input" accept="image/*">
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
            <h3 class="text-lg font-semibold mb-2">Hapus Profil Anak</h3>
            <p class="text-gray-500 text-sm mb-5">Apakah Anda yakin ingin menghapus profil anak ini?</p>
            <div class="flex justify-end gap-3">
                <button @click="showDelete = false" class="btn-secondary">Batal</button>
                <button @click="deleteItem()" class="btn-danger">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
function muridPage() {
    return {
        items: [], kelasList: [], loading: true, showModal: false, showDelete: false,
        editId: null, deleteId: null, saving: false,
        form: { nama_murid: '', id_kelas: '', jenis_kelamin: '', tanggal_lahir: '', foto: null },

        async init() {
            const [mr, kr] = await Promise.all([api.get('/murid'), api.get('/kelas')]);
            this.loading = false;
            if (mr?.ok) this.items = mr.data.data || [];
            if (kr?.ok) this.kelasList = kr.data.data?.data || [];
        },
        openModal() { this.editId = null; this.form = { nama_murid: '', id_kelas: '', jenis_kelamin: '', tanggal_lahir: '', foto: null }; this.showModal = true; },
        editItem(item) {
            this.editId = item.id_murid;
            this.form = { nama_murid: item.nama_murid, id_kelas: item.id_kelas, jenis_kelamin: item.jenis_kelamin || '', tanggal_lahir: item.tanggal_lahir || '', foto: null };
            this.showModal = true;
        },
        confirmDelete(item) { this.deleteId = item.id_murid; this.showDelete = true; },
        async save() {
            this.saving = true;
            const fd = new FormData();
            Object.entries(this.form).forEach(([k, v]) => { if (v != null) fd.append(k, v); });
            const res = this.editId
                ? await api.post(`/murid/${this.editId}`, fd, true)
                : await api.post('/murid', fd, true);
            this.saving = false;
            if (res?.ok) {
                this.showModal = false;
                Alpine.store('notif').success('Profil anak berhasil disimpan');
                const mr = await api.get('/murid');
                if (mr?.ok) this.items = mr.data.data || [];
            } else Alpine.store('notif').error(res?.data?.message || 'Gagal menyimpan');
        },
        async deleteItem() {
            const res = await api.del(`/murid/${this.deleteId}`);
            this.showDelete = false;
            if (res?.ok) {
                Alpine.store('notif').success('Profil anak berhasil dihapus');
                this.items = this.items.filter(i => i.id_murid !== this.deleteId);
            } else Alpine.store('notif').error(res?.data?.message || 'Gagal menghapus');
        },
        formatDate(d) { return d ? new Date(d).toLocaleDateString('id-ID') : '-'; },
    };
}
</script>
@endsection
