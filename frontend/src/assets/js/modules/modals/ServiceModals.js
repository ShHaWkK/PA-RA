document.addEventListener('DOMContentLoaded', () => {
    const addServiceButton = document.getElementById('addServiceButton');
    const addServiceModal = document.getElementById('addServiceModal');
    const closeAddServiceModal = document.getElementById('closeAddServiceModal');
    const serviceDetailModal = document.getElementById('serviceDetailModal');
    const closeServiceDetailModal = document.getElementById('closeServiceDetailModal');

    function openModal(modal) {
        modal.style.display = 'block';
    }

    function closeModal(modal) {
        modal.style.display = 'none';
    }

    if (addServiceButton) {
        addServiceButton.addEventListener('click', () => {
            openModal(addServiceModal);
        });
    }

    if (closeAddServiceModal) {
        closeAddServiceModal.addEventListener('click', () => {
            closeModal(addServiceModal);
        });
    }

    if (closeServiceDetailModal) {
        closeServiceDetailModal.addEventListener('click', () => {
            closeModal(serviceDetailModal);
        });
    }

    window.addEventListener('click', (event) => {
        if (event.target === addServiceModal) {
            closeModal(addServiceModal);
        } else if (event.target === serviceDetailModal) {
            closeModal(serviceDetailModal);
        }
    });
});
