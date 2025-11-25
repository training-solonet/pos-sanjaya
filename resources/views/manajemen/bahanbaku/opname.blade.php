<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>POS Sanjaya - Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#3B82F6",
            secondary: "#1E40AF",
            accent: "#F59E0B",
            success: "#10B981",
            danger: "#EF4444",
            dark: "#1F2937",
          },
        },
      },
    };
  </script>
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");

    body {
      font-family: "Inter", sans-serif;
    }

    .scrollbar-hide {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    .scrollbar-hide::-webkit-scrollbar {
      display: none;
    }

    /* Responsive sidebar styles */
    @media (max-width: 1023px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar:not(.-translate-x-full) {
        transform: translateX(0);
      }
    }

    @media (min-width: 1024px) {
      .sidebar {
        transform: translateX(0) !important;
      }
    }
  </style>
</head>

<body class="bg-gray-50 min-h-screen lg:flex">
  <!-- Mobile Overlay -->
  <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()">
  </div>

  <!-- Sidebar -->
   @include('layouts.manajemen.sidebar')

  <!-- Sidebar Overlay for Mobile -->
  <div id="sidebarOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()">
  </div>

  <!-- Main Content -->
  <div class="content flex-1 lg:flex-1">
    <!-- Header -->
     @include('layouts.manajemen.header')
              <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="space-y-6">
                <!-- Page Header dengan Back Button dan Action Buttons -->
                <div class="flex flex-col gap-4">
                    <!-- Back Button -->
                    <div>
                        <a href="{{ route("management.bahanbaku.index") }}" 
                            class="inline-flex items-center px-3 py-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Stok Bahan
                        </a>
                    </div>
                    
                    <!-- Title and Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <!-- Page Title -->
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                Stok Opname Bahan Baku
                            </h1>
                            <p class="text-gray-600 mt-1">
                                Kelola dan monitor stok fisik bahan baku vs sistem
                            </p>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <button onclick="startNewOpname()"
                                class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-plus"></i>
                                <span>Mulai Opname Baru</span>
                            </button>
                            <button onclick="showHistory()"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-history"></i>
                                <span>Riwayat</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Bahan Baku</p>
                                <p class="text-2xl font-bold text-gray-900">6</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-boxes text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Sudah Dihitung</p>
                                <p class="text-2xl font-bold text-green-600">2</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Selisih Ditemukan</p>
                                <p class="text-2xl font-bold text-red-600">1</p>
                            </div>
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Progress</p>
                                <p class="text-2xl font-bold text-blue-600">33%</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-pie text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <i
                                    class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="searchInput" placeholder="Cari bahan baku atau scan barcode..."
                                    class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors" />
                                <button class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                    <i class="fas fa-qrcode text-gray-400 hover:text-green-600 transition-colors"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <select id="categoryFilter"
                                class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400">
                                <option value="">Semua Kategori</option>
                                <option value="bahan utama">Bahan Utama</option>
                                <option value="bahan pembantu">Bahan Pembantu</option>
                            </select>
                            <select id="statusFilter"
                                class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400">
                                <option value="">Semua Status</option>
                                <option value="pending">Belum Dihitung</option>
                                <option value="counted">Sudah Dihitung</option>
                                <option value="discrepancy">Ada Selisih</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Product List -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="p-6 border-b">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Daftar Bahan Baku
                            </h3>
                            <div class="flex items-center space-x-2">
                                <button onclick="toggleView('grid')" id="gridViewBtn"
                                    class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors">
                                    <i class="fas fa-th-large"></i>
                                </button>
                                <button onclick="toggleView('list')" id="listViewBtn"
                                    class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Grid View -->
                    <div id="gridView" class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        <!-- Product Items -->
                        <div class="product-item border border-gray-200 rounded-xl p-4 hover:border-green-400 transition-all cursor-pointer"
                            data-status="counted" onclick="openCountModal('Beras Premium', 'beras-premium', 50, 50)">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">
                                        Tepung Protein Tinggi
                                    </h4>
                                    <p class="text-sm text-gray-500">SKU: BR-001</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Kategori: Bahan Utama
                                    </p>
                                </div>
                                <span
                                    class="status-badge bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                    <i class="fas fa-check mr-1"></i>Selesai
                                </span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Stok Sistem:</span>
                                    <span class="font-medium">50 kg</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Stok Fisik:</span>
                                    <span class="font-medium text-green-600">50 kg</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Selisih:</span>
                                    <span class="font-medium text-green-600">0 kg</span>
                                </div>
                            </div>
                        </div>

                        <div class="product-item border border-gray-200 rounded-xl p-4 hover:border-green-400 transition-all cursor-pointer"
                            data-status="discrepancy" onclick="openCountModal('Daging Ayam', 'daging-ayam', 20, 18)">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">Ragi Instan</h4>
                                    <p class="text-sm text-gray-500">SKU: DA-001</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Kategori: Bahan Utama
                                    </p>
                                </div>
                                <span
                                    class="status-badge bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Selisih
                                </span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Stok Sistem:</span>
                                    <span class="font-medium">20 kg</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Stok Fisik:</span>
                                    <span class="font-medium text-red-600">18 kg</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Selisih:</span>
                                    <span class="font-medium text-red-600">-2 kg</span>
                                </div>
                            </div>
                        </div>

                        <div class="product-item border border-gray-200 rounded-xl p-4 hover:border-green-400 transition-all cursor-pointer"
                            data-status="pending" onclick="openCountModal('Cabai Merah', 'cabai-merah', 5, null)">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">Gula Pasir</h4>
                                    <p class="text-sm text-gray-500">SKU: CM-001</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Kategori: Bahan Utama
                                    </p>
                                </div>
                                <span
                                    class="status-badge bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Stok Sistem:</span>
                                    <span class="font-medium">5 kg</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Stok Fisik:</span>
                                    <span class="font-medium text-gray-400">Belum dihitung</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Selisih:</span>
                                    <span class="font-medium text-gray-400">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="product-item border border-gray-200 rounded-xl p-4 hover:border-green-400 transition-all cursor-pointer"
                            data-status="counted" onclick="openCountModal('Minyak Goreng', 'minyak-goreng', 10, 12)">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">Minyak Goreng</h4>
                                    <p class="text-sm text-gray-500">SKU: MG-001</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Kategori: Bahan Pembantu
                                    </p>
                                </div>
                                <span
                                    class="status-badge bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                    <i class="fas fa-plus mr-1"></i>Lebih
                                </span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Stok Sistem:</span>
                                    <span class="font-medium">10 liter</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Stok Fisik:</span>
                                    <span class="font-medium text-blue-600">12 liter</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Selisih:</span>
                                    <span class="font-medium text-blue-600">+2 liter</span>
                                </div>
                            </div>
                        </div>

                        <div class="product-item border border-gray-200 rounded-xl p-4 hover:border-green-400 transition-all cursor-pointer"
                            data-status="pending" onclick="openCountModal('Bawang Merah', 'bawang-merah', 8, null)">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">Mentega</h4>
                                    <p class="text-sm text-gray-500">SKU: BM-001</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Kategori: Bahan Pembantu
                                    </p>
                                </div>
                                <span
                                    class="status-badge bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Stok Sistem:</span>
                                    <span class="font-medium">8 kg</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Stok Fisik:</span>
                                    <span class="font-medium text-gray-400">Belum dihitung</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Selisih:</span>
                                    <span class="font-medium text-gray-400">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="product-item border border-gray-200 rounded-xl p-4 hover:border-green-400 transition-all cursor-pointer"
                            data-status="pending" onclick="openCountModal('Garam Dapur', 'garam-dapur', 3, null)">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">Garam</h4>
                                    <p class="text-sm text-gray-500">SKU: GD-001</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Kategori: Bahan Pembantu
                                    </p>
                                </div>
                                <span
                                    class="status-badge bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Stok Sistem:</span>
                                    <span class="font-medium">3 kg</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Stok Fisik:</span>
                                    <span class="font-medium text-gray-400">Belum dihitung</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Selisih:</span>
                                    <span class="font-medium text-gray-400">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List View (Hidden by default) -->
                    <div id="listView" class="hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Bahan Baku
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            SKU
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Kategori
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Stok Sistem
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Stok Fisik
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Selisih
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            Beras Premium
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">BR-001</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Sembako</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">50 kg</td>
                                        <td class="px-6 py-4 text-sm text-green-600">50 kg</td>
                                        <td class="px-6 py-4 text-sm text-green-600">0 kg</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Selesai</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button onclick="openCountModal('Beras Premium', 'beras-premium', 50, 50)"
                                                class="text-green-600 hover:text-green-800">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- More table rows... -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Count Modal -->
    <div id="countModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Hitung Stok</h3>
                        <button onclick="closeCountModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <h4 id="productName" class="font-semibold text-gray-900"></h4>
                            <p id="productSku" class="text-sm text-gray-500"></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Stok Sistem:</span>
                                <span id="systemStock" class="font-medium"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Stok Fisik Saat Ini:</span>
                                <span id="currentPhysicalStock" class="font-medium"></span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Stok Fisik</label>
                            <div class="flex items-center space-x-3">
                                <button onclick="decreaseCount()"
                                    class="w-10 h-10 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="physicalCount" min="0"
                                    class="flex-1 text-center p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400 text-lg font-medium" />
                                <button onclick="increaseCount()"
                                    class="w-10 h-10 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div id="differenceDisplay" class="bg-blue-50 rounded-lg p-4 hidden">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-700">Selisih:</span>
                                <span id="difference" class="font-medium text-blue-700"></span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                            <textarea id="countNotes" rows="3" placeholder="Tambahkan catatan tentang kondisi stok..."
                                class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400"></textarea>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t">
                    <div class="flex space-x-3">
                        <button onclick="closeCountModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button onclick="saveCount()"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Modal -->
    <div id="historyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Riwayat Stok Opname
                        </h3>
                        <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div class="space-y-4">
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-semibold text-gray-900">
                                        Opname Bahan Baku - 1 Oktober 2025
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        Dimulai: 08:00 WIB | Selesai: 10:30 WIB
                                    </p>
                                </div>
                                <span
                                    class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Selesai</span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Total Bahan Baku</p>
                                    <p class="text-lg font-semibold">35</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Selisih Found</p>
                                    <p class="text-lg font-semibold text-red-600">8</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Nilai Selisih</p>
                                    <p class="text-lg font-semibold text-red-600">Rp 250.000</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Petugas</p>
                                    <p class="text-lg font-semibold">Admin</p>
                                </div>
                            </div>
                        </div>
                        <!-- More history items... -->
                    </div>
                </div>
            </div>
        </div>
    </div>

   <script>
    let sidebarOpen = false;
    let currentView = "grid";
    let currentProduct = null;
    let allProducts = [];

    // CSRF Token untuk AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Load data dari server
    async function loadData() {
        try {
            const response = await fetch('/management/opname?ajax=1');
            const result = await response.json();
            
            if (result.success) {
                allProducts = result.data;
                renderProducts();
                loadSummary();
            } else {
                console.error('Gagal memuat data:', result.message);
            }
        } catch (error) {
            console.error('Error loading data:', error);
        }
    }

    // Load summary data
    async function loadSummary() {
        try {
            const response = await fetch('/management/opname/summary');
            const result = await response.json();
            
            if (result.success) {
                updateStatistics(result);
            }
        } catch (error) {
            console.error('Error loading summary:', error);
        }
    }

    // Render products ke grid view
    function renderProducts() {
        const gridView = document.getElementById('gridView');
        gridView.innerHTML = '';

        allProducts.forEach(product => {
            const status = product.status;
            const statusConfig = getStatusConfig(status);
            const selisih = product.selisih !== null ? product.selisih : null;
            const selisihText = selisih !== null ? 
                `${selisih > 0 ? '+' : ''}${selisih} ${product.satuan}` : '-';
            const selisihClass = selisih > 0 ? 'text-blue-600' : 
                               selisih < 0 ? 'text-red-600' : 'text-green-600';

            const productItem = document.createElement('div');
            productItem.className = `product-item border border-gray-200 rounded-xl p-4 hover:border-green-400 transition-all cursor-pointer`;
            productItem.setAttribute('data-status', status);
            productItem.setAttribute('data-id', product.id);
            productItem.onclick = () => openCountModal(product);

            productItem.innerHTML = `
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">${product.nama}</h4>
                        <p class="text-sm text-gray-500">SKU: ${product.kode}</p>
                        <p class="text-sm text-gray-600 mt-1">Kategori: ${product.kategori}</p>
                    </div>
                    <span class="status-badge ${statusConfig.badgeClass} px-2 py-1 rounded-full text-xs font-medium">
                        <i class="${statusConfig.icon} mr-1"></i>${statusConfig.text}
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Stok Sistem:</span>
                        <span class="font-medium">${product.stok_sistem} ${product.satuan}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Stok Fisik:</span>
                        <span class="font-medium ${product.stok_fisik !== null ? statusConfig.stockClass : 'text-gray-400'}">
                            ${product.stok_fisik !== null ? `${product.stok_fisik} ${product.satuan}` : 'Belum dihitung'}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Selisih:</span>
                        <span class="font-medium ${product.stok_fisik !== null ? selisihClass : 'text-gray-400'}">
                            ${selisihText}
                        </span>
                    </div>
                </div>
            `;

            gridView.appendChild(productItem);
        });
    }

    // Get status configuration
    function getStatusConfig(status) {
        const configs = {
            'pending': {
                badgeClass: 'bg-yellow-100 text-yellow-800',
                icon: 'fas fa-clock',
                text: 'Pending',
                stockClass: 'text-gray-400'
            },
            'counted': {
                badgeClass: 'bg-green-100 text-green-800',
                icon: 'fas fa-check',
                text: 'Selesai',
                stockClass: 'text-green-600'
            },
            'discrepancy': {
                badgeClass: 'bg-red-100 text-red-800',
                icon: 'fas fa-exclamation-triangle',
                text: 'Selisih',
                stockClass: 'text-red-600'
            }
        };
        return configs[status] || configs.pending;
    }

    // Update statistics
    function updateStatistics(summary) {
        document.querySelector('.grid.grid-cols-1.md\\:grid-cols-4 .bg-white:nth-child(1) .text-2xl').textContent = summary.total_bahan;
        document.querySelector('.grid.grid-cols-1.md\\:grid-cols-4 .bg-white:nth-child(2) .text-2xl').textContent = summary.dihitung;
        document.querySelector('.grid.grid-cols-1.md\\:grid-cols-4 .bg-white:nth-child(3) .text-2xl').textContent = summary.selisih;
        document.querySelector('.grid.grid-cols-1.md\\:grid-cols-4 .bg-white:nth-child(4) .text-2xl').textContent = `${summary.progress}%`;
    }

    // Update date time
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        };
        document.getElementById("currentDateTime").textContent =
            now.toLocaleDateString("id-ID", options);
    }

    // Toggle sidebar
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("mobileOverlay");

        if (sidebarOpen) {
            sidebar.classList.add("-translate-x-full");
            overlay.classList.add("hidden");
        } else {
            sidebar.classList.remove("-translate-x-full");
            overlay.classList.remove("hidden");
        }
        sidebarOpen = !sidebarOpen;
    }

    // Toggle view between grid and list
    function toggleView(view) {
        const gridView = document.getElementById("gridView");
        const listView = document.getElementById("listView");
        const gridBtn = document.getElementById("gridViewBtn");
        const listBtn = document.getElementById("listViewBtn");

        if (view === "grid") {
            gridView.classList.remove("hidden");
            listView.classList.add("hidden");
            gridBtn.classList.add("bg-green-100", "text-green-600");
            gridBtn.classList.remove("bg-gray-100", "text-gray-600");
            listBtn.classList.add("bg-gray-100", "text-gray-600");
            listBtn.classList.remove("bg-green-100", "text-green-600");
        } else {
            gridView.classList.add("hidden");
            listView.classList.remove("hidden");
            listBtn.classList.add("bg-green-100", "text-green-600");
            listBtn.classList.remove("bg-gray-100", "text-gray-600");
            gridBtn.classList.add("bg-gray-100", "text-gray-600");
            gridBtn.classList.remove("bg-green-100", "text-green-600");
        }
        currentView = view;
    }

    // Count Modal Functions
    function openCountModal(product) {
        currentProduct = product;

        document.getElementById("productName").textContent = product.nama;
        document.getElementById("productSku").textContent = `SKU: ${product.kode}`;
        document.getElementById("systemStock").textContent = `${product.stok_sistem} ${product.satuan}`;
        document.getElementById("currentPhysicalStock").textContent = 
            product.stok_fisik !== null ? `${product.stok_fisik} ${product.satuan}` : "Belum dihitung";
        document.getElementById("physicalCount").value = product.stok_fisik || "";

        updateDifference();
        document.getElementById("countModal").classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeCountModal() {
        document.getElementById("countModal").classList.add("hidden");
        document.body.style.overflow = "";
        document.getElementById("countNotes").value = "";
        currentProduct = null;
    }

    function increaseCount() {
        const input = document.getElementById("physicalCount");
        const currentValue = parseInt(input.value) || 0;
        input.value = currentValue + 1;
        updateDifference();
    }

    function decreaseCount() {
        const input = document.getElementById("physicalCount");
        const currentValue = parseInt(input.value) || 0;
        if (currentValue > 0) {
            input.value = currentValue - 1;
            updateDifference();
        }
    }

    function updateDifference() {
        const physicalCount = parseInt(document.getElementById("physicalCount").value) || 0;
        const systemStock = currentProduct ? currentProduct.stok_sistem : 0;
        const difference = physicalCount - systemStock;
        const unit = currentProduct ? currentProduct.satuan : "unit";

        const differenceDisplay = document.getElementById("differenceDisplay");
        const differenceSpan = document.getElementById("difference");

        if (physicalCount !== 0) {
            differenceDisplay.classList.remove("hidden");
            differenceSpan.textContent = `${difference > 0 ? "+" : ""}${difference} ${unit}`;

            if (difference > 0) {
                differenceDisplay.className = "bg-blue-50 rounded-lg p-4";
                differenceSpan.className = "font-medium text-blue-700";
            } else if (difference < 0) {
                differenceDisplay.className = "bg-red-50 rounded-lg p-4";
                differenceSpan.className = "font-medium text-red-700";
            } else {
                differenceDisplay.className = "bg-green-50 rounded-lg p-4";
                differenceSpan.className = "font-medium text-green-700";
            }
        } else {
            differenceDisplay.classList.add("hidden");
        }
    }

    // Save count to server
    async function saveCount() {
        const physicalCount = parseInt(document.getElementById("physicalCount").value);
        const notes = document.getElementById("countNotes").value;

        if (isNaN(physicalCount) || physicalCount < 0) {
            alert("Jumlah stok fisik harus berupa angka yang valid!");
            return;
        }

        try {
            const response = await fetch('/management/opname', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id_bahan: currentProduct.id,
                    tgl: new Date().toISOString().split('T')[0],
                    stok_fisik: physicalCount,
                    catatan: notes
                })
            });

            const result = await response.json();

            if (result.success) {
                closeCountModal();
                showSuccessMessage("Stok berhasil disimpan!");
                // Reload data untuk update tampilan
                loadData();
            } else {
                alert('Gagal menyimpan data: ' + result.message);
            }
        } catch (error) {
            console.error('Error saving count:', error);
            alert('Terjadi kesalahan saat menyimpan data');
        }
    }

    // Start new opname session
    async function startNewOpname() {
        if (confirm("Apakah Anda yakin ingin memulai stok opname baru? Ini akan mereset semua data yang belum disimpan.")) {
            try {
                const response = await fetch('/management/opname/session/new', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        tanggal: new Date().toISOString().split('T')[0]
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showSuccessMessage("Sesi opname baru berhasil dimulai!");
                    // Reload data untuk update tampilan
                    loadData();
                } else {
                    alert('Gagal memulai sesi baru: ' + result.message);
                }
            } catch (error) {
                console.error('Error starting new opname:', error);
                alert('Terjadi kesalahan saat memulai sesi baru');
            }
        }
    }

    // Search and filter functions
    document.getElementById("searchInput").addEventListener("input", function () {
        const searchTerm = this.value.toLowerCase();
        const productItems = document.querySelectorAll(".product-item");

        productItems.forEach((item) => {
            const productName = item.querySelector("h4").textContent.toLowerCase();
            const sku = item.querySelector("p").textContent.toLowerCase();

            if (productName.includes(searchTerm) || sku.includes(searchTerm)) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });
    });

    document.getElementById("categoryFilter").addEventListener("change", function () {
        const category = this.value.toLowerCase();
        const productItems = document.querySelectorAll(".product-item");

        productItems.forEach((item) => {
            const categoryElements = item.querySelectorAll("p");
            let itemCategory = "";

            categoryElements.forEach((p) => {
                if (p.textContent.includes("Kategori:")) {
                    itemCategory = p.textContent
                        .replace("Kategori:", "")
                        .trim()
                        .toLowerCase();
                }
            });

            const categoryMatch = category === "" || itemCategory.includes(category);

            if (categoryMatch) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });
    });

    document.getElementById("statusFilter").addEventListener("change", function () {
        const status = this.value;
        const productItems = document.querySelectorAll(".product-item");

        productItems.forEach((item) => {
            if (status === "" || item.getAttribute("data-status") === status) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });
    });

    function showSuccessMessage(message) {
        const notification = document.createElement("div");
        notification.className = "fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50";
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                ${message}
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Close modal when clicking outside
    document.getElementById("countModal").addEventListener("click", function (e) {
        if (e.target === this) {
            closeCountModal();
        }
    });

    document.getElementById("historyModal").addEventListener("click", function (e) {
        if (e.target === this) {
            closeHistoryModal();
        }
    });

    // Listen to input changes
    document.getElementById("physicalCount").addEventListener("input", updateDifference);

    // Initialize
    document.addEventListener("DOMContentLoaded", function () {
        updateDateTime();
        setInterval(updateDateTime, 60000);
        loadData(); // Load data saat halaman dimuat

        // Initialize mobile state
        if (window.innerWidth < 1024) {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("mobileOverlay");
            sidebar.classList.add("-translate-x-full");
            overlay.classList.add("hidden");
        }
    });
</script>
</body>

</html>