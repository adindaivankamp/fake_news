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

        function closeProfilePopup(popup) {
            if (!popup) return;
            popup.setAttribute('hidden', '');
            document.querySelectorAll('[data-profile-toggle="' + popup.id + '"]').forEach(btn => {
                btn.setAttribute('aria-expanded', 'false');
                syncNavbarActiveState(btn, false);
            });
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
                } else {
                    closeProfilePopup(popup);
                }
                return;
            }

            const closeButton = e.target.closest('[data-profile-close]');
            if (closeButton) {
                e.preventDefault();
                e.stopPropagation();
                const popup = closeButton.closest('.lh-profile-popup');
                closeProfilePopup(popup);
                return;
            }

            // If click is outside any open popup, close them
            const openPopups = document.querySelectorAll('.lh-profile-popup:not([hidden])');
            if (openPopups.length) {
                let clickedInside = false;
                openPopups.forEach(popup => { if (popup.contains(e.target)) clickedInside = true; });
                if (!clickedInside) {
                    openPopups.forEach(popup => {
                        closeProfilePopup(popup);
                    });
                }
            }
        });

        // Escape key closes popups and modals
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.lh-profile-popup:not([hidden])').forEach(popup => {
                    closeProfilePopup(popup);
                });
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
                    if (popup) closeProfilePopup(popup);

                    // Attempt an AJAX logout then redirect to the login page on success.
                    const token = logoutForm.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.content || '';
                    fetch(logoutForm.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({})
                    }).then(function (resp) {
                        if (resp.ok) {
                            window.location.href = logoutForm.dataset.redirect || '/login';
                        } else {
                            try { logoutForm.submit(); } catch (e) { window.location.href = logoutForm.dataset.redirect || '/login'; }
                        }
                    }).catch(function () {
                        try { logoutForm.submit(); } catch (e) { window.location.href = logoutForm.dataset.redirect || '/login'; }
                    });
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

        function isFieldEditable(fieldElement) {
            return fieldElement && fieldElement.dataset.editable === 'true';
        }

        function openEditModal(fieldElement) {
            if (!editModal) return;
            if (!isFieldEditable(fieldElement)) return;
            const popupRoot = fieldElement.closest('.lh-profile-popup');
            if (popupRoot) {
                closeProfilePopup(popupRoot);
            }

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

        editableFields.forEach(field => {
            field.addEventListener('click', function (e) {
                // Open editor when the profile popup is visible so users can add missing data
                const popupRoot = this.closest('.lh-profile-popup');
                if (popupRoot && !popupRoot.hasAttribute('hidden') && isFieldEditable(this)) {
                    e.stopPropagation();
                    openEditModal(this);
                }
            });
        });
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

        /* Mobile navbar moving indicator: creates a single dot that slides under the active icon */
        function getCenterLeft(btn, container) {
            if (!btn || !container) return 0;
            const btnRect = btn.getBoundingClientRect();
            const contRect = container.getBoundingClientRect();
            return (btnRect.left - contRect.left) + (btnRect.width / 2);
        }

        function moveIndicator(indicator, btn, container, animate = true) {
            if (!indicator || !btn || !container) return;
            const center = getCenterLeft(btn, container);
            const offset = Math.round(center - (indicator.offsetWidth / 2));
            // set CSS var to move via transform (GPU accelerated)
            if (!animate) {
                // temporarily disable transition
                indicator.style.transition = 'none';
                indicator.style.setProperty('--indicator-x', offset + 'px');
                // force layout
                // eslint-disable-next-line no-unused-expressions
                indicator.offsetHeight;
                indicator.style.transition = '';
                return;
            }
            indicator.style.setProperty('--indicator-x', offset + 'px');
        }

        function initMobileNavIndicator() {
            if (!mobileNavbarQuery.matches) return;
            
            // wait for fonts to load using document.fonts.ready, with fallback delay
            var initializeIndicator = function() {
                document.querySelectorAll('.lh-nav-icons--mobile, .lh-nav-icons').forEach(container => {
                    if (!container) return;
                    container.style.position = container.style.position || 'relative';
                    let indicator = container.querySelector('.lh-nav-indicator');
                    if (!indicator) {
                        indicator = document.createElement('div');
                        indicator.className = 'lh-nav-indicator';
                        container.appendChild(indicator);
                    }
                    // first, find and mark the active button based on aria-current or .lh-nav-btn--active
                    let active = container.querySelector('.lh-nav-btn--active');
                    if (!active) {
                        active = container.querySelector('.lh-nav-btn[aria-current="page"]');
                        if (active) {
                            active.classList.add('lh-nav-btn--active');
                        }
                    }
                    // fallback to first button if none marked
                    if (!active) {
                        active = container.querySelector('.lh-nav-btn');
                    }
                    if (active) moveIndicator(indicator, active, container, false);
                });
            };
            
            // wait for fonts to be ready, then initialize
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(initializeIndicator);
            } else {
                // fallback for browsers without document.fonts
                setTimeout(initializeIndicator, 300);
            }
        }

        // Move indicator on nav button click (delegated)
        document.addEventListener('click', function (e) {
            if (!mobileNavbarQuery.matches) return;
            const navBtn = e.target.closest('.lh-nav-btn');
            if (!navBtn) return;
            const container = navBtn.closest('.lh-nav-icons--mobile') || navBtn.closest('.lh-nav-icons');
            if (!container) return;
            const indicator = container.querySelector('.lh-nav-indicator');
            // Update active class cleanly
            container.querySelectorAll('.lh-nav-btn').forEach(b => {
                b.classList.remove('lh-nav-btn--active');
                b.removeAttribute('aria-current');
            });
            navBtn.classList.add('lh-nav-btn--active');
            navBtn.setAttribute('aria-current', 'page');
            if (indicator) moveIndicator(indicator, navBtn, container, true);
        });

        // Reposition indicators on resize / orientation change
        let _resizeTimer = null;
        window.addEventListener('resize', function () {
            if (_resizeTimer) clearTimeout(_resizeTimer);
            _resizeTimer = setTimeout(function () {
                document.querySelectorAll('.lh-nav-icons--mobile, .lh-nav-icons').forEach(container => {
                    const indicator = container.querySelector('.lh-nav-indicator');
                    const active = container.querySelector('.lh-nav-btn--active') || container.querySelector('.lh-nav-btn[aria-current="page"]');
                    if (indicator && active) moveIndicator(indicator, active, container, true);
                });
            }, 120);
        });
        
        // Also listen for orientation change and font loading
        window.addEventListener('orientationchange', function() {
            setTimeout(function() {
                document.querySelectorAll('.lh-nav-icons--mobile, .lh-nav-icons').forEach(container => {
                    const indicator = container.querySelector('.lh-nav-indicator');
                    const active = container.querySelector('.lh-nav-btn--active') || container.querySelector('.lh-nav-btn[aria-current="page"]');
                    if (indicator && active) moveIndicator(indicator, active, container, false);
                });
            }, 100);
        });

        // initialize indicator after initial active restore
        initMobileNavIndicator();

        function updateFieldOnServer(fieldType, fieldValue) {
            const fieldMap = { name: 'name', email: 'email', phone: 'phone_number' };
            const fieldKey = fieldMap[fieldType]; if (!fieldKey) return Promise.resolve({ success: false, message: 'Field tidak valid' });
            return fetch('/profile/update', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }, body: JSON.stringify({ [fieldKey]: fieldValue }) }).then(async response => { const data = await response.json().catch(() => ({})); if (!response.ok) return { success: false, message: data.message || 'Gagal memperbarui profil' }; return data; });
        }
    } catch (err) {
        console.error('profile-popup.js error', err);
    }
});
