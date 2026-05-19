// Consolidated handler for profile popup and edit modal
document.addEventListener('DOMContentLoaded', function () {
    try {
        const mobileNavbarQuery = window.matchMedia('(max-width: 560px)');

        function getNavbarRoot(toggle) {
            return toggle ? toggle.closest('.lh-navbar') : null;
        }

        function getInitialActiveButtons(navbar) {
            if (!navbar) return [];
            return Array.from(navbar.querySelectorAll('.lh-nav-btn[data-navbar-active-original="true"]'));
        }

        function markInitialMobileActiveState() {
            if (!mobileNavbarQuery.matches) return;
            document.querySelectorAll('.lh-navbar').forEach(navbar => {
                navbar.querySelectorAll('.lh-nav-btn[aria-current="page"]').forEach(btn => {
                    btn.dataset.navbarActiveOriginal = 'true';
                });
            });
        }

        function clearMobileActiveState(navbar) {
            if (!navbar) return;
            navbar.querySelectorAll('.lh-nav-btn').forEach(btn => btn.classList.remove('lh-nav-btn--active'));
        }

        function restoreMobileActiveState(navbar) {
            if (!navbar) return;
            navbar.querySelectorAll('.lh-nav-btn').forEach(btn => {
                if (btn.dataset.navbarActiveOriginal === 'true') {
                    btn.classList.add('lh-nav-btn--active');
                    btn.setAttribute('aria-current', 'page');
                } else {
                    btn.removeAttribute('aria-current');
                }
            });
        }

        function syncNavbarActiveState(toggle, isOpen) {
            const navbar = getNavbarRoot(toggle);
            if (!navbar || !mobileNavbarQuery.matches) return;

            clearMobileActiveState(navbar);

            if (isOpen) {
                toggle.classList.add('lh-nav-btn--active');
                toggle.setAttribute('aria-current', 'page');
                return;
            }

            toggle.removeAttribute('aria-current');
            restoreMobileActiveState(navbar);
        }

        // Delegated toggle handler
        document.addEventListener('click', function (e) {
            const toggle = e.target.closest('.js-profile-toggle');
            if (toggle) {
                e.preventDefault();
                // Prevent other click handlers on the page from running and closing the popup immediately
                e.stopPropagation();
                if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
                const targetId = toggle.dataset.profileToggle;
                const popup = document.getElementById(targetId);
                if (!popup) return;
                const isHidden = popup.hasAttribute('hidden');
                if (isHidden) {
                    // Show popup
                    popup.removeAttribute('hidden');
                    toggle.setAttribute('aria-expanded', 'true');
                    syncNavbarActiveState(toggle, true);
                    // mark all toggles for this popup
                    document.querySelectorAll('[data-profile-toggle="' + targetId + '"]').forEach(btn => btn.setAttribute('aria-expanded', 'true'));
                    // add overlay
                    if (!document.getElementById('profile-popup-overlay')) {
                        const overlay = document.createElement('div');
                        overlay.id = 'profile-popup-overlay';
                        overlay.className = 'lh-profile-popup-overlay';
                        // Ensure overlay covers viewport but stays behind the popup
                        overlay.style.position = 'fixed';
                        overlay.style.top = '0';
                        overlay.style.left = '0';
                        overlay.style.width = '100%';
                        overlay.style.height = '100%';
                        overlay.style.background = 'transparent';
                        overlay.style.zIndex = '10000';
                        overlay.addEventListener('click', function () {
                            popup.setAttribute('hidden', '');
                            document.querySelectorAll('[data-profile-toggle="' + targetId + '"]').forEach(btn => btn.setAttribute('aria-expanded', 'false'));
                            overlay.remove();
                        });
                        document.body.appendChild(overlay);
                        // Position popup near toggle and bring above overlay (use fixed to avoid stacking context issues)
                        try {
                            const rect = toggle.getBoundingClientRect();
                            popup.style.position = 'fixed';
                            // Prefer placing below the toggle; adjust if near bottom
                            const top = Math.min(window.innerHeight - 16, rect.bottom + 8);
                            const left = Math.min(window.innerWidth - 16, rect.left);
                            popup.style.top = (top) + 'px';
                            popup.style.left = (left) + 'px';
                            popup.style.zIndex = '10001';
                            const card = popup.querySelector('.lh-profile-popup__card');
                            if (card) {
                                card.style.zIndex = '10002';
                                card.style.position = 'relative';
                            }
                        } catch (e) {
                            // ignore
                        }
                    }
                } else {
                    popup.setAttribute('hidden', '');
                    document.querySelectorAll('[data-profile-toggle="' + targetId + '"]').forEach(btn => btn.setAttribute('aria-expanded', 'false'));
                    syncNavbarActiveState(toggle, false);
                    const ov = document.getElementById('profile-popup-overlay'); if (ov) ov.remove();
                }
                return;
            }

            // If click is outside any open popup, close them
            const openPopups = document.querySelectorAll('.lh-profile-popup:not([hidden])');
            if (openPopups.length) {
                let clickedInside = false;
                openPopups.forEach(popup => { if (popup.contains(e.target)) clickedInside = true; });
                if (!clickedInside) {
                    openPopups.forEach(popup => {
                        popup.setAttribute('hidden', '');
                        document.querySelectorAll('[data-profile-toggle="' + popup.id + '"]').forEach(btn => {
                            btn.setAttribute('aria-expanded', 'false');
                            syncNavbarActiveState(btn, false);
                        });
                    });
                    const ov = document.getElementById('profile-popup-overlay'); if (ov) ov.remove();
                }
            }
        });

        // Escape key closes popups and modals
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.lh-profile-popup:not([hidden])').forEach(popup => {
                    popup.setAttribute('hidden', '');
                    document.querySelectorAll('[data-profile-toggle="' + popup.id + '"]').forEach(btn => {
                        btn.setAttribute('aria-expanded', 'false');
                        syncNavbarActiveState(btn, false);
                    });
                });
                const ov = document.getElementById('profile-popup-overlay'); if (ov) ov.remove();
                // close edit modal if open
                const editModal = document.getElementById('field-edit-modal'); if (editModal && !editModal.hasAttribute('hidden')) editModal.setAttribute('hidden', '');
            }
        });

        // Instead of intercepting form submit (which may be blocked), allow native submit.
        // But ensure clicking the logout button closes the popup so user sees the action.
        const logoutForm = document.getElementById('lh-logout-form');
        if (logoutForm) {
            const logoutBtn = logoutForm.querySelector('button[type="submit"]');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function (ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    // close popup overlay so submit can proceed visually
                    const popup = logoutBtn.closest('.lh-profile-popup');
                    if (popup) {
                        popup.setAttribute('hidden', '');
                        document.querySelectorAll('[data-profile-toggle="' + popup.id + '"]').forEach(btn => btn.setAttribute('aria-expanded', 'false'));
                    }
                    const ov = document.getElementById('profile-popup-overlay'); if (ov) ov.remove();
                    // Programmatically submit the form to avoid other handlers blocking the native submit
                    try {
                        // if the form has an onsubmit handler that prevents submission, use a cloned form
                        if (typeof logoutForm.submit === 'function') {
                            // small delay to allow UI update
                            setTimeout(function () { logoutForm.submit(); }, 50);
                        } else {
                            // fallback: create a new form and submit
                            const f = document.createElement('form');
                            f.method = 'POST';
                            f.action = logoutForm.action;
                            const token = logoutForm.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.content || '';
                            const inp = document.createElement('input');
                            inp.type = 'hidden'; inp.name = '_token'; inp.value = token;
                            f.appendChild(inp);
                            document.body.appendChild(f);
                            setTimeout(function () { f.submit(); }, 50);
                        }
                    } catch (err) {
                        // As a last resort, redirect to login (may not log out server-side)
                        window.location.href = logoutForm.dataset.redirect || '/login';
                    }
                });
            }
        }

        // Edit modal handlers (only initialize if elements exist)
        const profilePopup = document.getElementById('user-profile-popup');
        const editableFields = document.querySelectorAll('.js-editable-field');
        const editModal = document.getElementById('field-edit-modal');
        const editForm = document.getElementById('field-edit-form');
        const editInput = document.getElementById('field-edit-input');
        const editTypeInput = document.getElementById('field-edit-type');
        const editTitle = document.getElementById('field-edit-title');
        const closeModalBtn = document.querySelector('.lh-field-edit-modal__close');
        const cancelBtn = document.getElementById('field-edit-cancel');

        function getFieldLabel(fieldType) {
            const labels = { name: 'Nama Pengguna', email: 'Email', phone: 'Nomor WhatsApp' };
            return labels[fieldType] || 'Informasi';
        }

        function openEditModal(fieldElement) {
            if (!editModal) return;
            const fieldType = fieldElement.getAttribute('data-field');
            const fieldSpan = fieldElement.querySelector('span');
            const currentValue = fieldSpan?.textContent?.trim();
            editTitle.textContent = 'Edit ' + getFieldLabel(fieldType);
            editTypeInput.value = fieldType;
            if (fieldType === 'email') { editInput.type = 'email'; editInput.placeholder = 'Masukkan email'; }
            else if (fieldType === 'phone') { editInput.type = 'tel'; editInput.placeholder = 'Masukkan nomor WhatsApp'; }
            else { editInput.type = 'text'; editInput.placeholder = 'Masukkan nama'; }
            if (!currentValue?.includes('Tambahkan') && currentValue !== '-') editInput.value = currentValue; else editInput.value = '';
            editModal.removeAttribute('hidden'); editInput.focus();
        }

        function closeEditModal() { if (editModal) { editModal.setAttribute('hidden', ''); if (editForm) editForm.reset(); } }

        editableFields.forEach(field => { field.addEventListener('click', function () { if (document.getElementById('profile-popup-overlay')) openEditModal(this); }); });
        if (closeModalBtn) closeModalBtn.addEventListener('click', closeEditModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeEditModal);
        if (editModal) editModal.addEventListener('click', function (e) { if (e.target === this || e.target.className === 'lh-field-edit-modal__overlay') closeEditModal(); });
        if (editForm) {
            editForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const fieldType = editTypeInput.value;
                const fieldValue = editInput.value.trim();
                const fieldElement = document.querySelector('.js-editable-field[data-field="' + fieldType + '"]');
                if (!fieldValue) { alert('Nilai tidak boleh kosong'); return; }
                updateFieldOnServer(fieldType, fieldValue).then(data => {
                    if (!data.success) { alert(data.message || 'Gagal memperbarui profil'); return; }
                    if (fieldElement) { const fieldSpan = fieldElement.querySelector('span'); fieldSpan.textContent = fieldValue; }
                    closeEditModal();
                }).catch(() => alert('Gagal memperbarui profil'));
            });
        }

        // Mark and restore the initial active button state for mobile.
        markInitialMobileActiveState();
        document.querySelectorAll('.lh-navbar').forEach(navbar => {
            if (!mobileNavbarQuery.matches) return;
            restoreMobileActiveState(navbar);
        });

        function updateFieldOnServer(fieldType, fieldValue) {
            const fieldMap = { name: 'name', email: 'email', phone: 'phone_number' };
            const fieldKey = fieldMap[fieldType]; if (!fieldKey) return Promise.resolve({ success: false, message: 'Field tidak valid' });
            return fetch('/profile/update', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }, body: JSON.stringify({ [fieldKey]: fieldValue }) }).then(async response => { const data = await response.json().catch(() => ({})); if (!response.ok) return { success: false, message: data.message || 'Gagal memperbarui profil' }; return data; });
        }
    } catch (err) {
        console.error('profile-popup.js error', err);
    }
});
