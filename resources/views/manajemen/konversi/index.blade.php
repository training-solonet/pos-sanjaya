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
                                <div class="flex items-center space-x-2">
                                    <button onclick="swapUnits()"
                                        class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <button onclick="clearAll()"
                                        class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Konvert Konten -->
                        <div class="p-6">
                            <div id="unitManager" class="hidden mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-green-900">Konversi Satuan</h3>
                                    <button onclick="showAddUnitForm()"
                                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Satuan
                                    </button>
                                </div>

                                <!-- Add Unit Form -->
                                <div id="addUnitForm" class="hidden mb-6">
                                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
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

                                        <div class="px-6 py-6">
                                            <div class="space-y-5">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Nama Satuan <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" id="newUnitName" placeholder="Contoh: Krat Telur"
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors">
                                                    <p class="text-xs text-gray-500 mt-1">Nama kemasan yang akan digunakan
                                                    </p>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Kode Singkat <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" id="newUnitCode" placeholder="Contoh: KRAT"
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors uppercase">
                                                    <p class="text-xs text-gray-500 mt-1">Singkatan untuk kemudahan (maks 8
                                                        huruf)</p>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                                        Berapa isi dalam 1 kemasan? <span class="text-red-500">*</span>
                                                    </label>

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
                                                                <option value="pcs">pcs (buah/pieces)</option>
                                                                <option value="kg">kg (kilogram)</option>
                                                                <option value="L">L (liter)</option>
                                                                <option value="m">m (meter)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

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
                                <div id="customUnitsList" class="space-y-2">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>

                            <!-- Result Summary -->
                            <div id="resultSummary" class="mt-6 p-4 bg-green-50 rounded-xl border border-green-100">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-green-600"></i>
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
                        <div id="historyList" class="p-6">
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-history text-3xl mb-3 text-gray-300"></i>
                                <p class="text-sm">Belum ada riwayat konversi</p>
                            </div>
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
        </div>
    </main>
@endsection

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

    // Sidebar toggle function
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar.classList.toggle('show');
        overlay.classList.toggle('hidden');
    }

    // Data konversi satuan
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
        loadHistory();
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
        const fromUnit = document.getElementById('fromUnit').value;
        const toUnit = document.getElementById('toUnit').value;

        document.getElementById('fromUnitLabel').textContent = fromUnit;
        document.getElementById('toUnitLabel').textContent = toUnit;
    }

    function convert() {
        const fromValue = parseFloat(document.getElementById('fromValue').value);
        const fromUnit = document.getElementById('fromUnit').value;
        const toUnit = document.getElementById('toUnit').value;

        updateUnitLabels();

        if (isNaN(fromValue) || fromValue === '') {
            document.getElementById('toValue').value = '';
            document.getElementById('resultSummary').classList.add('hidden');
            return;
        }

        // Only custom units conversion
        const result = convertCustomUnits(fromValue, fromUnit, toUnit);

        document.getElementById('toValue').value = result.toLocaleString('id-ID');

        // Show result summary
        const summary =
        `${fromValue.toLocaleString('id-ID')} ${fromUnit} = ${result.toLocaleString('id-ID')} ${toUnit}`;
        document.getElementById('summaryText').textContent = summary;
        document.getElementById('resultSummary').classList.remove('hidden');

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

        const tempUnit = fromSelect.value;
        fromSelect.value = toSelect.value;
        toSelect.value = tempUnit;

        fromValue.value = toValue.value.replace(/[.,]/g, '');

        convert();
    }

    function clearAll() {
        document.getElementById('fromValue').value = '';
        document.getElementById('toValue').value = '';
        document.getElementById('resultSummary').classList.add('hidden');
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
        document.getElementById('fromUnit').value = fromUnit;
        document.getElementById('toUnit').value = toUnit;
        document.getElementById('fromValue').value = fromValue;

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
    document.getElementById('toValue').addEventListener('click', function() {
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
        document.getElementById('addUnitForm').classList.remove('hidden');
        // Focus on first input
        setTimeout(() => {
            document.getElementById('newUnitName').focus();
        }, 100);
        // Reset form
        resetForm();
    }

    function hideAddUnitForm() {
        document.getElementById('addUnitForm').classList.add('hidden');
        resetForm();
    }

    function resetForm() {
        // Clear form inputs
        document.getElementById('newUnitName').value = '';
        document.getElementById('newUnitCode').value = '';
        document.getElementById('newUnitFactor').value = '';
        document.getElementById('newUnitBase').value = 'pcs';

        // Reset displays
        updateSimpleDisplay();
    }

    function updateSimpleDisplay() {
        const name = document.getElementById('newUnitName').value.trim();
        const factor = document.getElementById('newUnitFactor').value;
        const base = document.getElementById('newUnitBase').value;

        // Update display elements
        document.getElementById('unitNameDisplay').textContent = name || 'Kemasan';
        document.getElementById('factorDisplay').textContent = factor || '?';
        document.getElementById('baseUnitDisplay').textContent = base;
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
            alert('Harap isi nama satuan!');
            document.getElementById('newUnitName').focus();
            return;
        }

        if (!code) {
            alert('Harap isi kode satuan!');
            document.getElementById('newUnitCode').focus();
            return;
        }

        if (!factor || factor <= 0) {
            alert('Harap isi jumlah yang benar (lebih dari 0)!');
            document.getElementById('newUnitFactor').focus();
            return;
        }

        // Check if code already exists
        if (units.custom[code.toLowerCase()]) {
            alert('Kode satuan sudah digunakan! Gunakan kode yang berbeda.');
            document.getElementById('newUnitCode').focus();
            return;
        }

        // Add to custom units
        const newUnit = {
            name: name,
            code: code.toLowerCase(),
            factor: factor,
            baseUnit: baseUnit,
            id: Date.now()
        };

        customUnits.push(newUnit);
        units.custom[code.toLowerCase()] = {
            name: name,
            factor: factor,
            baseUnit: baseUnit
        };

        // Save to localStorage
        localStorage.setItem('customUnits', JSON.stringify(customUnits));

        // Update UI
        loadCustomUnitsList();
        populateUnits();
        hideAddUnitForm();

        // Show success message
        alert(`Satuan "${name}" berhasil ditambahkan!`);
    }

    function loadCustomUnitsList() {
        const listContainer = document.getElementById('customUnitsList');

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
        if (confirm('Hapus satuan kustom ini?')) {
            // Remove from customUnits array
            customUnits = customUnits.filter(unit => unit.code !== code);

            // Remove from units object
            delete units.custom[code];

            // Save to localStorage
            localStorage.setItem('customUnits', JSON.stringify(customUnits));

            // Update UI
            loadCustomUnitsList();
            populateUnits();

            showToast('Satuan kustom berhasil dihapus!', 'success');
        }
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
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.custom-toast');
        existingToasts.forEach(toast => toast.remove());

        const toast = document.createElement('div');
        toast.className = 'custom-toast fixed top-4 right-4 z-50 min-w-[320px] max-w-md';

        let bgColor, iconColor, icon;
        switch (type) {
            case 'success':
                bgColor = 'bg-white border-l-4 border-green-500 shadow-lg';
                iconColor = 'text-green-500';
                icon = 'fas fa-check-circle';
                break;
            case 'error':
                bgColor = 'bg-white border-l-4 border-red-500 shadow-lg';
                iconColor = 'text-red-500';
                icon = 'fas fa-exclamation-circle';
                break;
            default:
                bgColor = 'bg-white border-l-4 border-blue-500 shadow-lg';
                iconColor = 'text-blue-500';
                icon = 'fas fa-info-circle';
        }

        toast.innerHTML = `
                <div class="${bgColor} rounded-lg p-4 animate-slide-in">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <i class="${icon} ${iconColor} text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">${message}</p>
                        </div>
                        <button onclick="this.closest('.custom-toast').remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            `;

        document.body.appendChild(toast);

        // Auto remove after delay
        setTimeout(() => {
            if (document.body.contains(toast)) {
                toast.style.transform = 'translateX(100%)';
                toast.style.opacity = '0';
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }
        }, type === 'error' ? 5000 : 3000);
    }
</script>
