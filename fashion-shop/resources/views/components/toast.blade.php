@props([
    'message' => '',
    'delay' => 3000,
])

@if ($message)
    <div class="toast-container position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999;">
        <div class="toast fade show bg-success text-white px-4 py-3" role="alert" data-bs-delay="{{ $delay }}">
            <div class="toast-body text-center fw-semibold">
                {{ $message }}
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
