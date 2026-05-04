/**
 * Lensa Hoax - Pencarian JavaScript
 * Handles form submission, file uploads, and result display
 */

document.addEventListener('DOMContentLoaded', function () {
    // Element references
    const inputInformasi = document.getElementById('inputInformasi');
    const fileInput = document.getElementById('fileInput');
    const btnUnggah = document.getElementById('btnUnggah');
    const btnTelusuri = document.getElementById('btnTelusuri');
    const hasilPenelusuran = document.getElementById('hasilPenelusuran');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const searchParams = new URLSearchParams(window.location.search);
    const prefilledInformasi = (searchParams.get('informasi') || searchParams.get('q') || '').trim();

    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // State: store uploaded file for later processing
    let uploadedFile = null;

    // ==================== Event Listeners ====================

    /**
     * Handle upload button click
     */
    btnUnggah.addEventListener('click', function () {
        clearImagePreview(); // Clear previous preview if any
        fileInput.click();
    });

    /**
     * Handle file selection
     */
    fileInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            showError('File harus berupa gambar (JPEG, PNG, GIF)');
            fileInput.value = ''; // Reset input
            return;
        }

        // Validate file size (5MB max)
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            showError('Ukuran file maksimal 5MB');
            fileInput.value = '';
            return;
        }

        // Store file and show preview
        uploadedFile = file;
        showImagePreview(file);
    });

    /**
     * Handle search button click
     */
    btnTelusuri.addEventListener('click', function () {
        // If file is uploaded, search by image
        if (uploadedFile) {
            performImageSearch(uploadedFile);
            return;
        }

        // Otherwise, search by text
        const informasi = inputInformasi.value.trim();

        if (!informasi) {
            showError('Silakan masukkan informasi terlebih dahulu');
            return;
        }

        if (informasi.length < 10) {
            showError('Informasi minimal harus 10 karakter');
            return;
        }

        searchText(informasi);
    });

    /**
     * Allow Enter key to search (Ctrl+Enter or Cmd+Enter)
     */
    inputInformasi.addEventListener('keydown', function (event) {
        if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
            event.preventDefault();
            btnTelusuri.click();
        }
    });

    /**
     * Clear image preview when user focuses on textarea
     */
    inputInformasi.addEventListener('focus', function () {
        if (uploadedFile) {
            clearImagePreview();
        }
    });

    // ==================== Functions ====================

    /**
     * Show image preview in input panel
     */
    function showImagePreview(file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const imageDataUrl = e.target.result;
            imagePreview.src = imageDataUrl;
            imagePreviewContainer.style.display = 'block';
            inputInformasi.style.display = 'none'; // Hide textarea when image is shown
        };
        reader.readAsDataURL(file);
    }

    /**
     * Clear image preview and show textarea again
     */
    function clearImagePreview() {
        imagePreviewContainer.style.display = 'none';
        imagePreview.src = '';
        inputInformasi.style.display = 'block';
        uploadedFile = null;
        fileInput.value = '';
    }

    /**
     * Search by text
     */
    function searchText(informasi) {
        showLoading();
        clearImagePreview(); // Clear image preview when searching by text

        fetch('/telusuri', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                informasi: informasi,
            }),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    displayResult(data);
                } else {
                    showError(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                showError('Gagal menghubungi server. Periksa koneksi internet Anda.');
            });
    }

    /**
     * Perform image search
     */
    function performImageSearch(file) {
        showLoading();

        const formData = new FormData();
        formData.append('gambar', file);

        fetch('/telusuri-gambar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Support both 'status' and 'success' response format
                const isSuccess = data.status === 'success' || data.success === true;
                
                if (isSuccess) {
                    // Handle both response formats
                    const resultData = data.data || data;
                    displayResult(resultData);
                    clearImagePreview();
                } else {
                    showError(data.message || 'Gagal mengupload gambar. Silakan coba lagi.');
                    fileInput.value = '';
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                showError('Gagal mengupload gambar. Periksa koneksi internet Anda.');
                fileInput.value = '';
            });
    }

    /**
     * Display loading state
     */
    function showLoading() {
        hasilPenelusuran.innerHTML = `
            <div class="lh-result-loading">
                <div class="lh-spinner"></div>
                <span>Menganalisis informasi...</span>
            </div>
        `;
    }

    /**
     * Display error message
     */
    function showError(message) {
        hasilPenelusuran.innerHTML = `
            <div class="lh-result-empty" style="color: #B71C1C;">
                ⚠️ ${escapeHtml(message)}
            </div>
        `;
    }

    /**
     * Display search result
     */
    function displayResult(data) {
        // Handle image detection response format (with nested 'data' key)
        let verdict = data.verdict || data.indication;
        let confidence = data.confidence || data.confidence_score?.hoax || 50;
        let summary = data.summary;
        let sources = data.sources;

        // Normalize and map verdict label
        const normalizedVerdict = String(verdict || '').toLowerCase();
        const verdictMap = {
            hoax: { label: 'HOAX', className: 'lh-verdict--hoax' },
            valid: { label: 'FAKTA', className: 'lh-verdict--valid' },
            unclear: { label: 'PERLU VERIFIKASI', className: 'lh-verdict--unclear' },
            fakta: { label: 'FAKTA', className: 'lh-verdict--valid' },
        };

        const verdictInfo = verdictMap[normalizedVerdict] || verdictMap.unclear;

        const safeConfidence = Number.isFinite(Number(confidence))
            ? Math.max(0, Math.min(100, Math.round(Number(confidence))))
            : 50;

        let hoaxPercent = safeConfidence;
        if (normalizedVerdict === 'valid') {
            hoaxPercent = 100 - safeConfidence;
        } else if (normalizedVerdict === 'unclear') {
            hoaxPercent = 50;
        }
        const faktaPercent = 100 - hoaxPercent;

        // Build sources HTML
        let sourcesHtml = '<li>Belum ada sumber yang terdeteksi.</li>';
        if (Array.isArray(sources) && sources.length > 0) {
            sourcesHtml = sources.map(source => {
                const safeTitle = escapeHtml(source?.title || 'Sumber tanpa judul');
                const safeUrl = escapeHtml(source?.url || '#');
                const hasUrl = Boolean(source?.url);
                const sourceLink = hasUrl
                    ? `<a href="${safeUrl}" target="_blank" rel="noopener noreferrer">${safeUrl}</a>`
                    : '-';

                return `<li>${safeTitle}. Diakses dari ${sourceLink}</li>`;
            }).join('');
        }

        const safeSummary = escapeHtml(summary || 'Belum ada penjelasan detail untuk hasil pencarian ini.');

        hasilPenelusuran.innerHTML = `
            <article class="lh-result-view">
                <div class="lh-result-view__head">
                    <p class="lh-result-view__lead">Informasi tersebut terindikasi :</p>
                    <h3 class="lh-result-view__verdict ${verdictInfo.className}">${verdictInfo.label}</h3>
                    <p class="lh-result-view__subtitle">Dengan presentase hoax sebagai berikut :</p>
                </div>

                <div class="lh-result-meter">
                    <div class="lh-result-meter__track" aria-label="Visualisasi persentase hoax dan fakta">
                        <div class="lh-result-meter__hoax" style="width: ${hoaxPercent}%;"></div>
                        <div class="lh-result-meter__fakta" style="width: ${faktaPercent}%;"></div>
                        <div class="lh-result-meter__badge">${hoaxPercent}% | ${faktaPercent}%</div>
                    </div>
                    <div class="lh-result-meter__labels">
                        <span>Hoax</span>
                        <span>Fakta</span>
                    </div>
                </div>

                <section class="lh-result-view__explain">
                    <p class="lh-result-view__explain-title">Penjelasan Hasil:</p>
                    <p class="lh-result-view__explain-text">${safeSummary}</p>
                </section>

                <hr class="lh-result-view__divider">

                <section class="lh-result-view__sources">
                    <p>Berikut tautan sumber terkait verifikasi informasi anda :</p>
                    <ul>${sourcesHtml}</ul>
                </section>

                <hr class="lh-result-view__divider">

                <section class="lh-result-view__footer">
                    <p>
                        Informasi ini telah ditelusuri beberapa orang dengan hasil yang sama sebelumnya.
                        Ingin memulai penelusuran kembali untuk informasi yang lebih baru?
                    </p>
                    <button class="lh-btn lh-btn--search lh-result-action" id="btnTelusuriUlang" type="button">
                        <iconify-icon icon="ic:outline-search" width="22" height="22"></iconify-icon>
                        Telusuri
                    </button>
                </section>
            </article>
        `;

        const btnTelusuriUlang = document.getElementById('btnTelusuriUlang');
        if (btnTelusuriUlang) {
            btnTelusuriUlang.addEventListener('click', function () {
                inputInformasi.focus();
                inputInformasi.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ==================== Clear empty state on first focus ====================
    inputInformasi.addEventListener('focus', function () {
        if (hasilPenelusuran.innerHTML.includes('lh-result-empty')) {
            hasilPenelusuran.innerHTML = `
                <div class="lh-result-empty">
                    Masukkan informasi atau upload gambar untuk mulai penelusuran
                </div>
            `;
        }
    });

    // Initialize with empty state
    hasilPenelusuran.innerHTML = `
        <div class="lh-result-empty">
            Masukkan informasi atau upload gambar untuk mulai penelusuran
        </div>
    `;

    if (prefilledInformasi) {
        inputInformasi.value = prefilledInformasi;
        hasilPenelusuran.innerHTML = `
            <div class="lh-result-empty">
                Informasi dari pencarian populer sudah terisi. Klik Telusuri untuk memulai verifikasi.
            </div>
        `;
    }
});
