document.addEventListener('DOMContentLoaded', () => {
    const elements = {
        confirmModal: document.getElementById('confirmModal'),
        closeConfirmModal: document.getElementById('closeConfirmModal'),
        cancelRemoveBtn: document.getElementById('cancelRemoveBtn'),
        confirmRemoveBtn: document.getElementById('confirmRemoveBtn'),
        removeManagerBtn: document.getElementById('removeManagerBtn'),
        assignManagerForm: document.getElementById('assignManagerForm'),
        managerSelect: document.getElementById('manager_user_id'),
        card: document.querySelector('.card')
    };

    const toggleModal = (show) => {
        elements.confirmModal.classList.toggle('show', show);
        document.body.style.overflow = show ? 'hidden' : '';
    };

    const handleValidation = (e) => {
        e.preventDefault();
        const {
            managerSelect,
            assignManagerForm
        } = elements;
        managerSelect.classList.remove('is-invalid');
        const errorElement = managerSelect.parentNode.querySelector('.error-message');
        if (errorElement) errorElement.remove();

        if (!managerSelect.value) {
            managerSelect.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = 'Vui lòng chọn người quản lý';
            managerSelect.parentNode.insertAdjacentElement('afterend', errorDiv);
            return;
        }
        assignManagerForm.submit();
    };

    const handleRemoveManager = () => {
        const {
            assignManagerForm,
            managerSelect
        } = elements;
        managerSelect.value = '';
        assignManagerForm.submit();
    };

    const animateCard = () => {
        const {
            card
        } = elements;
        if (card) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }
    };

    // Event Listeners
    elements.closeConfirmModal?.addEventListener('click', () => toggleModal(false));
    elements.cancelRemoveBtn?.addEventListener('click', () => toggleModal(false));
    elements.removeManagerBtn?.addEventListener('click', () => toggleModal(true));
    elements.confirmRemoveBtn?.addEventListener('click', () => {
        toggleModal(false);
        handleRemoveManager();
    });
    elements.assignManagerForm?.addEventListener('submit', handleValidation);
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-backdrop')) toggleModal(false);
    });

    animateCard();
});