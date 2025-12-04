@extends('layouts.manajemen.index')

@section('content')
  <!-- Main Content -->
  <div class="content flex-1 lg:flex-1">
    <!-- Page Content -->
    <main class="p-3 sm:p-4 md:p-6 lg:p-8">
      <div class="space-y-4 md:space-y-6">
        <!-- Header - Responsif -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
          <div class="w-full sm:w-auto">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Stok Bahan Baku</h2>
          </div>
          <div class="flex flex-wrap gap-2 w-full sm:w-auto mt-2 sm:mt-0">
            <a href="{{ route('management.opname.index') }}" 
               class="flex-1 sm:flex-none px-3 py-2 sm:px-4 sm:py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center justify-center text-sm sm:text-base">
              <i class="fas fa-clipboard-check mr-2 text-sm"></i>
              <span class="hidden xs:inline">Stok Opname</span>
              <span class="xs:hidden">Opname</span>
            </a>
            <button onclick="openAddBahanModal()" 
                    class="flex-1 sm:flex-none px-3 py-2 sm:px-4 sm:py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all flex items-center justify-center text-sm sm:text-base">
              <i class="fas fa-plus mr-2 text-sm"></i>
              <span class="hidden xs:inline">Tambah Bahan</span>
              <span class="xs:hidden">Tambah</span>
            </button>
          </div>
        </div>

        <!-- Alert Stok Rendah -->
        <div id="lowStockAlert" class="bg-red-50 border border-red-200 rounded-lg p-3 md:p-4 hidden">
          <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
            <span id="alertText" class="text-red-800 font-medium text-sm sm:text-base"></span>
          </div>
        </div>

        <!-- Search & Filter - Responsif -->
        <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4">
          <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <div class="flex-1">
              <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchInput" placeholder="Cari bahan baku..." 
                       class="w-full pl-10 pr-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base"
                       onkeyup="filterData()">
              </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full sm:w-auto">
              <select id="categoryFilter" class="px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base w-full sm:w-auto"
                      onchange="filterData()">
                <option value="">Semua Kategori</option>
                <option value="Bahan Utama">Bahan Utama</option>
                <option value="Bahan Pembantu">Bahan Pembantu</option>
              </select>
              <select id="statusFilter" class="px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base w-full sm:w-auto"
                      onchange="filterData()">
                <option value="">Semua Status</option>
                <option value="Cukup">Stok Cukup</option>
                <option value="Rendah">Stok Rendah</option>
                <option value="Habis">Habis</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Stock Grid - Responsif -->
        <div id="stockGrid" class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
          @foreach($bahan_baku as $item)
            @php
              $status = 'Cukup';
              if ($item->stok == 0) {
                $status = 'Habis';
              } elseif ($item->stok <= $item->min_stok) {
                $status = 'Rendah';
              }
            @endphp
            <div class="bahan-item bg-white rounded-lg border {{ $status == 'Cukup' ? 'border-gray-200' : 'border-red-200' }} p-4 sm:p-6"
                 data-nama="{{ strtolower($item->nama) }}"
                 data-kategori="{{ $item->kategori }}"
                 data-status="{{ $status }}">
              <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="flex-1 min-w-0">
                  <h3 class="font-semibold text-gray-900 text-sm sm:text-base truncate" title="{{ $item->nama }}">
                    {{ $item->nama }}
                  </h3>
                </div>
                <span class="px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap ml-2 
                  {{ $status == 'Cukup' ? 'bg-green-100 text-green-600' : 
                     ($status == 'Rendah' ? 'bg-red-100 text-red-600' : 'bg-red-200 text-red-700') }}">
                  {{ $status }}
                </span>
              </div>
              <div class="space-y-2 sm:space-y-2">
                <div class="flex justify-between items-center">
                  <span class="text-xs sm:text-sm text-gray-500">Stok Saat Ini</span>
                  <span class="font-medium text-xs sm:text-sm {{ $status != 'Cukup' ? 'text-red-600' : '' }}">{{ $item->stok }} kg</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs sm:text-sm text-gray-500">Min. Stok</span>
                  <span class="font-medium text-xs sm:text-sm">{{ $item->min_stok }} kg</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs sm:text-sm text-gray-500">Harga/kg</span>
                  <span class="font-medium text-xs sm:text-sm">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs sm:text-sm text-gray-500">Kategori</span>
                  <span class="font-medium text-xs sm:text-sm">{{ $item->kategori }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs sm:text-sm text-gray-500">Terakhir Update</span>
                  <span class="font-medium text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->tglupdate)->format('d M Y H:i') }}</span>
                </div>
              </div>
              <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200 flex flex-wrap gap-2">
                <button onclick="openEditBahanModal({{ $item->id }})" 
                        class="flex-1 min-w-[70px] px-2 py-1.5 sm:px-3 sm:py-2 bg-blue-100 text-blue-600 rounded-lg text-xs sm:text-sm hover:bg-blue-200 transition-colors flex items-center justify-center">
                  <i class="fas fa-edit mr-1 sm:mr-2 text-xs"></i>
                  <span>Edit</span>
                </button>
                <button onclick="tambahStok({{ $item->id }})" 
                        class="flex-1 min-w-[70px] px-2 py-1.5 sm:px-3 sm:py-2 
                          {{ $status == 'Cukup' ? 'bg-green-100 text-green-600 hover:bg-green-200' : 
                             ($status == 'Rendah' ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-red-200 text-red-700 hover:bg-red-300') }} 
                          rounded-lg text-xs sm:text-sm transition-colors flex items-center justify-center">
                  <i class="fas fa-plus mr-1 sm:mr-2 text-xs"></i>
                  <span>Tambah</span>
                </button>
                <button onclick="deleteBahan({{ $item->id }})" 
                        class="flex-1 min-w-[70px] px-2 py-1.5 sm:px-3 sm:py-2 bg-red-100 text-red-600 rounded-lg text-xs sm:text-sm hover:bg-red-200 transition-colors flex items-center justify-center">
                  <i class="fas fa-trash mr-1 sm:mr-2 text-xs"></i>
                  <span>Hapus</span>
                </button>
              </div>
            </div>
          @endforeach
        </div>

        <!-- No Data Message -->
        <div id="noDataMessage" class="hidden text-center py-8 sm:py-12">
          <div class="text-gray-400 text-5xl sm:text-6xl mb-3 sm:mb-4">
            <i class="fas fa-search"></i>
          </div>
          <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-1 sm:mb-2">Tidak ada data yang ditemukan</h3>
          <p class="text-gray-500 text-sm sm:text-base">Coba ubah filter pencarian atau kata kunci</p>
        </div>
      </div>
    </main>
  </div>

  <!-- Add Bahan Baku Modal -->
  <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-start justify-center min-h-screen p-3 sm:p-4">
      <div class="bg-white rounded-lg w-full max-w-md mx-auto my-8">
        <div class="flex items-center justify-between p-4 sm:p-6 border-b">
          <h3 class="text-lg sm:text-xl font-semibold">Tambah Bahan Baku Baru</h3>
          <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form id="addBahanForm" class="p-4 sm:p-6 space-y-4">
          @csrf
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Nama Bahan Baku</label>
            <input type="text" name="nama" required 
                   class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
          </div>
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Kategori</label>
            <select name="kategori" required 
                    class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
              <option value="">Pilih Kategori</option>
              <option value="Bahan Utama">Bahan Utama</option>
              <option value="Bahan Pembantu">Bahan Pembantu</option>
            </select>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Stok Awal (kg)</label>
              <input type="number" name="stok" value="0" min="0" 
                     class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
            </div>
            <div>
              <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Min. Stok (kg)</label>
              <input type="number" name="min_stok" value="0" min="0" 
                     class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
            </div>
          </div>
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Harga Satuan (per kg)</label>
            <input type="number" name="harga_satuan" value="0" min="0" 
                   class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
          </div>
          <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 pt-4">
            <button type="button" onclick="closeAddModal()" 
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm sm:text-base">
              Batal
            </button>
            <button type="submit" 
                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all text-sm sm:text-base">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Bahan Baku Modal -->
  <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-start justify-center min-h-screen p-3 sm:p-4">
      <div class="bg-white rounded-lg w-full max-w-md mx-auto my-8">
        <div class="flex items-center justify-between p-4 sm:p-6 border-b">
          <h3 class="text-lg sm:text-xl font-semibold">Edit Bahan Baku</h3>
          <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form id="editBahanForm" class="p-4 sm:p-6 space-y-4">
          @csrf
          @method('PUT')
          <input type="hidden" name="id" id="edit_id">
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Nama Bahan Baku</label>
            <input type="text" name="nama" id="edit_nama" required 
                   class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
          </div>
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Kategori</label>
            <select name="kategori" id="edit_kategori" required 
                    class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
              <option value="">Pilih Kategori</option>
              <option value="Bahan Utama">Bahan Utama</option>
              <option value="Bahan Pembantu">Bahan Pembantu</option>
            </select>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Stok (kg)</label>
              <input type="number" name="stok" id="edit_stok" min="0" 
                     class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
            </div>
            <div>
              <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Min. Stok (kg)</label>
              <input type="number" name="min_stok" id="edit_min_stok" min="0" 
                     class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
            </div>
          </div>
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Harga Satuan (per kg)</label>
            <input type="number" name="harga_satuan" id="edit_harga_satuan" min="0" 
                   class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
          </div>
          <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 pt-4">
            <button type="button" onclick="closeEditModal()" 
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm sm:text-base">
              Batal
            </button>
            <button type="submit" 
                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all text-sm sm:text-base">
              Update
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Tambah Stok Modal -->
  <div id="tambahStokModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-start justify-center min-h-screen p-3 sm:p-4">
      <div class="bg-white rounded-lg w-full max-w-md mx-auto my-8">
        <div class="flex items-center justify-between p-4 sm:p-6 border-b">
          <h3 class="text-lg sm:text-xl font-semibold">Tambah Stok Bahan Baku</h3>
          <button onclick="closeTambahStokModal()" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form id="tambahStokForm" class="p-4 sm:p-6 space-y-4">
          @csrf
          <input type="hidden" name="id" id="tambah_stok_id">
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Nama Bahan Baku</label>
            <input type="text" id="tambah_stok_nama" readonly 
                   class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-sm sm:text-base">
          </div>
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Jumlah Stok Tambahan (kg)</label>
            <input type="number" name="tambah_stok" id="tambah_stok_jumlah" min="1" required 
                   class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
          </div>
          <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 pt-4">
            <button type="button" onclick="closeTambahStokModal()" 
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm sm:text-base">
              Batal
            </button>
            <button type="submit" 
                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all text-sm sm:text-base">
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let isSubmitting = false;

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
      document.body.style.overflow = 'hidden';
    }

    function closeAddModal() {
      document.getElementById('addModal').classList.add('hidden');
      document.body.style.overflow = 'auto';
      document.getElementById('addBahanForm').reset();
    }

    function openEditBahanModal(bahanId) {
      showLoading('Memuat data bahan baku...');
      
      fetch(`/management/bahanbaku/${bahanId}`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Bahan baku tidak ditemukan');
          }
          return response.json();
        })
        .then(data => {
          hideLoading();
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
          document.body.style.overflow = 'hidden';
        })
        .catch(error => {
          hideLoading();
          console.error('Error:', error);
          showError(error.message || 'Gagal memuat data bahan baku');
        });
    }

    function closeEditModal() {
      document.getElementById('editModal').classList.add('hidden');
      document.body.style.overflow = 'auto';
    }

    function tambahStok(bahanId) {
      showLoading('Memuat data bahan baku...');
      
      fetch(`/management/bahanbaku/${bahanId}`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Bahan baku tidak ditemukan');
          }
          return response.json();
        })
        .then(data => {
          hideLoading();
          if (data.error) {
            throw new Error(data.error);
          }
          
          document.getElementById('tambah_stok_id').value = data.id;
          document.getElementById('tambah_stok_nama').value = data.nama;
          document.getElementById('tambah_stok_jumlah').value = '';
          document.getElementById('tambahStokModal').classList.remove('hidden');
          document.body.style.overflow = 'hidden';
          
          setTimeout(() => {
            document.getElementById('tambah_stok_jumlah').focus();
          }, 100);
        })
        .catch(error => {
          hideLoading();
          console.error('Error:', error);
          showError(error.message || 'Gagal memuat data bahan baku');
        });
    }

    function closeTambahStokModal() {
      document.getElementById('tambahStokModal').classList.add('hidden');
      document.body.style.overflow = 'auto';
    }

    // Delete bahan baku
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
          showLoading('Menghapus bahan baku...');
          
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
            hideLoading();
            if (data.success) {
              Swal.fire('Terhapus!', data.message || 'Bahan baku berhasil dihapus.', 'success');
              location.reload();
            } else {
              Swal.fire('Error', data.message || 'Gagal menghapus bahan baku', 'error');
            }
          })
          .catch(error => {
            hideLoading();
            console.error('Error:', error);
            Swal.fire('Error', error.message || 'Terjadi kesalahan', 'error');
          });
        }
      });
    }

    // Form submission handlers
    document.getElementById('addBahanForm').addEventListener('submit', function(e) {
      e.preventDefault();
      if (isSubmitting) return;
      isSubmitting = true;
      
      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());
      
      data.stok = parseInt(data.stok);
      data.min_stok = parseInt(data.min_stok);
      data.harga_satuan = parseInt(data.harga_satuan);
      
      showLoading('Menyimpan bahan baku...');
      
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
        hideLoading();
        isSubmitting = false;
        if (data.success) {
          Swal.fire('Sukses', data.message || 'Bahan baku berhasil ditambahkan', 'success');
          closeAddModal();
          location.reload();
        } else {
          Swal.fire('Error', data.message || 'Gagal menambahkan bahan baku', 'error');
        }
      })
      .catch(error => {
        hideLoading();
        isSubmitting = false;
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Terjadi kesalahan', 'error');
      });
    });

    document.getElementById('editBahanForm').addEventListener('submit', function(e) {
      e.preventDefault();
      if (isSubmitting) return;
      isSubmitting = true;
      
      const bahanId = document.getElementById('edit_id').value;
      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());
      
      delete data._token;
      delete data._method;
      
      data.stok = parseInt(data.stok);
      data.min_stok = parseInt(data.min_stok);
      data.harga_satuan = parseInt(data.harga_satuan);
      
      showLoading('Mengupdate bahan baku...');
      
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
        hideLoading();
        isSubmitting = false;
        if (data.success) {
          Swal.fire('Sukses', data.message || 'Bahan baku berhasil diupdate', 'success');
          closeEditModal();
          location.reload();
        } else {
          Swal.fire('Error', data.message || 'Gagal mengupdate bahan baku', 'error');
        }
      })
      .catch(error => {
        hideLoading();
        isSubmitting = false;
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Terjadi kesalahan', 'error');
      });
    });

    // Form tambah stok
    document.getElementById('tambahStokForm').addEventListener('submit', function(e) {
      e.preventDefault();
      if (isSubmitting) return;
      isSubmitting = true;
      
      const bahanId = document.getElementById('tambah_stok_id').value;
      const tambahStok = parseInt(document.getElementById('tambah_stok_jumlah').value);
      
      showLoading('Menambahkan stok...');
      
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
        hideLoading();
        isSubmitting = false;
        if (data.success) {
          Swal.fire('Sukses', data.message || 'Stok berhasil ditambahkan', 'success');
          closeTambahStokModal();
          location.reload();
        } else {
          Swal.fire('Error', data.message || 'Gagal menambah stok', 'error');
        }
      })
      .catch(error => {
        hideLoading();
        isSubmitting = false;
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Terjadi kesalahan', 'error');
      });
    });

    // Filter data function dengan debounce
    let filterTimeout;
    function filterData() {
      clearTimeout(filterTimeout);
      filterTimeout = setTimeout(() => {
        performFilter();
      }, 300);
    }

    function performFilter() {
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

    // Utility functions
    function showLoading(message = 'Memuat...') {
      Swal.fire({
        title: message,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
    }

    function hideLoading() {
      Swal.close();
    }

    function showError(message) {
      Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        confirmButtonColor: '#EF4444',
        confirmButtonText: 'OK'
      });
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
      
      // Tambahkan event listener untuk ESC key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeAddModal();
          closeEditModal();
          closeTambahStokModal();
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
      
      // Handle window resize untuk responsif
      function handleResize() {
        if (window.innerWidth >= 1024) {
          const sidebar = document.getElementById('sidebar');
          const overlay = document.getElementById('sidebarOverlay');
          sidebar.classList.add('-translate-x-full');
          overlay.classList.add('hidden');
          sidebarOpen = false;
        }
      }
      
      // Initial check
      handleResize();
      window.addEventListener('resize', handleResize);
    });
</script>

<style>
  /* Custom styles untuk responsifitas tambahan */
  @media (max-width: 640px) {
    .xs\:grid-cols-2 {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }
  
  @media (max-width: 480px) {
    .xs\:grid-cols-2 {
      grid-template-columns: repeat(1, minmax(0, 1fr));
    }
  }
  
  /* Mencegah overflow pada mobile */
  .truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  
  /* Better modal scrolling on mobile */
  .overflow-y-auto {
    -webkit-overflow-scrolling: touch;
  }
</style>
@endsection