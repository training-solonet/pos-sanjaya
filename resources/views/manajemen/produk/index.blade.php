<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>POS Sanjaya - Manajemen Produk</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <h2 class="text-2xl font-bold text-gray-900">Stok Produk Jadi</h2>
          <div class="flex gap-2">
            <a href="{{ route("management.produk.index") }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center">
              <i class="fas fa-clipboard-check mr-2"></i>Stok Produk
            </a>
            <button onclick="openAddProductModal()" class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all flex items-center">
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
            <select id="kategoriFilter" class="px-4 py-2 border border-gray-300 rounded-lg">
              <option value="">Semua Kategori</option>
              <option value="Roti Tawar">Roti Tawar</option>
              <option value="Roti Manis">Roti Manis</option>
              <option value="Donat">Donat</option>
              <option value="Pastry">Pastry</option>
            </select>
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
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min. Stok</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expired Date</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200" id="productTableBody">
                @foreach($produk as $item)
                <tr class="product-row" 
                    data-status="{{ $item->stok == 0 ? 'habis' : ($item->stok <= $item->min_stok ? 'rendah' : 'tersedia') }}"
                    data-kategori="{{ $item->bahan_baku->nama ?? 'Umum' }}">
                  <td class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                      @php
                        $iconColor = 'amber';
                        $iconClass = 'fas fa-bread-slice';
                        
                        // Determine icon based on product name or category
                        if (str_contains(strtolower($item->nama), 'cokelat') || str_contains(strtolower($item->nama), 'chocolate')) {
                          $iconColor = 'orange';
                          $iconClass = 'fas fa-cookie-bite';
                        } elseif (str_contains(strtolower($item->nama), 'keju') || str_contains(strtolower($item->nama), 'cheese')) {
                          $iconColor = 'yellow';
                          $iconClass = 'fas fa-cheese';
                        } elseif (str_contains(strtolower($item->nama), 'strawberry')) {
                          $iconColor = 'pink';
                          $iconClass = 'fas fa-ice-cream';
                        } elseif (str_contains(strtolower($item->nama), 'kismis') || str_contains(strtolower($item->nama), 'raisin')) {
                          $iconColor = 'purple';
                          $iconClass = 'fas fa-candy-cane';
                        } elseif (str_contains(strtolower($item->nama), 'pisang') || str_contains(strtolower($item->nama), 'banana')) {
                          $iconColor = 'yellow';
                          $iconClass = 'fas fa-drumstick-bite';
                        } elseif (str_contains(strtolower($item->nama), 'donat') || str_contains(strtolower($item->nama), 'donut')) {
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
                      <div class="w-10 h-10 bg-{{ $iconColor }}-100 rounded-lg flex items-center justify-center">
                        <i class="{{ $iconClass }} text-{{ $iconColor }}-600"></i>
                      </div>
                      <div>
                        <span class="font-medium text-gray-900 block">{{ $item->nama }}</span>
                        <span class="text-xs text-gray-500">{{ $item->bahan_baku->nama ?? 'Umum' }}</span>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm {{ $item->stok == 0 ? 'text-danger font-medium' : ($item->stok <= $item->min_stok ? 'text-orange-600 font-medium' : 'text-gray-900') }}">
                    {{ $item->stok }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-500">{{ $item->min_stok }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                  <td class="px-6 py-4">
                    @php
                      $expiredDate = \Carbon\Carbon::parse($item->kadaluarsa);
                      $daysUntilExpired = $expiredDate->diffInDays(now());
                      $isExpiredSoon = $daysUntilExpired <= 3;
                      $isExpired = $expiredDate->isPast();
                    @endphp
                    <div class="text-sm">
                      <span class="{{ $isExpired ? 'text-red-600 font-medium' : ($isExpiredSoon ? 'text-orange-600 font-medium' : 'text-gray-900') }}">
                        {{ $expiredDate->format('d M Y') }}
                      </span>
                      <div class="text-xs {{ $isExpired ? 'text-red-600' : ($isExpiredSoon ? 'text-orange-600' : 'text-gray-500') }}">
                        @if($isExpired)
                          Sudah kadaluarsa
                        @else
                          {{ $daysUntilExpired }} hari lagi
                        @endif
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex space-x-2">
                      <button onclick="openEditProductModal({{ $item->id }})" class="text-green-600 hover:text-green-700">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button onclick="deleteProduct({{ $item->id }})" class="text-danger hover:text-red-600">
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
            <input type="text" name="nama" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bahan Baku</label>
            <select name="id_bahan_baku" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" id="add_id_bahan_baku">
              <option value="">Pilih Bahan Baku</option>
              <!-- Options akan diisi via JavaScript -->
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Stok Awal</label>
              <input type="number" name="stok" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Min. Stok</label>
              <input type="number" name="min_stok" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual</label>
            <input type="number" name="harga" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kedaluwarsa</label>
            <input type="date" name="kadaluarsa" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
          </div>
          <div class="flex space-x-3 pt-4">
            <button type="button" onclick="closeAddModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
              Batal
            </button>
            <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
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
            <input type="text" name="nama" id="edit_nama" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bahan Baku</label>
            <select name="id_bahan_baku" id="edit_id_bahan_baku" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
              <option value="">Pilih Bahan Baku</option>
              <!-- Options akan diisi via JavaScript -->
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Stok</label>
              <input type="number" name="stok" id="edit_stok" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Min. Stok</label>
              <input type="number" name="min_stok" id="edit_min_stok" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual</label>
            <input type="number" name="harga" id="edit_harga" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kedaluwarsa</label>
            <input type="date" name="kadaluarsa" id="edit_kadaluarsa" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
          </div>
          <div class="flex space-x-3 pt-4">
            <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
              Batal
            </button>
            <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
              Update
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

 <script>
    let sidebarOpen = false;
    let bahanBakuData = [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Toggle sidebar
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');
      
      sidebarOpen = !sidebarOpen;
      
      if (sidebarOpen) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
      } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
      }
    }

    // Modal functions
    function openAddProductModal() {
      loadBahanBakuOptions('add');
      document.getElementById('addModal').classList.remove('hidden');
    }

    function closeAddModal() {
      document.getElementById('addModal').classList.add('hidden');
      document.getElementById('addProductForm').reset();
    }

    function openEditProductModal(productId) {
      // Fetch product data and populate form - GUNAKAN ROUTE YANG BENAR
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
          
          // Format date untuk input type="date"
          const kadaluarsaDate = new Date(data.kadaluarsa);
          document.getElementById('edit_kadaluarsa').value = kadaluarsaDate.toISOString().split('T')[0];
          
          // Load bahan baku options dan set yang dipilih
          loadBahanBakuOptions('edit', data.id_bahan_baku);
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

    // Load bahan baku options for select - FIXED VERSION
    function loadBahanBakuOptions(modalType, selectedId = null) {
      // Gunakan endpoint yang benar
      const endpoint = '/management/api/bahan-baku';

      if (bahanBakuData.length === 0) {
        fetch(endpoint)
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            bahanBakuData = data;
            populateBahanBakuSelect(modalType, selectedId);
          })
          .catch(error => {
            console.error(`Error fetching from ${endpoint}:`, error);
            // Fallback ke endpoint lain jika perlu
            fetch('/api/bahan-baku')
              .then(response => {
                if (!response.ok) throw new Error('Fallback failed');
                return response.json();
              })
              .then(data => {
                bahanBakuData = data;
                populateBahanBakuSelect(modalType, selectedId);
              })
              .catch(fallbackError => {
                console.error('Fallback also failed:', fallbackError);
                Swal.fire('Error', 'Gagal memuat data bahan baku', 'error');
              });
          });
      } else {
        populateBahanBakuSelect(modalType, selectedId);
      }
    }

    function populateBahanBakuSelect(modalType, selectedId) {
      const selectElement = document.getElementById(`${modalType}_id_bahan_baku`);
      if (!selectElement) return;
      
      selectElement.innerHTML = '<option value="">Pilih Bahan Baku</option>';
      
      bahanBakuData.forEach(bahan => {
        const option = document.createElement('option');
        option.value = bahan.id;
        option.textContent = bahan.nama;
        if (selectedId && bahan.id == selectedId) {
          option.selected = true;
        }
        selectElement.appendChild(option);
      });
    }

    // Form submission handlers - FIXED VERSION (menggunakan JSON seperti bahan baku)
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());
      
      // Convert numbers
      data.stok = parseInt(data.stok);
      data.min_stok = parseInt(data.min_stok);
      data.harga = parseInt(data.harga);
      data.id_bahan_baku = parseInt(data.id_bahan_baku);
      
      fetch('/management/produk', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => {
        if (!response.ok) {
          return response.json().then(err => { throw new Error(err.message || 'Network error'); });
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          Swal.fire('Sukses', data.message || 'Produk berhasil ditambahkan', 'success');
          closeAddModal();
          setTimeout(() => location.reload(), 1500);
        } else {
          Swal.fire('Error', data.message || 'Gagal menambahkan produk', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Terjadi kesalahan', 'error');
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
      data.id_bahan_baku = parseInt(data.id_bahan_baku);
      
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
      .then(response => {
        if (!response.ok) {
          return response.json().then(err => { throw new Error(err.message || 'Network error'); });
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          Swal.fire('Sukses', data.message || 'Produk berhasil diupdate', 'success');
          closeEditModal();
          setTimeout(() => location.reload(), 1500);
        } else {
          Swal.fire('Error', data.message || 'Gagal mengupdate produk', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Terjadi kesalahan', 'error');
      });
    });

    // Delete product - FIXED VERSION
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
          fetch(`/management/produk/${productId}`, {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            }
          })
          .then(response => {
            if (!response.ok) {
              return response.json().then(err => { throw new Error(err.message || 'Network error'); });
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              Swal.fire('Terhapus!', data.message || 'Produk berhasil dihapus.', 'success');
              setTimeout(() => location.reload(), 1500);
            } else {
              Swal.fire('Error', data.message || 'Gagal menghapus produk', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', error.message || 'Terjadi kesalahan', 'error');
          });
        }
      });
    }

    // Search and filter functionality
    document.getElementById('searchInput').addEventListener('input', function() {
      filterProducts();
    });

    document.getElementById('kategoriFilter').addEventListener('change', function() {
      filterProducts();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
      filterProducts();
    });

    function filterProducts() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const kategoriFilter = document.getElementById('kategoriFilter').value;
      const statusFilter = document.getElementById('statusFilter').value;
      const rows = document.querySelectorAll('.product-row');
      
      rows.forEach(row => {
        const productName = row.querySelector('td:first-child .font-medium').textContent.toLowerCase();
        const productKategori = row.getAttribute('data-kategori');
        const productStatus = row.getAttribute('data-status');
        
        const matchesSearch = productName.includes(searchTerm);
        const matchesKategori = !kategoriFilter || productKategori === kategoriFilter;
        const matchesStatus = !statusFilter || productStatus === statusFilter;
        
        if (matchesSearch && matchesKategori && matchesStatus) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

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

    // Handle window resize
    window.addEventListener('resize', function() {
      if (window.innerWidth >= 1024) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        sidebarOpen = false;
      }
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
      const addModal = document.getElementById('addModal');
      const editModal = document.getElementById('editModal');
      if (e.target === addModal) {
        closeAddModal();
      }
      if (e.target === editModal) {
        closeEditModal();
      }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      setInterval(updateDateTime, 60000);
      
      // Load bahan baku data awal
      loadBahanBakuOptions('add');
    });
  </script>