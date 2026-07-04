import Alpine from 'alpinejs';
window.Alpine = Alpine;

window.api = {
    baseUrl: '/api',
    getToken()  { return localStorage.getItem('aluna_token'); },
    setToken(t) { localStorage.setItem('aluna_token', t); },
    removeToken(){ localStorage.removeItem('aluna_token'); },
    getUser()   { const u = localStorage.getItem('aluna_user'); return u ? JSON.parse(u) : null; },
    setUser(u)  { localStorage.setItem('aluna_user', JSON.stringify(u)); },
    removeUser(){ localStorage.removeItem('aluna_user'); },

    async request(method, path, body = null, isForm = false) {
        const headers = { 'Accept': 'application/json' };
        const token = this.getToken();
        if (token) headers['Authorization'] = `Bearer ${token}`;
        let options = { method, headers };
        if (body) {
            if (isForm) { options.body = body; }
            else { headers['Content-Type'] = 'application/json'; options.body = JSON.stringify(body); }
        }
        const res = await fetch(`${this.baseUrl}${path}`, options);
        const data = await res.json();
        if (res.status === 401) {
            this.removeToken(); this.removeUser();
            window.location.href = '/login'; return;
        }
        return { ok: res.ok, status: res.status, data };
    },
    get(path)              { return this.request('GET', path); },
    post(path, body, form) { return this.request('POST', path, body, form); },
    put(path, body)        { return this.request('PUT', path, body); },
    del(path)              { return this.request('DELETE', path); },
};

Alpine.store('notif', {
    show: false, message: '', type: 'success', timer: null,
    success(msg) { this._show('success', msg, 3500); },
    error(msg)   { this._show('error', msg, 4000); },
    _show(type, msg, ms) {
        this.type = type; this.message = msg; this.show = true;
        clearTimeout(this.timer);
        this.timer = setTimeout(() => { this.show = false; }, ms);
    },
});

document.addEventListener('DOMContentLoaded', () => {
    const publicPaths = ['/login'];
    if (!publicPaths.includes(window.location.pathname) && !api.getToken()) {
        window.location.href = '/login';
    }
    if (window.location.pathname === '/login' && api.getToken()) {
        window.location.href = '/dashboard';
    }
});

Alpine.start();
