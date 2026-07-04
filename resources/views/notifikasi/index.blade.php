@extends('layouts.app')
@section('title', 'Notifikasi — Aluna')
@section('content')
<div x-data="notifikasiPage()" x-init="init()">

    <div class="page-banner flex items-center justify-between">
        <h1>Notifikasi</h1>
        <button x-show="unread > 0" @click="markRead()"
                class="text-xs text-gray-600 hover:text-gray-800 bg-white/60 px-3 py-1 rounded-full">
            Tandai semua dibaca
        </button>
    </div>

    {{-- Empty state --}}
    <template x-if="!loading && items.length === 0">
        <div class="text-center py-16">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <p class="text-gray-400 text-sm">Tidak ada notifikasi</p>
        </div>
    </template>

    <template x-if="loading">
        <p class="text-center text-gray-400 py-8 text-sm">Memuat notifikasi...</p>
    </template>

    {{-- Notifikasi list --}}
    <div class="space-y-3">
        <template x-for="item in items" :key="item.id_notif">
            <div class="rounded-2xl px-5 py-4 flex items-start justify-between gap-4 cursor-pointer transition-all hover:shadow-sm"
                 :class="item.is_read ? 'bg-gray-50' : 'bg-white border border-gray-100 shadow-sm'"
                 @click="klikNotif(item)">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    {{-- Icon tipe --}}
                    <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                         :style="item.tipe==='laporan' ? 'background:#C2DFF4' : item.tipe==='balasan' ? 'background:#EFC9EA' : 'background:#F3F4F6'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             :stroke="item.tipe==='laporan' ? '#4A90C5' : item.tipe==='balasan' ? '#C47DBF' : '#9CA3AF'">
                            <template x-if="item.tipe==='laporan'">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </template>
                            <template x-if="item.tipe==='balasan'">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </template>
                            <template x-if="item.tipe==='artikel'">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"/>
                            </template>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800" x-text="item.judul"></p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="item.pesan"></p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span x-show="!item.is_read" class="w-2 h-2 rounded-full" style="background:#EFC9EA"></span>
                    <span class="text-xs text-gray-400" x-text="formatDate(item.created_at)"></span>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function notifikasiPage() {
    return {
        items: [], unread: 0, loading: true,

        async init() {
            const r = await api.get('/notifikasi');
            this.loading = false;
            if (r?.ok) {
                this.items = r.data.data?.notifikasi || [];
                this.unread = r.data.data?.unread || 0;
            }
        },

        async markRead() {
            await api.post('/notifikasi/read');
            this.items = this.items.map(i => ({ ...i, is_read: true }));
            this.unread = 0;
        },

        klikNotif(item) {
            item.is_read = true;
            if (item.tipe === 'laporan' || item.tipe === 'balasan') {
                window.location.href = '/laporan';
            }
        },

        formatDate(dt) {
            if (!dt) return '-';
            const d = new Date(dt);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + ' ' +
                   d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        },
    };
}
</script>
@endsection
