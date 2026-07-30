@extends('layouts.app')
@section('title', 'Laporan Kegiatan — Aluna')
@section('content')
<div x-data="laporanMurid()" x-init="init()">

    <div class="page-banner flex items-center gap-3">
        <a :href="`/presensi/detail?kelas_id=${kelasId}`" class="text-gray-600 hover:text-gray-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1>Laporan Kegiatan</h1>
    </div>

    <div class="max-w-2xl space-y-4">
        {{-- Nama murid pill + tombol --}}
        <div class="flex items-center justify-between gap-4">
            <div class="rounded-full px-6 py-2.5 font-semibold text-gray-800 text-sm flex-1" style="background:#EFC9EA">
                <span x-text="namaMurid"></span>
            </div>
            <div class="flex gap-2">
                <button @click="editMode=!editMode" class="btn-blue">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </button>
                <button @click="save()" class="btn-green" :disabled="saving">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
                </button>
            </div>
        </div>

        {{-- Form laporan --}}
        <div class="bg-gray-50 rounded-2xl p-5 space-y-4">
            <div>
                <label class="label">Judul Laporan</label>
                <input type="text" x-model="form.judul_laporan" class="input"
                       placeholder="Contoh: Perkembangan Motorik Bulan Ini" :disabled="!editMode && !!existingId">
            </div>
            <div>
                <label class="label">Ketik kegiatan murid di sini!</label>
                <textarea x-model="form.isi_laporan" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-sm text-gray-700 resize-none focus:outline-none focus:ring-2 h-36"
                          style="--tw-ring-color:#C2DFF4"
                          placeholder="Tuliskan kegiatan dan perkembangan murid..."
                          :disabled="!editMode && !!existingId"></textarea>
            </div>

            <div class="flex items-center gap-4">
                <div>
                    <label class="label">Indikator Penilaian</label>
                    <select x-model="form.indikator" class="input" :disabled="!editMode && !!existingId">
                        <option value="">-- Pilih Indikator --</option>
                        <option value="BB">BB — Belum Berkembang</option>
                        <option value="MB">MB — Mulai Berkembang</option>
                        <option value="BSH">BSH — Berkembang Sesuai Harapan</option>
                        <option value="BSB">BSB — Berkembang Sangat Baik</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="label">Catatan Indikator (opsional)</label>
                    <input type="text" x-model="form.indikator_catatan" class="input" placeholder="Catatan singkat untuk indikator" :disabled="!editMode && !!existingId">
                </div>
            </div>

            {{-- Tampilkan indikator di bawah description saat view mode --}}
            <template x-if="!editMode && !!existingId">
                <div class="pt-1">
                    <template x-if="form.indikator">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-700 mb-1">Indikator:</p>
                                <p class="text-xs inline-block px-2 py-1 rounded-full text-white" :class="{
                                    'bg-gray-500': form.indikator==='BB',
                                    'bg-yellow-500': form.indikator==='MB',
                                    'bg-green-500': form.indikator==='BSH',
                                    'bg-blue-600': form.indikator==='BSB'
                                }" x-text="form.indikator + ' · ' + (form.indikator_catatan || '')"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div class="flex items-center gap-6 flex-wrap">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600 font-medium">Untuk :</span>
                    <select x-model="form.id_orang_tua" class="px-4 py-1.5 rounded-full border text-sm focus:outline-none focus:ring-2"
                            style="border-color:#EFC9EA;--tw-ring-color:#EFC9EA;background:rgba(239,201,234,0.15)" :disabled="!editMode && !!existingId">
                        <option value="">-- Orang Tua --</option>
                        <template x-for="ot in orangTuaList" :key="ot.id_orang_tua">
                            <option :value="ot.id_orang_tua" x-text="ot.user?.nama || '-'"></option>
                        </template>
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600 font-medium">Dari :</span>
                    <select x-model="form.id_guru" class="px-4 py-1.5 rounded-full border text-sm focus:outline-none focus:ring-2"
                            style="border-color:#C2DFF4;--tw-ring-color:#C2DFF4;background:rgba(194,223,244,0.15)" :disabled="!editMode && !!existingId">
                        <option value="">-- Guru --</option>
                        <template x-for="g in guruList" :key="g.id_guru">
                            <option :value="g.id_guru" x-text="g.user?.nama || '-'"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        {{-- Riwayat laporan sebelumnya --}}
        <template x-if="riwayat.length > 0">
            <div>
                <h3 class="font-semibold text-gray-700 text-sm mb-3">Riwayat Laporan</h3>
                <div class="space-y-2">
                    <template x-for="l in riwayat" :key="l.id_laporan">
                        <div class="rounded-xl p-3 border border-gray-100 bg-white flex items-start justify-between gap-3 cursor-pointer hover:border-pink-200 transition-colors"
                             @click="loadLaporan(l)">
                            <div>
                                <p class="text-sm font-medium text-gray-800" x-text="l.judul_laporan"></p>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="formatDate(l.created_at)"></p>
                                <template x-if="l.indikator">
                                    <p class="text-xs mt-2 inline-block px-2 py-1 rounded-full text-white text-[11px]" :class="{
                                        'bg-gray-500': l.indikator==='BB',
                                        'bg-yellow-500': l.indikator==='MB',
                                        'bg-green-500': l.indikator==='BSH',
                                        'bg-blue-600': l.indikator==='BSB'
                                    }" x-text="l.indikator + ' · ' + (l.indikator_catatan || '')"></p>
                                </template>
                            </div>
                            <span class="badge-pink text-xs shrink-0" x-text="`${l.balasan?.length||0} balas`"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function laporanMurid() {
    const params = new URLSearchParams(location.search);
    return {
        muridId:   params.get('murid_id'),
        namaMurid: params.get('nama') || 'Murid',
        jadwalId:  params.get('jadwal_id'),
        kelasId:   params.get('kelas_id'),
        existingId: null,
        editMode: true,
        saving: false,
        guruList: [],
        orangTuaList: [],
        riwayat: [],
        form: { judul_laporan: '', isi_laporan: '', id_guru: '', id_orang_tua: '', indikator: '', indikator_catatan: '' },

        async init() {
            // Load guru list
            const gr = await api.get('/users');
            const guruRes = await fetch('/api/kelas', { headers: { Authorization: 'Bearer ' + api.getToken(), Accept: 'application/json' } });

            // Simpler: hit presensi endpoint for guru list
            const [mr, lr] = await Promise.all([
                api.get(`/murid/${this.muridId}`),
                api.get(`/laporan?murid_id=${this.muridId}`),
            ]);

            if (mr?.ok) {
                const murid = mr.data.data;
                // Pre-fill orang tua
                if (murid.orang_tua?.id_orang_tua) {
                    this.form.id_orang_tua = murid.orang_tua.id_orang_tua;
                }
            }

            if (lr?.ok) {
                this.riwayat = (lr.data.data?.data || []).filter(l => l.id_murid == this.muridId);
            }

            // Load guru options from users
            const ur = await api.get('/users');
            if (ur?.ok) {
                const users = ur.data.data?.data || [];
                // We need guru — fetch from kelas jadwal
            }

            // Auto-fill current user as guru if role guru
            const currentUser = api.getUser();
            if ((currentUser?.roles||[]).includes('guru')) {
                // Will be set by service from JWT
            }
        },

        loadLaporan(l) {
            this.existingId = l.id_laporan;
            this.form.judul_laporan = l.judul_laporan;
            this.form.isi_laporan = l.isi_laporan || '';
            this.form.indikator = l.indikator || '';
            this.form.indikator_catatan = l.indikator_catatan || '';
            this.editMode = false;
        },

        async save() {
            if (!this.form.judul_laporan.trim()) {
                Alpine.store('notif').error('Judul laporan tidak boleh kosong');
                return;
            }

            this.saving = true;
            const payload = {
                id_murid:     this.muridId,
                id_jadwal:    this.jadwalId ? parseInt(this.jadwalId) : null,
                judul_laporan: this.form.judul_laporan,
                isi_laporan:  this.form.isi_laporan,
                indikator: this.form.indikator || null,
                indikator_catatan: this.form.indikator_catatan || null,
            };

            let r;
            if (this.existingId) {
                r = await api.put(`/laporan/${this.existingId}`, payload);
            } else {
                r = await api.post('/laporan', payload);
            }

            this.saving = false;
            if (r?.ok) {
                Alpine.store('notif').success('Laporan kegiatan berhasil disimpan');
                this.existingId = r.data.data?.id_laporan || this.existingId;
                this.editMode = false;
                // Reload riwayat
                const lr = await api.get('/laporan');
                if (lr?.ok) this.riwayat = (lr.data.data?.data||[]).filter(l => l.id_murid == this.muridId);
            } else {
                Alpine.store('notif').error(r?.data?.message || 'Gagal menyimpan laporan');
            }
        },

        formatDate(dt) {
            return dt ? new Date(dt).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
        },
    };
}
</script>
@endsection
