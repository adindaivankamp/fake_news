/**
 * Profile Popup Handler
 * Manages showing/hiding user profile popup and edit field functionality
 */

document.addEventListener('DOMContentLoaded', function () {
    const profileToggles = document.querySelectorAll('.js-profile-toggle');
    const profilePopup = document.getElementById('user-profile-popup');
    const editableFields = document.querySelectorAll('.js-editable-field');
    const editModal = document.getElementById('field-edit-modal');
    const editForm = document.getElementById('field-edit-form');
    const editInput = document.getElementById('field-edit-input');
    const editTypeInput = document.getElementById('field-edit-type');
    const editTitle = document.getElementById('field-edit-title');
    const closeModalBtn = document.querySelector('.lh-field-edit-modal__close');
    const cancelBtn = document.getElementById('field-edit-cancel');

    if (!profilePopup || profileToggles.length === 0) {
        return;
    }

    // Get the popup ID from toggle button
    const popupId = profileToggles[0]?.getAttribute('data-profile-toggle');

    /**
     * Toggle popup visibility
     */
    function togglePopup(event) {
        event.preventDefault();
        event.stopPropagation();

        const isHidden = profilePopup.hasAttribute('hidden');

        if (isHidden) {
            // Show popup
            profilePopup.removeAttribute('hidden');
            profileToggles.forEach(btn => {
                btn.setAttribute('aria-expanded', 'true');
            });

            // Create overlay to close on click outside
            createOverlay();
        } else {
            // Hide popup
            closePopup();
        }
    }

    /**
     * Close popup
     */
    function closePopup() {
        profilePopup.setAttribute('hidden', '');
        profileToggles.forEach(btn => {
            btn.setAttribute('aria-expanded', 'false');
        });

        // Remove overlay
        removeOverlay();
    }

    /**
     * Create overlay to detect clicks outside popup
     */
    function createOverlay() {
        // Remove existing overlay first
        removeOverlay();

        const overlay = document.createElement('div');
        overlay.className = 'lh-profile-popup-overlay';
        overlay.id = 'profile-popup-overlay';

        overlay.addEventListener('click', function () {
            closePopup();
        });

        // Close on Escape key
        const escapeHandler = function (event) {
            if (event.key === 'Escape') {
                closePopup();
                document.removeEventListener('keydown', escapeHandler);
            }
        };

        document.addEventListener('keydown', escapeHandler);

        document.body.appendChild(overlay);
    }

    /**
     * Remove overlay
     */
    function removeOverlay() {
        const overlay = document.getElementById('profile-popup-overlay');
        if (overlay) {
            overlay.remove();
        }
    }

    /**
     * Attach event listeners to all toggle buttons
     */
    profileToggles.forEach(btn => {
        btn.addEventListener('click', togglePopup);
    });

    /**
     * Close popup when clicking logout form
     */
    const logoutForm = profilePopup?.querySelector('.lh-profile-popup__logout-form');
    if (logoutForm) {
        logoutForm.addEventListener('submit', function () {
            setTimeout(closePopup, 100);
        });
    }

    /**
     * Close popup when clicking logout link
     */
    const logoutLink = profilePopup?.querySelector('a.lh-profile-popup__logout');
    if (logoutLink && !logoutForm) {
        logoutLink.addEventListener('click', function () {
            closePopup();
        });
    }

    // ==================== Field Edit Modal ====================

    /**
     * Get field label for modal title
     */
    function getFieldLabel(fieldType) {
        const labels = {
            name: 'Nama Pengguna',
            email: 'Email',
            phone: 'Nomor WhatsApp'
        };
        return labels[fieldType] || 'Informasi';
    }

    /**
     * Open edit modal for field
     */
    function openEditModal(fieldElement) {
        const fieldType = fieldElement.getAttribute('data-field');
        const fieldSpan = fieldElement.querySelector('span');
        const currentValue = fieldSpan?.textContent?.trim();

        // Set modal title and input type
        editTitle.textContent = 'Edit ' + getFieldLabel(fieldType);
        editTypeInput.value = fieldType;

        // Set input placeholder and type
        if (fieldType === 'email') {
            editInput.type = 'email';
            editInput.placeholder = 'Masukkan email';
        } else if (fieldType === 'phone') {
            editInput.type = 'tel';
            editInput.placeholder = 'Masukkan nomor WhatsApp';
        } else {
            editInput.type = 'text';
            editInput.placeholder = 'Masukkan nama';
        }

        // Set current value if not empty placeholder
        if (!currentValue?.includes('Tambahkan') && currentValue !== '-') {
            editInput.value = currentValue;
        } else {
            editInput.value = '';
        }

        // Show modal
        editModal.removeAttribute('hidden');
        editInput.focus();
    }

    /**
     * Close edit modal
     */
    function closeEditModal() {
        editModal.setAttribute('hidden', '');
        editForm.reset();
    }

    /**
     * Handle field click to open edit modal
     */
    editableFields.forEach(field => {
        field.addEventListener('click', function () {
            if (document.querySelector('.lh-profile-popup-overlay')) {
                openEditModal(this);
            }
        });
    });

    /**
     * Handle modal close button
     */
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeEditModal);
    }

    /**
     * Handle modal cancel button
     */
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeEditModal);
    }

    /**
     * Close modal on Escape key
     */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !editModal.hasAttribute('hidden')) {
            closeEditModal();
        }
    });

    /**
     * Close modal on overlay click
     */
    editModal.addEventListener('click', function (e) {
        if (e.target === this || e.target.className === 'lh-field-edit-modal__overlay') {
            closeEditModal();
        }
    });

    /**
     * Handle form submission for edit field
     */
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const fieldType = editTypeInput.value;
            const fieldValue = editInput.value.trim();
            const fieldElement = document.querySelector(`.js-editable-field[data-field="${fieldType}"]`);

            if (!fieldValue) {
                alert('Nilai tidak boleh kosong');
                return;
            }

            updateFieldOnServer(fieldType, fieldValue)
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'Gagal memperbarui profil');
                        return;
                    }

                    if (fieldElement) {
                        const fieldSpan = fieldElement.querySelector('span');
                        fieldSpan.textContent = fieldValue;
                    }

                    closeEditModal();
                })
                .catch(() => {
                    alert('Gagal memperbarui profil');
                });
        });
    }

    /**
     * Send field update to server
     */
    function updateFieldOnServer(fieldType, fieldValue) {
        const fieldMap = {
            name: 'name',
            email: 'email',
            phone: 'phone_number'
        };

        const fieldKey = fieldMap[fieldType];
        if (!fieldKey) {
            return Promise.resolve({ success: false, message: 'Field tidak valid' });
        }

        // Send AJAX request to update profile
        return fetch('/profile/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                [fieldKey]: fieldValue
            })
        }).then(async response => {
            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                return {
                    success: false,
                    message: data.message || 'Gagal memperbarui profil'
                };
            }

            return data;
        });
    }
});
