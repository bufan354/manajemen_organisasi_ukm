// System Modal Logic
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    const content = document.getElementById(modalId + '-content');
    if (modal && content) {
        modal.classList.remove('pointer-events-none', 'opacity-0');
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const content = document.getElementById(modalId + '-content');
    if (modal && content) {
        modal.classList.add('pointer-events-none', 'opacity-0');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
    }
}

// Delete Modal Logic
function openDeleteModal(name) {
    const targetNameEl = document.getElementById('deleteTargetName');
    if (targetNameEl) targetNameEl.innerText = name;
    
    const modal = document.getElementById('deleteModal');
    const content = document.getElementById('deleteModalContent');
    if (modal && content) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const content = document.getElementById('deleteModalContent');
    if (modal && content) {
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
}
