@extends('layouts.manajemen.index')

@section('content')
  <!-- Main Content -->
  {{-- <div class="content flex-1 lg:flex-1"> --}}
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
              // Hitung perhitungan konversi
              $stokDalamSatuanKecil = $item->stok; // Nilai yang disimpan di database (dalam satuan kecil)
              $satuanKecil = $item->satuan_kecil; // Satuan kecil (misal: kg)
              $satuanBesar = $item->satuan_besar; // Satuan besar (misal: Karung)
              $jumlahKonversi = $item->jumlah_konversi; // Jumlah konversi (misal: 25)
              
              // Hitung stok dalam satuan besar
              $stokDalamSatuanBesar = floor($stokDalamSatuanKecil / $jumlahKonversi);
              $sisaStok = $stokDalamSatuanKecil % $jumlahKonversi;
              
              // Format tampilan
              if ($sisaStok > 0) {
                $displayStok = number_format($stokDalamSatuanKecil) . ' ' . $satuanKecil;
                $displayKonversi = number_format($stokDalamSatuanBesar) . ' ' . $satuanBesar . ' + ' . $sisaStok . ' ' . $satuanKecil;
              } else {
                $displayStok = number_format($stokDalamSatuanKecil) . ' ' . $satuanKecil;
                $displayKonversi = number_format($stokDalamSatuanBesar) . ' ' . $satuanBesar;
              }
              
              // Status stok
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
                  <div class="text-right">
                    <span class="font-medium text-xs sm:text-sm {{ $status != 'Cukup' ? 'text-red-600' : '' }}">
                      {{ $displayStok }}
                    </span>
                    <br>
                    <small class="text-gray-500 text-xs">
                      ({{ $displayKonversi }})
                    </small>
                  </div>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs sm:text-sm text-gray-500">Min. Stok</span>
                  <span class="font-medium text-xs sm:text-sm">
                    {{ number_format($item->min_stok) }} {{ $satuanKecil }}
                  </span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs sm:text-sm text-gray-500">Harga/{{ $satuanBesar }}</span>
                  <span class="font-medium text-xs sm:text-sm">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs sm:text-sm text-gray-500">Kategori</span>
                  <span class="font-medium text-xs sm:text-sm">{{ $item->kategori }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs sm:text-sm text-gray-500">Konversi</span>
                  <span class="font-medium text-xs sm:text-sm">
                    1 {{ $satuanBesar }} = {{ $jumlahKonversi }} {{ $satuanKecil }}
                  </span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs sm:text-sm text-gray-500">Terakhir Update</span>
                  <span class="font-medium text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->tglupdate)->format('d M Y H:i') }}</span>
                </div>
              </div>
              <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200 flex flex-wrap gap-2">
                <button onclick="showDetail({{ $item->id }})" 
                        class="flex-1 min-w-[70px] px-2 py-1.5 sm:px-3 sm:py-2 bg-purple-100 text-purple-600 rounded-lg text-xs sm:text-sm hover:bg-purple-200 transition-colors flex items-center justify-center">
                  <i class="fas fa-eye mr-1 sm:mr-2 text-xs"></i>
                  <span>Detail</span>
                </button>
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

        <!-- Pagination -->
        @if($bahan_baku->hasPages())
        <div class="flex flex-col items-center justify-center space-y-4 mt-6">
          <div class="text-sm text-gray-600">
            Menampilkan <span class="font-medium">{{ $bahan_baku->firstItem() }}</span> 
            sampai <span class="font-medium">{{ $bahan_baku->lastItem() }}</span> 
            dari <span class="font-medium">{{ $bahan_baku->total() }}</span> bahan baku
          </div>
          
          <nav class="flex items-center justify-center space-x-1">
            <!-- Previous Page Link -->
            @if($bahan_baku->onFirstPage())
              <span class="px-3 py-1.5 text-gray-400 cursor-not-allowed rounded-lg border border-gray-200 bg-gray-50">
                <i class="fas fa-chevron-left text-xs"></i>
              </span>
            @else
              <a href="{{ $bahan_baku->previousPageUrl() }}" class="px-3 py-1.5 text-gray-700 hover:bg-gray-100 rounded-lg border border-gray-300 transition-colors">
                <i class="fas fa-chevron-left text-xs"></i>
              </a>
            @endif

            <!-- Page Numbers -->
            @php
              $currentPage = $bahan_baku->currentPage();
              $lastPage = $bahan_baku->lastPage();
              $start = max(1, $currentPage - 2);
              $end = min($lastPage, $currentPage + 2);
            @endphp

            @if($start > 1)
              <a href="{{ $bahan_baku->url(1) }}" class="px-3 py-1.5 text-gray-700 hover:bg-gray-100 rounded-lg border border-gray-300 transition-colors">1</a>
              @if($start > 2)
                <span class="px-2 py-1.5 text-gray-400">...</span>
              @endif
            @endif

            @for($page = $start; $page <= $end; $page++)
              @if($page == $currentPage)
                <span class="px-3 py-1.5 bg-green-500 text-white font-medium rounded-lg border border-green-500">{{ $page }}</span>
              @else
                <a href="{{ $bahan_baku->url($page) }}" class="px-3 py-1.5 text-gray-700 hover:bg-gray-100 rounded-lg border border-gray-300 transition-colors">{{ $page }}</a>
              @endif
            @endfor

            @if($end < $lastPage)
              @if($end < $lastPage - 1)
                <span class="px-2 py-1.5 text-gray-400">...</span>
              @endif
              <a href="{{ $bahan_baku->url($lastPage) }}" class="px-3 py-1.5 text-gray-700 hover:bg-gray-100 rounded-lg border border-gray-300 transition-colors">{{ $lastPage }}</a>
            @endif

            <!-- Next Page Link -->
            @if($bahan_baku->hasMorePages())
              <a href="{{ $bahan_baku->nextPageUrl() }}" class="px-3 py-1.5 text-gray-700 hover:bg-gray-100 rounded-lg border border-gray-300 transition-colors">
                <i class="fas fa-chevron-right text-xs"></i>
              </a>
            @else
              <span class="px-3 py-1.5 text-gray-400 cursor-not-allowed rounded-lg border border-gray-200 bg-gray-50">
                <i class="fas fa-chevron-right text-xs"></i>
              </span>
            @endif
          </nav>
        </div>
        @endif

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
  {{-- </div> --}}

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
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Satuan</label>
            <select name="id_konversi" id="add_id_konversi" required 
                    class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base"
                    onchange="updateAddKonversiInfo()">
              <option value="">Pilih Satuan</option>
              @foreach($konversi as $konv)
              <option value="{{ $konv->id }}" 
                      data-jumlah="{{ $konv->jumlah }}" 
                      data-satuan-kecil="{{ $konv->satuan_kecil }}"
                      data-satuan-besar="{{ $konv->satuan_besar }}">
                {{ $konv->satuan_besar }} ({{ $konv->jumlah }} {{ $konv->satuan_kecil }})
              </option>
              @endforeach
            </select>
          </div>
          
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Pilih Satuan Input</label>
            <div class="flex space-x-4 mb-4">
              <label class="inline-flex items-center">
                <input type="radio" name="satuan_input" value="kecil" checked 
                       class="form-radio text-green-500 focus:ring-green-500"
                       onchange="updateAddInputLabels()">
                <span class="ml-2">Satuan Kecil</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="satuan_input" value="besar" 
                       class="form-radio text-green-500 focus:ring-green-500"
                       onchange="updateAddInputLabels()">
                <span class="ml-2">Satuan Besar</span>
              </label>
            </div>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2" id="add_stok_label">Stok Awal</label>
              <input type="number" name="stok" id="add_stok" value="0" min="0" step="1" required 
                     class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
            </div>
            <div>
              <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2" id="add_min_stok_label">Min. Stok</label>
              <input type="number" name="min_stok" id="add_min_stok" value="0" min="0" step="1" required 
                     class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
            </div>
          </div>
          
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Harga Satuan (per <span id="add_harga_satuan_unit">Satuan Besar</span>)</label>
            <input type="number" name="harga_satuan" value="0" min="0" required 
                   class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
          </div>
          
          <div id="addKonversiInfo" class="bg-blue-50 p-3 rounded-lg hidden">
            <p class="text-xs text-blue-700">
              <i class="fas fa-info-circle mr-1"></i>
              <span id="addKonversiText">Konversi: 1 Satuan Besar = X Satuan Kecil</span>
              <br>
              <span id="addInputInfo">Input dalam: Satuan Kecil</span>
            </p>
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
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Satuan</label>
            <select name="id_konversi" id="edit_id_konversi" required 
                    class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
              <option value="">Pilih Satuan</option>
              @foreach($konversi as $konv)
              <option value="{{ $konv->id }}" 
                      data-jumlah="{{ $konv->jumlah }}" 
                      data-satuan-kecil="{{ $konv->satuan_kecil }}">
                {{ $konv->satuan_besar }} ({{ $konv->jumlah }} {{ $konv->satuan_kecil }})
              </option>
              @endforeach
            </select>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Stok (dalam satuan kecil)</label>
              <input type="number" name="stok" id="edit_stok" min="0" step="1" 
                     class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
            </div>
            <div>
              <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Min. Stok (dalam satuan kecil)</label>
              <input type="number" name="min_stok" id="edit_min_stok" min="0" step="1" 
                     class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
            </div>
          </div>
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Harga Satuan (per satuan besar)</label>
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
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Konversi Satuan</label>
            <p class="text-sm text-gray-900" id="tambah_stok_konversi"></p>
          </div>
          
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Pilih Satuan Input</label>
            <div class="flex space-x-4 mb-4">
              <label class="inline-flex items-center">
                <input type="radio" name="satuan_input" value="kecil" checked 
                       class="form-radio text-green-500 focus:ring-green-500"
                       onchange="updateTambahInputLabel()">
                <span class="ml-2">Satuan Kecil</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="satuan_input" value="besar" 
                       class="form-radio text-green-500 focus:ring-green-500"
                       onchange="updateTambahInputLabel()">
                <span class="ml-2">Satuan Besar</span>
              </label>
            </div>
          </div>
          
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2" id="tambah_stok_label">Jumlah Stok Tambahan</label>
            <input type="number" name="tambah_stok" id="tambah_stok_jumlah" min="1" step="1" required 
                   class="w-full px-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
          </div>
          
          <div id="tambahKonversiInfo" class="bg-blue-50 p-3 rounded-lg">
            <p class="text-xs text-blue-700">
              <i class="fas fa-info-circle mr-1"></i>
              <span id="tambahKonversiText">Konversi: 1 Satuan Besar = X Satuan Kecil</span>
              <br>
              <span id="tambahInputInfo">Input dalam: Satuan Kecil</span>
            </p>
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

  <!-- Detail Bahan Baku Modal -->
  <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-start justify-center min-h-screen p-3 sm:p-4">
      <div class="bg-white rounded-lg w-full max-w-md mx-auto my-8">
        <div class="flex items-center justify-between p-4 sm:p-6 border-b">
          <h3 class="text-lg sm:text-xl font-semibold">Detail Bahan Baku</h3>
          <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="p-4 sm:p-6 space-y-4">
          <div>
            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Nama Bahan Baku</label>
            <p id="detail_nama" class="text-gray-900 text-lg font-medium"></p>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
              <p id="detail_kategori" class="text-gray-900"></p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <span id="detail_status" class="px-2 py-1 text-xs font-medium rounded-full"></span>
            </div>
          </div>

          <div class="border-t pt-4">
            <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Stok</h4>
            <div class="space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Stok Saat Ini</span>
                <div class="text-right">
                  <p id="detail_stok_kecil" class="font-medium text-gray-900"></p>
                  <p id="detail_stok_besar" class="text-xs text-gray-500"></p>
                </div>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Min. Stok</span>
                <p id="detail_min_stok" class="font-medium text-gray-900"></p>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Harga Satuan</span>
                <p id="detail_harga_satuan" class="font-medium text-gray-900"></p>
              </div>
            </div>
          </div>

          <div class="border-t pt-4">
            <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Konversi</h4>
            <div class="space-y-2">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Satuan Besar</span>
                <p id="detail_satuan_besar" class="font-medium text-gray-900"></p>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Satuan Kecil</span>
                <p id="detail_satuan_kecil" class="font-medium text-gray-900"></p>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Konversi</span>
                <p id="detail_konversi" class="font-medium text-gray-900"></p>
              </div>
              <div class="bg-blue-50 p-3 rounded-lg">
                <p id="detail_konversi_info" class="text-xs text-blue-700"></p>
              </div>
            </div>
          </div>

          <div class="border-t pt-4">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Terakhir Update</span>
              <p id="detail_tglupdate" class="font-medium text-gray-900 text-sm"></p>
            </div>
          </div>
        </div>
        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 p-4 sm:p-6 pt-0">
          <button type="button" onclick="closeDetailModal()" 
                  class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm sm:text-base">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let isSubmitting = false;
    let currentSatuanData = {
        besar: '',
        kecil: '',
        jumlah: 1
    };

    // Fungsi untuk update info konversi di modal tambah
    function updateAddKonversiInfo() {
        const selectElement = document.getElementById('add_id_konversi');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const satuanBesar = selectedOption.text.split(' (')[0];
            const jumlah = selectedOption.getAttribute('data-jumlah');
            const satuanKecil = selectedOption.getAttribute('data-satuan-kecil');
            
            // Simpan data satuan
            currentSatuanData = {
                besar: satuanBesar,
                kecil: satuanKecil,
                jumlah: parseInt(jumlah) || 1
            };
            
            // Update info konversi
            document.getElementById('addKonversiText').textContent = 
                `Konversi: 1 ${satuanBesar} = ${jumlah} ${satuanKecil}`;
            document.getElementById('add_harga_satuan_unit').textContent = satuanBesar;
            
            // Update label input berdasarkan pilihan satuan
            updateAddInputLabels();
            
            document.getElementById('addKonversiInfo').classList.remove('hidden');
        } else {
            document.getElementById('addKonversiInfo').classList.add('hidden');
        }
    }

    // Fungsi untuk update label input di modal tambah
    function updateAddInputLabels() {
        const satuanInput = document.querySelector('input[name="satuan_input"]:checked');
        if (!satuanInput) return;
        
        const satuanValue = satuanInput.value;
        
        if (satuanValue === 'kecil') {
            document.getElementById('add_stok_label').textContent = `Stok Awal (${currentSatuanData.kecil})`;
            document.getElementById('add_min_stok_label').textContent = `Min. Stok (${currentSatuanData.kecil})`;
            document.getElementById('addInputInfo').textContent = `Input dalam: ${currentSatuanData.kecil}`;
        } else {
            document.getElementById('add_stok_label').textContent = `Stok Awal (${currentSatuanData.besar})`;
            document.getElementById('add_min_stok_label').textContent = `Min. Stok (${currentSatuanData.besar})`;
            document.getElementById('addInputInfo').textContent = `Input dalam: ${currentSatuanData.besar}`;
        }
    }

    // Fungsi untuk update label input di modal tambah stok
    function updateTambahInputLabel() {
        const satuanInput = document.querySelector('#tambahStokModal input[name="satuan_input"]:checked');
        if (!satuanInput) return;
        
        const satuanValue = satuanInput.value;
        
        if (satuanValue === 'kecil') {
            document.getElementById('tambah_stok_label').textContent = `Jumlah Stok Tambahan (${currentSatuanData.kecil})`;
            document.getElementById('tambahInputInfo').textContent = `Input dalam: ${currentSatuanData.kecil}`;
        } else {
            document.getElementById('tambah_stok_label').textContent = `Jumlah Stok Tambahan (${currentSatuanData.besar})`;
            document.getElementById('tambahInputInfo').textContent = `Input dalam: ${currentSatuanData.besar}`;
        }
    }

    // Modal functions
    function openAddBahanModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Reset form
        document.getElementById('addBahanForm').reset();
        
        // Set default radio button
        document.querySelector('#addModal input[name="satuan_input"][value="kecil"]').checked = true;
        
        // Update konversi info
        updateAddKonversiInfo();
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
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
                document.getElementById('edit_id_konversi').value = data.id_konversi;
                
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

    function showDetail(bahanId) {
        showLoading('Memuat detail bahan baku...');
        
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
                
                // Hitung perhitungan konversi
                const stokKecil = data.stok;
                const satuanKecil = data.konversi ? data.konversi.satuan_kecil : 'kg';
                const satuanBesar = data.konversi ? data.konversi.satuan_besar : 'kg';
                const jumlahKonversi = data.konversi ? data.konversi.jumlah : 1;
                
                // Hitung stok dalam satuan besar
                const stokBesar = Math.floor(stokKecil / jumlahKonversi);
                const sisaStok = stokKecil % jumlahKonversi;
                
                let displayStokBesar = '';
                if (sisaStok > 0) {
                    displayStokBesar = `${stokBesar} ${satuanBesar} + ${sisaStok} ${satuanKecil}`;
                } else {
                    displayStokBesar = `${stokBesar} ${satuanBesar}`;
                }
                
                // Hitung status
                let status = 'Cukup';
                let statusClass = 'bg-green-100 text-green-600';
                if (data.stok == 0) {
                    status = 'Habis';
                    statusClass = 'bg-red-200 text-red-700';
                } else if (data.stok <= data.min_stok) {
                    status = 'Rendah';
                    statusClass = 'bg-red-100 text-red-600';
                }
                
                // Isi data ke modal detail
                document.getElementById('detail_nama').textContent = data.nama;
                document.getElementById('detail_kategori').textContent = data.kategori;
                document.getElementById('detail_status').textContent = status;
                document.getElementById('detail_status').className = `px-2 py-1 text-xs font-medium rounded-full ${statusClass}`;
                
                document.getElementById('detail_stok_kecil').textContent = `${stokKecil} ${satuanKecil}`;
                document.getElementById('detail_stok_besar').textContent = `(${displayStokBesar})`;
                document.getElementById('detail_min_stok').textContent = `${data.min_stok} ${satuanKecil}`;
                document.getElementById('detail_harga_satuan').textContent = `Rp ${formatRupiah(data.harga_satuan)}`;
                
                document.getElementById('detail_satuan_besar').textContent = satuanBesar;
                document.getElementById('detail_satuan_kecil').textContent = satuanKecil;
                document.getElementById('detail_konversi').textContent = `1 ${satuanBesar} = ${jumlahKonversi} ${satuanKecil}`;
                document.getElementById('detail_konversi_info').textContent = `1 ${satuanBesar} = ${jumlahKonversi} ${satuanKecil}`;
                
                document.getElementById('detail_tglupdate').textContent = new Date(data.tglupdate).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                document.getElementById('detailModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showError(error.message || 'Gagal memuat detail bahan baku');
            });
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
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
                
                const satuanKecil = data.konversi ? data.konversi.satuan_kecil : 'kg';
                const satuanBesar = data.konversi ? data.konversi.satuan_besar : 'kg';
                const jumlahKonversi = data.konversi ? data.konversi.jumlah : 1;
                
                // Simpan data satuan untuk modal tambah stok
                currentSatuanData = {
                    besar: satuanBesar,
                    kecil: satuanKecil,
                    jumlah: parseInt(jumlahKonversi) || 1
                };
                
                // Update info konversi
                document.getElementById('tambah_stok_konversi').textContent = 
                    `1 ${satuanBesar} = ${jumlahKonversi} ${satuanKecil}`;
                document.getElementById('tambahKonversiText').textContent = 
                    `Konversi: 1 ${satuanBesar} = ${jumlahKonversi} ${satuanKecil}`;
                
                // Reset radio button ke kecil
                const radioKecil = document.querySelector('#tambahStokModal input[name="satuan_input"][value="kecil"]');
                const radioBesar = document.querySelector('#tambahStokModal input[name="satuan_input"][value="besar"]');
                if (radioKecil) radioKecil.checked = true;
                if (radioBesar) radioBesar.checked = false;
                
                // Update label
                updateTambahInputLabel();
                
                // Reset input value
                document.getElementById('tambah_stok_jumlah').value = '';
                
                // Tampilkan modal
                document.getElementById('tambahStokModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Fokus ke input
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
        
        data.stok = parseInt(data.stok) || 0;
        data.min_stok = parseInt(data.min_stok) || 0;
        data.harga_satuan = parseInt(data.harga_satuan) || 0;
        data.id_konversi = parseInt(data.id_konversi);
        // satuan_input sudah ada dari radio button
        
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
                Swal.fire({
                    title: 'Sukses!',
                    text: data.message || 'Bahan baku berhasil ditambahkan',
                    icon: 'success',
                    confirmButtonColor: '#10B981',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        closeAddModal();
                        location.reload();
                    }
                });
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
        
        data.stok = parseInt(data.stok) || 0;
        data.min_stok = parseInt(data.min_stok) || 0;
        data.harga_satuan = parseInt(data.harga_satuan) || 0;
        data.id_konversi = parseInt(data.id_konversi);
        
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
        const tambahStokValue = parseInt(document.getElementById('tambah_stok_jumlah').value) || 0;
        const satuanInput = document.querySelector('#tambahStokModal input[name="satuan_input"]:checked').value;
        
        if (tambahStokValue <= 0) {
            showError('Jumlah stok tambahan harus lebih dari 0');
            isSubmitting = false;
            return;
        }
        
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
                tambah_stok: tambahStokValue,
                satuan_input: satuanInput
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
                Swal.fire({
                    title: 'Sukses!',
                    text: data.message || 'Stok berhasil ditambahkan',
                    icon: 'success',
                    confirmButtonColor: '#10B981',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        closeTambahStokModal();
                        location.reload();
                    }
                });
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

    function formatRupiah(angka) {
        if (!angka) return '0';
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Initialize on page load
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
                closeDetailModal();
            }
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            const tambahStokModal = document.getElementById('tambahStokModal');
            const detailModal = document.getElementById('detailModal');
            
            if (e.target === addModal) closeAddModal();
            if (e.target === editModal) closeEditModal();
            if (e.target === tambahStokModal) closeTambahStokModal();
            if (e.target === detailModal) closeDetailModal();
        });
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

    /* Style untuk pagination */
    .pagination-link {
        min-width: 2.5rem;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .pagination-link:hover {
        background-color: #f3f4f6;
    }

    .pagination-active {
        background-color: #10B981;
        color: white;
    }

    .pagination-disabled {
        background-color: #f9fafb;
        color: #9ca3af;
        cursor: not-allowed;
    }
</style>
@endsection