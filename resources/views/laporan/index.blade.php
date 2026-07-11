@extends('layouts.app')
@section('title', 'Laporan Kegiatan — Aluna')
@section('content')
<div x-data="laporanPage()" x-init="init()">

    <div class="page-banner"><h1>Laporan Kegiatan Murid</h1></div>

    {{-- Filter + Toolbar --}}
    <div class="flex items-center gap-3 mb-5 flex-wrap">
        <div class="relative">
            <input type="text" x-model="search" class="input-search" placeholder="Cari Nama Murid">
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        {{-- Date filter: hanya muncul saat editMode aktif --}}
        <template x-if="editMode">
            <div>
                {{-- Sembunyikan icon bawaan browser dengan CSS, tampilkan hanya satu icon --}}
                <input type="date" x-model="filterTanggal"
                       class="input [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute"
                       style="width:160px;border-radius:9999px;padding:0.5rem 1rem;position:relative">
            </div>
        </template>

        <div class="ml-auto flex gap-2">
            <template x-if="canCreate">
                <button @click="openModal()" class="btn-pink">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah
                </button>
            </template>
            <template x-if="canDelete">
                <button @click="toggleEdit()" class="btn-blue">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span x-text="editMode ? 'Selesai' : 'Edit'"></span>
                </button>
            </template>
            <template x-if="editMode && selected.length > 0">
                <button @click="confirmDeleteSelected()" class="btn-salmon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus (<span x-text="selected.length"></span>)
                </button>
            </template>
        </div>
    </div>

    {{-- List rows pill style --}}
    <div class="space-y-2">
        <template x-if="loading">
            <p class="text-center text-gray-400 py-8 text-sm">Memuat data...</p>
        </template>
        <template x-if="!loading && filtered.length===0">
            <p class="text-center text-gray-400 py-8 text-sm">Belum ada laporan</p>
        </template>
        <template x-for="item in filtered" :key="item.id_laporan">
            <div class="flex items-center gap-4 px-5 py-3.5 rounded-full cursor-pointer hover:opacity-90 transition-opacity"
                 style="background:rgba(239,201,234,0.3)"
                 @click="editMode ? null : viewDetail(item)">
                {{-- Checkbox hanya tampil saat editMode --}}
                <template x-if="editMode">
                    <input type="checkbox" :value="item.id_laporan" x-model="selected" @click.stop class="rounded shrink-0">
                </template>
                <div class="flex-1 grid grid-cols-3 gap-4" @click="editMode ? null : viewDetail(item)">
                    <span class="text-sm text-gray-800 font-medium" x-text="item.murid?.nama_murid||'-'"></span>
                    <span class="text-sm text-gray-500 text-center" x-text="item.murid?.jenis_kelamin==='laki-laki'?'L':'P'"></span>
                    <span class="text-sm text-gray-500 text-right" x-text="formatDate(item.created_at)"></span>
                </div>
            </div>
        </template>
    </div>

    {{-- Modal Tambah --}}
    <div x-show="showModal" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showModal=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6" @click.stop>
            <h3 class="font-bold text-gray-800 mb-5">Buat Laporan Kegiatan</h3>
            <form @submit.prevent="save()" class="space-y-4">
                <div><label class="label">Judul Laporan</label><input type="text" x-model="form.judul_laporan" class="input" required></div>
                <div><label class="label">Isi Laporan</label><textarea x-model="form.isi_laporan" class="input h-28 resize-none" style="border-radius:1rem"></textarea></div>
                <div>
                    <label class="label">Indikator Penilaian</label>
                    <select x-model="form.indikator" class="input">
                        <option value="">-- Pilih Indikator --</option>
                        <option value="BB">BB — Belum Berkembang</option>
                        <option value="MB">MB — Mulai Berkembang</option>
                        <option value="BSH">BSH — Berkembang Sesuai Harapan</option>
                        <option value="BSB">BSB — Berkembang Sangat Baik</option>
                    </select>
                </div>
                <div>
                    <label class="label">Catatan Indikator (opsional)</label>
                    <input type="text" x-model="form.indikator_catatan" class="input" placeholder="Catatan singkat untuk indikator">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showModal=false" class="btn-salmon">Batal</button>
                    <button type="submit" class="btn-green" :disabled="saving" x-text="saving?'Menyimpan...':'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div x-show="showDetail" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showDetail=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[85vh] flex flex-col" @click.stop>
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-800" x-text="detail?.judul_laporan"></h3>
                <p class="text-xs text-gray-400 mt-1">
                    <span x-text="detail?.murid?.nama_murid"></span> ·
                    <span x-text="detail?.guru?.user?.nama"></span> ·
                    <span x-text="formatDate(detail?.created_at)"></span>
                </p>
            </div>
            <div class="flex-1 overflow-y-auto p-5">
                        <p class="text-sm text-gray-700 mb-3" x-text="detail?.isi_laporan||'-'"></p>
                        <template x-if="detail?.indikator">
                            <p class="text-sm font-semibold mt-2" x-text="'Indikator: ' + detail.indikator + (detail.indikator_catatan ? ' · ' + detail.indikator_catatan : '')"></p>
                        </template>
                <h4 class="font-semibold text-sm text-gray-700 mb-3">Balasan</h4>
                <div class="space-y-3 mb-4">
                    <template x-if="!detail?.balasan?.length">
                        <p class="text-sm text-gray-400">Belum ada balasan</p>
                    </template>
                    <template x-for="b in detail?.balasan||[]" :key="b.id_balasan">
                        <div class="rounded-xl p-3" style="background:rgba(194,223,244,0.25)">
                            <p class="text-xs font-semibold text-gray-700" x-text="b.user?.nama"></p>
                            <p class="text-sm text-gray-600 mt-0.5" x-text="b.isi_balasan"></p>
                        </div>
                    </template>
                </div>
                <div>
                    <label class="label">Kirim Balasan</label>
                    <textarea x-model="balasanText" class="input h-20 resize-none" style="border-radius:1rem" placeholder="Tulis balasan..."></textarea>
                    <button @click="kirimBalasan()" class="btn-pink mt-2" :disabled="!balasanText.trim()||sendingBalasan">
                        <span x-text="sendingBalasan?'Mengirim...':'Kirim Balasan'"></span>
                    </button>
                </div>
            </div>
            <div class="p-4 border-t flex justify-end gap-3">
                <template x-if="canDelete">
                    <button @click="confirmDeleteOne(detail)" class="btn-salmon">Hapus</button>
                </template>
                <button @click="showDetail=false" class="btn-secondary">Tutup</button>
            </div>
        </div>
    </div>

    {{-- Confirm Delete --}}
    <div x-show="showDelete" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showDelete=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center" @click.stop>
            <p class="text-gray-700 text-sm mb-5">Hapus laporan yang dipilih?</p>
            <div class="flex justify-center gap-3">
                <button @click="showDelete=false" class="btn-secondary">Batal</button>
                <button @click="deleteSelected()" class="btn-salmon">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
function laporanPage() {
    const user = api.getUser()||{};
    return {
        items:[], loading:true, search:'', filterTanggal:'', selected:[],
        showModal:false, showDetail:false, showDelete:false,
        editMode: false,
        detail:null, deleteIds:[], saving:false, sendingBalasan:false,
        form:{judul_laporan:'', isi_laporan:'', indikator:'', indikator_catatan:''}, balasanText:'',
        canCreate:(user.roles||[]).some(r=>['admin','guru'].includes(r)),
        canDelete:(user.roles||[]).some(r=>['admin','guru'].includes(r)),
        toggleEdit(){ this.editMode=!this.editMode; if(!this.editMode) this.selected=[]; },

        get filtered(){
            return this.items.filter(i=>{
                const nameMatch=!this.search||i.murid?.nama_murid?.toLowerCase().includes(this.search.toLowerCase());
                const dateMatch=!this.filterTanggal||i.created_at?.startsWith(this.filterTanggal);
                return nameMatch&&dateMatch;
            });
        },

        async init(){ const r=await api.get('/laporan'); this.loading=false; if(r?.ok) this.items=r.data.data?.data||[]; },
        openModal(){ this.form={judul_laporan:'',isi_laporan:''}; this.showModal=true; },
        async viewDetail(item){ const r=await api.get(`/laporan/${item.id_laporan}`); if(r?.ok){this.detail=r.data.data;this.showDetail=true;} },
        confirmDeleteSelected(){ this.deleteIds=[...this.selected]; this.showDelete=true; },
        confirmDeleteOne(item){ this.deleteIds=[item.id_laporan]; this.showDelete=true; this.showDetail=false; },
        async save(){
            this.saving=true;
            const r=await api.post('/laporan',this.form); this.saving=false;
            if(r?.ok){this.showModal=false;Alpine.store('notif').success('Laporan berhasil dibuat');await this.init();}
            else Alpine.store('notif').error(r?.data?.message||'Gagal menyimpan');
        },
        async kirimBalasan(){
            if(!this.balasanText.trim()) return;
            this.sendingBalasan=true;
            const r=await api.post(`/laporan/${this.detail.id_laporan}/balas`,{isi_balasan:this.balasanText});
            this.sendingBalasan=false;
            if(r?.ok){this.balasanText='';Alpine.store('notif').success('Balasan terkirim');await this.viewDetail(this.detail);}
            else Alpine.store('notif').error(r?.data?.message||'Gagal mengirim');
        },
        async deleteSelected(){
            for(const id of this.deleteIds) await api.del(`/laporan/${id}`);
            this.showDelete=false; this.selected=[]; this.showDetail=false;
            Alpine.store('notif').success('Laporan berhasil dihapus'); await this.init();
        },
        formatDate(dt){ return dt?new Date(dt).toLocaleDateString('id-ID',{day:'2-digit',month:'2-digit',year:'numeric'}):'-'; },
    };
}
</script>
@endsection
