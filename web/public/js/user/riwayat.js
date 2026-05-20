// Data Riwayat Manager (Versi Database Live)
class RiwayatManager {
    constructor() {
        this.riwayatData = window.realRiwayatData || [];
        console.log('RiwayatManager initialized with data:', this.riwayatData);
        this.render();
        this.attachEventListeners();
    }

    getData() {
        return this.riwayatData;
    }

    formatDateDisplay(date) {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const d = new Date(date);
        const dayName = days[d.getDay()];
        const day = d.getDate();
        const month = months[d.getMonth()];
        const year = d.getFullYear();
        
        return `${dayName}, ${day} ${month} ${year}`;
    }

    render() {
        const container = document.getElementById('riwayatContainer');
        const emptyState = document.getElementById('emptyState');
        const data = this.getData();

        if (data.length === 0) {
            container.style.display = 'none';
            emptyState.style.display = 'flex';
            return;
        }

        container.style.display = 'flex';
        emptyState.style.display = 'none';
        container.innerHTML = data.map(item => this.renderItem(item)).join('');
        this.attachItemEventListeners();
    }

    renderItem(item) {
        const hoaxPercent = item.status === 'fakta' ? 0 : (Number(item.confidence) || 0);
        const queryPreview = this.truncateText(item.query, 88);
        const resultLabel = item.status === 'benar' ? 'fakta' : item.status === 'palsu' ? 'Palsu' : 'Hoax';
        
        return `
            <div class="riwayat-group">
                <div class="riwayat-group-date">${this.formatDateDisplay(item.date)}</div>
                <div class="riwayat-item-container" data-id="${item.id}">
                    <div class="riwayat-section riwayat-query-box">
                        <button type="button" class="riwayat-section-toggle" data-section-toggle="query-${item.id}" aria-expanded="false">
                            <span class="riwayat-section-head">
                                <span class="riwayat-section-kicker">Informasi Berita</span>
                                <span class="riwayat-box-title">Klik untuk melihat detail berita</span>
                            </span>
                            <span class="riwayat-section-toggle-meta">
                                <span class="riwayat-section-preview">${this.escapeHtml(queryPreview)}</span>
                                <iconify-icon icon="mdi:chevron-down" width="18" height="18" class="riwayat-section-chevron"></iconify-icon>
                            </span>
                        </button>
                        <div class="riwayat-section-panel" id="query-${item.id}" hidden>
                            <p class="riwayat-query">${this.escapeHtml(item.query)}</p>
                        </div>
                    </div>

                    <div class="riwayat-section riwayat-results-box">
                        <button type="button" class="riwayat-section-toggle" data-section-toggle="result-${item.id}" aria-expanded="false">
                            <span class="riwayat-section-head">
                                <span class="riwayat-section-kicker">Hasil Pencarian</span>
                                <span class="riwayat-box-title">Klik untuk lihat ringkasan dan tautan</span>
                            </span>
                            <span class="riwayat-section-toggle-meta">
                                <span class="riwayat-result-chip ${item.status}">${hoaxPercent}% ${resultLabel}</span>
                                <iconify-icon icon="mdi:chevron-down" width="18" height="18" class="riwayat-section-chevron"></iconify-icon>
                            </span>
                        </button>
                        <div class="riwayat-section-panel" id="result-${item.id}" hidden>
                            <div class="riwayat-result-content">
                                <div class="riwayat-result-percent ${item.status}">
                                    <span class="riwayat-result-percent-value">${hoaxPercent}%</span>
                                    <span class="riwayat-result-percent-label">${resultLabel}</span>
                                </div>
                                <p class="riwayat-result-summary">${this.convertUrlsToLinks(item.description)}</p>
                                <p class="riwayat-result-footnote">Tautan pada teks dapat diklik langsung dan dibuka di tab baru.</p>
                            </div>
                        </div>
                    </div>

                    <div class="riwayat-item-actions">
                        <button class="riwayat-action-btn umpan-balik" title="Kirim umpan balik" data-id="${item.id}">
                            <iconify-icon icon="mdi:comment" width="16" height="16"></iconify-icon>
                        </button>
                        <button class="riwayat-action-btn hapus" title="Hapus dari riwayat" data-id="${item.id}">
                            <iconify-icon icon="mdi:trash" width="16" height="16"></iconify-icon>
                        </button>
       
                    </div>
                </div>
            </div>
        `;
    }

    escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }

    truncateText(text, maxLength) {
        if (typeof text !== 'string') return '';
        const normalized = text.replace(/\s+/g, ' ').trim();
        return normalized.length <= maxLength ? normalized : `${normalized.slice(0, maxLength).trimEnd()}...`;
    }

    convertUrlsToLinks(text) {
        if (typeof text !== 'string') return this.escapeHtml(String(text));
        let escaped = this.escapeHtml(text);
        const urlRegex = /https?:\/\/[^\s<>"]+/gi;
        return escaped.replace(urlRegex, (url) => {
            let cleanUrl = url;
            let suffix = '';
            while (cleanUrl && /[.,;:!?\)]$/.test(cleanUrl)) {
                suffix = cleanUrl.slice(-1) + suffix;
                cleanUrl = cleanUrl.slice(0, -1);
            }
            const href = cleanUrl.replace(/&amp;/g, '&');
            return `<a href="${href}" target="_blank" rel="noopener noreferrer" style="color: #c41e3a; text-decoration: underline; font-weight: 500; cursor: pointer;">${cleanUrl}</a>${suffix}`;
        });
    }

    // Fungsi Hapus (Soft Delete ke Server)
    deleteItem(id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        // Tampilkan loading/disable sebentar kalau perlu
        fetch(`/riwayat-saya/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hapus item dari array memory JS
                this.riwayatData = this.riwayatData.filter(item => item.id !== parseInt(id));
                // Render ulang DOM
                this.render();
            } else {
                alert(data.message || 'Gagal menghapus riwayat.');
            }
        })
        .catch(err => {
            console.error('Delete error:', err);
            alert('Terjadi kesalahan saat menghapus riwayat.');
        });
    }


    // Fungsi Hapus Semua Riwayat (Mass Soft Delete ke Server)
    deleteAll() {
        if (confirm('Apakah Anda yakin ingin menghapus seluruh riwayat pencarian Anda? Tindakan ini tidak dapat dibatalkan.')) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            
            fetch('/riwayat-saya', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Kosongkan array data di memory browser
                    this.riwayatData = [];
                    // Render ulang tampilan (akan otomatis memicu Empty State)
                    this.render();
                } else {
                    alert(data.message || 'Gagal menghapus semua riwayat.');
                }
            })
            .catch(err => {
                console.error('Delete all error:', err);
                alert('Terjadi kesalahan saat menghapus semua riwayat.');
            });
        }
    }

    attachEventListeners() {
        // 🔥 PERBAIKAN: Hubungkan kembali ke fungsi deleteAll() tanpa alert izin admin
        const hapusSemuaBtn = document.getElementById('hapusSemuaBtn');
        if (hapusSemuaBtn) {
            hapusSemuaBtn.addEventListener('click', () => this.deleteAll());
        }
    }

    attachItemEventListeners() {
        // Accordion Toggle
        document.querySelectorAll('.riwayat-section-toggle').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const button = e.currentTarget;
                const targetId = button.dataset.sectionToggle;
                const panel = document.getElementById(targetId);
                if (!panel) return;

                const isExpanded = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', String(!isExpanded));
                panel.hidden = isExpanded;
                button.closest('.riwayat-section')?.classList.toggle('is-open', !isExpanded);
                const icon = button.querySelector('.riwayat-section-chevron');
                if (icon) icon.setAttribute('icon', isExpanded ? 'mdi:chevron-down' : 'mdi:chevron-up');
            });
        });

        // Tombol Feedback
        document.querySelectorAll('.riwayat-action-btn.umpan-balik').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                const data = this.getData();
                const itemData = data.find(d => d.id === parseInt(id));
                if (itemData) this.showFeedbackForm(itemData);
            });
        });

        // 🔥 KEMBALI: Tombol Hapus 
        document.querySelectorAll('.riwayat-action-btn.hapus').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                if (confirm('Hapus item ini dari riwayat Anda?')) {
                    this.deleteItem(id);
                }
            });
        });

    }
    
    // Modal Feedback
    showFeedbackForm(item) {
        const feedbackHtml = `
            <div class="riwayat-feedback-modal" id="feedbackModal">
                <div class="riwayat-feedback-content">
                    <div class="riwayat-feedback-header">
                        <h3>Kirim Umpan Balik</h3>
                        <button class="riwayat-feedback-close" id="closeFeedback">
                            <iconify-icon icon="mdi:close" width="20" height="20"></iconify-icon>
                        </button>
                    </div>
                    <div class="riwayat-feedback-body">
                        <p class="riwayat-feedback-item">${this.escapeHtml(item.query)}</p>
                        <textarea class="riwayat-feedback-textarea" id="feedbackText" placeholder="Tulis umpan balik Anda di sini..." rows="5"></textarea>
                        <p class="riwayat-feedback-hint">Umpan balik Anda membantu kami meningkatkan kualitas verifikasi.</p>
                        <p id="feedbackStatus" style="color: #c41e3a; font-size: 13px; font-weight:bold; margin-top:5px;"></p>
                    </div>
                    <div class="riwayat-feedback-footer">
                        <button class="riwayat-feedback-cancel" id="cancelFeedback">Batal</button>
                        <button class="riwayat-feedback-submit" id="submitFeedback">
                            <iconify-icon icon="mdi:send" width="16" height="16"></iconify-icon>
                            Kirim Umpan Balik
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        const existing = document.getElementById('feedbackModal');
        if (existing) existing.remove();
        
        document.body.insertAdjacentHTML('beforeend', feedbackHtml);
        
        const modal = document.getElementById('feedbackModal');
        const closeBtn = document.getElementById('closeFeedback');
        const cancelBtn = document.getElementById('cancelFeedback');
        const submitBtn = document.getElementById('submitFeedback');
        const statusEl = document.getElementById('feedbackStatus');
        
        const closeModal = () => modal.remove();
        
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        
        submitBtn.addEventListener('click', () => {
            const text = document.getElementById('feedbackText').value.trim();
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            if (!text) {
                statusEl.textContent = 'Silakan tulis umpan balik Anda terlebih dahulu.';
                return;
            }

            submitBtn.disabled = true;
            statusEl.textContent = 'Mengirim...';
            
            fetch('/feedback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    feedback: text,
                    request_id: item.id
                }),
            })
            .then(resp => resp.json())
            .then(resdata => {
                statusEl.textContent = 'Terima kasih! Umpan balik berhasil dikirim.';
                setTimeout(() => { closeModal(); }, 1200);
            })
            .catch(err => {
                statusEl.textContent = 'Terjadi kesalahan saat mengirim umpan balik.';
                submitBtn.disabled = false;
            });
        });
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        }, { once: true });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.riwayatManager = new RiwayatManager();
});