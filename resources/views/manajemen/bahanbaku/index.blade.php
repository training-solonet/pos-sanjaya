@extends('layouts.manajemen.header')

@section('title', 'Stok Bahan Baku')
@section('content')
    <main class="p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-2xl font-bold text-gray-900">Stok Bahan Baku</h2>
                <div class="flex gap-2">
                    {{-- <a href="{{ route('manajemen.opname.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                        <i class="fas fa-clipboard-check mr-2"></i>Stok Opname
                    </a> --}}
                    <a href="{{ route('manajemen.bahanbaku.create') }}" class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all flex items-center">
                        <i class="fas fa-plus mr-2"></i>Tambah Bahan
                    </a>
                </div>
            </div>

            <!-- Alert Stok Rendah -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4" id="lowStockAlert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    <span class="text-red-800 font-medium">Peringatan: beberapa bahan baku memiliki stok rendah!</span>
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
                    <select id="categoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" onchange="filterData()">
                        <option value="">Semua Kategori</option>
                        <option value="Bahan Utama">Bahan Utama</option>
                        <option value="Bahan Pembantu">Bahan Pembantu</option>
                    </select>
                    <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" onchange="filterData()">
                        <option value="">Semua Status</option>
                        <option value="Cukup">Stok Cukup</option>
                        <option value="Rendah">Stok Rendah</option>
                        <option value="Habis">Habis</option>
                    </select>
                </div>
            </div>

            <!-- Stock Grid -->
            <div id="stockGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"></div>

        </div>
    </main>

    <!-- No Data Message (initially hidden) -->
    <div id="noDataMessage" class="hidden text-center py-12">
        <div class="text-gray-400 text-6xl mb-4">
            <i class="fas fa-search"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data yang ditemukan</h3>
        <p class="text-gray-500">Coba ubah filter pencarian atau kata kunci</p>
    </div>

    @push('scripts')
    <script>
        // Data from server (JSON-encoded). Use empty array fallback to avoid Blade parse issues
        const bahanBakuData = @json($bahanBaku ?? []);

        // Sample fallback data for development when server provides no data
        const _sampleBahanBaku = [
            { id: 1, nama: 'Tepung Terigu Protein Tinggi', stokSaatIni: 50, minStok: 20, satuan: 'kg', harga: 12000, kategori: 'Bahan Utama', status: 'Cukup' },
            { id: 2, nama: 'Ragi Instan', stokSaatIni: 2, minStok: 5, satuan: 'kg', harga: 85000, kategori: 'Bahan Utama', status: 'Rendah' }
        ];

        if(!Array.isArray(bahanBakuData) || bahanBakuData.length === 0){
            bahanBakuData = _sampleBahanBaku;
        }

        function formatRupiah(amount){
            return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0}).format(amount);
        }

        function generateStockCard(item){
            const statusConfig = {
                'Cukup': { bg: 'border-gray-200', badge: 'bg-green-100 text-green-600', textColor: 'text-gray-900', button: 'bg-green-100 text-green-600 hover:bg-green-200' },
                'Rendah': { bg: 'border-red-200', badge: 'bg-red-100 text-red-600', textColor: 'text-red-600', button: 'bg-red-100 text-red-600 hover:bg-red-200' },
                'Habis': { bg: 'border-red-300', badge: 'bg-red-200 text-red-700', textColor: 'text-red-700', button: 'bg-red-200 text-red-700 hover:bg-red-300' }
            };
            const cfg = statusConfig[item.status]||statusConfig['Cukup'];
            return `
                <div class="bg-white rounded-lg border ${cfg.bg} p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">${item.nama}</h3>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${cfg.badge}">${item.status}</span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Stok Saat Ini</span><span class="font-medium ${item.status!=='Cukup'?cfg.textColor:''}">${item.stokSaatIni} ${item.satuan}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Min. Stok</span><span class="font-medium">${item.minStok} ${item.satuan}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Harga/${item.satuan}</span><span class="font-medium">${formatRupiah(item.harga)}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Kategori</span><span class="font-medium">${item.kategori}</span></div>
                    </div>
                    <div class="mt-4 pt-4 border-t flex space-x-2">
                        <a href="{{ url('manajemen/bahanbaku') }}/${item.id}/edit" class="flex-1 px-3 py-2 bg-primary/10 text-primary rounded-lg text-sm hover:bg-primary/20">Edit</a>
                        <button class="flex-1 px-3 py-2 ${cfg.button} rounded-lg text-sm">Tambah</button>
                    </div>
                </div>
            `;
        }

        function renderStockGrid(data){
            const grid=document.getElementById('stockGrid');
            const noDataMessage=document.getElementById('noDataMessage');
            if(!grid) return;
            if(data.length===0){grid.innerHTML=''; if(noDataMessage) noDataMessage.classList.remove('hidden'); return;} 
            if(noDataMessage) noDataMessage.classList.add('hidden');
            grid.innerHTML=data.map(item=>generateStockCard(item)).join('');
        }

        function filterData(){
            const searchTerm=document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter=document.getElementById('categoryFilter').value;
            const statusFilter=document.getElementById('statusFilter').value;
            let filtered=bahanBakuData.filter(item=>{
                const matchesSearch=item.nama.toLowerCase().includes(searchTerm);
                const matchesCategory=!categoryFilter||item.kategori===categoryFilter;
                const matchesStatus=!statusFilter||item.status===statusFilter;
                return matchesSearch&&matchesCategory&&matchesStatus;
            });
            renderStockGrid(filtered);
            updateLowStockAlert(filtered);
        }

        function updateLowStockAlert(data=bahanBakuData){
            const lowStockItems=data.filter(i=>i.status==='Rendah'||i.status==='Habis');
            const el=document.getElementById('lowStockAlert');
            if(el){
                const span=el.querySelector('span');
                if(lowStockItems.length>0){span.textContent=`Peringatan: ${lowStockItems.length} bahan baku memiliki stok rendah atau habis!`; el.classList.remove('hidden');}
                else el.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded',function(){ renderStockGrid(bahanBakuData); updateLowStockAlert(); });
    </script>
    @endpush

@endsection

