import Swal from 'sweetalert2';

const confirmDeleteDialog = async (message, title = 'Xác nhận xóa') => {
    const result = await Swal.fire({
        title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#9ca3af',
        reverseButtons: true,
    });

    return result.isConfirmed;
};

window.ffConfirmLivewireDelete = async (triggerButton, methodName, recordId, message) => {
    const componentElement = triggerButton ? triggerButton.closest('[wire\\:id]') : null;
    const componentId = componentElement ? componentElement.getAttribute('wire:id') : null;

    if (!componentId || !window.Livewire) {
        return;
    }

    const isConfirmed = await confirmDeleteDialog(
        message || 'Bạn có chắc muốn xóa dữ liệu này?',
        'Xóa dữ liệu?'
    );

    if (isConfirmed) {
        window.Livewire.find(componentId).call(methodName, recordId);
    }
};

document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    if (!form.matches('form[data-confirm-delete="1"]')) {
        return;
    }

    event.preventDefault();

    const message = form.getAttribute('data-confirm-message') || 'Bạn có chắc muốn xóa dữ liệu này?';
    const isConfirmed = await confirmDeleteDialog(message, 'Xóa dữ liệu?');

    if (isConfirmed) {
        HTMLFormElement.prototype.submit.call(form);
    }
});
