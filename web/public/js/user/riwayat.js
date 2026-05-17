// Data Riwayat Manager
class RiwayatManager {
    constructor() {
        this.storageKey = 'riwayat_pencarian';
        this.storageVersionKey = 'riwayat_pencarian_version';
        this.currentDummyVersion = 3;
        this.initDummyData();
        this.render();
        this.attachEventListeners();
    }

    countSentences(text) {
        if (typeof text !== 'string') {
            return 0;
        }

        return text
            .split(/[.!?]+/)
            .map(part => part.trim())
            .filter(Boolean)
            .length;
    }

    needsDummyUpgrade(existingData) {
        if (!Array.isArray(existingData) || existingData.length === 0) {
            return true;
        }

        const requiredIds = [1, 2, 3, 4, 5];
        return requiredIds.some(requiredId => {
            const item = existingData.find(entry => entry.id === requiredId);

            if (!item) {
                return true;
            }

            return this.countSentences(item.query) < 3 || this.countSentences(item.description) < 3;
        });
    }

    // Inisialisasi data dummy jika belum ada
    initDummyData() {
        const existing = localStorage.getItem(this.storageKey);
        const existingVersion = Number(localStorage.getItem(this.storageVersionKey) || 0);
        
        const dummyData = [
                {
                    id: 1,
                    query: 'Penggerebekan markas judi online internasional di kawasan Hayam Wuruk, Jakarta Barat, membuka dugaan pergeseran basis operasi kejahatan transnasional ke Indonesia. Interpol Indonesia menyebut jaringan yang sebelumnya banyak beroperasi di Myanmar, Kamboja, Laos, dan Vietnam mulai memindahkan peladen (server) serta aktivitasnya ke sejumlah kota di Indonesia. Operasi ini menunjukkan eskalasi upaya penegakan hukum dalam menangani sindikat perjudian online yang semakin canggih dan terorganisir.',
                    status: 'hoax',
                    description: 'Investigasi mendalam menunjukkan bahwa informasi ini mengandung elemen yang telah dilebih-lebihkan dan diputarbalikkan dari fakta sebenarnya. Verifikasi dari lembaga terkait mengungkapkan bahwa beberapa detail operasi tidak sesuai dengan laporan resmi yang dirilis. Berdasarkan fakta-fakta yang ada, klaim ini diklasifikasikan sebagai informasi yang menyesatkan dengan tingkat hoaks mencapai 98%. Sumber: https://www.delik.com/ | https://www.kompas.com/ | https://www.tempo.co/',
                    sources: ['Delik.com', 'Kompas.com', 'Tempo.co'],
                    date: new Date(2025, 5, 7),
                    confidence: 98
                },
                {
                    id: 2,
                    query: 'Air Terjun Curug Awur Awuran kembali terbuka untuk wisatawan setelah ditutup selama tiga bulan akibat perbaikan infrastruktur. Pemerintah lokal mengumumkan bahwa semua fasilitas telah diperbaharui dengan standar keselamatan internasional. Diharapkan destinasi wisata ini akan menarik kembali jutaan pengunjung dari berbagai wilayah.',
                    status: 'hoax',
                    description: 'Penelusuran lebih lanjut dari verifikasi independen menemukan bahwa informasi tentang pembukaan kembali destinasi wisata ini belum dikonfirmasi oleh otoritas resmi. Data yang tersedia menunjukkan inkonsistensi dengan jadwal pemeliharaan yang dijadwalkan sebelumnya. Hasil analisis menyatakan bahwa informasi ini memiliki indikasi tingkat kepercayaan rendah dengan estimasi hoaks 85%. Sumber: https://www.liputan6.com/ | https://www.cnnindonesia.com/',
                    sources: ['Liputan6.com', 'CNN Indonesia'],
                    date: new Date(2025, 5, 7),
                    confidence: 85
                },
                {
                    id: 3,
                    query: 'Presiden mengumumkan program liburan panjang selama dua bulan untuk seluruh pegawai negeri sipil sebagai bagian dari kebijakan kesejahteraan. Pengumuman ini menyebutkan bahwa program akan dimulai pada kuartal ketiga dan akan meningkatkan produktivitas karyawan. Beberapa media terkemuka sudah meliput berita ini dengan antusiasme yang tinggi.',
                    status: 'palsu',
                    description: 'Verifikasi dari kantor kepresidenan menunjukkan bahwa pengumuman tersebut merupakan hasil manipulasi dari pernyataan asli presiden. Pernyataan sebenarnya hanya menyebutkan program peninjauan kembali kebijakan cuti, bukan liburan panjang dua bulan. Hasil investigasi jurnalistik mengklasifikasikan informasi ini sebagai palsu dengan persentase ketidakakuratan mencapai 75%. Sumber: https://www.kompas.com/ | https://www.detik.com/',
                    sources: ['Kompas.com', 'Detik.com'],
                    date: new Date(2025, 5, 6),
                    confidence: 75
                },
                {
                    id: 4,
                    query: 'Mengonsumsi vitamin C dalam dosis tinggi telah diklaim dapat mencegah infeksi virus COVID-19 secara efektif. Beberapa sumber online menyatakan bahwa konsumsi suplemen vitamin C dosis tinggi setiap hari dapat meningkatkan kekebalan tubuh hingga 90 persen. Klaim ini telah menyebar luas di berbagai platform media sosial dan grup diskusi kesehatan online.',
                    status: 'hoax',
                    description: 'Penelitian ilmiah yang komprehensif dari lembaga kesehatan internasional membuktikan bahwa vitamin C memang penting untuk sistem imun tubuh, namun tidak ada bukti yang cukup untuk menyatakan bahwa dosis tinggi dapat mencegah COVID-19. Studi klinis menunjukkan bahwa konsumsi vitamin C berlebihan justru dapat menimbulkan efek samping bagi kesehatan ginjal dan pencernaan. Berdasarkan evidensi medis terkini, klaim ini dinyatakan hoaks dengan tingkat keyakinan 92%. Sumber: https://www.who.int/ | https://www.kemkes.go.id/',
                    sources: ['WHO', 'Kemenkes RI'],
                    date: new Date(2025, 5, 5),
                    confidence: 92
                },
                {
                    id: 5,
                    query: 'Penelitian terbaru telah mengidentifikasi beberapa bahan alami yang terbukti secara ilmiah memiliki manfaat nyata untuk perawatan kulit wajah. Beberapa universitas terkemuka di dunia telah melakukan uji klinis terhadap ekstrak tumbuhan tertentu yang menghasilkan hasil positif. Industri kecantikan global mulai mengintegrasikan temuan ini ke dalam produk-produk perawatan kulit modern mereka.',
                    status: 'benar',
                    description: 'Penelitian ilmiah yang dipublikasikan di jurnal-jurnal internasional terkemuka memvalidasi bahwa berbagai bahan alami seperti ekstrak teh hijau, vitamin E, dan minyak zaitun memiliki manfaat nyata untuk kesehatan kulit. Studi klinis menunjukkan peningkatan elastisitas kulit dan pengurangan tanda-tanda penuaan pada pengguna reguler. Data yang tersedia menunjukkan bahwa informasi ini akurat dan dapat dipercaya dengan tingkat kepercayaan 88%. Sumber: https://www.okezone.com/ | https://www.ncbi.nlm.nih.gov/',
                    sources: ['Lifestyle.okezone.com', 'Beauty Journal'],
                    date: new Date(2025, 5, 4),
                    confidence: 88
                }
            ];

        if (!existing) {
            localStorage.setItem(this.storageKey, JSON.stringify(dummyData));
            localStorage.setItem(this.storageVersionKey, String(this.currentDummyVersion));
            return;
        }

        try {
            const parsedData = JSON.parse(existing);
            const mustUpgrade = existingVersion < this.currentDummyVersion || this.needsDummyUpgrade(parsedData);

            if (!mustUpgrade) {
                return;
            }

            const dummyById = new Map(dummyData.map(item => [item.id, item]));
            const mergedData = Array.isArray(parsedData)
                ? parsedData.map(item => {
                    const updated = dummyById.get(item.id);

                    if (!updated) {
                        return item;
                    }

                    return {
                        ...item,
                        ...updated,
                        id: item.id
                    };
                })
                : [];

            const existingIds = new Set(mergedData.map(item => item.id));
            dummyData.forEach(item => {
                if (!existingIds.has(item.id)) {
                    mergedData.push(item);
                }
            });

            localStorage.setItem(this.storageKey, JSON.stringify(mergedData));
            localStorage.setItem(this.storageVersionKey, String(this.currentDummyVersion));
        } catch (error) {
            localStorage.setItem(this.storageKey, JSON.stringify(dummyData));
            localStorage.setItem(this.storageVersionKey, String(this.currentDummyVersion));
        }
    }

    // Get data dari localStorage
    getData() {
        const data = localStorage.getItem(this.storageKey);
        return data ? JSON.parse(data) : [];
    }

    // Save data ke localStorage
    saveData(data) {
        localStorage.setItem(this.storageKey, JSON.stringify(data));
    }

    // Group data by date
    groupByDate(data) {
        const grouped = {};
        
        data.forEach(item => {
            const dateObj = new Date(item.date);
            const dateKey = this.formatDateKey(dateObj);
            
            if (!grouped[dateKey]) {
                grouped[dateKey] = {
                    date: dateObj,
                    dateString: this.formatDateDisplay(dateObj),
                    items: []
                };
            }
            
            grouped[dateKey].items.push(item);
        });

        // Sort by date (descending)
        return Object.values(grouped).sort((a, b) => b.date - a.date);
    }

    // Format tanggal untuk key
    formatDateKey(date) {
        return date.toISOString().split('T')[0];
    }

    // Format tanggal untuk display (Minggu, 7 Juni 2025)
    formatDateDisplay(date) {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const dayName = days[date.getDay()];
        const day = date.getDate();
        const month = months[date.getMonth()];
        const year = date.getFullYear();
        
        return `${dayName}, ${day} ${month} ${year}`;
    }

    // Get status badge
    getStatusBadge(status) {
        const badges = {
            'hoax': { icon: 'mdi:alert-circle', text: 'HOAX' },
            'palsu': { icon: 'mdi:alert', text: 'PALSU' },
            'benar': { icon: 'mdi:check-circle', text: 'BENAR' }
        };
        return badges[status] || badges['hoax'];
    }

    // Render semua data
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

        // Re-attach event listeners untuk delete buttons
        this.attachItemEventListeners();
    }

    // Render single item
    renderItem(item) {
        const hoaxPercent = item.status === 'benar' ? 0 : (Number(item.confidence) || 0);
        const queryPreview = this.truncateText(item.query, 88);
        const resultPreview = this.truncateText(item.description, 110);
        const resultLabel = item.status === 'benar' ? 'Akurat' : item.status === 'palsu' ? 'Palsu' : 'Hoax';
        return `
            <div class="riwayat-group">
                <div class="riwayat-group-date">${this.formatDateDisplay(new Date(item.date))}</div>
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
                        <button class="riwayat-action-btn bagikan" title="Bagikan" data-id="${item.id}">
                            <iconify-icon icon="mdi:share-variant" width="16" height="16"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Escape HTML untuk keamanan
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    truncateText(text, maxLength) {
        if (typeof text !== 'string') {
            return '';
        }

        const normalized = text.replace(/\s+/g, ' ').trim();
        if (normalized.length <= maxLength) {
            return normalized;
        }

        return `${normalized.slice(0, maxLength).trimEnd()}...`;
    }

    // Convert URLs dalam teks menjadi clickable links
    convertUrlsToLinks(text) {
        if (typeof text !== 'string') {
            return this.escapeHtml(String(text));
        }

        // Escape HTML terlebih dahulu
        let escaped = this.escapeHtml(text);
        
        // Regex untuk detect URLs - lebih simple dan reliable
        const urlRegex = /https?:\/\/[^\s<>"]+/gi;
        
        // Replace setiap URL dengan anchor tag
        return escaped.replace(urlRegex, (url) => {
            // Hapus trailing punctuation
            let cleanUrl = url;
            let suffix = '';
            
            // Check trailing characters
            while (cleanUrl && /[.,;:!?\)]$/.test(cleanUrl)) {
                suffix = cleanUrl.slice(-1) + suffix;
                cleanUrl = cleanUrl.slice(0, -1);
            }
            
            // Generate anchor tag dengan HTML entities di-preserve
            const href = cleanUrl.replace(/&amp;/g, '&'); // Unescape untuk href
            return `<a href="${href}" target="_blank" rel="noopener noreferrer" style="color: #c41e3a; text-decoration: underline; font-weight: 500; cursor: pointer;">${cleanUrl}</a>${suffix}`;
        });
    }

    // Delete item
    deleteItem(id) {
        const data = this.getData();
        const filtered = data.filter(item => item.id !== parseInt(id));
        this.saveData(filtered);
        this.render();
    }

    // Delete all
    deleteAll() {
        if (confirm('Apakah Anda yakin ingin menghapus semua riwayat pencarian?')) {
            localStorage.removeItem(this.storageKey);
            this.render();
        }
    }

    // Attach event listeners untuk delete all button
    attachEventListeners() {
        const hapusSemuaBtn = document.getElementById('hapusSemuaBtn');
        if (hapusSemuaBtn) {
            hapusSemuaBtn.addEventListener('click', () => this.deleteAll());
        }
    }

    // Attach event listeners untuk item buttons
    attachItemEventListeners() {
        document.querySelectorAll('.riwayat-section-toggle').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const button = e.currentTarget;
                const targetId = button.dataset.sectionToggle;
                const panel = document.getElementById(targetId);

                if (!panel) {
                    return;
                }

                const isExpanded = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', String(!isExpanded));
                panel.hidden = isExpanded;
                button.closest('.riwayat-section')?.classList.toggle('is-open', !isExpanded);
                const icon = button.querySelector('.riwayat-section-chevron');
                if (icon) {
                    icon.setAttribute('icon', isExpanded ? 'mdi:chevron-down' : 'mdi:chevron-up');
                }
            });
        });

        // Delete buttons
        document.querySelectorAll('.riwayat-action-btn.hapus').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                if (confirm('Hapus item ini dari riwayat?')) {
                    this.deleteItem(id);
                }
            });
        });

        // Feedback buttons
        document.querySelectorAll('.riwayat-action-btn.umpan-balik').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                const data = this.getData();
                const itemData = data.find(d => d.id === parseInt(id));
                
                if (itemData) {
                    this.showFeedbackForm(itemData);
                }
            });
        });

        // Share buttons
        document.querySelectorAll('.riwayat-action-btn.bagikan').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                const data = this.getData();
                const itemData = data.find(d => d.id === parseInt(id));
                
                if (itemData && navigator.share) {
                    navigator.share({
                        title: 'Lensa Hoax - Hasil Verifikasi',
                        text: `${itemData.query}\n\nStatus: ${itemData.status}`,
                        url: window.location.href
                    }).catch(err => console.log('Share cancelled:', err));
                } else if (itemData) {
                    alert('Fitur bagikan akan segera hadir');
                }
            });
        });
    }
    
    // Show feedback form modal
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
        
        // Remove existing modal if any
        const existing = document.getElementById('feedbackModal');
        if (existing) existing.remove();
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', feedbackHtml);
        
        // Add event listeners
        const modal = document.getElementById('feedbackModal');
        const closeBtn = document.getElementById('closeFeedback');
        const cancelBtn = document.getElementById('cancelFeedback');
        const submitBtn = document.getElementById('submitFeedback');
        
        const closeModal = () => modal.remove();
        
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        
        submitBtn.addEventListener('click', () => {
            const text = document.getElementById('feedbackText').value.trim();
            if (text) {
                alert('Terima kasih! Umpan balik Anda telah dikirim.');
                closeModal();
            } else {
                alert('Silakan tulis umpan balik Anda terlebih dahulu.');
            }
        });
        
        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        }, { once: true });
    }
}

// Helper test function - bisa di-call dari console untuk debug
function testLinkConversion() {
    const testText = "Sumber: https://www.delik.com/ | https://www.kompas.com/ | https://www.tempo.co/";
    const manager = new RiwayatManager();
    const result = manager.convertUrlsToLinks(testText);
    console.log("Input:", testText);
    console.log("Output HTML:", result);
    
    // Create test element to show it visually
    const div = document.createElement('div');
    div.innerHTML = result;
    console.log("Rendered HTML:", div.innerHTML);
    
    return result;
}

// Initialize saat DOM ready
let riwayatManager = null;

document.addEventListener('DOMContentLoaded', () => {
    riwayatManager = new RiwayatManager();
});
// Test: Panggil testLinkConversion() dari console untuk verify link conversion
// Output akan show HTML anchor tags dengan proper styling
