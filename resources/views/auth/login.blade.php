<!DOCTYPE html>
<html lang="id" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Aluna Monitoring</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-white min-h-screen font-sans">

<div x-data="loginPage()" class="flex min-h-screen">

    {{-- Kiri: Logo --}}
    <div class="flex-1 flex items-center justify-center p-12">
        <div class="text-center select-none">
            {{-- Icon turtle / lifebuoy --}}
            <div class="flex items-center justify-center mb-2">
                <div class="w-36 h-36 rounded-full border-4 flex items-center justify-center"
                     style="border-color:#C2DFF4; background:rgba(194,223,244,0.15)">
                    <svg viewBox="0 0 80 80" class="w-24 h-24" fill="none">
                        {{-- Simplified turtle / Aluna logo icon --}}
                        <circle cx="40" cy="40" r="28" stroke="#C2DFF4" stroke-width="5" fill="white"/>
                        <circle cx="40" cy="40" r="14" fill="#C2DFF4"/>
                        <circle cx="40" cy="18" r="5" fill="#C2DFF4"/>
                        <circle cx="40" cy="62" r="5" fill="#C2DFF4"/>
                        <circle cx="18" cy="40" r="5" fill="#C2DFF4"/>
                        <circle cx="62" cy="40" r="5" fill="#C2DFF4"/>
                        <circle cx="24" cy="24" r="4" fill="#C2DFF4"/>
                        <circle cx="56" cy="24" r="4" fill="#C2DFF4"/>
                        <circle cx="24" cy="56" r="4" fill="#C2DFF4"/>
                        <circle cx="56" cy="56" r="4" fill="#C2DFF4"/>
                    </svg>
                </div>
            </div>

            {{-- Brand name --}}
            <p class="text-gray-400 text-sm font-medium tracking-widest uppercase mb-1">Sekolah</p>
            <div class="flex items-baseline justify-center gap-0.5 mb-1">
                <span class="text-6xl font-bold" style="color:#C2DFF4">A</span>
                <span class="text-6xl font-bold text-green-400">l</span>
                <span class="text-6xl font-bold" style="color:#7EC8E3">u</span>
                <span class="text-6xl font-bold" style="color:#EFC9EA">n</span>
                <span class="text-6xl font-bold" style="color:#EFC9EA">a</span>
            </div>
            <p class="text-gray-400 text-xs font-semibold tracking-[0.4em] uppercase">Montessori</p>
        </div>
    </div>

    {{-- Kanan: Form --}}
    <div class="flex-1 flex items-center justify-center p-12">
        <div class="w-full max-w-sm">
            <form @submit.prevent="login()" class="space-y-5">
                {{-- Email --}}
                <div>
                    <label class="block text-sm text-gray-700 mb-2">Email</label>
                    <input
                        type="email"
                        x-model="form.email"
                        placeholder="xxxxxx xxxx xxxx"
                        required
                        autocomplete="email"
                        class="w-full px-5 py-3.5 rounded-full border border-gray-200 text-sm text-gray-700 placeholder-gray-300 focus:outline-none focus:border-blue-200 focus:ring-2 focus:ring-blue-100 transition-all bg-white"
                    >
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input
                            :type="showPwd ? 'text' : 'password'"
                            x-model="form.password"
                            placeholder="xxxxxxxxxxx@example.com"
                            required
                            autocomplete="current-password"
                            class="w-full px-5 py-3.5 rounded-full border border-gray-200 text-sm text-gray-700 placeholder-gray-300 focus:outline-none focus:border-blue-200 focus:ring-2 focus:ring-blue-100 transition-all bg-white pr-12"
                        >
                        <button type="button" @click="showPwd = !showPwd"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!showPwd" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path x-show="showPwd" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Button Login --}}
                <div class="pt-2">
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3.5 rounded-full text-sm font-medium text-gray-700 transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-60"
                        style="background-color:#EFC9EA; hover:opacity-90"
                        @mouseenter="$el.style.opacity='0.85'"
                        @mouseleave="$el.style.opacity='1'"
                    >
                        <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="loading ? 'Memproses...' : 'Login'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Toast error — bottom right --}}
    <div x-show="error"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-6 right-6 bg-white rounded-2xl shadow-lg px-5 py-4 flex items-center gap-3 min-w-56 border border-gray-100">
        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center shrink-0"
             style="border-color:#EFC9EA">
            <svg class="w-4 h-4" fill="none" stroke="#EFC9EA" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <span class="text-sm text-gray-600" x-text="error"></span>
    </div>

</div>

<script>
function loginPage() {
    return {
        form: { email: '', password: '' },
        showPwd: false,
        loading: false,
        error: null,

        async login() {
            this.loading = true;
            this.error = null;
            const res = await api.post('/auth/login', this.form);
            this.loading = false;

            if (res?.ok) {
                api.setToken(res.data.data.token);
                api.setUser(res.data.data.user);
                window.location.href = '/dashboard';
            } else {
                this.error = res?.data?.message || 'Login tidak berhasil';
                setTimeout(() => { this.error = null; }, 4000);
            }
        },
    };
}
</script>
</body>
</html>
