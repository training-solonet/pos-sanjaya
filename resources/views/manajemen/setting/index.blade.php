@extends('layouts.manajemen.index')

@section('content')
    <!-- Main Content -->
    <main class="p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Pajak dan Promo</h2>
                    <p class="text-gray-600 mt-1">Kelola pajak dan promo transaksi</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button id="pajakTab" onclick="switchTab('pajak')"
                            class="tab-button active px-6 py-3 border-b-2 border-green-500 text-green-600 font-medium transition-colors">
                            <i class="fas fa-percentage mr-2"></i>Pajak
                        </button>
                        <button id="promoTab" onclick="switchTab('promo')"
                            class="tab-button px-6 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium transition-colors">
                            <i class="fas fa-tags mr-2"></i>Promo
                        </button>
                        <button id="bundleTab" onclick="switchTab('bundle')"
                            class="tab-button px-6 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium transition-colors">
                            <i class="fas fa-layer-group mr-2"></i>Bundle Promo
                        </button>
                    </nav>
                </div>

                <!-- Pajak Content -->
                <div id="pajakContent" class="tab-content p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Pajak</h3>
                        <button onclick="openAddPajakModal()"
                            class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all flex items-center">
                            <i class="fas fa-plus mr-2"></i> Tambah Pajak
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pajak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Persentase</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($pajaks as $pajak)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $pajak->nama_pajak }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-gray-900 font-semibold">{{ $pajak->persen }}%</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">
                                            {{ \Carbon\Carbon::parse($pajak->start_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($pajak->status)
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <button onclick="editPajak({{ $pajak->id }})"
                                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="togglePajakStatus({{ $pajak->id }}, {{ $pajak->status ? 'false' : 'true' }})"
                                                    class="px-3 py-1 {{ $pajak->status ? 'bg-gray-500' : 'bg-green-500' }} text-white rounded hover:opacity-80 transition-opacity">
                                                    <i class="fas fa-{{ $pajak->status ? 'times' : 'check' }}"></i>
                                                </button>
                                                <button onclick="deletePajak({{ $pajak->id }})"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-2"></i>
                                            <p>Belum ada data pajak</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Promo Content -->
                <div id="promoContent" class="tab-content hidden p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Promo</h3>
                        <button onclick="openAddPromoModal()"
                            class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all flex items-center">
                            <i class="fas fa-plus mr-2"></i> Tambah Promo
                        </button>
                    </div>

                    <div class="overflow-x-auto mb-8">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Promo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Promo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Akhir</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($promos->where('jenis', '!=', 'bundle') as $promo)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="font-mono font-semibold text-green-600">{{ $promo->kode_promo }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $promo->nama_promo }}</div>
                                            @if($promo->is_stackable)
                                                <span class="text-xs text-blue-600"><i class="fas fa-layer-group mr-1"></i>Stackable</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ ucfirst(str_replace('_', ' ', $promo->jenis)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if(in_array($promo->jenis, ['diskon_persen']))
                                                <span class="text-gray-900 font-semibold">{{ $promo->nilai }}%</span>
                                            @else
                                                <span class="text-gray-900 font-semibold">Rp {{ number_format($promo->nilai, 0, ',', '.') }}</span>
                                            @endif
                                            @if($promo->min_transaksi > 0)
                                                <div class="text-xs text-gray-500">Min: Rp {{ number_format($promo->min_transaksi, 0, ',', '.') }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($promo->start_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($promo->end_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($promo->status)
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <button onclick="editPromo({{ $promo->id }})"
                                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="togglePromoStatus({{ $promo->id }}, {{ $promo->status ? 'false' : 'true' }})"
                                                    class="px-3 py-1 {{ $promo->status ? 'bg-gray-500' : 'bg-green-500' }} text-white rounded hover:opacity-80 transition-opacity">
                                                    <i class="fas fa-{{ $promo->status ? 'times' : 'check' }}"></i>
                                                </button>
                                                <button onclick="deletePromo({{ $promo->id }})"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-2"></i>
                                            <p>Belum ada data promo</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bundle Promo Content -->
                <div id="bundleContent" class="tab-content hidden p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Bundle Promo</h3>
                            <p class="text-sm text-gray-600 mt-1">Paket bundling produk dengan harga spesial</p>
                        </div>
                        <button onclick="openAddBundleModal()"
                            class="px-4 py-2 bg-gradient-to-r from-purple-400 to-purple-700 text-white rounded-lg hover:from-purple-500 hover:to-purple-800 transition-all flex items-center">
                            <i class="fas fa-plus mr-2"></i> Tambah Bundle
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Bundle</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Bundle</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Bundle</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Akhir</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse ($promos->where('jenis', 'bundle') as $bundle)
                                        <tr>
                                            <td class="px-6 py-4">
                                                <div class="font-mono font-semibold text-purple-600">{{ $bundle->kode_promo }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-medium text-gray-900">{{ $bundle->nama_promo }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-600">
                                                    @if($bundle->bundleProducts && $bundle->bundleProducts->count() > 0)
                                                        @foreach($bundle->bundleProducts as $item)
                                                            <div class="flex items-center gap-2">
                                                                <i class="fas fa-cookie-bite text-xs text-gray-400"></i>
                                                                <span>{{ $item->produk->nama ?? 'Produk tidak ditemukan' }} ({{ $item->quantity }}x)</span>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <span class="text-gray-400 italic">Belum ada produk</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-gray-900 font-semibold">Rp {{ number_format($bundle->nilai, 0, ',', '.') }}</span>
                                                @if($bundle->min_transaksi > 0)
                                                    <div class="text-xs text-gray-500">Qty: {{ $bundle->min_transaksi }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                                                        {{ $bundle->stok > 10 ? 'bg-green-100 text-green-800' : ($bundle->stok > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                        <i class="fas fa-box mr-1"></i>{{ $bundle->stok }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($bundle->start_date)->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($bundle->end_date)->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($bundle->status)
                                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>Aktif
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <button onclick="editBundle({{ $bundle->id }})"
                                                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick="togglePromoStatus({{ $bundle->id }}, {{ $bundle->status ? 'false' : 'true' }})"
                                                        class="px-3 py-1 {{ $bundle->status ? 'bg-gray-500' : 'bg-green-500' }} text-white rounded hover:opacity-80 transition-opacity">
                                                        <i class="fas fa-{{ $bundle->status ? 'times' : 'check' }}"></i>
                                                    </button>
                                                    <button onclick="deletePromo({{ $bundle->id }})"
                                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                                <i class="fas fa-box-open text-4xl mb-2"></i>
                                                <p>Belum ada bundle promo</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Pajak -->
    <div id="pajakModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 id="pajakModalTitle" class="text-xl font-bold text-gray-900">Tambah Pajak</h3>
                <button onclick="closePajakModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="pajakForm" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="pajakId" name="pajak_id">
                <input type="hidden" id="pajakMethod" name="_method" value="POST">
                
                <div>
                    <label for="nama_pajak" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Pajak <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_pajak" name="nama_pajak" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Contoh: PPN, Pajak Daerah">
                    <span class="text-red-500 text-xs hidden" id="error_nama_pajak"></span>
                </div>

                <div>
                    <label for="persen" class="block text-sm font-medium text-gray-700 mb-2">
                        Persentase (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="persen" name="persen" required min="0" max="100" step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Contoh: 10">
                    <span class="text-red-500 text-xs hidden" id="error_persen"></span>
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="start_date" name="start_date" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <span class="text-red-500 text-xs hidden" id="error_start_date"></span>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" id="status" name="status" value="1" checked
                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">Aktif</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closePajakModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Promo -->
    <div id="promoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 id="promoModalTitle" class="text-xl font-bold text-gray-900">Tambah Promo</h3>
                <button onclick="closePromoModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="promoForm" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="promoId" name="promo_id">
                <input type="hidden" id="promoMethod" name="_method" value="POST">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="kode_promo" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Promo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="kode_promo" name="kode_promo" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 uppercase"
                            placeholder="Contoh: DISKON2024">
                        <span class="text-red-500 text-xs hidden" id="error_kode_promo"></span>
                    </div>

                    <div>
                        <label for="nama_promo" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Promo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_promo" name="nama_promo" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Contoh: Diskon Tahun Baru">
                        <span class="text-red-500 text-xs hidden" id="error_nama_promo"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Promo <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis" name="jenis" required onchange="toggleMaksPotongan()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Pilih Jenis</option>
                            <option value="diskon_persen">Diskon Persentase</option>
                            <option value="cashback">Cashback</option>
                        </select>
                        <span class="text-red-500 text-xs hidden" id="error_jenis"></span>
                    </div>

                    <div>
                        <label for="nilai" class="block text-sm font-medium text-gray-700 mb-2">
                            Nilai <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="nilai" name="nilai" required min="0" step="1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Nilai (% atau Rp)">
                        <span class="text-red-500 text-xs hidden" id="error_nilai"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="min_transaksi" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Transaksi (Rp)
                        </label>
                        <input type="number" id="min_transaksi" name="min_transaksi" min="0" step="1000" value="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="0">
                        <span class="text-red-500 text-xs hidden" id="error_min_transaksi"></span>
                    </div>

                    <div id="maksPotonganWrapper" style="display: none;">
                        <label for="maks_potongan" class="block text-sm font-medium text-gray-700 mb-2">
                            Maksimal Potongan (Rp)
                        </label>
                        <input type="number" id="maks_potongan" name="maks_potongan" min="0" step="1000"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Kosongkan jika tidak ada batas">
                        <span class="text-red-500 text-xs hidden" id="error_maks_potongan"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="promo_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="promo_start_date" name="start_date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <span class="text-red-500 text-xs hidden" id="error_start_date_promo"></span>
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="end_date" name="end_date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <span class="text-red-500 text-xs hidden" id="error_end_date"></span>
                    </div>
                </div>

                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="is_stackable" name="is_stackable" value="1"
                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">Bisa Digabung (Stackable)</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" id="promo_status" name="status" value="1" checked
                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">Aktif</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closePromoModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Bundle -->
    <div id="bundleModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 id="bundleModalTitle" class="text-xl font-bold text-gray-900">Tambah Bundle Promo</h3>
                <button onclick="closeBundleModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="bundleForm" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="bundleId" name="bundle_id">
                <input type="hidden" id="bundleMethod" name="_method" value="POST">
                <input type="hidden" name="type" value="bundle">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="bundle_kode_promo" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Bundle <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="bundle_kode_promo" name="kode_promo" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 uppercase"
                            placeholder="Contoh: BUNDLE2024">
                        <span class="text-red-500 text-xs hidden" id="error_bundle_kode_promo"></span>
                    </div>

                    <div>
                        <label for="bundle_nama_promo" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Bundle <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="bundle_nama_promo" name="nama_promo" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Contoh: Paket Hemat Lebaran">
                        <span class="text-red-500 text-xs hidden" id="error_bundle_nama_promo"></span>
                    </div>
                </div>

                <!-- Product Selection -->
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Pilih Produk Bundle <span class="text-red-500">*</span>
                        </label>
                        <button type="button" onclick="addBundleProduct()" 
                            class="px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors">
                            <i class="fas fa-plus mr-1"></i>Tambah Produk
                        </button>
                    </div>
                    
                    <div id="bundleProductList" class="space-y-2">
                        <!-- Bundle products will be added here dynamically -->
                    </div>
                    <span class="text-red-500 text-xs hidden" id="error_bundle_products"></span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="bundle_harga" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Bundle (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="bundle_harga" name="nilai" required min="0" step="1000"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 font-semibold text-green-600"
                                placeholder="Harga paket bundle">
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-calculator"></i>
                            </div>
                        </div>
                        <div class="mt-1 text-xs text-gray-500 hidden" id="original_price_info">
                            <i class="fas fa-info-circle text-blue-500"></i> 
                            Harga Normal: <span class="font-semibold" id="original_price_display">Rp 0</span>
                        </div>
                        <span class="text-red-500 text-xs hidden" id="error_bundle_harga"></span>
                    </div>

                    <div>
                        <label for="bundle_min_transaksi" class="block text-sm font-medium text-gray-700 mb-2">
                            Quantity Jumlah
                        </label>
                        <input type="number" id="bundle_min_transaksi" name="min_transaksi" min="1" step="1" value="1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Jumlah quantity bundle">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="bundle_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="bundle_start_date" name="start_date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label for="bundle_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="bundle_end_date" name="end_date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="bundle_stok" class="block text-sm font-medium text-gray-700 mb-2">
                            Stok Bundle <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="bundle_stok" name="stok" required min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Masukkan stok bundle">
                        <span class="text-red-500 text-xs hidden" id="error_bundle_stok"></span>
                    </div>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" id="bundle_status" name="status" value="1" checked
                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">Aktif</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closeBundleModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Available products for bundle selection
        const availableProducts = @json($produks);
        let bundleProductCounter = 0;

        function switchTab(tab) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-green-500', 'text-green-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab content
            document.getElementById(tab + 'Content').classList.remove('hidden');
            
            // Add active class to selected tab
            const activeTab = document.getElementById(tab + 'Tab');
            activeTab.classList.add('active', 'border-green-500', 'text-green-600');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            
            // Save active tab to localStorage
            localStorage.setItem('activeTab', tab);
        }

        // Restore active tab on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTab = localStorage.getItem('activeTab');
            if (savedTab) {
                switchTab(savedTab);
            }
        });

        function openAddPajakModal() {
            document.getElementById('pajakModalTitle').textContent = 'Tambah Pajak';
            document.getElementById('pajakForm').reset();
            document.getElementById('pajakId').value = '';
            document.getElementById('pajakMethod').value = 'POST';
            document.getElementById('status').checked = true;
            
            // Clear errors
            document.querySelectorAll('#pajakForm .text-red-500').forEach(el => el.classList.add('hidden'));
            
            document.getElementById('pajakModal').classList.remove('hidden');
        }

        function closePajakModal() {
            document.getElementById('pajakModal').classList.add('hidden');
        }

        function openAddPromoModal() {
            // Save active tab
            localStorage.setItem('activeTab', 'promo');
            
            document.getElementById('promoModalTitle').textContent = 'Tambah Promo';
            document.getElementById('promoForm').reset();
            document.getElementById('promoId').value = '';
            document.getElementById('promoMethod').value = 'POST';
            document.getElementById('promo_status').checked = true;
            
            // Hide maksimal potongan by default
            document.getElementById('maksPotonganWrapper').style.display = 'none';
            
            // Clear errors
            document.querySelectorAll('#promoForm .text-red-500').forEach(el => el.classList.add('hidden'));
            
            document.getElementById('promoModal').classList.remove('hidden');
        }

        function toggleMaksPotongan() {
            const jenis = document.getElementById('jenis').value;
            const wrapper = document.getElementById('maksPotonganWrapper');
            
            // Show maksimal potongan only for diskon_persen
            if (jenis === 'diskon_persen') {
                wrapper.style.display = 'block';
            } else {
                wrapper.style.display = 'none';
                document.getElementById('maks_potongan').value = '';
            }
        }

        function closePromoModal() {
            document.getElementById('promoModal').classList.add('hidden');
        }

        // Handle Pajak Form Submit
        document.getElementById('pajakForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const pajakId = document.getElementById('pajakId').value;
            const url = pajakId 
                ? `/management/setting/${pajakId}`
                : '{{ route("management.setting.store") }}';
            
            const formData = new FormData(this);
            
            // Convert checkbox to boolean
            formData.set('status', document.getElementById('status').checked ? '1' : '0');
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    closePajakModal();
                    location.reload();
                } else {
                    // Display validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorElement = document.getElementById(`error_${key}`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[key][0];
                                errorElement.classList.remove('hidden');
                            }
                        });
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            }
        });

        // Handle Promo Form Submit
        document.getElementById('promoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const promoId = document.getElementById('promoId').value;
            const url = promoId 
                ? `/management/setting/${promoId}`
                : '{{ route("management.setting.store") }}';
            
            const formData = new FormData(this);
            formData.append('type', 'promo');
            
            // Convert checkboxes to boolean
            formData.set('is_stackable', document.getElementById('is_stackable').checked ? '1' : '0');
            formData.set('status', document.getElementById('promo_status').checked ? '1' : '0');
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    closePromoModal();
                    location.reload();
                } else {
                    // Display validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorElement = document.getElementById(`error_${key}`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[key][0];
                                errorElement.classList.remove('hidden');
                            }
                        });
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            }
        });

        async function editPajak(id) {
            try {
                const response = await fetch(`/management/setting/${id}/edit`);
                const data = await response.json();
                
                console.log('Edit Pajak - Data received:', data);
                
                if (response.ok) {
                    document.getElementById('pajakModalTitle').textContent = 'Edit Pajak';
                    document.getElementById('pajakId').value = data.id;
                    document.getElementById('pajakMethod').value = 'PUT';
                    document.getElementById('nama_pajak').value = data.nama_pajak || '';
                    document.getElementById('persen').value = data.persen || '';
                    document.getElementById('start_date').value = data.start_date || '';
                    document.getElementById('status').checked = data.status == 1 || data.status == true;
                    
                    // Clear errors
                    document.querySelectorAll('#pajakForm .text-red-500').forEach(el => el.classList.add('hidden'));
                    
                    document.getElementById('pajakModal').classList.remove('hidden');
                } else {
                    alert('Gagal mengambil data pajak');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data');
            }
        }

        async function editPromo(id) {
            // Save active tab
            localStorage.setItem('activeTab', 'promo');
            
            try {
                const response = await fetch(`/management/setting/${id}/edit?type=promo`);
                const data = await response.json();
                
                console.log('Edit Promo - Data received:', data);
                
                if (response.ok) {
                    document.getElementById('promoModalTitle').textContent = 'Edit Promo';
                    document.getElementById('promoId').value = data.id;
                    document.getElementById('promoMethod').value = 'PUT';
                    document.getElementById('kode_promo').value = data.kode_promo || '';
                    document.getElementById('nama_promo').value = data.nama_promo || '';
                    document.getElementById('jenis').value = data.jenis || '';
                    document.getElementById('nilai').value = data.nilai || '';
                    document.getElementById('min_transaksi').value = data.min_transaksi || 0;
                    document.getElementById('maks_potongan').value = data.maks_potongan || '';
                    document.getElementById('promo_start_date').value = data.start_date || '';
                    document.getElementById('end_date').value = data.end_date || '';
                    document.getElementById('is_stackable').checked = data.is_stackable == 1 || data.is_stackable == true;
                    document.getElementById('promo_status').checked = data.status == 1 || data.status == true;
                    
                    // Toggle maksimal potongan visibility based on jenis
                    toggleMaksPotongan();
                    
                    // Clear errors
                    document.querySelectorAll('#promoForm .text-red-500').forEach(el => el.classList.add('hidden'));
                    
                    document.getElementById('promoModal').classList.remove('hidden');
                } else {
                    alert('Gagal mengambil data promo');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data');
            }
        }

        async function togglePajakStatus(id, status) {
            if (!confirm('Yakin ingin mengubah status pajak ini?')) return;
            
            try {
                const response = await fetch(`/management/setting/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ toggle_status: true, status: status })
                });

                const data = await response.json();

                if (response.ok) {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal mengubah status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        }

        async function togglePromoStatus(id, status) {
            if (!confirm('Yakin ingin mengubah status promo ini?')) return;
            
            try {
                const response = await fetch(`/management/setting/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ toggle_status: true, type: 'promo', status: status })
                });

                const data = await response.json();

                if (response.ok) {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal mengubah status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        }

        async function deletePajak(id) {
            if (!confirm('Yakin ingin menghapus pajak ini? Data yang sudah terhapus tidak dapat dikembalikan.')) return;
            
            try {
                const response = await fetch(`/management/setting/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus pajak');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        }

        async function deletePromo(id) {
            if (!confirm('Yakin ingin menghapus promo ini? Data yang sudah terhapus tidak dapat dikembalikan.')) return;
            
            try {
                const response = await fetch(`/management/setting/${id}?type=promo`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus promo');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        }

        // Close modal when clicking outside
        document.getElementById('pajakModal').addEventListener('click', function(e) {
            if (e.target === this) closePajakModal();
        });

        document.getElementById('promoModal').addEventListener('click', function(e) {
            if (e.target === this) closePromoModal();
        });

        document.getElementById('bundleModal').addEventListener('click', function(e) {
            if (e.target === this) closeBundleModal();
        });

        // Bundle Modal Functions
        function openAddBundleModal() {
            // Save active tab
            localStorage.setItem('activeTab', 'bundle');
            
            document.getElementById('bundleModalTitle').textContent = 'Tambah Bundle Promo';
            document.getElementById('bundleForm').reset();
            document.getElementById('bundleId').value = '';
            document.getElementById('bundleMethod').value = 'POST';
            document.getElementById('bundle_status').checked = true;
            document.getElementById('bundleProductList').innerHTML = '';
            document.getElementById('bundle_min_transaksi').value = '1';
            
            // Hide original price info
            const originalPriceInfo = document.getElementById('original_price_info');
            if (originalPriceInfo) {
                originalPriceInfo.classList.add('hidden');
            }
            
            bundleProductCounter = 0;
            
            // Add one product row by default
            addBundleProduct();
            
            // Clear errors
            document.querySelectorAll('#bundleForm .text-red-500').forEach(el => el.classList.add('hidden'));
            
            document.getElementById('bundleModal').classList.remove('hidden');
        }

        function closeBundleModal() {
            document.getElementById('bundleModal').classList.add('hidden');
        }

        function addBundleProduct() {
            const container = document.getElementById('bundleProductList');
            const index = bundleProductCounter++;
            
            const productRow = document.createElement('div');
            productRow.className = 'p-4 bg-white rounded-lg border border-gray-200';
            productRow.id = `bundle-product-${index}`;
            
            let productOptions = '<option value="">Pilih produk...</option>';
            availableProducts.forEach(product => {
                const sku = product.sku || 'N/A';
                productOptions += `<option value="${product.id}" data-price="${product.harga}" data-name="${product.nama}" data-sku="${sku}">${sku}</option>`;
            });
            
            productRow.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">SKU Produk</label>
                        <select name="bundle_products[${index}][produk_id]" required
                            class="bundle-product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm bg-white font-mono"
                            onchange="calculateBundlePrice(); updateProductInfo(${index})">
                            ${productOptions}
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nama Produk</label>
                        <input type="text" readonly
                            class="product-name-${index} w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-700 font-medium"
                            placeholder="Nama Produk" value="">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Harga Satuan</label>
                        <input type="text" readonly
                            class="product-price-${index} w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-600 font-semibold"
                            placeholder="Rp 0" value="Rp 0">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                        <input type="number" name="bundle_products[${index}][quantity]" value="1" min="1" required
                            class="bundle-quantity w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                            placeholder="1" oninput="calculateBundlePrice(); updateProductInfo(${index})">
                    </div>
                    <div class="md:col-span-2 flex items-end">
                        <button type="button" onclick="removeBundleProduct(${index})"
                            class="w-full px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(productRow);
        }

        function removeBundleProduct(index) {
            const row = document.getElementById(`bundle-product-${index}`);
            if (row) {
                row.remove();
                calculateBundlePrice();
            }
        }

        // Update product info display when product is selected
        function updateProductInfo(index) {
            const select = document.querySelector(`#bundle-product-${index} .bundle-product-select`);
            const priceInput = document.querySelector(`.product-price-${index}`);
            const nameInput = document.querySelector(`.product-name-${index}`);
            
            if (select && select.value) {
                const selectedOption = select.options[select.selectedIndex];
                const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                const name = selectedOption.getAttribute('data-name') || '';
                const formattedPrice = new Intl.NumberFormat('id-ID').format(price);
                
                if (priceInput) {
                    priceInput.value = `Rp ${formattedPrice}`;
                }
                if (nameInput) {
                    nameInput.value = name;
                }
            } else {
                if (priceInput) {
                    priceInput.value = 'Rp 0';
                }
                if (nameInput) {
                    nameInput.value = '';
                }
            }
        }

        // Calculate total bundle price based on selected products
        function calculateBundlePrice() {
            let totalPrice = 0;
            const productSelects = document.querySelectorAll('.bundle-product-select');
            const quantityInputs = document.querySelectorAll('.bundle-quantity');
            
            productSelects.forEach((select, index) => {
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                    const quantity = parseFloat(quantityInputs[index]?.value) || 0;
                    totalPrice += (price * quantity);
                }
            });
            
            // Update bundle price input
            const bundleHargaInput = document.getElementById('bundle_harga');
            if (bundleHargaInput) {
                bundleHargaInput.value = Math.round(totalPrice);
            }
            
            // Update original price display
            const originalPriceInfo = document.getElementById('original_price_info');
            const originalPriceDisplay = document.getElementById('original_price_display');
            if (originalPriceInfo && originalPriceDisplay && totalPrice > 0) {
                const formattedTotal = new Intl.NumberFormat('id-ID').format(totalPrice);
                originalPriceDisplay.textContent = `Rp ${formattedTotal}`;
                originalPriceInfo.classList.remove('hidden');
            } else if (originalPriceInfo) {
                originalPriceInfo.classList.add('hidden');
            }
        }

        // Handle Bundle Form Submit
        document.getElementById('bundleForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const bundleId = document.getElementById('bundleId').value;
            const url = bundleId 
                ? `/management/setting/${bundleId}`
                : '{{ route("management.setting.store") }}';
            
            const formData = new FormData(this);
            formData.append('jenis', 'bundle');
            formData.set('status', document.getElementById('bundle_status').checked ? '1' : '0');
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    closeBundleModal();
                    location.reload();
                } else {
                    // Display validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorElement = document.getElementById(`error_${key}`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[key][0];
                                errorElement.classList.remove('hidden');
                            }
                        });
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            }
        });

        async function editBundle(id) {
            // Save active tab
            localStorage.setItem('activeTab', 'bundle');
            
            try {
                const response = await fetch(`/management/setting/${id}/edit?type=bundle`);
                const data = await response.json();
                
                if (response.ok) {
                    document.getElementById('bundleModalTitle').textContent = 'Edit Bundle Promo';
                    document.getElementById('bundleId').value = data.id;
                    document.getElementById('bundleMethod').value = 'PUT';
                    document.getElementById('bundle_kode_promo').value = data.kode_promo;
                    document.getElementById('bundle_nama_promo').value = data.nama_promo;
                    document.getElementById('bundle_harga').value = data.nilai;
                    document.getElementById('bundle_min_transaksi').value = data.min_transaksi;
                    document.getElementById('bundle_stok').value = data.stok || 0;
                    document.getElementById('bundle_start_date').value = data.start_date || '';
                    document.getElementById('bundle_end_date').value = data.end_date || '';
                    document.getElementById('bundle_status').checked = data.status;
                    
                    // Load bundle products
                    document.getElementById('bundleProductList').innerHTML = '';
                    bundleProductCounter = 0;
                    if (data.bundle_products && data.bundle_products.length > 0) {
                        data.bundle_products.forEach(item => {
                            addBundleProduct();
                            const lastIndex = bundleProductCounter - 1;
                            const row = document.getElementById(`bundle-product-${lastIndex}`);
                            if (row) {
                                const select = row.querySelector('select');
                                const qtyInput = row.querySelector('input[type="number"]');
                                
                                // Set values
                                select.value = item.produk_id;
                                qtyInput.value = item.quantity;
                                
                                // Update product info (nama produk dan harga)
                                updateProductInfo(lastIndex);
                            }
                        });
                        
                        // Calculate total bundle price
                        calculateBundlePrice();
                    } else {
                        addBundleProduct();
                    }
                    
                    document.getElementById('bundleModal').classList.remove('hidden');
                } else {
                    alert('Gagal mengambil data bundle');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data');
            }
        }
    </script>
@endsection
