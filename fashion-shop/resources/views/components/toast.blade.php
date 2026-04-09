@props([
    'message' => '',
    'success' => null,
    'error' => null,
    'type' => null,
    'delay' => 3000,
])

@php
    $successMessage = $success ?? session('success');
    $errorMessage = $error ?? session('error');
    $toastMessage = $message ?: ($successMessage ?: $errorMessage);
    $toastType = $type ?? ($errorMessage ? 'error' : 'success');
    $toastClass =
        $toastType === 'error'
            ? 'border-red-200 bg-red-50 text-red-700 shadow-red-100/80'
            : 'border-emerald-200 bg-emerald-50 text-emerald-700 shadow-emerald-100/80';
    $iconClass = $toastType === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check';
@endphp

@if ($toastMessage)
    <div id="app-toast"
        class="fixed left-1/2 top-5 z-9999 w-[calc(100%-2rem)] max-w-md -translate-x-1/2 transition-all duration-300">
        <div class="overflow-hidden rounded-2xl border px-4 py-3 shadow-xl backdrop-blur {{ $toastClass }}">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/80">
                    <i class="fa-solid {{ $iconClass }} text-base"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold leading-5">
                        {{ $toastMessage }}
                    </p>
                </div>
                <button type="button" id="app-toast-close"
                    class="shrink-0 rounded-full p-1 text-current/60 transition hover:bg-white/70 hover:text-current"
                    aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastEl = document.getElementById('app-toast')
            const closeBtn = document.getElementById('app-toast-close')

            if (!toastEl) {
                return
            }

            requestAnimationFrame(() => {
                toastEl.classList.add('opacity-100', 'translate-y-0')
                toastEl.classList.remove('opacity-0', '-translate-y-3')
            })

            const hideToast = () => {
                toastEl.classList.remove('opacity-100', 'translate-y-0')
                toastEl.classList.add('opacity-0', '-translate-y-3')
                window.setTimeout(() => toastEl.remove(), 300)
            }

            toastEl.classList.add('opacity-0', '-translate-y-3')
            window.setTimeout(hideToast, {{ $delay }})

            if (closeBtn) {
                closeBtn.addEventListener('click', hideToast, {
                    once: true
                })
            }
        })
    </script>
@endif
