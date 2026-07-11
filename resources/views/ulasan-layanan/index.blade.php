@extends('layouts.app')
@section('title', 'Ulasan Layanan — Aluna')
@section('content')
<div x-data="ulasanLayananPage()" x-init="init()">
    {{-- Header / Overall rating --}}
    <div class="page-banner flex items-center justify-between gap-4 mb-6">
        <h1 class="text-lg font-bold text-gray-900">Rating Keseluruhan Layanan Sekolah</h1>
        <div class="text-right">
            <div class="text-sm text-gray-500">Rata-rata</div>
            <div class="text-2xl font-bold text-gray-900" x-text="overallAvgText"></div>
            <div class="text-xs text-gray-400 mt-1" x-text="overallReviewsText"></div>
        </div>
    </div>

    {{-- Distribusi rating (opsional, tapi membantu grafik sederhana) --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm mb-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-gray-800 text-sm">Distribusi Rating</h2>
        </div>

        <div class="space-y-2">
            <template x-for="n in [5,4,3,2,1]" :key="n">
                <div class="flex items-center gap-3">
                    <div class="w-10 text-sm text-gray-700 font-semibold" x-text="n+'★'"></div>
                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full" :style="barStyle(n)"></div>
                    </div>
                    <div class="w-16 text-right text-xs text-gray-500" x-text="distribution[n] + ' ('+percentText(n)+'%)'"></div>
                </div>
            </template>
        </div>
    </div>

    {{-- Button Tambah Ulasan --}}
    <div class="relative">
        <div class="text-sm text-gray-600 mb-3" x-show="loading">Memuat ulasan...</div>

        <template x-if="!loading">
            <div class="flex items-center justify-end mb-3">
                <template x-if="canAdd">
                    <button @click="openAddModal()" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold text-white shadow-sm" style="background:#C87CC8;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Ulasan
                    </button>
                </template>
            </div>
        </template>
    </div>

    {{-- List ulasan --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
        <template x-if="!loading && ulasan.length === 0">
            <p class="text-center text-gray-500 py-8 text-sm">Belum ada ulasan layanan</p>
        </template>

        <template x-if="ulasan.length > 0">
            <div class="space-y-3">
                <template x-for="u in ulasan" :key="u.id_ulasan">
                    <div class="rounded-xl border border-gray-100 bg-white p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7a4 4 0 100-8 4 4 0 000 8z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 8v6"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 11h-6"/>
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-gray-900 text-sm" x-text="u.user?.nama || '-'"></p>
                                    <span class="text-xs text-gray-400" x-text="formatDate(u.created_at)"></span>
                                </div>

                                <div class="text-sm text-gray-600" x-text="u.isi_ulasan || '-'"></div>
                            </div>

                            <div class="shrink-0 text-right">
                                <div class="text-sm text-gray-500 mb-1">Rating</div>
                                <div class="text-lg font-bold text-[#C87CC8]" x-text="u.rating + '★'"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>

    {{-- Modal Tambah Ulasan --}}
    <div x-show="showAddModal" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showAddModal=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto" @click.stop>
            <h3 class="font-bold text-gray-800 mb-5">Tambah Ulasan</h3>

            <form @submit.prevent="submitAdd()" class="space-y-4">
                <div>
                    <label class="label">Rating (1 - 5)</label>
                    <div class="flex gap-2 items-center">
                        <template x-for="n in [1,2,3,4,5]" :key="n">
                            <button type="button" @click="form.rating = n"
                                    class="px-3 py-2 rounded-xl border text-sm font-semibold transition"
                                    :class="form.rating === n ? 'border-[#C87CC8] bg-[#F7E6F7] text-[#9E3AA5]' : 'border-gray-200 bg-white text-gray-700 hover:border-pink-200'">
                                <span x-text="n+'★'"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="label">Isi Ulasan</label>
                    <textarea x-model="form.isi_ulasan" class="input h-28 resize-none" style="border-radius:1rem" placeholder="Tulis ulasan..." required></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showAddModal=false" class="btn-salmon">Batal</button>
                    <button type="submit" class="btn-green" :disabled="saving" x-text="saving ? 'Menyimpan...' : 'Simpan Ulasan'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function ulasanLayananPage() {
    return {
        loading: true,
        overallAvg: 0,
        overallTotalReviews: 0,
        distribution: {1:0,2:0,3:0,4:0,5:0},
        ulasan: [],
        canAdd: false,

        // modal
        showAddModal: false,
        saving: false,
        form: { rating: 5, isi_ulasan: '' },

        get overallAvgText() {
            return this.overallAvg.toFixed(2) + '/5';
        },
        get overallReviewsText() {
            return this.overallTotalReviews + ' ulasan';
        },

        barStyle(n) {
            const total = this.overallTotalReviews || 0;
            const count = this.distribution[n] || 0;
            const percent = total > 0 ? (count / total) * 100 : 0;
            return { width: percent.toFixed(0) + '%', backgroundColor: '#EFC9EA' };
        },

        percentText(n) {
            const total = this.overallTotalReviews || 0;
            const count = this.distribution[n] || 0;
            return total > 0 ? ((count / total) * 100).toFixed(0) : '0';
        },

        formatDate(date) {
            try {
                return date ? new Date(date).toLocaleString('id-ID', { day:'2-digit', month:'short', year:'numeric' }) : '-';
            } catch(e) { return '-' }
        },

        openAddModal() {
            this.form = { rating: 5, isi_ulasan: '' };
            this.showAddModal = true;
        },

        async submitAdd() {
            if (!this.form.isi_ulasan || String(this.form.isi_ulasan).trim().length === 0) {
                Alpine.store('notif').error('Isi ulasan tidak boleh kosong');
                return;
            }

            this.saving = true;
            try {
                const payload = {
                    rating: this.form.rating,
                    isi_ulasan: this.form.isi_ulasan
                };

                const r = await api.post('/ulasan-layanan', payload);
                if (r?.ok) {
                    Alpine.store('notif').success('Ulasan berhasil disimpan');
                    this.showAddModal = false;
                    await this.loadData();
                } else {
                    Alpine.store('notif').error(r?.data?.message || 'Gagal menyimpan ulasan');
                }
            } catch(e) {
                Alpine.store('notif').error('Gagal menyimpan ulasan');
            } finally {
                this.saving = false;
            }
        },

        async loadData() {
            this.loading = true;
            try {
                const r = await api.get('/ulasan-layanan?limit=50');
                if (r?.ok) {
                    const data = r.data?.data || r.data || {};
                    this.overallAvg = Number(data.overall_avg_rating ?? 0);
                    this.overallTotalReviews = Number(data.overall_total_reviews ?? 0);
                    this.distribution = data.distribution || {1:0,2:0,3:0,4:0,5:0};
                    this.ulasan = data.ulasan || [];
                } else {
                    this.overallAvg = 0;
                    this.overallTotalReviews = 0;
                    this.distribution = {1:0,2:0,3:0,4:0,5:0};
                    this.ulasan = [];
                }
            } finally {
                this.loading = false;
            }
        },

        async init() {
            const user = api.getUser() || {};
            const roles = user.roles || [];
            this.canAdd = roles.includes('orang_tua');

            await this.loadData();
        }
    }
}
</script>
@endsection
