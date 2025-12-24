@extends('layouts.kasir.index')

@section('page-title', 'data custommer')
@section('page-description', 'Sistem kasir dan penjualan')

@section('content')
 <main class="p-4 sm:p-6 lg:p-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Data Customer</h3>
                    <div class="flex items-center space-x-2">
                        <button id="addCustomerBtn" onclick="openAddModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"><i class="fas fa-plus mr-2"></i> Tambah Customer</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="customersTable" class="bg-white divide-y divide-gray-200">
                            <!-- rows injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="customerModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-lg p-6">
            <h4 id="modalTitle" class="text-lg font-semibold mb-4">Tambah Customer</h4>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium">Nama <span class="text-red-500">*</span></label>
                    <input id="customerName" class="w-full mt-1 px-3 py-2 border rounded-lg" placeholder="Nama customer">
                </div>
                <div>
                    <label class="text-sm font-medium">Telepon</label>
                    <input id="customerPhone" class="w-full mt-1 px-3 py-2 border rounded-lg" placeholder="08xxxxxxxx">
                </div>
                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input id="customerEmail" class="w-full mt-1 px-3 py-2 border rounded-lg" placeholder="email@contoh.com">
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-100 rounded-lg">Batal</button>
                <button onclick="saveCustomer()" class="px-4 py-2 bg-green-600 text-white rounded-lg">Simpan</button>
            </div>
        </div>
    </div>

    <script>
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

        // LocalStorage key
        const STORAGE_KEY = 'pos_customers';
        let editingId = null;

        function getCustomers() {
            try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); } catch(e) { return []; }
        }

        function saveCustomers(customers) { localStorage.setItem(STORAGE_KEY, JSON.stringify(customers)); }

        function renderCustomers() {
            const tbody = document.getElementById('customersTable');
            const customers = getCustomers();
            tbody.innerHTML = '';
            if (customers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada customer.</td></tr>';
                return;
            }
            customers.forEach((c, idx) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-4 py-3 text-sm text-gray-700">${idx+1}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${escapeHtml(c.name)}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${escapeHtml(c.phone || '')}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${escapeHtml(c.email || '')}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        <button onclick="openEditModal(${c.id})" class="px-2 py-1 bg-blue-600 text-white rounded text-xs mr-2">Edit</button>
                        <button onclick="deleteCustomer(${c.id})" class="px-2 py-1 bg-red-500 text-white rounded text-xs">Hapus</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function escapeHtml(text) {
            return String(text || '').replace(/[&<>\"']/g, function (s) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[s]; });
        }

        function openAddModal() {
            editingId = null;
            document.getElementById('modalTitle').textContent = 'Tambah Customer';
            document.getElementById('customerName').value = '';
            document.getElementById('customerPhone').value = '';
            document.getElementById('customerEmail').value = '';
            document.getElementById('customerModal').classList.remove('hidden');
            document.getElementById('customerModal').classList.add('flex');
        }

        function openEditModal(id) {
            const customers = getCustomers();
            const c = customers.find(x => x.id === id);
            if (!c) return alert('Customer tidak ditemukan');
            editingId = id;
            document.getElementById('modalTitle').textContent = 'Edit Customer';
            document.getElementById('customerName').value = c.name;
            document.getElementById('customerPhone').value = c.phone || '';
            document.getElementById('customerEmail').value = c.email || '';
            document.getElementById('customerModal').classList.remove('hidden');
            document.getElementById('customerModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('customerModal').classList.add('hidden');
            document.getElementById('customerModal').classList.remove('flex');
        }

        function saveCustomer() {
            const name = document.getElementById('customerName').value.trim();
            const phone = document.getElementById('customerPhone').value.trim();
            const email = document.getElementById('customerEmail').value.trim();
            if (!name) return alert('Nama customer wajib diisi');
            const customers = getCustomers();
            if (editingId) {
                const idx = customers.findIndex(x => x.id === editingId);
                if (idx !== -1) {
                    customers[idx].name = name; customers[idx].phone = phone; customers[idx].email = email;
                }
            } else {
                const id = Date.now();
                customers.push({ id, name, phone, email });
            }
            saveCustomers(customers);
            renderCustomers();
            closeModal();
        }

        function deleteCustomer(id) {
            if (!confirm('Hapus customer ini?')) return;
            const customers = getCustomers().filter(x => x.id !== id);
            saveCustomers(customers);
            renderCustomers();
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 60000);
            renderCustomers();
        });
    </script>
@endsection