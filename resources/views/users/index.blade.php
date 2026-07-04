@extends('layouts.app')
@section('title', 'Manajemen Pengguna — Aluna')
@section('content')
<div x-data="usersPage()" x-init="init()">

    <div class="page-banner"><h1>Manajemen Pengguna</h1></div>

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

    <div class="overflow-x-auto">
        <table class="aluna-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th class="text-center">Peran</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="loading">
                    <tr><td colspan="5" class="text-center py-8 text-gray-400 text-sm" style="background:transparent">Memuat data...</td></tr>
                </template>
                <template x-if="!loading && filtered.length===0">
                    <tr><td colspan="5" class="text-center py-8 text-gray-400 text-sm" style="background:transparent">Belum ada pengguna</td></tr>
                </template>
                <template x-for="(item,idx) in filtered" :key="item.id_user">
                    <tr>
                        <td x-text="idx+1"></td>
                        <td class="font-medium" x-text="item.nama"></td>
                        <td class="text-gray-500" x-text="item.email"></td>
                        <td class="text-center">
                            <div class="flex flex-wrap gap-1 justify-center">
                                <template x-for="r in item.roles||[]" :key="r.nama_role">
                                    <span class="badge-blue text-xs" x-text="r.nama_role"></span>
                                </template>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-3">
                                <button @click="editItem(item)" class="text-teal-500 hover:text-teal-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button @click="confirmDelete(item)" class="text-red-400 hover:text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- Modal Form --}}
    <div x-show="showModal" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showModal=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto" @click.stop>
            <h3 class="font-bold text-gray-800 mb-5" x-text="editId?'Edit Pengguna':'Tambah Pengguna'"></h3>
            <form @submit.prevent="save()" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="label">Nama</label><input type="text" x-model="form.nama" class="input" required></div>
                    <div><label class="label">Email</label><input type="email" x-model="form.email" class="input" required></div>
                    <div><label class="label">No. HP</label><input type="tel" x-model="form.no_hp" class="input"></div>
                    <div><label class="label">Status</label>
                        <select x-model="form.status" class="input">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="label">Password <span class="text-gray-400 font-normal" x-show="editId">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" x-model="form.password" class="input" :required="!editId" autocomplete="new-password">
                </div>
                <template x-if="form.password">
                    <div>
                        <label class="label">Konfirmasi Password</label>
                        <input type="password" x-model="form.password_confirmation" class="input" placeholder="Ulangi password" autocomplete="new-password">
                    </div>
                </template>
                <div>
                    <label class="label">Peran</label>
                    <div class="flex gap-4 mt-1">
                        <template x-for="r in ['admin','guru','orang_tua']" :key="r">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" :value="r" x-model="form.roles" class="rounded">
                                <span class="text-sm text-gray-700" x-text="r"></span>
                            </label>
                        </template>
                    </div>
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
            <p class="text-gray-700 text-sm mb-5">Hapus pengguna ini?</p>
            <div class="flex justify-center gap-3">
                <button @click="showDelete=false" class="btn-secondary">Batal</button>
                <button @click="deleteItem()" class="btn-salmon">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
function usersPage() {
    return {
        items:[], meta:{}, loading:true, search:'', showModal:false, showDelete:false,
        editId:null, deleteId:null, saving:false,
        form:{nama:'',email:'',no_hp:'',status:'aktif',password:'',password_confirmation:'',roles:[]},
        get filtered(){ return this.items.filter(i=>i.nama?.toLowerCase().includes(this.search.toLowerCase())||i.email?.toLowerCase().includes(this.search.toLowerCase())); },

        async init(){ const r=await api.get('/users'); this.loading=false; if(r?.ok){this.items=r.data.data?.data||[];this.meta=r.data.data;} },
        openModal(){ this.editId=null; this.form={nama:'',email:'',no_hp:'',status:'aktif',password:'',password_confirmation:'',roles:[]}; this.showModal=true; },
        editItem(item){ this.editId=item.id_user; this.form={nama:item.nama||'',email:item.email,no_hp:item.no_hp||'',status:item.status,password:'',password_confirmation:'',roles:item.roles?.map(r=>r.nama_role)||[]}; this.showModal=true; },
        confirmDelete(item){ this.deleteId=item.id_user; this.showDelete=true; },
        async save(){
            this.saving=true;
            const r=this.editId?await api.put(`/users/${this.editId}`,this.form):await api.post('/users',this.form);
            this.saving=false;
            if(r?.ok){this.showModal=false;Alpine.store('notif').success('Pengguna berhasil disimpan');await this.init();}
            else Alpine.store('notif').error(r?.data?.message||'Gagal menyimpan');
        },
        async deleteItem(){
            const r=await api.del(`/users/${this.deleteId}`); this.showDelete=false;
            if(r?.ok){Alpine.store('notif').success('Pengguna berhasil dihapus');await this.init();}
            else Alpine.store('notif').error(r?.data?.message||'Gagal menghapus');
        },
    };
}
</script>
@endsection
