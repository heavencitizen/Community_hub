<div x-data="{
        toasts: [],
        add(type, message, duration = 4000) {
            if (!message) return;
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type, message, duration, visible: true });
            setTimeout(() => {
                this.remove(id);
            }, duration);
        },
        remove(id) {
            const t = this.toasts.find(x => x.id === id);
            if (t) {
                t.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(x => x.id !== id);
                }, 250);
            }
        },
        init() {
            window.addEventListener('toast', (e) => {
                const detail = e.detail || {};
                this.add(detail.type || 'info', detail.message || '', detail.duration || 4000);
            });
            @if(session('success'))
                this.add('success', @json(session('success')));
            @endif
            @if(session('error'))
                this.add('error', @json(session('error')));
            @endif
            @if(session('warning'))
                this.add('warning', @json(session('warning')));
            @endif
            @if(session('info'))
                this.add('info', @json(session('info')));
            @endif
        }
    }" 
    class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible" 
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-4 opacity-0 sm:translate-y-0 sm:translate-x-6"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="pointer-events-auto relative overflow-hidden rounded-2xl bg-white/95 backdrop-blur-md p-3.5 shadow-2xl border flex items-start gap-3 transition"
             :class="{
                 'border-emerald-200 text-emerald-950 shadow-emerald-500/10': toast.type === 'success',
                 'border-rose-200 text-rose-950 shadow-rose-500/10': toast.type === 'error',
                 'border-amber-200 text-amber-950 shadow-amber-500/10': toast.type === 'warning',
                 'border-indigo-200 text-indigo-950 shadow-indigo-500/10': toast.type === 'info'
             }">
            <!-- Icon -->
            <div class="mt-0.5 flex-shrink-0">
                <template x-if="toast.type === 'success'">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                </template>
                <template x-if="toast.type === 'error'">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
                </template>
                <template x-if="toast.type === 'warning'">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-base"></i>
                </template>
                <template x-if="toast.type === 'info'">
                    <i class="fa-solid fa-circle-info text-indigo-500 text-base"></i>
                </template>
            </div>

            <!-- Content -->
            <div class="flex-grow min-w-0 pr-2">
                <div class="text-[10px] font-extrabold uppercase tracking-wider mb-0.5"
                     :class="{
                         'text-emerald-600': toast.type === 'success',
                         'text-rose-600': toast.type === 'error',
                         'text-amber-600': toast.type === 'warning',
                         'text-indigo-600': toast.type === 'info'
                     }"
                     x-text="toast.type === 'success' ? 'Berhasil' : (toast.type === 'error' ? 'Peringatan / Gagal' : (toast.type === 'warning' ? 'Perhatian' : 'Informasi'))">
                </div>
                <p class="text-xs font-semibold leading-relaxed text-slate-800" x-text="toast.message"></p>
            </div>

            <!-- Close Button -->
            <button type="button" @click="remove(toast.id)" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition flex-shrink-0">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>

            <!-- Animated Countdown Progress Bar -->
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-100 overflow-hidden">
                <div class="h-full toast-progress-bar"
                     :class="{
                         'bg-emerald-500': toast.type === 'success',
                         'bg-rose-500': toast.type === 'error',
                         'bg-amber-500': toast.type === 'warning',
                         'bg-indigo-500': toast.type === 'info'
                     }"
                     :style="`animation: toastProgress ${toast.duration}ms linear forwards;`"></div>
            </div>
        </div>
    </template>
</div>

<style>
@keyframes toastProgress {
    from { width: 100%; }
    to { width: 0%; }
}
</style>