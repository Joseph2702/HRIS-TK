<!DOCTYPE html>
<html lang="id" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aluna Monitoring')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.6; transform: scale(1.2); }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-white font-sans">

{{-- Toast notifikasi --}}
<div x-data x-show="$store.notif.show"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1"
     x-transition:leave="transition ease-in duration-150"  x-transition:leave-end="opacity-0 translate-y-1"
     class="fixed bottom-5 right-5 z-50 bg-white border border-gray-100 shadow-xl rounded-2xl px-5 py-3.5 flex items-center gap-3 min-w-60">
    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
         :style="$store.notif.type==='success' ? 'background:#C2DFF4' : 'background:#EFC9EA'">
        <svg class="w-4 h-4" fill="none" stroke-width="2.5" viewBox="0 0 24 24"
             :stroke="$store.notif.type==='success' ? '#4A90C5' : '#C47DBF'">
            <template x-if="$store.notif.type==='success'">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </template>
            <template x-if="$store.notif.type!=='success'">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </template>
        </svg>
    </div>
    <span class="text-sm text-gray-700 flex-1" x-text="$store.notif.message"></span>
    <button @click="$store.notif.show=false" class="text-gray-300 hover:text-gray-500 text-xl leading-none ml-2">×</button>
</div>

<div class="flex h-screen overflow-hidden">

    {{-- ══ SIDEBAR ══ --}}
    <aside class="w-64 flex-shrink-0 bg-white flex flex-col h-full" x-data="sidebar()" x-init="init()">

        {{-- User info --}}
        <div class="px-5 pt-6 pb-4">
            <div class="flex flex-col items-center text-center gap-2">
                <div class="w-14 h-14 rounded-full bg-gray-100 border-2 border-gray-200 flex items-center justify-center overflow-hidden">
                    <template x-if="userFoto">
                        <img :src="'/storage/'+userFoto" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!userFoto">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </template>
                </div>
                <div>
                    <p class="font-semibold text-sm text-gray-800" x-text="userName"></p>
                    <p class="text-xs text-gray-400" x-text="userEmail"></p>
                </div>
            </div>
        </div>

        <div class="mx-5 border-t border-gray-200 mb-3"></div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-3 space-y-0.5">


            <a href="/dashboard" class="sidebar-link" :class="{active: isActive('/dashboard')}">

                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="/artikel" class="sidebar-link" x-show="hasPermission('artikel.view')" :class="{active: isActive('/artikel')}">


                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tentang Sekolah
            </a>

            <a href="/presensi" class="sidebar-link" x-show="hasPermission('presensi.view')" :class="{active: isActive('/presensi')}">


                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Presensi
            </a>

            <a href="/laporan" class="sidebar-link" x-show="hasPermission('laporan.view')" :class="{active: isActive('/laporan')}">


                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Laporan Kegiatan
            </a>

            <a href="/layanan" class="sidebar-link" :class="{active: isActive('/layanan')}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Layanan Sekolah
            </a>

            <a href="/notifikasi" class="sidebar-link" :class="{active: isActive('/notifikasi')}">
                {{-- Bell icon dengan dot merah jika ada unread --}}
                <div class="relative shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    {{-- Dot merah dengan pulse animation --}}
                    <template x-if="unreadCount > 0">
                        <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-red-500 flex items-center justify-center"
                              style="animation: pulse 2s cubic-bezier(0.4,0,0.6,1) infinite">
                        </span>
                    </template>
                </div>
                Notifikasi
                {{-- Badge count --}}
                <template x-if="unreadCount > 0">
                    <span class="ml-auto inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full text-xs font-bold text-white"
                          style="background:#C87CC8"
                          x-text="unreadCount > 99 ? '99+' : unreadCount">
                    </span>
                </template>
            </a>

            <a href="/profil" class="sidebar-link" :class="{active: isActive('/profil')}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pengaturan Profil
            </a>

            <template x-if="hasRole('admin')">
                <div class="space-y-0.5">
                    <a href="/users" class="sidebar-link" x-show="hasPermission('user.manage')" :class="{active: isActive('/users')}">


                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Manajemen Pengguna
                    </a>
                    <a href="/roles" class="sidebar-link" x-show="hasRole('admin') && hasPermission('role.manage')" :class="{active: isActive('/roles')}">


                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Peran
                    </a>
                </div>
            </template>

            {{-- Ulasan Layanan (paling bawah) --}}
            <a href="/ulasan-layanan" class="sidebar-link" :class="{active: isActive('/ulasan-layanan')}"
               x-show="hasRole('admin') || hasRole('orang_tua')">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.83 9.83 0 01-4.083-.87L3 20l1.25-3.125A7.969 7.969 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Ulasan Layanan
            </a>
        
        </nav>

        {{-- Logout (paling bawah sidebar, untuk semua role) --}}
        <div class="px-5 pb-5">
            <button @click="logout()" class="w-full py-3.5 rounded-full font-semibold text-white text-sm transition-opacity hover:opacity-85" style="background:#C87CC8">
                Logout
            </button>
        </div>

    </aside>


    {{-- ══ DIVIDER UNGU ══ --}}
    <div class="w-2 flex-shrink-0" style="background:#C87CC8"></div>

    {{-- ══ KONTEN UTAMA ══ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-white">
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
function sidebar() {
    const user = api.getUser() || {};
    return {
        userName:    user.nama  || 'User',
        userEmail:   user.email || '',
        userFoto:    user.foto_profile || null,
        unreadCount: 0,

        async init() {
            await this.fetchUnread();
            // Poll setiap 30 detik
            setInterval(() => this.fetchUnread(), 30000);
        },

        async fetchUnread() {
            const r = await api.get('/notifikasi');
            if (r?.ok) this.unreadCount = r.data.data?.unread || 0;
        },

        isActive(path) { return window.location.pathname === path || window.location.pathname.startsWith(path + '/') || window.location.pathname.startsWith(path + '?'); },
        hasRole(role)  { return (user.roles || []).includes(role); },
        hasPermission(permission) { return this.hasRole('admin') || (user.permissions || []).includes(permission); },

        async logout() {
            await api.post('/auth/logout');
            api.removeToken();
            api.removeUser();
            window.location.href = '/login';
        },
    };
}
</script>
</body>
</html>
