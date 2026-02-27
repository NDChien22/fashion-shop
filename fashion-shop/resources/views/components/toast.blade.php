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
    $toastBgClass = $toastType === 'error' ? 'bg-danger' : 'bg-success';
@endphp

@if ($toastMessage)
    <div class="toast-container position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999;">
        <div class="toast fade show {{ $toastBgClass }} text-white px-4 py-3" role="alert"
            data-bs-delay="{{ $delay }}">
            <div class="toast-body text-center fw-semibold">
                {{ $toastMessage }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastEl = document.querySelector('.toast')
            if (toastEl) {
                new bootstrap.Toast(toastEl).show()
            }
        })
    </script>
@endif
