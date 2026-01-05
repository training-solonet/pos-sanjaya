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
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kode Member</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Customer</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Telepon</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Total Poin</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($customers as $index => $customer)
                            <tr class="hover:bg-green-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-600">{{ ($customers->currentPage() - 1) * $customers->perPage() + $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($customer->kode_member)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <i class="fas fa-id-card mr-1"></i>
                                            {{ $customer->kode_member }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-xs">-</span>
                                    @endif
                                </td>
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
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800">
                                        <i class="fas fa-coins mr-1"></i>
                                        {{ number_format($customer->total_poin ?? 0) }} Poin
                                    </span>
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
                                <td colspan="7" class="px-6 py-12 text-center">
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
                        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>Kode member akan dibuat otomatis</p>
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

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[70] backdrop-blur-sm">
        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-2xl animate-fadeIn">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900">Konfirmasi Hapus</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-6">Yakin ingin menghapus customer ini?</p>
            <div class="flex justify-end gap-3">
                <button onclick="closeDeleteModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition text-sm">
                    <i class="fas fa-times mr-1"></i> Batal
                </button>
                <button onclick="confirmDelete()" class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition shadow-sm text-sm">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>

    <script>
        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        
        let editingId = null;

        function openAddModal() {
            editingId = null;
            const modal = document.getElementById('customerModal');
            const modalTitle = document.getElementById('modalTitle');
            const customerId = document.getElementById('customerId');
            const customerName = document.getElementById('customerName');
            const customerPhone = document.getElementById('customerPhone');
            const customerEmail = document.getElementById('customerEmail');
            
            if (modalTitle) modalTitle.textContent = 'Tambah Customer';
            if (customerId) customerId.value = '';
            if (customerName) customerName.value = '';
            if (customerPhone) customerPhone.value = '';
            if (customerEmail) customerEmail.value = '';
            
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
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
                    
                    const modalTitle = document.getElementById('modalTitle');
                    const customerId = document.getElementById('customerId');
                    const customerName = document.getElementById('customerName');
                    const customerPhone = document.getElementById('customerPhone');
                    const customerEmail = document.getElementById('customerEmail');
                    const modal = document.getElementById('customerModal');
                    
                    if (modalTitle) modalTitle.textContent = `Edit Customer - ${customer.kode_member || 'No Member'}`;
                    if (customerId) customerId.value = customer.id;
                    if (customerName) customerName.value = customer.nama;
                    if (customerPhone) customerPhone.value = customer.telepon || '';
                    if (customerEmail) customerEmail.value = customer.email !== 'no-reply@example.com' ? customer.email : '';
                    
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    }
                } else {
                    alert(result.message || 'Customer tidak ditemukan');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal mengambil data customer');
            }
        }

        function closeModal() {
            const modal = document.getElementById('customerModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
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
        const customerForm = document.getElementById('customerForm');
        if (customerForm) {
            customerForm.addEventListener('submit', async function(e) {
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
        }

        let customerToDelete = null;

        function deleteCustomer(id) {
            customerToDelete = id;
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            customerToDelete = null;
        }

        async function confirmDelete() {
            if (!customerToDelete) return;
            
            const deleteBtn = event.target;
            const originalText = deleteBtn.innerHTML;
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menghapus...';
            
            try {
                const response = await fetch(`/kasir/customer/${customerToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    closeDeleteModal();
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showToast(result.message || 'Gagal menghapus customer', 'error');
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menghapus customer', 'error');
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = originalText;
            }
        }
    </script>
@endsection