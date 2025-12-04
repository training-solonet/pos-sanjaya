@extends('layouts.manajemen.index')

@php
    \Carbon\Carbon::setLocale('id');
@endphp

@section('content')
    <!-- Main Content -->
    <div class="content flex-1 lg:flex-1">

        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-2xl font-bold text-gray-900">Stok Produk Jadi</h2>
                    <div class="flex gap-2">
                        <a href="{{ route('management.updateproduk.index') }}"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                            <i class="fas fa-clipboard-check mr-2"></i>Stok Produk
                        </a>
                        <button onclick="openAddProductModal()"
                            class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all flex items-center">
                            <i class="fas fa-plus mr-2"></i> Produk Baru
                        </button>
                    </div>
                </div>

                <!-- Search & Filter -->
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" id="searchInput" placeholder="Cari produk..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">Semua Status</option>
                            <option value="tersedia">Tersedia</option>
                            <option value="rendah">Stok Rendah</option>
                            <option value="habis">Habis</option>
                        </select>
                    </div>
                </div>

                <!-- Product Table -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min. Stok
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expired Date</th>
                                    <th class="px6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200" id="productTableBody">
                                @foreach ($produk as $item)
                                    @php
                                        // PERBAIKAN 1: Bulatkan sisa_hari ke bawah (floor)
                                        $daysUntilExpired = floor($item->sisa_hari);
                                        $isExpired = $daysUntilExpired < 0;
                                        $isExpiredToday = $daysUntilExpired === 0;
                                        $isExpiredSoon = $daysUntilExpired > 0 && $daysUntilExpired <= 7;
                                        $stockStatus = $item->status_stok;
                                        
                                        // Tentukan warna dan ikon berdasarkan status stok
                                        if ($stockStatus === 'habis') {
                                            $stockColor = 'text-red-600 font-medium';
                                            $stockBg = 'bg-red-100';
                                            $stockIcon = 'fas fa-times-circle';
                                            $stockText = 'Habis';
                                        } elseif ($stockStatus === 'rendah') {
                                            $stockColor = 'text-orange-600 font-medium';
                                            $stockBg = 'bg-orange-100';
                                            $stockIcon = 'fas fa-exclamation-triangle';
                                            $stockText = 'Rendah';
                                        } else {
                                            $stockColor = 'text-green-600 font-medium';
                                            $stockBg = 'bg-green-100';
                                            $stockIcon = 'fas fa-check-circle';
                                            $stockText = 'Aman';
                                        }
                                        
                                        // Tentukan warna dan ikon berdasarkan status kadaluarsa
                                        if ($isExpired) {
                                            $expiryColor = 'text-red-600 font-medium';
                                            $expiryBg = 'bg-red-100';
                                            $expiryIcon = 'fas fa-exclamation-triangle';
                                            $expiryText = 'Expired';
                                        } elseif ($isExpiredToday) {
                                            $expiryColor = 'text-orange-600 font-medium';
                                            $expiryBg = 'bg-orange-100';
                                            $expiryIcon = 'fas fa-exclamation-circle';
                                            $expiryText = 'Hari Ini';
                                        } elseif ($isExpiredSoon) {
                                            $expiryColor = 'text-orange-600 font-medium';
                                            $expiryBg = 'bg-orange-100';
                                            $expiryIcon = 'fas fa-clock';
                                            // PERBAIKAN 2: Format sederhana "X hari"
                                            $expiryText = $daysUntilExpired . ' hari';
                                        } else {
                                            $expiryColor = 'text-green-600 font-medium';
                                            $expiryBg = 'bg-green-100';
                                            $expiryIcon = 'fas fa-check-circle';
                                            // PERBAIKAN 2: Format sederhana "X hari"
                                            $expiryText = $daysUntilExpired . ' hari';
                                        }
                                    @endphp
                                    
                                    <tr class="product-row"
                                        data-status="{{ $stockStatus }}"
                                        data-expiry="{{ $isExpired ? 'expired' : ($isExpiredToday ? 'today' : ($isExpiredSoon ? 'soon' : 'safe')) }}">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                @php
                                                    $iconColor = 'amber';
                                                    $iconClass = 'fas fa-bread-slice';

                                                    // Determine icon based on product name
                                                    if (
                                                        str_contains(strtolower($item->nama), 'cokelat') ||
                                                        str_contains(strtolower($item->nama), 'chocolate')
                                                    ) {
                                                        $iconColor = 'orange';
                                                        $iconClass = 'fas fa-cookie-bite';
                                                    } elseif (
                                                        str_contains(strtolower($item->nama), 'keju') ||
                                                        str_contains(strtolower($item->nama), 'cheese')
                                                    ) {
                                                        $iconColor = 'yellow';
                                                        $iconClass = 'fas fa-cheese';
                                                    } elseif (str_contains(strtolower($item->nama), 'strawberry')) {
                                                        $iconColor = 'pink';
                                                        $iconClass = 'fas fa-ice-cream';
                                                    } elseif (
                                                        str_contains(strtolower($item->nama), 'kismis') ||
                                                        str_contains(strtolower($item->nama), 'raisin')
                                                    ) {
                                                        $iconColor = 'purple';
                                                        $iconClass = 'fas fa-candy-cane';
                                                    } elseif (
                                                        str_contains(strtolower($item->nama), 'pisang') ||
                                                        str_contains(strtolower($item->nama), 'banana')
                                                    ) {
                                                        $iconColor = 'yellow';
                                                        $iconClass = 'fas fa-drumstick-bite';
                                                    } elseif (
                                                        str_contains(strtolower($item->nama), 'donat') ||
                                                        str_contains(strtolower($item->nama), 'donut')
                                                    ) {
                                                        $iconColor = 'brown';
                                                        $iconClass = 'fas fa-circle';
                                                    } elseif (str_contains(strtolower($item->nama), 'abon')) {
                                                        $iconColor = 'red';
                                                        $iconClass = 'fas fa-hotdog';
                                                    } elseif (str_contains(strtolower($item->nama), 'sobek')) {
                                                        $iconColor = 'amber';
                                                        $iconClass = 'fas fa-bread-slice';
                                                    } elseif (str_contains(strtolower($item->nama), 'croissant')) {
                                                        $iconColor = 'orange';
                                                        $iconClass = 'fas fa-moon';
                                                    }
                                                @endphp
                                                <div
                                                    class="w-10 h-10 bg-{{ $iconColor }}-100 rounded-lg flex items-center justify-center">
                                                    <i class="{{ $iconClass }} text-{{ $iconColor }}-600"></i>
                                                </div>
                                                <div>
                                                    <!-- PERBAIKAN 3: Hapus ID di bawah nama produk -->
                                                    <span class="font-medium text-gray-900 block">{{ $item->nama }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-2">
                                                <span class="{{ $stockColor }}">{{ $item->stok }}</span>
                                                <span class="px-2 py-1 text-xs rounded-full {{ $stockBg }} {{ $stockColor }}">
                                                    <i class="{{ $stockIcon }} mr-1"></i>{{ $stockText }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->min_stok }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-2">
                                                <div>
                                                    <span class="text-sm {{ $expiryColor }}">
                                                        {{ \Carbon\Carbon::parse($item->kadaluarsa)->format('d M Y') }}
                                                    </span>
                                                    <div class="text-xs {{ $expiryColor }}">
                                                        @if($isExpired)
                                                            Sudah expired
                                                        @elseif($isExpiredToday)
                                                            Kadaluarsa hari ini
                                                        @else
                                                            <!-- PERBAIKAN 4: Format sederhana "X hari lagi" -->
                                                            {{ $daysUntilExpired }} hari lagi
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="px-2 py-1 text-xs rounded-full {{ $expiryBg }} {{ $expiryColor }}">
                                                    <i class="{{ $expiryIcon }}"></i>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-2">
                                                <!-- Tombol Detail -->
                                                <button onclick="showProductDetail({{ $item->id }})"
                                                    class="text-blue-600 hover:text-blue-700" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <!-- Tombol Edit -->
                                                <button onclick="openEditProductModal({{ $item->id }})"
                                                    class="text-green-600 hover:text-green-700" title="Edit Produk">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <!-- Tombol Hapus -->
                                                <button onclick="deleteProduct({{ $item->id }})"
                                                    class="text-red-600 hover:text-red-700" title="Hapus Produk">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Product Modal -->
    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg w-full max-w-md">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-semibold">Tambah Produk Baru</h3>
                    <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="addProductForm" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" name="nama" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stok Awal</label>
                            <input type="number" name="stok" value="0" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min. Stok</label>
                            <input type="number" name="min_stok" value="0" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual</label>
                        <input type="number" name="harga" value="0" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kedaluwarsa</label>
                        <input type="date" name="kadaluarsa" required min="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeAddModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg w-full max-w-md">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-semibold">Edit Produk</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="editProductForm" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" name="nama" id="edit_nama" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stok</label>
                            <input type="number" name="stok" id="edit_stok" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min. Stok</label>
                            <input type="number" name="min_stok" id="edit_min_stok" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual</label>
                        <input type="number" name="harga" id="edit_harga" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kedaluwarsa</label>
                        <input type="date" name="kadaluarsa" id="edit_kadaluarsa" required min="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeEditModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Produk -->
    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-semibold">Detail Produk</h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="detailContent" class="p-6">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Format date function untuk modal detail
    function formatDateForDetail(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 
                       'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const dayName = days[date.getDay()];
        const day = date.getDate();
        const monthName = months[date.getMonth()];
        const year = date.getFullYear();
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        
        return `${dayName}, ${day} ${monthName} ${year} ${hours}:${minutes}`;
    }

    // Format simple date untuk riwayat
    function formatSimpleDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        const day = date.getDate();
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 
                       'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const monthName = months[date.getMonth()];
        const year = date.getFullYear();
        
        return `${day} ${monthName} ${year}`;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Tampilkan notifikasi jika ada
        showNotifications();
        
        // Filter products
        filterProducts();
    });

    // Tampilkan notifikasi dari session
    function showNotifications() {
        @if(session('notifications'))
            const notifications = @json(session('notifications'));
            
            notifications.forEach(notification => {
                setTimeout(() => {
                    Swal.fire({
                        title: notification.title,
                        html: `
                            <div class="text-left">
                                <p class="mb-2">${notification.message}</p>
                                ${notification.products.length > 0 ? 
                                    `<div class="mt-3">
                                        <p class="text-sm font-medium mb-1">Daftar Produk:</p>
                                        <ul class="list-disc pl-5 text-sm">
                                            ${notification.products.map(product => `<li>${product}</li>`).join('')}
                                        </ul>
                                    </div>` : ''
                                }
                            </div>
                        `,
                        icon: notification.type === 'danger' ? 'error' : 'warning',
                        confirmButtonColor: notification.type === 'danger' ? '#EF4444' : '#F59E0B',
                        confirmButtonText: 'Mengerti'
                    });
                }, 500);
            });
        @endif
    }

    // Modal functions
    function openAddProductModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.getElementById('addProductForm').reset();
    }

    function openEditProductModal(productId) {
        fetch(`/management/produk/${productId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Produk tidak ditemukan');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }

                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_nama').value = data.nama;
                document.getElementById('edit_stok').value = data.stok;
                document.getElementById('edit_min_stok').value = data.min_stok;
                document.getElementById('edit_harga').value = data.harga;
                document.getElementById('edit_kadaluarsa').value = data.kadaluarsa;

                document.getElementById('editModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', error.message || 'Gagal memuat data produk', 'error');
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // Show product detail - PERBAIKAN BESAR DI SINI
    function showProductDetail(productId) {
        // Show loading
        Swal.fire({
            title: 'Memuat...',
            text: 'Sedang memuat detail produk...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`/management/produk/${productId}`)
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.error) {
                    Swal.fire('Error', data.error, 'error');
                    return;
                }
                
                const product = data;
                // PERBAIKAN: Bulatkan sisa_hari untuk modal detail
                const sisaHariBulat = Math.floor(product.sisa_hari);
                
                let content = `
                    <div class="space-y-6">
                        <!-- Product Header - PERBAIKAN: Hapus ID Produk -->
                        <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-lg p-6 text-white">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div>
                                    <h2 class="text-2xl font-bold">${product.nama}</h2>
                                </div>
                                <div class="text-right">
                                    <p class="text-3xl font-bold">${product.stok} pcs</p>
                                    <p class="text-blue-100">Stok Tersedia</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Product Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Stok Minimum</p>
                                        <p class="text-lg font-bold text-gray-900">${product.min_stok} pcs</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-tag text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Harga Jual</p>
                                        <p class="text-lg font-bold text-gray-900">Rp ${parseInt(product.harga).toLocaleString('id-ID')}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-calendar-times text-orange-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Kadaluarsa</p>
                                        <p class="text-lg font-bold ${sisaHariBulat < 0 ? 'text-red-600' : (sisaHariBulat <= 7 ? 'text-orange-600' : 'text-gray-900')}">
                                            ${formatSimpleDate(product.kadaluarsa)}
                                            <br>
                                            <span class="text-sm ${sisaHariBulat < 0 ? 'text-red-500' : (sisaHariBulat <= 7 ? 'text-orange-500' : 'text-gray-500')}">
                                                ${sisaHariBulat < 0 ? 'Sudah kadaluarsa' : (sisaHariBulat == 0 ? 'Kadaluarsa hari ini' : `${sisaHariBulat} hari lagi`)}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-${product.status_stok === 'habis' ? 'red' : (product.status_stok === 'rendah' ? 'orange' : 'green')}-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas ${product.status_stok === 'habis' ? 'fa-times-circle' : (product.status_stok === 'rendah' ? 'fa-exclamation-triangle' : 'fa-check-circle')} text-${product.status_stok === 'habis' ? 'red' : (product.status_stok === 'rendah' ? 'orange' : 'green')}-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Status Stok</p>
                                        <p class="text-lg font-bold text-${product.status_stok === 'habis' ? 'red' : (product.status_stok === 'rendah' ? 'orange' : 'green')}-600">
                                            ${product.status_stok === 'habis' ? 'Habis' : (product.status_stok === 'rendah' ? 'Rendah' : 'Aman')}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                `;
                
                // Add stock history if available
                if (product.update_stok_history && product.update_stok_history.length > 0) {
                    content += `
                        <!-- Stock History -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Stok Masuk</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Awal</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Masuk</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Stok</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kadaluarsa</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                    `;
                    
                    product.update_stok_history.forEach(history => {
                        // PERBAIKAN: Format tanggal dengan benar dan bulatkan hari
                        const kadaluarsaDate = new Date(history.kadaluarsa);
                        const now = new Date();
                        const diffTime = kadaluarsaDate - now;
                        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                        
                        content += `
                            <tr class="border-t border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">
                                    ${formatDateForDetail(history.tanggal_update)}
                                </td>
                                <td class="px-4 py-3 text-sm">${history.stok_awal} pcs</td>
                                <td class="px-4 py-3 text-sm text-green-600 font-medium">+${history.stok_baru} pcs</td>
                                <td class="px-4 py-3 text-sm font-bold">${history.total_stok} pcs</td>
                                <td class="px-4 py-3 text-sm">
                                    <div>
                                        ${formatSimpleDate(history.kadaluarsa)}
                                        <div class="text-xs ${diffDays < 0 ? 'text-red-500' : (diffDays <= 7 ? 'text-orange-500' : 'text-gray-500')}">
                                            ${diffDays < 0 ? 'Expired' : (diffDays === 0 ? 'Hari ini' : diffDays + ' hari')}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">${history.keterangan || '-'}</td>
                            </tr>
                        `;
                    });
                    
                    content += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                } else {
                    content += `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                            <i class="fas fa-info-circle text-yellow-500 text-3xl mb-3"></i>
                            <p class="text-yellow-700">Belum ada riwayat stok untuk produk ini.</p>
                        </div>
                    `;
                }
                
                // Tambahkan footer dengan timestamp
                content += `
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500">Terakhir diperbarui: ${formatDateForDetail(product.updated_at || new Date())}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Manager: Admin</p>
                                <p class="text-xs text-gray-400" id="detailTimestamp"></p>
                            </div>
                        </div>
                    </div>
                `;
                
                content += `</div>`;
                
                document.getElementById('detailContent').innerHTML = content;
                document.getElementById('detailModal').classList.remove('hidden');
                
                // Update timestamp in realtime
                updateDetailTimestamp();
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat memuat detail produk', 'error');
            });
    }

    // Function untuk update timestamp di modal detail
    function updateDetailTimestamp() {
        const timestampElement = document.getElementById('detailTimestamp');
        if (timestampElement) {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 
                           'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            
            timestampElement.textContent = `${dayName}, ${date} ${monthName} ${year} ${hours}:${minutes}`;
        }
    }

    // Form submission handlers
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        // Convert numbers
        data.stok = parseInt(data.stok);
        data.min_stok = parseInt(data.min_stok);
        data.harga = parseInt(data.harga);
        
        // Validate kadaluarsa
        const kadaluarsa = new Date(data.kadaluarsa);
        const today = new Date();
        if (kadaluarsa < today) {
            Swal.fire('Error', 'Tanggal kadaluarsa tidak boleh kurang dari hari ini!', 'error');
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Sedang menyimpan produk...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/management/produk', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: data.message || 'Produk berhasil ditambahkan',
                        icon: 'success',
                        confirmButtonColor: '#10B981',
                    }).then(() => {
                        closeAddModal();
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Gagal menambahkan produk', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat menyimpan produk', 'error');
            });
    });

    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const productId = document.getElementById('edit_id').value;

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        // Remove _token and _method from data
        delete data._token;
        delete data._method;

        // Convert numbers
        data.stok = parseInt(data.stok);
        data.min_stok = parseInt(data.min_stok);
        data.harga = parseInt(data.harga);
        
        // Validate kadaluarsa
        const kadaluarsa = new Date(data.kadaluarsa);
        const today = new Date();
        if (kadaluarsa < today) {
            Swal.fire('Error', 'Tanggal kadaluarsa tidak boleh kurang dari hari ini!', 'error');
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Mengupdate...',
            text: 'Sedang mengupdate produk...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`/management/produk/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-HTTP-Method-Override': 'PUT',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: data.message || 'Produk berhasil diupdate',
                        icon: 'success',
                        confirmButtonColor: '#10B981',
                    }).then(() => {
                        closeEditModal();
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Gagal mengupdate produk', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat mengupdate produk', 'error');
            });
    });

    // Delete product
    function deleteProduct(productId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Produk yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Sedang menghapus produk...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                fetch(`/management/produk/${productId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: data.message || 'Produk berhasil dihapus.',
                                icon: 'success',
                                confirmButtonColor: '#10B981',
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Gagal menghapus produk', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        console.error('Error:', error);
                        Swal.fire('Error', 'Terjadi kesalahan', 'error');
                    });
            }
        });
    }

    // Search and filter functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        filterProducts();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        filterProducts();
    });

    function filterProducts() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const rows = document.querySelectorAll('.product-row');

        rows.forEach(row => {
            const productName = row.querySelector('td:first-child .font-medium').textContent.toLowerCase();
            const productStatus = row.getAttribute('data-status');

            const matchesSearch = productName.includes(searchTerm);
            const matchesStatus = !statusFilter || productStatus === statusFilter;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const addModal = document.getElementById('addModal');
        const editModal = document.getElementById('editModal');
        const detailModal = document.getElementById('detailModal');
        
        if (e.target === addModal) {
            closeAddModal();
        }
        if (e.target === editModal) {
            closeEditModal();
        }
        if (e.target === detailModal) {
            closeDetailModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddModal();
            closeEditModal();
            closeDetailModal();
        }
    });
</script>
@endsection