@extends('layouts.kasir.index')

@section('page-title', 'Data Customer')
@section('page-description', 'Sistem kasir dan penjualan')

@section('content')
 <main class="p-4 sm:p-6 lg:p-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Data Customer</h3>
                        <p class="text-sm text-gray-500 mt-1">Total: {{ $customers->total() }} customer</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="openAddModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-sm transition">
                            <i class="fas fa-plus mr-2"></i> Tambah Customer
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Customer</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Telepon</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($customers as $index => $customer)
                            <tr class="hover:bg-green-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-600">{{ ($customers->currentPage() - 1) * $customers->perPage() + $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $customer->nama }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    @if($customer->telepon)
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-phone text-green-600 mr-2 text-xs"></i>
                                            {{ $customer->telepon }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    @if($customer->email && $customer->email !== 'no-reply@example.com')
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-envelope text-blue-600 mr-2 text-xs"></i>
                                            {{ $customer->email }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditModal({{ $customer->id }})" class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-xs font-medium transition shadow-sm">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </button>
                                        <button onclick="deleteCustomer({{ $customer->id }})" class="px-3 py-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 text-xs font-medium transition shadow-sm">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-sm text-gray-500 font-medium">Belum ada customer</p>
                                    <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Customer" untuk menambahkan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($customers->hasPages())
                <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                    <div class="text-sm text-gray-600">
                        Menampilkan {{ $customers->firstItem() }} - {{ $customers->lastItem() }} dari {{ $customers->total() }} customer
                    </div>
                    <div class="flex gap-2">
                        @if($customers->onFirstPage())
                            <button disabled class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg text-sm cursor-not-allowed">
                                <i class="fas fa-chevron-left mr-1"></i> Sebelumnya
                            </button>
                        @else
                            <a href="{{ $customers->previousPageUrl() }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition">
                                <i class="fas fa-chevron-left mr-1"></i> Sebelumnya
                            </a>
                        @endif

                        <div class="flex items-center gap-1">
                            @foreach($customers->getUrlRange(1, $customers->lastPage()) as $page => $url)
                                @if($page == $customers->currentPage())
                                    <span class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition">{{ $page }}</a>
                                @endif
                            @endforeach
                        </div>

                        @if($customers->hasMorePages())
                            <a href="{{ $customers->nextPageUrl() }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition">
                                Selanjutnya <i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        @else
                            <button disabled class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg text-sm cursor-not-allowed">
                                Selanjutnya <i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="customerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[60] backdrop-blur-sm">
        <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-2xl animate-fadeIn">
            <div class="flex items-center justify-between mb-6">
                <h4 id="modalTitle" class="text-xl font-bold text-gray-900">Tambah Customer</h4>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="customerForm">
                @csrf
                <input type="hidden" id="customerId">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-green-600 mr-1"></i> Nama Customer <span class="text-red-500">*</span>
                        </label>
                        <input id="customerName" name="nama" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" placeholder="Masukkan nama customer" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-phone text-blue-600 mr-1"></i> Telepon
                        </label>
                        <input id="customerPhone" name="telepon" type="tel" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" placeholder="08xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-purple-600 mr-1"></i> Email
                        </label>
                        <input id="customerEmail" name="email" type="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" placeholder="email@contoh.com">
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        
        // Sidebar toggle (mobile)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        // Date/time
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            const dateTimeElement = document.getElementById('currentDateTime');
            if (dateTimeElement) dateTimeElement.textContent = now.toLocaleDateString('id-ID', options);
        }

        let editingId = null;

        function openAddModal() {
            editingId = null;
            document.getElementById('modalTitle').textContent = 'Tambah Customer';
            document.getElementById('customerId').value = '';
            document.getElementById('customerName').value = '';
            document.getElementById('customerPhone').value = '';
            document.getElementById('customerEmail').value = '';
            document.getElementById('customerModal').classList.remove('hidden');
            document.getElementById('customerModal').classList.add('flex');
        }

        async function openEditModal(id) {
          try {
                const response = await fetch(`/kasir/customer/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const customer = result.data;
                    editingId = id;
                    document.getElementById('modalTitle').textContent = 'Edit Customer';
                    document.getElementById('customerId').value = customer.id;
                    document.getElementById('customerName').value = customer.nama;
                    document.getElementById('customerPhone').value = customer.telepon || '';
                    document.getElementById('customerEmail').value = customer.email !== 'no-reply@example.com' ? customer.email : '';
                    document.getElementById('customerModal').classList.remove('hidden');
                    document.getElementById('customerModal').classList.add('flex');
                } else {
                    alert(result.message || 'Customer tidak ditemukan');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal mengambil data customer');
            }
        }

        function closeModal() {
            document.getElementById('customerModal').classList.add('hidden');
            document.getElementById('customerModal').classList.remove('flex');
        }

        // Toast notification
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-[70] transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-600' : 'bg-red-600'
            }`;
            toast.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.classList.add('opacity-0'), 2500);
            setTimeout(() => toast.remove(), 3000);
        }

        // Handle form submit
        document.getElementById('customerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
            
            const formData = new FormData(this);
            const data = {
                nama: formData.get('nama'),
                telepon: formData.get('telepon'),
                email: formData.get('email') || 'no-reply@example.com'
            };
            
            const id = document.getElementById('customerId').value;
            const url = id ? `/kasir/customer/${id}` : '/kasir/customer';
            const method = id ? 'PUT' : 'POST';
            
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    closeModal();
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showToast(result.message || 'Gagal menyimpan customer', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menyimpan customer', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        async function deleteCustomer(id) {
            if (!confirm('Yakin ingin menghapus customer ini?')) return;
            
            try {
                const response = await fetch(`/kasir/customer/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showToast(result.message || 'Gagal menghapus customer', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menghapus customer', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 60000);
        });
    </script>
@endsection