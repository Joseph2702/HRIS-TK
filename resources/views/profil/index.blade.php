@extends('layouts.app')
@section('title', 'Pengaturan Profil — Aluna')
@section('content')
<div x-data="profilPage()" x-init="init()">

    <div class="page-banner"><h1>Pengaturan Profil</h1></div>

    {{-- ── FORM PROFIL DIRI ─────────────────────────────────── --}}
    <div class="bg-gray-50 rounded-2xl p-6 mb-4">
        <template x-if="isOrangTua">
            <div class="rounded-full px-5 py-2 mb-5 font-semibold text-gray-800 text-sm" style="background:#EFC9EA">
                Profil Orang Tua
            </div>
        </template>

        <div class="flex gap-8">
            {{-- Fields --}}
            <div class="flex-1 space-y-4">
                <div>
                    <label class="label">Nama Lengkap</label>
                    <input type="text" x-model="form.nama" class="input" placeholder="Masukkan nama lengkap">
                </div>
                <template x-if="isOrangTua">
                    <div>
                        <label class="label">Pekerjaan</label>
                        <input type="text" x-model="form.pekerjaan" class="input" placeholder="Pekerjaan">
                    </div>
                </template>
                <template x-if="isGuru">
                    <div>
                        <label class="label">Spesialisasi</label>
                        <input type="text" x-model="form.spesialisasi" class="input" placeholder="Contoh: Seni & Kreativitas">
                    </div>
                </template>
                <div>
                    <label class="label">Alamat Email</label>
                    <input type="email" x-model="form.email" class="input" disabled>
                </div>
                <div>
                    <label class="label">Ubah Password <span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span></label>
                    <div class="relative">
                        <input :type="showPwd?'text':'password'" x-model="form.password" class="input pr-10" placeholder="Masukkan password baru">
                        <button type="button" @click="showPwd=!showPwd" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!showPwd" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path x-show="showPwd" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="label">Konfirmasi Password</label>
                    <div class="relative">
                        <input :type="showPwd2?'text':'password'" x-model="form.password_confirmation" class="input pr-10" placeholder="Ulangi password baru">
                        <button type="button" @click="showPwd2=!showPwd2" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Foto profil --}}
            <div class="flex flex-col items-center gap-3 w-44 shrink-0">
                <div class="w-32 h-32 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center">
                    <template x-if="fotoPreview">
                        <img :src="fotoPreview" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!fotoPreview">
                        <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </template>
                </div>
                <label class="btn-blue cursor-pointer text-xs">
                    Ubah Foto
                    <input type="file" class="hidden" accept="image/*" @change="onFoto($event)">
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button @click="resetForm()" class="btn-salmon">Batal</button>
            <button @click="saveProfil()" class="btn-green" :disabled="saving" x-text="saving?'Menyimpan...':'Simpan'"></button>
        </div>
    </div>

    {{-- ── PROFIL ANAK (Orang Tua) ─────────────────────────── --}}
    <template x-if="isOrangTua">
        <div class="space-y-4 mb-4">
            {{-- Header section --}}
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-800 text-base">Profil Anak</h2>
                <button @click="openTambahAnak()" class="btn-pink text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Anak
                </button>
            </div>

            {{-- Loading --}}
            <template x-if="loadingAnak">
                <p class="text-sm text-gray-400 text-center py-4">Memuat data anak...</p>
            </template>

            {{-- Belum ada anak --}}
            <template x-if="!loadingAnak && anakList.length === 0">
                <div class="bg-gray-50 rounded-2xl p-6 text-center">
                    <p class="text-gray-400 text-sm">Belum ada data anak. Tambahkan profil anak Anda.</p>
                </div>
            </template>

            {{-- List anak --}}
            <template x-for="anak in anakList" :key="anak.id_murid">
                <div class="bg-gray-50 rounded-2xl overflow-hidden">
                    {{-- Header anak --}}
                    <div class="rounded-full mx-4 mt-4 px-5 py-2 font-semibold text-gray-800 text-sm flex items-center justify-between"
                         style="background:#EFC9EA">
                        <span x-text="anak.nama_murid"></span>
                        <div class="flex gap-2">
                            <button @click="editAnak(anak)" class="text-teal-600 hover:text-teal-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Detail anak --}}
                    <div class="p-5 grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Nama Lengkap</label>
                            <input type="text" :value="anak.nama_murid" class="input" disabled>
                        </div>
                        <div>
                            <label class="label">Jenis Kelamin</label>
                            <input type="text" :value="anak.jenis_kelamin === 'laki-laki' ? 'Laki-laki' : 'Perempuan'" class="input" disabled>
                        </div>
                        <div>
                            <label class="label">Tanggal Lahir</label>
                            <input type="text" :value="formatTgl(anak.tanggal_lahir)" class="input" disabled>
                        </div>
                        <div>
                            <label class="label">Kelas</label>
                            <input type="text" :value="anak.kelas?.nama_kelas || '-'" class="input" disabled>
                        </div>
                        <div>
                            <label class="label">Status</label>
                            <span :class="anak.status_murid==='aktif'?'badge-green':'badge-red'" x-text="anak.status_murid" class="mt-2 inline-block"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </template>



    {{-- Modal Edit Anak --}}
    <div x-show="showEditAnak" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showEditAnak=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
            <h3 class="font-bold text-gray-800 mb-5" x-text="editAnakId?'Edit Profil Anak':'Tambah Profil Anak'"></h3>
            <form @submit.prevent="saveAnak()" class="space-y-4">
                <div>
                    <label class="label">Nama Lengkap</label>
                    <input type="text" x-model="anakForm.nama_murid" class="input" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Jenis Kelamin</label>
                        <select x-model="anakForm.jenis_kelamin" class="input">
                            <option value="laki-laki">Laki-laki</option>
                            <option value="perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Tanggal Lahir</label>
                        <input type="date" x-model="anakForm.tanggal_lahir" class="input">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showEditAnak=false" class="btn-salmon">Batal</button>
                    <button type="submit" class="btn-green" :disabled="savingAnak"
                            x-text="savingAnak?'Menyimpan...':'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function profilPage() {
    const user = api.getUser() || {};
    return {
        // State
        saving: false, showPwd: false, showPwd2: false, fotoPreview: null, fotoFile: null,
        anakList: [], kelasList: [], loadingAnak: false,
        showEditAnak: false, editAnakId: null, savingAnak: false,
        anakForm: { nama_murid: '', jenis_kelamin: 'laki-laki', tanggal_lahir: '', id_kelas: '' },
        form: { nama: '', email: '', password: '', password_confirmation: '', pekerjaan: '', spesialisasi: '' },

        // Computed roles
        get isOrangTua() { return (user.roles||[]).includes('orang_tua'); },
        get isGuru()     { return (user.roles||[]).includes('guru'); },

        async init() {
            // Load profil
            const r = await api.get('/profil');
            if (r?.ok) {
                const p = r.data.data;
                this.form.nama     = p.nama || '';
                this.form.email    = p.email || '';
                this.form.pekerjaan   = p.orang_tua?.pekerjaan || '';
                this.form.spesialisasi = p.guru?.spesialisasi || '';
                if (p.foto_profile) this.fotoPreview = '/storage/' + p.foto_profile;
            }

            // Load kelas list untuk dropdown
            const kr = await api.get('/kelas');
            if (kr?.ok) this.kelasList = kr.data.data?.data || [];

            // Load data anak jika orang tua
            if (this.isOrangTua) {
                this.loadingAnak = true;
                const mr = await api.get('/murid');
                this.loadingAnak = false;
                if (mr?.ok) this.anakList = mr.data.data || [];
            }
        },

        onFoto(e) {
            const f = e.target.files[0];
            if (!f) return;
            this.fotoFile = f;
            const reader = new FileReader();
            reader.onload = ev => { this.fotoPreview = ev.target.result; };
            reader.readAsDataURL(f);
        },

        resetForm() { this.init(); },

        async saveProfil() {
            this.saving = true;
            const fd = new FormData();
            if (this.form.nama)   fd.append('nama', this.form.nama);
            if (this.form.pekerjaan)    fd.append('pekerjaan', this.form.pekerjaan);
            if (this.form.spesialisasi) fd.append('spesialisasi', this.form.spesialisasi);
            if (this.form.password)     fd.append('password', this.form.password);
            if (this.form.password_confirmation) fd.append('password_confirmation', this.form.password_confirmation);
            if (this.fotoFile) fd.append('foto_profile', this.fotoFile);

            const r = await api.post('/profil', fd, true);
            this.saving = false;
            if (r?.ok) {
                Alpine.store('notif').success('Profil berhasil disimpan');
                api.setUser({ ...api.getUser(), nama: r.data.data?.nama, foto_profile: r.data.data?.foto_profile });
                this.form.password = '';
                this.form.password_confirmation = '';
            } else {
                Alpine.store('notif').error(r?.data?.message || 'Gagal menyimpan profil');
            }
        },

        // ── Anak ──────────────────────────────────────────────
        openTambahAnak() {
            this.editAnakId = null;
            this.anakForm = { nama_murid: '', jenis_kelamin: 'laki-laki', tanggal_lahir: '', id_kelas: '' };
            this.showEditAnak = true;
        },

        editAnak(anak) {
            this.editAnakId = anak.id_murid;
            this.anakForm = {
                nama_murid:    anak.nama_murid,
                jenis_kelamin: anak.jenis_kelamin || 'laki-laki',
                tanggal_lahir: anak.tanggal_lahir || '',
                id_kelas:      anak.id_kelas || '',
            };
            this.showEditAnak = true;
        },

        async saveAnak() {
            this.savingAnak = true;
            const payload = {
                nama_murid: this.anakForm.nama_murid,
                jenis_kelamin: this.anakForm.jenis_kelamin,
                tanggal_lahir: this.anakForm.tanggal_lahir,
            };

            let r;
            if (this.editAnakId) {
                r = await api.post(`/murid/${this.editAnakId}`, payload);
            } else {
                r = await api.post('/murid', payload);
            }
            this.savingAnak = false;

            if (r?.ok) {
                this.showEditAnak = false;
                Alpine.store('notif').success(this.editAnakId ? 'Data anak berhasil diperbarui' : 'Profil anak berhasil ditambahkan');
                // Reload anak list
                const mr = await api.get('/murid');
                if (mr?.ok) this.anakList = mr.data.data || [];
            } else {
                Alpine.store('notif').error(r?.data?.message || 'Gagal menyimpan data anak');
            }
        },

        // ── Utils ─────────────────────────────────────────────
        formatTgl(tgl) {
            if (!tgl) return '-';
            return new Date(tgl).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        },

        async logout() {
            await api.post('/auth/logout');
            api.removeToken();
            api.removeUser();
            window.location.href = '/login';
        },
    };
}
</script>
@endsection
