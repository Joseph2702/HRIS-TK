@extends('layouts.app')
@section('title', 'Detail Artikel — Aluna')
@section('content')
<div x-data="artikelDetailPage()" x-init="init()">
    <div class="page-banner flex items-center gap-3 mb-6">
        <a href="/artikel" class="text-gray-600 hover:text-gray-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 x-text="item?.judul_artikel || 'Memuat detail...'" class="text-xl font-semibold text-gray-900"></h1>
    </div>

    <template x-if="loading">
        <div class="rounded-3xl border border-gray-100 bg-white p-8 text-center text-gray-500">Memuat artikel...</div>
    </template>

    <template x-if="!loading && !item">
        <div class="rounded-3xl border border-gray-100 bg-white p-8 text-center text-gray-500">Artikel tidak ditemukan.</div>
    </template>

    <template x-if="item">
        <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <template x-if="item.gambar_artikel">
                <img :src="'/storage/' + item.gambar_artikel" class="w-full rounded-3xl object-cover mb-6" alt="Gambar artikel">
            </template>
            <div class="mb-4 flex flex-wrap gap-3 items-center text-sm text-gray-500">
                <span x-text="item.status_artikel"></span>
                <span>•</span>
                <span x-text="formatDate(item.tanggal_publish)"></span>
            </div>
            <p class="text-gray-700 whitespace-pre-line" x-text="item.konten_artikel"></p>
            <template x-if="item.gambar_artikel_2">
                <img :src="'/storage/' + item.gambar_artikel_2" class="w-full rounded-3xl object-cover mt-6" alt="Gambar artikel 2">
            </template>
        </div>
    </template>
</div>

<script>
function artikelDetailPage() {
    return {
        item: null,
        loading: true,

        async init() {
            const artikelId = new URLSearchParams(window.location.search).get('id') || window.location.pathname.split('/').pop();
            const r = await api.get('/artikel/' + artikelId);
            this.loading = false;
            if (r?.ok) {
                this.item = r.data.data;
            }
        },

        formatDate(date) {
            return date ? new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
        },
    };
}
</script>
@endsection
