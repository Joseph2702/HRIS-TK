@extends('layouts.app')
@section('title', 'Peran Pengguna — Aluna')
@section('content')
<div x-data="rolesPage()" x-init="init()">

    <div class="page-banner"><h1>Peran Pengguna</h1></div>

    <div class="flex justify-end mb-5">
        <button @click="openModal()" class="btn-pink">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah
        </button>
    </div>

    {{-- Role cards sejajar --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <template x-if="loading">
            <div class="text-center text-gray-400 py-8 text-sm col-span-3">Memuat data...</div>
        </template>
        <template x-for="role in items" :key="role.id_role">
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                {{-- Header role --}}
                <div class="flex items-center justify-between mb-4">
                    <span class="badge-blue px-4 py-1 text-sm font-semibold" x-text="role.nama_role"></span>
                    <div class="flex gap-2">
                        <button @click="editItem(role)" class="text-teal-500 hover:text-teal-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button @click="confirmDelete(role)" class="text-red-400 hover:text-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Permission list --}}
                <div class="space-y-3 text-sm text-gray-600">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase tracking-[0.18em] text-gray-400">Hak Akses</span>
                        <span class="text-xs text-gray-500" x-text="role.permissionIds?.length + ' akses'"></span>
                    </div>
                    <template x-if="!permissions.length">
                        <p class="text-gray-400 text-xs">Loading hak akses...</p>
                    </template>
                    <template x-for="permission in permissions" :key="permission.id_permission">
                        <label class="flex items-center gap-2 rounded-xl border p-2 hover:border-teal-200 cursor-pointer">
                            <input type="checkbox" :value="permission.id_permission" x-model="role.permissionIds" class="rounded text-teal-600 focus:ring-teal-400">
                            <span x-text="permission.nama_permission"></span>
                        </label>
                    </template>
                    <div class="flex justify-end pt-2">
                        <button @click="savePermissions(role)" class="btn-teal text-sm">
                            <span x-text="savingRole===role.id_role ? 'Menyimpan...' : 'Simpan Hak Akses'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Modal Form --}}
    <div x-show="showModal" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showModal=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6" @click.stop>
            <h3 class="font-bold text-gray-800 mb-5" x-text="editId?'Edit Role':'Tambah Role'"></h3>
            <form @submit.prevent="save()" class="space-y-4">
                <div><label class="label">Nama Role</label><input type="text" x-model="form.nama_role" class="input" required placeholder="contoh: operator"></div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3 space-y-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="form.is_active" class="rounded">
                        <span class="text-sm font-medium text-gray-700">Aktifkan Role</span>
                    </label>
                    <p class="text-xs text-gray-400 ml-6">
                        Role yang dinonaktifkan tidak dapat diassign ke pengguna baru, namun pengguna yang sudah memiliki role ini tidak terpengaruh.
                    </p>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showModal=false" class="btn-salmon">Batal</button>
                    <button type="submit" class="btn-green" :disabled="saving" x-text="saving?'Menyimpan...':'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Confirm Delete --}}
    <div x-show="showDelete" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showDelete=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center" @click.stop>
            <p class="text-gray-700 text-sm mb-5">Hapus role ini?</p>
            <div class="flex justify-center gap-3">
                <button @click="showDelete=false" class="btn-secondary">Batal</button>
                <button @click="deleteItem()" class="btn-salmon">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
function rolesPage() {
    return {
        items: [], permissions: [], loading: true, showModal: false, showDelete: false,
        editId: null, deleteId: null, saving: false, savingRole: null,
        form: { nama_role: '', is_active: true },

        async init() {
            const r = await api.get('/roles');
            this.loading = false;
            if (r?.ok) {
                const payload = r.data.data || {};
                this.permissions = payload.permissions || [];
                this.items = (payload.roles || []).map(role => ({
                    ...role,
                    permissionIds: (role.permissions || []).map(p => p.id_permission),
                }));
            }
        },

        openModal() {
            this.editId = null;
            this.form = { nama_role: '', is_active: true };
            this.showModal = true;
        },

        editItem(role) {
            this.editId = role.id_role;
            this.form = { nama_role: role.nama_role, is_active: role.is_active };
            this.showModal = true;
        },

        confirmDelete(role) {
            this.deleteId = role.id_role;
            this.showDelete = true;
        },

        async save() {
            this.saving = true;
            const r = this.editId
                ? await api.put(`/roles/${this.editId}`, this.form)
                : await api.post('/roles', this.form);
            this.saving = false;
            if (r?.ok) {
                this.showModal = false;
                Alpine.store('notif').success(this.editId ? 'Role diperbarui' : 'Role ditambahkan');
                const rr = await api.get('/roles');
                if (rr?.ok) {
                    const payload = rr.data.data || {};
                    this.permissions = payload.permissions || [];
                    this.items = (payload.roles || []).map(role => ({
                        ...role,
                        permissionIds: (role.permissions || []).map(p => p.id_permission),
                    }));
                }
            } else {
                Alpine.store('notif').error(r?.data?.message || 'Gagal menyimpan');
            }
        },

        async savePermissions(role) {
            this.savingRole = role.id_role;
            const r = await api.post(`/roles/${role.id_role}/permissions`, { permission_ids: role.permissionIds || [] });
            this.savingRole = null;
            if (r?.ok) {
                Alpine.store('notif').success('Akses role berhasil diperbarui');
                const updated = r.data.data;
                role.permissions = updated.permissions || [];
                role.permissionIds = (role.permissions || []).map(p => p.id_permission);
            } else {
                Alpine.store('notif').error(r?.data?.message || 'Gagal memperbarui akses');
            }
        },

        async deleteItem() {
            const r = await api.del(`/roles/${this.deleteId}`);
            this.showDelete = false;
            if (r?.ok) {
                Alpine.store('notif').success('Role berhasil dihapus');
                this.items = this.items.filter(i => i.id_role !== this.deleteId);
            } else {
                Alpine.store('notif').error(r?.data?.message || 'Gagal menghapus');
            }
        },
    };
}
</script>
@endsection
