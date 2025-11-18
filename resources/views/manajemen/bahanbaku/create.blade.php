@extends('layouts.manajemen.index')

@section('content')
    <main class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-xl font-semibold mb-4">Tambah Bahan Baku</h2>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 p-3 rounded">
                    <ul class="text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('manajemen.bahanbaku.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required class="mt-1 block w-full border border-gray-300 rounded-md p-2" />
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stok</label>
                            <input type="number" name="stok" step="0.01" value="{{ old('stok') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Min Stok</label>
                            <input type="number" name="min_stok" step="0.01" value="{{ old('min_stok') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Satuan</label>
                            <input type="text" name="satuan" value="{{ old('satuan') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <input type="text" name="kategori" value="{{ old('kategori') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Harga Satuan</label>
                        <input type="number" name="harga_satuan" step="0.01" value="{{ old('harga_satuan') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" />
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('manajemen.bahanbaku.index') }}" class="px-4 py-2 bg-gray-200 rounded-md">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection
