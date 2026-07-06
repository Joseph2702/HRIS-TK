@extends('layouts.app')
@section('title', 'Layanan Sekolah — Aluna')
@section('content')
<div x-data="layananPage()" x-init="init()">

    <div class="page-banner"><h1>Layanan yang tersedia di Sekolah TK Aluna Montessori</h1></div>

    <div class="mb-5 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm flex items-center gap-3">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#E7F7EE] text-[#25D366]">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M20.52 3.48a11.9 11.9 0 00-16.84 0 11.8 11.8 0 00-3.5 8.43c0 2.09.55 4.14 1.6 5.96L2 22l4.34-1.14a11.8 11.8 0 005.96 1.6h.03a11.9 11.9 0 008.43-3.5 11.9 11.9 0 000-16.84zm-8.21 17.06h-.01a10.05 10.05 0 01-5.14-1.4l-.37-.22-3.24.84.87-3.16-.24-.38A10.05 10.05 0 014.76 7.1a8.96 8.96 0 0113.43 0 8.96 8.96 0 010 12.67 8.98 8.98 0 01-6.88 2.77zm4.2-6.18c-.23-.11-1.35-.66-1.56-.74-.21-.08-.36-.11-.51.11-.15.23-.58.74-.71.9-.13.16-.26.18-.49.07-.23-.11-.98-.36-1.86-1.15-.69-.61-1.15-1.36-1.28-1.59-.13-.23-.01-.35.1-.46.1-.1.23-.26.34-.38.11-.13.15-.22.23-.37.08-.15.04-.28-.02-.39-.07-.11-.51-1.23-.7-1.71-.18-.45-.36-.39-.51-.39-.13 0-.28-.01-.43-.01-.15 0-.39.06-.59.28-.2.23-.75.73-.75 1.78s.77 2.07.88 2.21c.11.14 1.52 2.33 3.68 3.26.51.22.9.35 1.21.45.51.16.98.14 1.35.09.41-.05 1.35-.55 1.54-1.08.19-.53.19-.98.13-1.08-.07-.11-.24-.17-.49-.28z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-gray-800">Butuh bantuan?</p>
            <p class="text-sm text-gray-500">Hubungi Sekolah langsung lewat WhatsApp untuk pertanyaan layanan.</p>
        </div>
        <a href="https://wa.me/6281234567890?text=Halo%20Admin%2C%20saya%20ingin%20bertanya%20tentang%20anak%20saya" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold text-white shadow-sm" style="background-color:#25D366;">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M20.52 3.48a11.9 11.9 0 00-16.84 0 11.8 11.8 0 00-3.5 8.43c0 2.09.55 4.14 1.6 5.96L2 22l4.34-1.14a11.8 11.8 0 005.96 1.6h.03a11.9 11.9 0 008.43-3.5 11.9 11.9 0 000-16.84zm-8.21 17.06h-.01a10.05 10.05 0 01-5.14-1.4l-.37-.22-3.24.84.87-3.16-.24-.38A10.05 10.05 0 014.76 7.1a8.96 8.96 0 0113.43 0 8.96 8.96 0 010 12.67 8.98 8.98 0 01-6.88 2.77zm4.2-6.18c-.23-.11-1.35-.66-1.56-.74-.21-.08-.36-.11-.51.11-.15.23-.58.74-.71.9-.13.16-.26.18-.49.07-.23-.11-.98-.36-1.86-1.15-.69-.61-1.15-1.36-1.28-1.59-.13-.23-.01-.35.1-.46.1-.1.23-.26.34-.38.11-.13.15-.22.23-.37.08-.15.04-.28-.02-.39-.07-.11-.51-1.23-.7-1.71-.18-.45-.36-.39-.51-.39-.13 0-.28-.01-.43-.01-.15 0-.39.06-.59.28-.2.23-.75.73-.75 1.78s.77 2.07.88 2.21c.11.14 1.52 2.33 3.68 3.26.51.22.9.35 1.21.45.51.16.98.14 1.35.09.41-.05 1.35-.55 1.54-1.08.19-.53.19-.98.13-1.08-.07-.11-.24-.17-.49-.28z"/>
            </svg>
            Hubungi WhatsApp
        </a>
    </div>

    <div class="flex items-center justify-between mb-5">
        <div class="relative">
            <input type="text" x-model="search" class="input-search" placeholder="Search here">
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <template x-if="canCreate">
            <button @click="openModal()" class="btn-pink">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah
            </button>
        </template>
    </div>

    <div class="rounded-2xl p-4 space-y-3" style="background:#EFC9EA">
        <template x-if="loading">
            <p class="text-center text-gray-500 py-6 text-sm">Memuat data...</p>
        </template>
        <template x-if="!loading && filtered.length === 0">
            <p class="text-center text-gray-500 py-6 text-sm">Belum ada layanan</p>
        </template>
        <template x-for="item in filtered" :key="item.id_artikel">
            <div class="bg-white rounded-xl p-4 flex items-start gap-4">
                <div class="flex gap-2 shrink-0">
                    <template x-if="item.gambar_artikel">
                        <img :src="'/storage/'+item.gambar_artikel" class="w-16 h-16 rounded-lg object-cover">
                    </template>
                    <template x-if="!item.gambar_artikel">
                        <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center">
                            <svg class="w-7 h-7 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </template>
                    <template x-if="item.gambar_artikel_2">
                        <img :src="'/storage/'+item.gambar_artikel_2" class="w-16 h-16 rounded-lg object-cover">
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm" x-text="item.judul_artikel"></p>
                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-2" x-text="item.konten_artikel"></p>
                    <div class="mt-2">
                        <a :href="'/layanan/'+item.id_artikel" class="text-xs text-blue-600 hover:underline">Lihat detail</a>
                    </div>
                </div>
                <div class="flex gap-2 shrink-0" x-show="canCreate">
                    <button @click="editItem(item)" class="text-teal-500 hover:text-teal-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <template x-if="canDelete">
                        <button @click="confirmDelete(item)" class="text-red-400 hover:text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </template>
                </div>
            </div>
        </template>
    </div>

    {{-- Modal Form --}}
    <div x-show="showModal" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showModal=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto" @click.stop>
            <h3 class="font-bold text-gray-800 mb-5" x-text="editId?'Edit Layanan':'Tambah Layanan'"></h3>
            <form @submit.prevent="save()" class="space-y-4">
                <div><label class="label">Judul Layanan</label><input type="text" x-model="form.judul_artikel" class="input" required></div>
                <div><label class="label">Deskripsi</label><textarea x-model="form.konten_artikel" class="input h-28 resize-none" style="border-radius:1rem" required></textarea></div>
                <div>
                    <label class="label">Foto 1 <span class="text-gray-400 font-normal">(maks. 100 MB)</span></label>
                    <input type="file" @change="onFoto1($event)" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:cursor-pointer" accept="image/*">
                    <template x-if="foto1Preview"><img :src="foto1Preview" class="mt-2 h-20 rounded-xl object-cover"></template>
                </div>
                <div>
                    <label class="label">Foto 2 <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="file" @change="onFoto2($event)" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:cursor-pointer" accept="image/*">
                    <template x-if="foto2Preview"><img :src="foto2Preview" class="mt-2 h-20 rounded-xl object-cover"></template>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showModal=false" class="btn-salmon">Batal</button>
                    <button type="submit" class="btn-green" :disabled="saving" x-text="saving?'Menyimpan...':'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showDelete" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showDelete=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center" @click.stop>
            <p class="text-gray-700 text-sm mb-5">Hapus layanan ini?</p>
            <div class="flex justify-center gap-3">
                <button @click="showDelete=false" class="btn-secondary">Batal</button>
                <button @click="deleteItem()" class="btn-salmon">Hapus</button>
            </div>
        </div>
    </div>

    <a href="https://wa.me/6281234567890?text=Halo%20Admin%2C%20saya%20ingin%20bertanya%20tentang%20anak%20saya" target="_blank" rel="noopener noreferrer" aria-label="Hubungi Sekolah via WhatsApp"
       class="fixed bottom-6 right-6 z-50 inline-flex h-14 w-14 items-center justify-center rounded-full text-white shadow-lg shadow-black/20 hover:scale-105 transition-transform"
       style="background-color:#25D366;">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M20.52 3.48a11.9 11.9 0 00-16.84 0 11.8 11.8 0 00-3.5 8.43c0 2.09.55 4.14 1.6 5.96L2 22l4.34-1.14a11.8 11.8 0 005.96 1.6h.03a11.9 11.9 0 008.43-3.5 11.9 11.9 0 000-16.84zm-8.21 17.06h-.01a10.05 10.05 0 01-5.14-1.4l-.37-.22-3.24.84.87-3.16-.24-.38A10.05 10.05 0 014.76 7.1a8.96 8.96 0 0113.43 0 8.96 8.96 0 010 12.67 8.98 8.98 0 01-6.88 2.77zm4.2-6.18c-.23-.11-1.35-.66-1.56-.74-.21-.08-.36-.11-.51.11-.15.23-.58.74-.71.9-.13.16-.26.18-.49.07-.23-.11-.98-.36-1.86-1.15-.69-.61-1.15-1.36-1.28-1.59-.13-.23-.01-.35.1-.46.1-.1.23-.26.34-.38.11-.13.15-.22.23-.37.08-.15.04-.28-.02-.39-.07-.11-.51-1.23-.7-1.71-.18-.45-.36-.39-.51-.39-.13 0-.28-.01-.43-.01-.15 0-.39.06-.59.28-.2.23-.75.73-.75 1.78s.77 2.07.88 2.21c.11.14 1.52 2.33 3.68 3.26.51.22.9.35 1.21.45.51.16.98.14 1.35.09.41-.05 1.35-.55 1.54-1.08.19-.53.19-.98.13-1.08-.07-.11-.24-.17-.49-.28z"/>
        </svg>
    </a>
</div>

<script>
function layananPage() {
    const user = api.getUser()||{};
    return {
        items:[], loading:true, search:'', showModal:false, showDelete:false,
        editId:null, deleteId:null, saving:false,
        foto1File:null, foto1Preview:null, foto2File:null, foto2Preview:null,
        form:{ judul_artikel:'', konten_artikel:'' },
        canCreate:(user.roles||[]).some(r=>['admin','guru'].includes(r)),
        canDelete:(user.roles||[]).includes('admin'),
        get filtered(){ return this.items.filter(i=>i.judul_artikel?.toLowerCase().includes(this.search.toLowerCase())); },

        async init(){
            // Layanan Sekolah: artikel bertipe layanan_sekolah
            const r=await api.get('/artikel?tipe=layanan_sekolah'); this.loading=false;
            if(r?.ok) this.items=r.data.data?.data||[];
        },

        onFoto1(e){ this.foto1File=e.target.files[0]; if(this.foto1File){const rd=new FileReader();rd.onload=ev=>{this.foto1Preview=ev.target.result;};rd.readAsDataURL(this.foto1File);} },
        onFoto2(e){ this.foto2File=e.target.files[0]; if(this.foto2File){const rd=new FileReader();rd.onload=ev=>{this.foto2Preview=ev.target.result;};rd.readAsDataURL(this.foto2File);} },

        openModal(){
            this.editId=null; this.form={judul_artikel:'',konten_artikel:''};
            this.foto1File=null; this.foto1Preview=null; this.foto2File=null; this.foto2Preview=null;
            this.showModal=true;
        },
        editItem(item){
            this.editId=item.id_artikel;
            this.form={judul_artikel:item.judul_artikel, konten_artikel:item.konten_artikel};
            this.foto1Preview=item.gambar_artikel?'/storage/'+item.gambar_artikel:null;
            this.foto2Preview=item.gambar_artikel_2?'/storage/'+item.gambar_artikel_2:null;
            this.foto1File=null; this.foto2File=null;
            this.showModal=true;
        },
        confirmDelete(item){ this.deleteId=item.id_artikel; this.showDelete=true; },

        async save(){
            this.saving=true;
            const fd=new FormData();
            fd.append('judul_artikel', this.form.judul_artikel);
            fd.append('konten_artikel', this.form.konten_artikel);
            fd.append('status_artikel', 'published');
            fd.append('tipe', 'layanan_sekolah');
            if(this.foto1File) fd.append('gambar_artikel', this.foto1File);
            if(this.foto2File) fd.append('gambar_artikel_2', this.foto2File);

            const r=this.editId
                ?await api.post(`/artikel/${this.editId}`, fd, true)
                :await api.post('/artikel', fd, true);
            this.saving=false;
            if(r?.ok){ this.showModal=false; Alpine.store('notif').success('Layanan berhasil disimpan'); await this.init(); }
            else Alpine.store('notif').error(r?.data?.message||'Gagal menyimpan');
        },

        async deleteItem(){
            const r=await api.del(`/artikel/${this.deleteId}`); this.showDelete=false;
            if(r?.ok){ Alpine.store('notif').success('Layanan berhasil dihapus'); await this.init(); }
            else Alpine.store('notif').error(r?.data?.message||'Gagal menghapus');
        },
    };
}
</script>
@endsection
