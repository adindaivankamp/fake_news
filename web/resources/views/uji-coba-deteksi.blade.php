<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uji Coba Lensa Hoax API</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: {
                            600: '#8b0000',
                            700: '#6b0000',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-red-50 min-h-screen p-8 font-sans">

    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg border border-red-100 overflow-hidden">

        <div class="bg-maroon-600 text-white p-6 text-center">
            <h1 class="text-2xl font-bold">Pastikan Fakta dengan Mudah</h1>
            <p class="text-sm opacity-80 mt-2">Verifikasi informasi melalui API Lensa Hoax</p>
        </div>

        <div class="p-6">
            <form id="hoaxForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Masukkan Teks Kabar/Berita:</label>
                    <textarea id="inputText" rows="5" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-600 focus:border-maroon-600 outline-none transition" placeholder="Contoh: Pemerintah membagikan Bantuan Sosial Ramadan sebesar Rp1,5 juta..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" id="submitBtn" class="bg-maroon-600 hover:bg-maroon-700 text-white font-semibold py-2 px-6 rounded-lg shadow transition flex items-center">
                        <span id="btnText">Telusuri Fakta</span>
                        <svg id="loadingSpinner" class="animate-spin ml-2 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <div id="resultArea" class="mt-8 hidden border-t border-gray-200 pt-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Hasil Penelusuran</h2>

                <div class="text-center mb-6">
                    <p class="text-sm text-gray-600">Informasi tersebut terindikasi:</p>
                    <h3 id="resultLabel" class="text-4xl font-extrabold text-red-600 my-2">HOAX</h3>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">Persentase Keyakinan:</p>
                    <div class="w-full bg-green-500 rounded-full h-6 flex overflow-hidden">
                        <div id="hoaxBar" class="bg-red-600 h-6 text-xs font-bold text-white flex items-center justify-center transition-all duration-1000" style="width: 0%">0%</div>
                        <div id="faktaBar" class="bg-green-500 h-6 text-xs font-bold text-white flex items-center justify-center transition-all duration-1000" style="width: 0%">0%</div>
                    </div>
                    <div class="flex justify-between text-xs font-semibold mt-1">
                        <span class="text-red-600">Hoax</span>
                        <span class="text-green-600">Fakta</span>
                    </div>
                </div>

                <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <p class="text-sm text-gray-800 font-medium mb-1">Penjelasan:</p>
                    <p id="explanationText" class="text-sm text-gray-600"></p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 mb-2 font-semibold">Tautan sumber terkait verifikasi:</p>
                    <ul id="sourceList" class="list-disc pl-5 text-sm text-blue-600 space-y-1">
                    </ul>
                </div>
            </div>

            <div id="errorArea" class="mt-6 hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span class="block sm:inline" id="errorMessage">Terjadi kesalahan sistem.</span>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('hoaxForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const inputText = document.getElementById('inputText').value;
            if (!inputText) {
                alert('Teks tidak boleh kosong!');
                return;
            }

            // UI Loading State
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('loadingSpinner');
            const submitBtn = document.getElementById('submitBtn');
            const resultArea = document.getElementById('resultArea');
            const errorArea = document.getElementById('errorArea');

            btnText.innerText = 'Memproses...';
            spinner.classList.remove('hidden');
            submitBtn.disabled = true;
            resultArea.classList.add('hidden');
            errorArea.classList.add('hidden');

            try {
                // Tembak API yang ada di Controller kamu
                const response = await fetch('/api/detect-text', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Penting untuk Laravel
                    },
                    body: JSON.stringify({
                        input_text: inputText
                    })
                });

                const json = await response.json();

                if (response.ok) {
                    // Update UI dengan data dari JSON
                    const data = json.data.analysis;
                    const labelElem = document.getElementById('resultLabel');

                    labelElem.innerText = data.label;
                    labelElem.className = data.label === 'HOAX' ? 'text-4xl font-extrabold text-red-600 my-2' : 'text-4xl font-extrabold text-green-600 my-2';

                    // Update Bar
                    document.getElementById('hoaxBar').style.width = data.percentage_hoax + '%';
                    document.getElementById('hoaxBar').innerText = data.percentage_hoax + '%';

                    document.getElementById('faktaBar').style.width = data.percentage_fact + '%';
                    document.getElementById('faktaBar').innerText = data.percentage_fact + '%';

                    document.getElementById('explanationText').innerText = data.explanation;

                    // Update List Sumber
                    const sourceList = document.getElementById('sourceList');
                    sourceList.innerHTML = '';
                    json.data.sources.forEach(source => {
                        const li = document.createElement('li');
                        li.innerHTML = `<a href="${source.url}" target="_blank" class="hover:underline">${source.title}</a>`;
                        sourceList.appendChild(li);
                    });

                    // Tampilkan Hasil
                    resultArea.classList.remove('hidden');
                } else {
                    throw new Error(json.message || 'Gagal menghubungi server.');
                }
            } catch (error) {
                errorArea.classList.remove('hidden');
                document.getElementById('errorMessage').innerText = error.message;
            } finally {
                // Kembalikan tombol ke semula
                btnText.innerText = 'Telusuri Fakta';
                spinner.classList.add('hidden');
                submitBtn.disabled = false;
            }
        });
    </script>
</body>

</html>