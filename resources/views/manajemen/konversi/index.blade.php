@extends('layouts.manajemen.index')

@section('content')
    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Konverter Card -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
                        <!-- Header -->
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-r from-green-400 to-green-700 bg-opacity-10 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-cube text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900">Konversi Satuan</h2>
                                        <p class="text-sm text-gray-500">Buat dan kelola satuan untuk kebutuhan bisnis Anda
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Converter Content -->
                        <div class="p-6">
                            <!-- Custom Unit Management (only show when custom category is selected) -->
                            <div id="customUnitManager"
                                class="hidden mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Konversi Satuan</h3>
                                    <button onclick="showAddUnitForm()"
                                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Satuan
                                    </button>
                                </div>

                                <!-- Add Unit Form -->
                                <div id="addUnitForm" class="hidden mb-6">
                                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                                        <!-- Simple Header -->
                                        <div class="px-6 py-4 border-b border-gray-200">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-lg font-semibold text-gray-900">Satuan Baru</h3>
                                                <button onclick="hideAddUnitForm()"
                                                    class="text-gray-400 hover:text-gray-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <p class="text-sm text-gray-500 mt-1">Isi form di bawah untuk menambah satuan
                                                baru</p>
                                        </div>

                                        <!-- Simple Form -->
                                        <div class="px-6 py-6">
                                            <div class="space-y-5">
                                                <!-- Nama Satuan -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Nama Satuan <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" id="newUnitName" placeholder="Contoh: Krat Telur"
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors">
                                                    <p class="text-xs text-gray-500 mt-1">Nama kemasan yang akan digunakan
                                                    </p>
                                                </div>

                                                <!-- Kode Singkat -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Kode Singkat <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" id="newUnitCode" placeholder="Contoh: KRAT"
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors uppercase">
                                                    <p class="text-xs text-gray-500 mt-1">Singkatan untuk kemudahan (maks 8
                                                        huruf)</p>
                                                </div>

                                                <!-- Konversi -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                                        Berapa isi dalam 1 kemasan? <span class="text-red-500">*</span>
                                                    </label>

                                                    <!-- Simple conversion display -->
                                                    <div class="bg-gray-50 rounded-lg p-4 mb-3">
                                                        <div class="text-center">
                                                            <div class="text-lg font-medium text-gray-700">
                                                                1 <span id="unitNameDisplay"
                                                                    class="text-green-600 font-bold">Kemasan</span> =
                                                                <span id="factorDisplay"
                                                                    class="text-green-600 font-bold">?</span>
                                                                <span id="baseUnitDisplay" class="text-gray-600">pcs</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-sm text-gray-600 mb-2">Jumlah</label>
                                                            <input type="number" id="newUnitFactor"
                                                                placeholder="Contoh: 30" min="1"
                                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors text-center text-lg font-medium">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm text-gray-600 mb-2">Satuan</label>
                                                            <select id="newUnitBase"
                                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors">
                                                                    <option value="" selected>Silakan Pilih Satuan</option>
                                                                    <option value="kg">kg</option>
                                                                    <option value="l">L (liter)</option>
                                                                    <option value="gram">gram</option>  
                                                                    <option value="pcs">pcs</option>
                                                                    <option value="ml">mL (mililiter)</option>
                                                                    <option value="slice">slice</option>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Contoh -->
                                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                                    <div class="flex items-start space-x-3">
                                                        <i class="fas fa-info-circle text-green-500 mt-0.5"></i>
                                                        <div>
                                                            <h4 class="text-sm font-medium text-green-800 mb-2">Contoh:</h4>
                                                            <ul class="text-xs text-green-700 space-y-1">
                                                                <li>• 1 Krat Telur = 30 pcs (30 butir telur)</li>
                                                                <li>• 1 Dus Mie = 40 pcs (40 bungkus mie)</li>
                                                                <li>• 1 Karung Beras = 25 kg (25 kilogram beras)</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Simple Actions -->
                                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                                            <div class="flex space-x-3">
                                                <button type="button" onclick="addCustomUnit()"
                                                    class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-200 transition-colors font-medium">
                                                    <i class="fas fa-save mr-2"></i>Simpan
                                                </button>
                                                <button type="button" onclick="hideAddUnitForm()"
                                                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-gray-200 transition-colors font-medium">
                                                    Batal
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Custom Units List -->
                                <div id="customUnitsList" class="space-y-2" style="max-height: 500px; overflow-y: auto;">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>



                            <!-- Result Summary -->
                            <div id="resultSummary"
                                class="mt-6 p-4 bg-gradient-to-r from-primary/5 to-secondary/5 rounded-xl border border-primary/10 hidden">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-success/10 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-success"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Hasil Konversi:</p>
                                        <p id="summaryText" class="text-lg font-semibold text-gray-900"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Side Panel -->
                <div class="space-y-6">
                    <!-- History -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Riwayat Konversi</h3>
                                    <p class="text-sm text-gray-500">Konversi terakhir</p>
                                </div>
                                <button onclick="clearHistory()"
                                    class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div id="historyList" class="p-6 space-y-4" style="max-height: 480px; overflow-y: auto;">
                            {{-- <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-history text-3xl mb-3 text-gray-300"></i>
                                <p class="text-sm">Belum ada riwayat konversi</p>
                            </div> --}}
                            @forelse ($konversi as $item)
                                <div class="p-4 border rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                                    @if($item->satuan && $item->satuan->nama)
                                        <p class="text-sm font-semibold text-gray-800 mb-2">
                                            <i class="fas fa-tag text-green-600 mr-1"></i>{{ $item->satuan->nama }}
                                        </p>
                                    @endif
                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold">Satuan Awal:</span> {{ $item->nilai ?? 1 }} {{ $item->satuan_besar }}
                                    </p>
                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold">Satuan Akhir:</span> {{ $item->jumlah }} {{ $item->satuan_kecil }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="far fa-clock mr-1"></i>
                                        @php
                                            // Jika waktu di tgl adalah 00:00, gunakan created_at
                                            $tanggal = \Carbon\Carbon::parse($item->tgl);
                                            if ($tanggal->format('H:i') == '00:00' && $item->created_at) {
                                                echo \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i');
                                            } else {
                                                echo $tanggal->format('d/m/Y H:i');
                                            }
                                        @endphp
                                    </p>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-8">
                                    <i class="fas fa-history text-3xl mb-3 text-gray-300"></i>
                                    <p class="text-sm">Belum ada riwayat konversi</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="bg-gradient-to-r from-accent/5 to-primary/5 rounded-2xl border border-accent/10 p-6">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-lightbulb text-accent"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">💡 Tips Satuan Kustom</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Buat satuan sesuai kemasan pembelian</li>
                                    <li>• Contoh: 1 krat = 30 pcs telur</li>
                                    <li>• Gunakan singkatan yang mudah diingat</li>
                                    <li>• Data tersimpan otomatis</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </main>
@endsection

<style>
    /* Loading Spinner */
    .spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #10b981;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Fade in animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Slide in animation */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Scale animation */
    @keyframes scaleIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Fade out animation */
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }

    /* Pulse animation */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }

    .animate-slideIn {
        animation: slideIn 0.3s ease-out;
    }

    .animate-scaleIn {
        animation: scaleIn 0.3s ease-out;
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Success ripple effect */
    @keyframes ripple {
        0% {
            transform: scale(0);
            opacity: 1;
        }
        100% {
            transform: scale(4);
            opacity: 0;
        }
    }

    .ripple-effect {
        position: relative;
        overflow: hidden;
    }

    .ripple-effect::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        transform: translate(-50%, -50%) scale(0);
    }

    .ripple-effect.active::after {
        animation: ripple 0.6s ease-out;
    }

    /* Smooth transitions */
    * {
        transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    /* Custom scrollbar */
    #customUnitsList::-webkit-scrollbar,
    #historyList::-webkit-scrollbar {
        width: 6px;
    }

    #customUnitsList::-webkit-scrollbar-track,
    #historyList::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #customUnitsList::-webkit-scrollbar-thumb,
    #historyList::-webkit-scrollbar-thumb {
        background: #10b981;
        border-radius: 10px;
    }

    #customUnitsList::-webkit-scrollbar-thumb:hover,
    #historyList::-webkit-scrollbar-thumb:hover {
        background: #059669;
    }

    /* Hover effects */
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Delete confirmation overlay */
    .delete-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.2s ease-out;
    }

    .delete-modal {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        max-width: 400px;
        animation: scaleIn 0.3s ease-out;
    }
</style>

<script>
    // Update current date and time
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        const dateTimeElement = document.getElementById('currentDateTime');
        if (dateTimeElement) {
            dateTimeElement.textContent = now.toLocaleDateString('id-ID', options);
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateDateTime();
        setInterval(updateDateTime, 60000);
    });

    // Data konversi dari server (di-inject oleh controller)
    const serverKonversi = @json($konversi ?? []);
    const serverSatuan = @json($satuan ?? []);

    // Data lokal/fallback
    const units = {
        custom: {
            pcs: {
                name: 'Pieces',
                factor: 1
            }
        }
    };

    let currentCategory = 'custom';
    let history = JSON.parse(localStorage.getItem('conversionHistory') || '[]');
    let customUnits = JSON.parse(localStorage.getItem('customUnits') || '[]');

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadCustomUnits();
        setCategory('custom');
        // Don't call loadHistory() on page load since we're using server-side data from Blade
    });

    function setCategory(category) {
        currentCategory = 'custom';

        // Show custom unit manager
        const customManager = document.getElementById('customUnitManager');
        customManager.classList.remove('hidden');
        loadCustomUnitsList();

        populateUnits();
    }

    function populateUnits() {
        const fromSelect = document.getElementById('fromUnit');
        const toSelect = document.getElementById('toUnit');

        // Check if elements exist
        if (!fromSelect || !toSelect) return;

        fromSelect.innerHTML = '';
        toSelect.innerHTML = '';

        const categoryUnits = units.custom;

        Object.keys(categoryUnits).forEach(unit => {
            const option1 = new Option(categoryUnits[unit].name, unit);
            const option2 = new Option(categoryUnits[unit].name, unit);
            fromSelect.add(option1);
            toSelect.add(option2);
        });

        // Set default values for custom category
        if (Object.keys(categoryUnits).length > 1) {
            const unitKeys = Object.keys(categoryUnits);
            fromSelect.value = unitKeys[1] || unitKeys[0]; // First custom unit
            toSelect.value = 'pcs'; // Base unit
        }

        updateUnitLabels();
        convert();
    }

    function updateUnitLabels() {
        const fromUnitEl = document.getElementById('fromUnit');
        const toUnitEl = document.getElementById('toUnit');
        const fromLabelEl = document.getElementById('fromUnitLabel');
        const toLabelEl = document.getElementById('toUnitLabel');

        if (!fromUnitEl || !toUnitEl || !fromLabelEl || !toLabelEl) return;

        fromLabelEl.textContent = fromUnitEl.value;
        toLabelEl.textContent = toUnitEl.value;
    }

    function convert() {
        const fromValueEl = document.getElementById('fromValue');
        const fromUnitEl = document.getElementById('fromUnit');
        const toUnitEl = document.getElementById('toUnit');

        // Check if elements exist
        if (!fromValueEl || !fromUnitEl || !toUnitEl) return;

        const fromValue = parseFloat(fromValueEl.value);
        const fromUnit = fromUnitEl.value;
        const toUnit = toUnitEl.value;

        updateUnitLabels();

        if (isNaN(fromValue) || fromValue === '') {
            const toValEl = document.getElementById('toValue');
            if (toValEl) toValEl.value = '';
            const resSummaryEl = document.getElementById('resultSummary');
            if (resSummaryEl && resSummaryEl.classList) resSummaryEl.classList.add('hidden');
            return;
        }

        // Only custom units conversion
        const result = convertCustomUnits(fromValue, fromUnit, toUnit);

        const toValEl2 = document.getElementById('toValue');
        if (toValEl2) toValEl2.value = result.toLocaleString('id-ID');

        // Show result summary
        const summary = `${fromValue.toLocaleString('id-ID')} ${fromUnit} = ${result.toLocaleString('id-ID')} ${toUnit}`;
        const summaryTextEl = document.getElementById('summaryText');
        if (summaryTextEl) summaryTextEl.textContent = summary;
        const resSummaryEl2 = document.getElementById('resultSummary');
        if (resSummaryEl2 && resSummaryEl2.classList) resSummaryEl2.classList.remove('hidden');

        // Add to history
        addToHistory(fromValue, fromUnit, result, toUnit);
    }

    function convertCustomUnits(value, fromUnit, toUnit) {
        const fromUnitData = units.custom[fromUnit];
        const toUnitData = units.custom[toUnit];

        // Check if both units have same base unit
        if (fromUnitData.baseUnit && toUnitData.baseUnit) {
            if (fromUnitData.baseUnit === toUnitData.baseUnit) {
                // Both have same base unit, convert directly
                return (value * fromUnitData.factor) / toUnitData.factor;
            } else {
                // Different base units, need conversion through base units
                const baseValue = value * fromUnitData.factor;

                // Convert between different base units if needed
                let convertedBaseValue = baseValue;
                if (fromUnitData.baseUnit !== toUnitData.baseUnit) {
                    convertedBaseValue = convertBetweenBaseUnits(baseValue, fromUnitData.baseUnit, toUnitData.baseUnit);
                }

                return convertedBaseValue / toUnitData.factor;
            }
        } else {
            // Simple factor-based conversion
            return (value * fromUnitData.factor) / toUnitData.factor;
        }
    }

    function convertBetweenBaseUnits(value, fromBase, toBase) {
        // Define conversion factors between base units
        const baseConversions = {
            'kg-g': 1000,
            'L-ml': 1000,
            'm-cm': 100,
            'pcs-pcs': 1
        };

        const conversionKey = `${fromBase}-${toBase}`;
        const reverseKey = `${toBase}-${fromBase}`;

        if (baseConversions[conversionKey]) {
            return value * baseConversions[conversionKey];
        } else if (baseConversions[reverseKey]) {
            return value / baseConversions[reverseKey];
        } else {
            // No direct conversion available, return as is
            return value;
        }
    }

    function swapUnits() {
        const fromSelect = document.getElementById('fromUnit');
        const toSelect = document.getElementById('toUnit');
        const fromValue = document.getElementById('fromValue');
        const toValue = document.getElementById('toValue');

        // Check if elements exist
        if (!fromSelect || !toSelect || !fromValue || !toValue) return;

        const tempUnit = fromSelect.value;
        fromSelect.value = toSelect.value;
        toSelect.value = tempUnit;

        fromValue.value = toValue.value.replace(/[.,]/g, '');

        convert();
    }

    function clearAll() {
        const fv = document.getElementById('fromValue'); if (fv) fv.value = '';
        const tv = document.getElementById('toValue'); if (tv) tv.value = '';
        const resEl = document.getElementById('resultSummary'); if (resEl && resEl.classList) resEl.classList.add('hidden');
    }

    function addToHistory(fromValue, fromUnit, toValue, toUnit) {
        const historyItem = {
            id: Date.now(),
            fromValue: fromValue,
            fromUnit: fromUnit,
            toValue: toValue,
            toUnit: toUnit,
            category: currentCategory,
            timestamp: new Date().toLocaleString('id-ID')
        };

        history.unshift(historyItem);
        history = history.slice(0, 10); // Keep only last 10

        localStorage.setItem('conversionHistory', JSON.stringify(history));
        loadHistory();
    }

    function loadHistory() {
        const historyList = document.getElementById('historyList');

        if (history.length === 0) {
            historyList.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-history text-3xl mb-3 text-gray-300"></i>
                        <p class="text-sm">Belum ada riwayat konversi</p>
                    </div>
                `;
            return;
        }

        historyList.innerHTML = history.map(item => `
                <div class="p-3 bg-gray-50 rounded-lg mb-2 cursor-pointer hover:bg-gray-100 transition-colors" 
                     onclick="useHistory(${item.fromValue}, '${item.fromUnit}', '${item.toUnit}', '${item.category}')">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                ${item.fromValue.toLocaleString('id-ID')} ${item.fromUnit} → ${item.toValue.toLocaleString('id-ID')} ${item.toUnit}
                            </p>
                            <p class="text-xs text-gray-500">${item.timestamp}</p>
                        </div>
                        <i class="fas fa-redo text-gray-400"></i>
                    </div>
                </div>
            `).join('');
    }

    function useHistory(fromValue, fromUnit, toUnit, category) {
        const fromUnitEl = document.getElementById('fromUnit');
        const toUnitEl = document.getElementById('toUnit');
        const fromValueEl = document.getElementById('fromValue');

        // Check if elements exist
        if (!fromUnitEl || !toUnitEl || !fromValueEl) return;

        fromUnitEl.value = fromUnit;
        toUnitEl.value = toUnit;
        fromValueEl.value = fromValue;

        convert();
    }

    function clearHistory() {
        if (confirm('Hapus semua riwayat konversi?')) {
            history = [];
            localStorage.removeItem('conversionHistory');
            loadHistory();
        }
    }

    // Copy result to clipboard
    const toValueEl = document.getElementById('toValue');
    if (toValueEl) {
        toValueEl.addEventListener('click', function() {
            this.select();
            document.execCommand('copy');

            // Show toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-success text-white px-4 py-2 rounded-lg shadow-lg z-50';
            toast.textContent = 'Hasil disalin ke clipboard!';
            document.body.appendChild(toast);

            setTimeout(() => {
                document.body.removeChild(toast);
            }, 2000);
        });
    }

    // Custom Units Management Functions
    function loadCustomUnits() {
        // Add default custom units if none exist
        if (customUnits.length === 0) {
            const defaultUnits = [{
                    name: 'Krat Telur',
                    code: 'krat',
                    factor: 30,
                    baseUnit: 'pcs',
                    id: 1
                },
                {
                    name: 'Dus Mie',
                    code: 'dus',
                    factor: 40,
                    baseUnit: 'pcs',
                    id: 2
                },
                {
                    name: 'Karung Beras',
                    code: 'karung',
                    factor: 25,
                    baseUnit: 'kg',
                    id: 3
                },
                {
                    name: 'Jerigen Minyak',
                    code: 'jerigen',
                    factor: 5,
                    baseUnit: 'L',
                    id: 4
                }
            ];

            customUnits = defaultUnits;
            localStorage.setItem('customUnits', JSON.stringify(customUnits));
        }

        customUnits.forEach(unit => {
            units.custom[unit.code] = {
                name: unit.name,
                factor: unit.factor,
                baseUnit: unit.baseUnit
            };
        });
    }

    function showAddUnitForm() {
        const f = document.getElementById('addUnitForm');
        if (f && f.classList) {
            f.classList.remove('hidden');
            f.classList.add('animate-fadeIn');
            
            // Smooth scroll to form
            setTimeout(() => {
                f.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        }
        
        // Focus on first input with delay for smooth animation
        setTimeout(() => {
            const n = document.getElementById('newUnitName');
            if (n) {
                n.focus();
                n.style.animation = 'pulse 1s ease-out';
            }
        }, 300);
        
        // Reset form
        resetForm();
    }

    function hideAddUnitForm() {
        const f = document.getElementById('addUnitForm');
        if (f && f.classList) {
            f.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => {
                f.classList.add('hidden');
                f.style.animation = '';
            }, 300);
        }
        resetForm();
    }

    function resetForm() {
        // Clear form inputs
        document.getElementById('newUnitName').value = '';
        document.getElementById('newUnitCode').value = '';
        document.getElementById('newUnitFactor').value = '';
        // Reset dropdown to default "Silakan Pilih Satuan"
        const baseEl = document.getElementById('newUnitBase');
        if (baseEl) {
            baseEl.value = '';
        }

        // Reset displays
        updateSimpleDisplay();
    }

    function updateSimpleDisplay() {
        const name = document.getElementById('newUnitName').value.trim();
        const factor = document.getElementById('newUnitFactor').value;
        const base = document.getElementById('newUnitBase').value;

        // Use the selected value as label or show placeholder
        let baseLabel = base || 'Satuan';

        // Update display elements
        document.getElementById('unitNameDisplay').textContent = name || 'Kemasan';
        document.getElementById('factorDisplay').textContent = factor || '?';
        document.getElementById('baseUnitDisplay').textContent = baseLabel;
    }

    // Simplified real-time updates
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('newUnitName');
        const codeInput = document.getElementById('newUnitCode');
        const factorInput = document.getElementById('newUnitFactor');
        const baseSelect = document.getElementById('newUnitBase');

        // Auto-generate code from name
        nameInput?.addEventListener('input', function() {
            if (!codeInput.value.trim()) {
                const autoCode = this.value.trim()
                    .toUpperCase()
                    .replace(/\s+/g, '')
                    .replace(/[^A-Z0-9]/g, '')
                    .substring(0, 8);
                codeInput.value = autoCode;
            }
            updateSimpleDisplay();
        });

        // Format code input
        codeInput?.addEventListener('input', function() {
            this.value = this.value
                .toUpperCase()
                .replace(/[^A-Z0-9]/g, '')
                .substring(0, 8);
            updateSimpleDisplay();
        });

        // Update display when values change
        factorInput?.addEventListener('input', updateSimpleDisplay);
        baseSelect?.addEventListener('change', updateSimpleDisplay);
    });

    function addCustomUnit() {
        const name = document.getElementById('newUnitName').value.trim();
        const code = document.getElementById('newUnitCode').value.trim().toUpperCase();
        const factor = parseFloat(document.getElementById('newUnitFactor').value);
        const baseUnit = document.getElementById('newUnitBase').value;

        // Simple validation
        if (!name) {
            showNotification('Harap isi nama satuan!', 'error');
            document.getElementById('newUnitName').focus();
            return;
        }

        if (!code) {
            showNotification('Harap isi kode satuan!', 'error');
            document.getElementById('newUnitCode').focus();
            return;
        }

        if (!factor || factor <= 0) {
            showNotification('Harap isi jumlah yang benar (lebih dari 0)!', 'error');
            document.getElementById('newUnitFactor').focus();
            return;
        }

        if (!baseUnit) {
            showNotification('Harap pilih satuan dasar!', 'error');
            document.getElementById('newUnitBase').focus();
            return;
        }

        // Show loading state
        const saveBtn = document.querySelector('button[onclick="addCustomUnit()"]');
        const originalHTML = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<div class="spinner inline-block mr-2"></div>Menyimpan...';
        saveBtn.classList.add('opacity-75', 'cursor-not-allowed');

        // Add to custom units
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch('/management/konversi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                new_satuan_name: name,
                new_satuan_code: code,
                jumlah: factor,
                satuan_kecil: baseUnit
            })
        }).then(res => res.json())
        .then(data => {
            if (data && data.success) {
                // Show success animation
                saveBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Tersimpan!';
                saveBtn.classList.add('ripple-effect', 'active');
                
                showNotification('Satuan berhasil ditambahkan!', 'success');
                
                // Reload with smooth transition
                setTimeout(() => {
                    location.reload();
                }, 800);
            } else {
                throw new Error((data && data.message) || 'Gagal menyimpan satuan ke server');
            }
        }).catch(err => {
            console.error(err);
            saveBtn.innerHTML = originalHTML;
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            showNotification(err.message || 'Terjadi kesalahan saat menyimpan satuan', 'error');
        });
    }

    function loadCustomUnitsList() {
        const listContainer = document.getElementById('customUnitsList');

        // If server provided konversi data, render that as authoritative list
        if (serverKonversi && serverKonversi.length > 0) {
            listContainer.innerHTML = serverKonversi.map((item, index) => `
                <div id="konversi-item-${item.id}" class="bg-white border border-gray-200 rounded-lg hover-lift animate-fadeIn" 
                     style="animation-delay: ${index * 0.05}s">
                    <!-- View Mode -->
                    <div id="view-${item.id}" class="flex items-center justify-between p-3">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                                    <i class="fas fa-cube text-white"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">${(item && item.satuan && item.satuan.nama) ? item.satuan.nama : (item.nama || 'Custom')}</h4>
                                    <p class="text-sm text-gray-500">${item.nilai || 1} ${item.satuan_besar || ''} = <span id="jumlah-display-${item.id}">${item.jumlah}</span> <span id="satuan-display-${item.id}">${item.satuan_kecil}</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="showEditForm(${item.id}, ${item.jumlah}, '${item.satuan_kecil}')" 
                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteKonversi(${item.id})" 
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Edit Mode -->
                    <div id="edit-${item.id}" class="hidden p-3 bg-blue-50 border-t border-blue-100">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Satuan <span class="text-red-500">*</span></label>
                                <input type="text" id="edit-nama-${item.id}" value="${(item && item.satuan && item.satuan.nama) ? item.satuan.nama : (item.nama || '')}" 
                                       placeholder="Contoh: Krat Telur"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Kode Satuan <span class="text-red-500">*</span></label>
                                <input type="text" id="edit-kode-${item.id}" value="${item.satuan_besar || ''}" 
                                       placeholder="Contoh: KRAT"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-colors uppercase">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                                    <input type="number" id="edit-jumlah-${item.id}" value="${item.jumlah}" min="0.01" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-colors">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Satuan Kecil <span class="text-red-500">*</span></label>
                                    <select id="edit-satuan-${item.id}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-colors">
                                        <option value="kg" ${item.satuan_kecil === 'kg' ? 'selected' : ''}>kg</option>
                                        <option value="l" ${item.satuan_kecil === 'l' ? 'selected' : ''}>l (liter)</option>
                                        <option value="gram" ${item.satuan_kecil === 'gram' ? 'selected' : ''}>gram</option>
                                        <option value="pcs" ${item.satuan_kecil === 'pcs' ? 'selected' : ''}>pcs</option>
                                        <option value="ml" ${item.satuan_kecil === 'ml' ? 'selected' : ''}>ml (mili liter)</option>
                                        <option value="slice" ${item.satuan_kecil === 'slice' ? 'selected' : ''}>slice</option>
                                    </select>
                                </div>
                            </div>
                            <div class="bg-blue-100 border border-blue-200 rounded-lg p-2">
                                <p class="text-xs text-blue-700 text-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    1 <span id="edit-preview-nama-${item.id}" class="font-semibold">${item.satuan_besar || 'Kemasan'}</span> = 
                                    <span id="edit-preview-jumlah-${item.id}" class="font-semibold">${item.jumlah}</span> 
                                    <span id="edit-preview-satuan-${item.id}" class="font-semibold">${item.satuan_kecil}</span>
                                </p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="updateKonversi(${item.id})" 
                                        class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                    <i class="fas fa-save mr-1"></i>Simpan
                                </button>
                                <button onclick="hideEditForm(${item.id})" 
                                        class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-medium">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            return;
        }

        // Fallback to localStorage-based customUnits if server has none
        if (customUnits.length === 0) {
            listContainer.innerHTML = `
                    <div class="text-center text-gray-500 py-4">
                        <i class="fas fa-cube text-2xl mb-2 text-gray-300"></i>
                        <p class="text-sm">Belum ada satuan kustom</p>
                        <p class="text-xs">Klik "Tambah Satuan" untuk menambah satuan baru</p>
                    </div>
                `;
            return;
        }

        listContainer.innerHTML = customUnits.map(unit => `
                <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cube text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">${unit.name}</h4>
                                <p class="text-sm text-gray-500">1 ${unit.code} = ${unit.factor} ${unit.baseUnit}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editCustomUnit('${unit.code}')" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteCustomUnit('${unit.code}')" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
    }

    function deleteCustomUnit(code) {
        // If server data exists we should delete by konversi id via API
        if (confirm('Hapus satuan kustom ini?')) {
            // Fallback: remove from localStorage if present
            customUnits = customUnits.filter(unit => unit.code !== code);
            delete units.custom[code];
            localStorage.setItem('customUnits', JSON.stringify(customUnits));
            loadCustomUnitsList();
            populateUnits();
            showToast('Satuan kustom berhasil dihapus!', 'success');
        }
    }

    // Delete konversi entry on server by id
    function deleteKonversi(id) {
        showDeleteConfirmation('Apakah Anda yakin ingin menghapus satuan ini?', () => {
            // Show loading overlay
            showLoadingOverlay('Menghapus...');
            
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch(`/management/konversi/${id}`, { 
                method: 'DELETE', 
                headers: { 
                    'Accept': 'application/json', 
                    'X-CSRF-TOKEN': csrf 
                } 
            })
            .then(res => res.json())
            .then(data => {
                hideLoadingOverlay();
                if (data && data.success) {
                    showNotification('Satuan berhasil dihapus!', 'success');
                    // Animate removal
                    const element = document.querySelector(`[onclick="deleteKonversi(${id})"]`).closest('.flex');
                    if (element) {
                        element.style.animation = 'fadeOut 0.3s ease-out';
                    }
                    setTimeout(() => {
                        location.reload();
                    }, 300);
                } else {
                    throw new Error((data && data.message) || 'Gagal menghapus entri');
                }
            }).catch(err => {
                hideLoadingOverlay();
                console.error(err);
                showNotification(err.message || 'Terjadi kesalahan saat menghapus entri', 'error');
            });
        });
    }

    function editCustomUnit(code) {
        const unit = customUnits.find(u => u.code === code);
        if (unit) {
            document.getElementById('newUnitName').value = unit.name;
            document.getElementById('newUnitCode').value = unit.code;
            document.getElementById('newUnitFactor').value = unit.factor;
            document.getElementById('newUnitBase').value = unit.baseUnit;

            // Change button to update
            const form = document.getElementById('addUnitForm');
            form.classList.remove('hidden');

            // Update form title and button
            form.querySelector('h4').textContent = 'Edit Satuan';
            form.querySelector('button[onclick="addCustomUnit()"]').onclick = () => updateCustomUnit(code);
            form.querySelector('button[onclick="addCustomUnit()"]').innerHTML =
                '<i class="fas fa-save mr-2"></i>Update';
        }
    }

    function updateCustomUnit(originalCode) {
        const name = document.getElementById('newUnitName').value.trim();
        const code = document.getElementById('newUnitCode').value.trim().toLowerCase();
        const factor = parseFloat(document.getElementById('newUnitFactor').value);
        const baseUnit = document.getElementById('newUnitBase').value;

        // Validation
        if (!name || !code || !factor || factor <= 0) {
            alert('Harap isi semua field dengan benar!');
            return;
        }

        // Check if code already exists (except current)
        if (code !== originalCode && units.custom[code]) {
            alert('Kode satuan sudah ada! Gunakan kode yang berbeda.');
            return;
        }

        // Update customUnits array
        const unitIndex = customUnits.findIndex(u => u.code === originalCode);
        if (unitIndex !== -1) {
            customUnits[unitIndex] = {
                ...customUnits[unitIndex],
                name: name,
                code: code,
                factor: factor,
                baseUnit: baseUnit
            };
        }

        // Remove old unit from units object
        delete units.custom[originalCode];

        // Add updated unit to units object
        units.custom[code] = {
            name: name,
            factor: factor,
            baseUnit: baseUnit
        };

        // Save to localStorage
        localStorage.setItem('customUnits', JSON.stringify(customUnits));

        // Reset form
        const form = document.getElementById('addUnitForm');
        form.querySelector('h4').textContent = 'Tambah Satuan Baru';
        form.querySelector('button[onclick="updateCustomUnit()"]').onclick = addCustomUnit;
        form.querySelector('button[onclick="updateCustomUnit()"]').innerHTML = '<i class="fas fa-save mr-2"></i>Simpan';

        // Update UI
        loadCustomUnitsList();
        populateUnits();
        hideAddUnitForm();

        showToast('Satuan kustom berhasil diupdate!', 'success');
    }

    function showToast(message, type = 'info') {
        showNotification(message, type);
    }

    // Enhanced notification system
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifs = document.querySelectorAll('.custom-notification');
        existingNotifs.forEach(notif => notif.remove());

        const notification = document.createElement('div');
        notification.className = 'custom-notification fixed top-4 right-4 z-50 min-w-[320px] max-w-md animate-slideIn';

        let bgColor, iconColor, icon, progressColor;
        switch (type) {
            case 'success':
                bgColor = 'bg-gradient-to-r from-green-500 to-green-600';
                iconColor = 'text-white';
                icon = 'fas fa-check-circle';
                progressColor = 'bg-green-700';
                break;
            case 'error':
                bgColor = 'bg-gradient-to-r from-red-500 to-red-600';
                iconColor = 'text-white';
                icon = 'fas fa-exclamation-circle';
                progressColor = 'bg-red-700';
                break;
            case 'warning':
                bgColor = 'bg-gradient-to-r from-yellow-500 to-yellow-600';
                iconColor = 'text-white';
                icon = 'fas fa-exclamation-triangle';
                progressColor = 'bg-yellow-700';
                break;
            default:
                bgColor = 'bg-gradient-to-r from-blue-500 to-blue-600';
                iconColor = 'text-white';
                icon = 'fas fa-info-circle';
                progressColor = 'bg-blue-700';
        }

        notification.innerHTML = `
            <div class="${bgColor} rounded-lg shadow-2xl overflow-hidden">
                <div class="p-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <i class="${icon} ${iconColor} text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white">${message}</p>
                        </div>
                        <button onclick="this.closest('.custom-notification').remove()" class="flex-shrink-0 text-white hover:text-gray-200 transition-colors">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="h-1 ${progressColor} notification-progress"></div>
            </div>
        `;

        document.body.appendChild(notification);

        // Progress bar animation
        const progressBar = notification.querySelector('.notification-progress');
        progressBar.style.width = '100%';
        progressBar.style.transition = 'width ' + (type === 'error' ? '5' : '3') + 's linear';
        setTimeout(() => {
            progressBar.style.width = '0%';
        }, 10);

        // Auto remove after delay
        const duration = type === 'error' ? 5000 : 3000;
        setTimeout(() => {
            if (document.body.contains(notification)) {
                notification.style.transform = 'translateX(120%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }
        }, duration);
    }

    // Delete confirmation modal
    function showDeleteConfirmation(message, onConfirm) {
        const overlay = document.createElement('div');
        overlay.className = 'delete-overlay';
        overlay.innerHTML = `
            <div class="delete-modal">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus</h3>
                    <p class="text-sm text-gray-500 mb-6">${message}</p>
                    <div class="flex space-x-3">
                        <button onclick="this.closest('.delete-overlay').remove()" 
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            Batal
                        </button>
                        <button onclick="confirmDelete()" 
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                            <i class="fas fa-trash mr-2"></i>Hapus
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        window.confirmDelete = function() {
            overlay.remove();
            onConfirm();
            delete window.confirmDelete;
        };
    }

    // Loading overlay
    function showLoadingOverlay(message = 'Memproses...') {
        const existing = document.querySelector('.loading-overlay');
        if (existing) existing.remove();

        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        overlay.style.animation = 'fadeIn 0.2s ease-out';
        overlay.innerHTML = `
            <div class="bg-white rounded-lg p-6 text-center animate-scaleIn">
                <div class="spinner mx-auto mb-4" style="width: 40px; height: 40px; border-width: 4px;"></div>
                <p class="text-gray-700 font-medium">${message}</p>
            </div>
        `;
        document.body.appendChild(overlay);
    }

    function hideLoadingOverlay() {
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) {
            overlay.style.animation = 'fadeOut 0.2s ease-out';
            setTimeout(() => overlay.remove(), 200);
        }
    }

    // Show edit form for a konversi item
    function showEditForm(id, currentJumlah, currentSatuan) {
        const viewEl = document.getElementById(`view-${id}`);
        const editEl = document.getElementById(`edit-${id}`);
        
        if (viewEl && editEl) {
            viewEl.classList.add('hidden');
            editEl.classList.remove('hidden');
            editEl.classList.add('animate-fadeIn');
            
            // Setup real-time preview updates
            const namaInput = document.getElementById(`edit-nama-${id}`);
            const kodeInput = document.getElementById(`edit-kode-${id}`);
            const jumlahInput = document.getElementById(`edit-jumlah-${id}`);
            const satuanSelect = document.getElementById(`edit-satuan-${id}`);
            
            const updatePreview = () => {
                const previewNama = document.getElementById(`edit-preview-nama-${id}`);
                const previewJumlah = document.getElementById(`edit-preview-jumlah-${id}`);
                const previewSatuan = document.getElementById(`edit-preview-satuan-${id}`);
                
                if (previewNama) previewNama.textContent = kodeInput.value || 'Kemasan';
                if (previewJumlah) previewJumlah.textContent = jumlahInput.value || '?';
                if (previewSatuan) previewSatuan.textContent = satuanSelect.value || 'Satuan';
            };
            
            // Auto-uppercase kode
            kodeInput?.addEventListener('input', function() {
                this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 8);
                updatePreview();
            });
            
            namaInput?.addEventListener('input', updatePreview);
            jumlahInput?.addEventListener('input', updatePreview);
            satuanSelect?.addEventListener('change', updatePreview);
            
            // Focus on nama input
            setTimeout(() => {
                if (namaInput) {
                    namaInput.focus();
                    namaInput.select();
                }
            }, 100);
        }
    }

    // Hide edit form
    function hideEditForm(id) {
        const viewEl = document.getElementById(`view-${id}`);
        const editEl = document.getElementById(`edit-${id}`);
        
        if (viewEl && editEl) {
            editEl.classList.add('hidden');
            viewEl.classList.remove('hidden');
        }
    }

    // Update konversi on server
    function updateKonversi(id) {
        const namaInput = document.getElementById(`edit-nama-${id}`);
        const kodeInput = document.getElementById(`edit-kode-${id}`);
        const jumlahInput = document.getElementById(`edit-jumlah-${id}`);
        const satuanSelect = document.getElementById(`edit-satuan-${id}`);
        
        if (!namaInput || !kodeInput || !jumlahInput || !satuanSelect) {
            showNotification('Form tidak ditemukan', 'error');
            return;
        }
        
        const nama = namaInput.value.trim();
        const kode = kodeInput.value.trim().toUpperCase();
        const jumlah = parseFloat(jumlahInput.value);
        const satuan = satuanSelect.value;
        
        // Validation
        if (!nama) {
            showNotification('Nama satuan harus diisi', 'error');
            namaInput.focus();
            return;
        }
        
        if (!kode) {
            showNotification('Kode satuan harus diisi', 'error');
            kodeInput.focus();
            return;
        }
        
        if (!jumlah || jumlah <= 0) {
            showNotification('Jumlah harus lebih dari 0', 'error');
            jumlahInput.focus();
            return;
        }
        
        if (!satuan) {
            showNotification('Harap pilih satuan kecil', 'error');
            satuanSelect.focus();
            return;
        }
        
        // Show loading
        const saveBtn = event.target;
        const originalHTML = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<div class="spinner inline-block mr-1" style="width: 16px; height: 16px; border-width: 2px;"></div>Menyimpan...';
        saveBtn.classList.add('opacity-75', 'cursor-not-allowed');
        
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch(`/management/konversi/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                new_satuan_name: nama,
                new_satuan_code: kode,
                jumlah: jumlah,
                satuan_kecil: satuan
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                // Show success animation
                saveBtn.innerHTML = '<i class="fas fa-check mr-1"></i>Tersimpan!';
                saveBtn.classList.add('ripple-effect', 'active');
                
                showNotification('Data berhasil diperbarui!', 'success');
                
                // Reload to show updated data
                setTimeout(() => {
                    location.reload();
                }, 800);
            } else {
                throw new Error((data && data.message) || 'Gagal memperbarui data');
            }
        })
        .catch(err => {
            console.error(err);
            saveBtn.innerHTML = originalHTML;
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            showNotification(err.message || 'Terjadi kesalahan saat memperbarui data', 'error');
        });
    }
</script>
