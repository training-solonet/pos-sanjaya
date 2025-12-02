@extends('layouts.manajemen.index')

@section('content')
  <!-- Main Content -->
  <div class="content flex-1 lg:flex-1">
    <!-- Header -->
    
    <!-- Page Content -->
    <main class="p-4 sm:p-6 lg:p-8">
      <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <h2 class="text-2xl font-bold text-gray-900">Stok Bahan Baku</h2>
          <div class="flex gap-2">
            <a href="{{ route("management.opname.index") }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center">
              <i class="fas fa-clipboard-check mr-2"></i>Stok Opname
            </a>
            <button onclick="openAddBahanModal()" class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all flex items-center">
              <i class="fas fa-plus mr-2"></i>Tambah Bahan
            </button>
          </div>
        </div>

        <!-- Alert Stok Rendah -->
        <div id="lowStockAlert" class="bg-red-50 border border-red-200 rounded-lg p-4 hidden">
          <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
            <span id="alertText" class="text-red-800 font-medium"></span>
          </div>
        </div>

        <!-- Search & Filter -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
              <input type="text" id="searchInput" placeholder="Cari bahan baku..." 
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                     onkeyup="filterData()">
            </div>
            <select id="categoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    onchange="filterData()">
              <option value="">Semua Kategori</option>
              <option value="Bahan Utama">Bahan Utama</option>
              <option value="Bahan Pembantu">Bahan Pembantu</option>
            </select>
            <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    onchange="filterData()">
              <option value="">Semua Status</option>
              <option value="Cukup">Stok Cukup</option>
              <option value="Rendah">Stok Rendah</option>
              <option value="Habis">Habis</option>
            </select>
          </div>
        </div>

        <!-- Stock Grid -->
        <div id="stockGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          @foreach($bahan_baku as $item)
            @php
              $status = 'Cukup';
              if ($item->stok == 0) {
                $status = 'Habis';
              } elseif ($item->stok <= $item->min_stok) {
                $status = 'Rendah';
              }
            @endphp
            <div class="bahan-item bg-white rounded-lg border {{ $status == 'Cukup' ? 'border-gray-200' : 'border-red-200' }} p-6"
                 data-nama="{{ strtolower($item->nama) }}"
                 data-kategori="{{ $item->kategori }}"
                 data-status="{{ $status }}">
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">{{ $item->nama }}</h3>
                <span class="px-2 py-1 text-xs font-medium rounded-full 
                  {{ $status == 'Cukup' ? 'bg-green-100 text-green-600' : 
                     ($status == 'Rendah' ? 'bg-red-100 text-red-600' : 'bg-red-200 text-red-700') }}">
                  {{ $status }}
                </span>
              </div>
              <div class="space-y-2">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-500">Stok Saat Ini</span>
                  <span class="font-medium {{ $status != 'Cukup' ? 'text-red-600' : '' }}">{{ $item->stok }} kg</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-500">Min. Stok</span>
                  <span class="font-medium">{{ $item->min_stok }} kg</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-500">Harga/kg</span>
                  <span class="font-medium">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-500">Kategori</span>
                  <span class="font-medium">{{ $item->kategori }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-500">Terakhir Update</span>
                  <span class="font-medium text-xs">{{ \Carbon\Carbon::parse($item->tglupdate)->format('d M Y H:i') }}</span>
                </div>
              </div>
              <div class="mt-4 pt-4 border-t flex space-x-2">
                <button onclick="openEditBahanModal({{ $item->id }})" class="flex-1 px-3 py-2 bg-blue-100 text-blue-600 rounded-lg text-sm hover:bg-blue-200">
                  Edit
                </button>
                <button onclick="tambahStok({{ $item->id }})" class="flex-1 px-3 py-2 
                  {{ $status == 'Cukup' ? 'bg-green-100 text-green-600 hover:bg-green-200' : 
                     ($status == 'Rendah' ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-red-200 text-red-700 hover:bg-red-300') }} 
                  rounded-lg text-sm">
                  Tambah
                </button>
                <button onclick="deleteBahan({{ $item->id }})" class="flex-1 px-3 py-2 bg-red-100 text-red-600 rounded-lg text-sm hover:bg-red-200">
                  Hapus
                </button>
              </div>
            </div>
          @endforeach
        </div>

        <!-- No Data Message -->
        <div id="noDataMessage" class="hidden text-center py-12">
          <div class="text-gray-400 text-6xl mb-4">
            <i class="fas fa-search"></i>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data yang ditemukan</h3>
          <p class="text-gray-500">Coba ubah filter pencarian atau kata kunci</p>
        </div>
      </div>
    </main>
  </div>

  <!-- Add Bahan Baku Modal -->
  <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="bg-white rounded-lg w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b">
          <h3 class="text-lg font-semibold">Tambah Bahan Baku Baru</h3>
          <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form id="addBahanForm" class="p-6 space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bahan Baku</label>
            <input type="text" name="nama" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
            <select name="kategori" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
              <option value="">Pilih Kategori</option>
              <option value="Bahan Utama">Bahan Utama</option>
              <option value="Bahan Pembantu">Bahan Pembantu</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Stok Awal (kg)</label>
              <input type="number" name="stok" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Min. Stok (kg)</label>
              <input type="number" name="min_stok" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Satuan (per kg)</label>
            <input type="number" name="harga_satuan" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
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

  <!-- Edit Bahan Baku Modal -->
  <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="bg-white rounded-lg w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b">
          <h3 class="text-lg font-semibold">Edit Bahan Baku</h3>
          <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form id="editBahanForm" class="p-6 space-y-4">
          @csrf
          @method('PUT')
          <input type="hidden" name="id" id="edit_id">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bahan Baku</label>
            <input type="text" name="nama" id="edit_nama" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
            <select name="kategori" id="edit_kategori" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
              <option value="">Pilih Kategori</option>
              <option value="Bahan Utama">Bahan Utama</option>
              <option value="Bahan Pembantu">Bahan Pembantu</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Stok (kg)</label>
              <input type="number" name="stok" id="edit_stok" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Min. Stok (kg)</label>
              <input type="number" name="min_stok" id="edit_min_stok" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Satuan (per kg)</label>
            <input type="number" name="harga_satuan" id="edit_harga_satuan" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
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

  <!-- Tambah Stok Modal -->
  <div id="tambahStokModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="bg-white rounded-lg w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b">
          <h3 class="text-lg font-semibold">Tambah Stok Bahan Baku</h3>
          <button onclick="closeTambahStokModal()" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form id="tambahStokForm" class="p-6 space-y-4">
          @csrf
          <input type="hidden" name="id" id="tambah_stok_id">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bahan Baku</label>
            <input type="text" id="tambah_stok_nama" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Stok Tambahan (kg)</label>
            <input type="number" name="tambah_stok" id="tambah_stok_jumlah" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
          </div>
          <div class="flex space-x-3 pt-4">
            <button type="button" onclick="closeTambahStokModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
              Batal
            </button>
            <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
              Tambah Stok
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('js')
<script>
    let sidebarOpen = false;
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
    function openAddBahanModal() {
      document.getElementById('addModal').classList.remove('hidden');
    }

    function closeAddModal() {
      document.getElementById('addModal').classList.add('hidden');
      document.getElementById('addBahanForm').reset();
    }

    function openEditBahanModal(bahanId) {
      // Gunakan route resource show
      fetch(`/management/bahanbaku/${bahanId}`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Bahan baku tidak ditemukan');
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
          document.getElementById('edit_harga_satuan').value = data.harga_satuan;
          document.getElementById('edit_kategori').value = data.kategori;
          
          document.getElementById('editModal').classList.remove('hidden');
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire('Error', error.message || 'Gagal memuat data bahan baku', 'error');
        });
    }

    function closeEditModal() {
      document.getElementById('editModal').classList.add('hidden');
    }

    function tambahStok(bahanId) {
      // Gunakan route resource show untuk mendapatkan data bahan baku
      fetch(`/management/bahanbaku/${bahanId}`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Bahan baku tidak ditemukan');
          }
          return response.json();
        })
        .then(data => {
          if (data.error) {
            throw new Error(data.error);
          }
          
          document.getElementById('tambah_stok_id').value = data.id;
          document.getElementById('tambah_stok_nama').value = data.nama;
          document.getElementById('tambah_stok_jumlah').value = '';
          
          document.getElementById('tambahStokModal').classList.remove('hidden');
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire('Error', error.message || 'Gagal memuat data bahan baku', 'error');
        });
    }

    function closeTambahStokModal() {
      document.getElementById('tambahStokModal').classList.add('hidden');
    }

    // Delete bahan baku - OPTIMIZED VERSION
    function deleteBahan(bahanId) {
      Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Bahan baku yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          // Gunakan route resource destroy
          fetch(`/management/bahanbaku/${bahanId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Content-Type': 'application/json',
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
              Swal.fire('Terhapus!', data.message || 'Bahan baku berhasil dihapus.', 'success');
              location.reload();
            } else {
              Swal.fire('Error', data.message || 'Gagal menghapus bahan baku', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', error.message || 'Terjadi kesalahan', 'error');
          });
        }
      });
    }

    // Form submission handlers - OPTIMIZED VERSION
    document.getElementById('addBahanForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());
      
      // Convert numbers
      data.stok = parseInt(data.stok);
      data.min_stok = parseInt(data.min_stok);
      data.harga_satuan = parseInt(data.harga_satuan);
      
      // Gunakan route resource store
      fetch('/management/bahanbaku', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json',
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
          Swal.fire('Sukses', data.message || 'Bahan baku berhasil ditambahkan', 'success');
          closeAddModal();
          location.reload();
        } else {
          Swal.fire('Error', data.message || 'Gagal menambahkan bahan baku', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Terjadi kesalahan', 'error');
      });
    });

    document.getElementById('editBahanForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const bahanId = document.getElementById('edit_id').value;
      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());
      
      // Remove _token and _method from data
      delete data._token;
      delete data._method;
      
      // Convert numbers
      data.stok = parseInt(data.stok);
      data.min_stok = parseInt(data.min_stok);
      data.harga_satuan = parseInt(data.harga_satuan);
      
      // Gunakan route resource update
      fetch(`/management/bahanbaku/${bahanId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-HTTP-Method-Override': 'PUT'
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
          Swal.fire('Sukses', data.message || 'Bahan baku berhasil diupdate', 'success');
          closeEditModal();
          location.reload();
        } else {
          Swal.fire('Error', data.message || 'Gagal mengupdate bahan baku', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Terjadi kesalahan', 'error');
      });
    });

    // Form tambah stok - OPTIMIZED VERSION (menggunakan route update)
    document.getElementById('tambahStokForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const bahanId = document.getElementById('tambah_stok_id').value;
      const tambahStok = parseInt(document.getElementById('tambah_stok_jumlah').value);
      
      // Gunakan route resource update dengan field tambah_stok
      fetch(`/management/bahanbaku/${bahanId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-HTTP-Method-Override': 'PUT'
        },
        body: JSON.stringify({ 
          tambah_stok: tambahStok 
        })
      })
      .then(response => {
        if (!response.ok) {
          return response.json().then(err => { throw new Error(err.message || 'Network error'); });
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          Swal.fire('Sukses', data.message || 'Stok berhasil ditambahkan', 'success');
          closeTambahStokModal();
          location.reload();
        } else {
          Swal.fire('Error', data.message || 'Gagal menambah stok', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Terjadi kesalahan', 'error');
      });
    });

    // Filter data function
    function filterData() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const categoryFilter = document.getElementById('categoryFilter').value;
      const statusFilter = document.getElementById('statusFilter').value;
      const items = document.querySelectorAll('.bahan-item');
      let visibleCount = 0;
      let lowStockCount = 0;
      
      items.forEach(item => {
        const nama = item.getAttribute('data-nama');
        const kategori = item.getAttribute('data-kategori');
        const status = item.getAttribute('data-status');
        
        const matchesSearch = nama.includes(searchTerm);
        const matchesCategory = !categoryFilter || kategori === categoryFilter;
        const matchesStatus = !statusFilter || status === statusFilter;
        
        if (matchesSearch && matchesCategory && matchesStatus) {
          item.style.display = 'block';
          visibleCount++;
          if (status === 'Rendah' || status === 'Habis') {
            lowStockCount++;
          }
        } else {
          item.style.display = 'none';
        }
      });
      
      // Show/hide no data message
      const noDataMessage = document.getElementById('noDataMessage');
      if (visibleCount === 0) {
        noDataMessage.classList.remove('hidden');
      } else {
        noDataMessage.classList.add('hidden');
      }
      
      // Update low stock alert
      updateLowStockAlert(lowStockCount);
    }

    // Update low stock alert
    function updateLowStockAlert(lowStockCount) {
      const alertElement = document.getElementById('lowStockAlert');
      const alertText = document.getElementById('alertText');
      
      if (lowStockCount > 0) {
        alertText.textContent = `Peringatan: ${lowStockCount} bahan baku memiliki stok rendah atau habis!`;
        alertElement.classList.remove('hidden');
      } else {
        alertElement.classList.add('hidden');
      }
    }

    // Initialize low stock alert on page load
    document.addEventListener('DOMContentLoaded', function() {
      let initialLowStockCount = 0;
      document.querySelectorAll('.bahan-item').forEach(item => {
        const status = item.getAttribute('data-status');
        if (status === 'Rendah' || status === 'Habis') {
          initialLowStockCount++;
        }
      });
      updateLowStockAlert(initialLowStockCount);
    });

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
      const tambahStokModal = document.getElementById('tambahStokModal');
      
      if (e.target === addModal) closeAddModal();
      if (e.target === editModal) closeEditModal();
      if (e.target === tambahStokModal) closeTambahStokModal();
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      setInterval(updateDateTime, 60000);
    });
</script>
@endsection