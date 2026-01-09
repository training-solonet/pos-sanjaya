<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\BundleProduct;
use App\Models\Pajak;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pajaks = Pajak::orderBy('created_at', 'desc')->get();
        $promos = Promo::with('bundleProducts.produk')->orderBy('created_at', 'desc')->get();
        $produks = \App\Models\Produk::where('stok', '>', 0)->orderBy('nama')->get();

        return view('manajemen.setting.index', compact('pajaks', 'promos', 'produks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $type = $request->input('type', 'pajak');

        if ($type === 'promo') {
            return $this->storePromo($request);
        }

        if ($type === 'bundle' || $request->jenis === 'bundle') {
            return $this->storeBundle($request);
        }

        return $this->storePajak($request);
    }

    /**
     * Store pajak
     */
    private function storePajak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pajak' => 'required|string|max:255',
            'persen' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'status' => 'boolean',
        ], [
            'nama_pajak.required' => 'Nama pajak wajib diisi',
            'persen.required' => 'Persentase wajib diisi',
            'persen.numeric' => 'Persentase harus berupa angka',
            'persen.min' => 'Persentase minimal 0',
            'persen.max' => 'Persentase maksimal 100',
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'start_date.date' => 'Format tanggal tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pajak = Pajak::create([
                'nama_pajak' => $request->nama_pajak,
                'persen' => $request->persen,
                'start_date' => $request->start_date,
                'status' => $request->has('status') ? true : false,
            ]);

            session()->flash('active_tab', 'pajak');

            return response()->json([
                'success' => true,
                'message' => 'Pajak berhasil ditambahkan',
                'data' => $pajak,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store promo
     */
    private function storePromo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_promo' => 'required|string|max:255|unique:promo,kode_promo',
            'nama_promo' => 'required|string|max:255',
            'jenis' => 'required|in:diskon_persen,cashback',
            'nilai' => 'required|numeric|min:0',
            'min_transaksi' => 'nullable|numeric|min:0',
            'maks_potongan' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_stackable' => 'boolean',
            'status' => 'boolean',
        ], [
            'kode_promo.required' => 'Kode promo wajib diisi',
            'kode_promo.unique' => 'Kode promo sudah digunakan',
            'nama_promo.required' => 'Nama promo wajib diisi',
            'jenis.required' => 'Jenis promo wajib dipilih',
            'jenis.in' => 'Jenis promo tidak valid',
            'nilai.required' => 'Nilai wajib diisi',
            'nilai.numeric' => 'Nilai harus berupa angka',
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'end_date.required' => 'Tanggal selesai wajib diisi',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $promo = Promo::create([
                'kode_promo' => strtoupper($request->kode_promo),
                'nama_promo' => $request->nama_promo,
                'jenis' => $request->jenis,
                'nilai' => $request->nilai,
                'min_transaksi' => $request->min_transaksi ?? 0,
                'maks_potongan' => $request->maks_potongan,
                'is_stackable' => $request->input('is_stackable') == '1' || $request->input('is_stackable') == 1 || $request->input('is_stackable') === true,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->input('status') == '1' || $request->input('status') == 1 || $request->input('status') === true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Promo berhasil ditambahkan',
                'data' => $promo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $type = $request->query('type', 'pajak');

        if ($type === 'promo') {
            $promo = Promo::findOrFail($id);
            $data = $promo->toArray();
            // Format dates to Y-m-d for date input
            $data['start_date'] = $promo->start_date ? \Carbon\Carbon::parse($promo->start_date)->format('Y-m-d') : null;
            $data['end_date'] = $promo->end_date ? \Carbon\Carbon::parse($promo->end_date)->format('Y-m-d') : null;

            return response()->json($data);
        }

        if ($type === 'bundle') {
            $promo = Promo::with('bundleProducts')->findOrFail($id);
            $data = $promo->toArray();
            // Format dates to Y-m-d for date input
            $data['start_date'] = $promo->start_date ? \Carbon\Carbon::parse($promo->start_date)->format('Y-m-d') : null;
            $data['end_date'] = $promo->end_date ? \Carbon\Carbon::parse($promo->end_date)->format('Y-m-d') : null;
            $data['bundle_products'] = $promo->bundleProducts->map(function ($item) {
                return [
                    'produk_id' => $item->produk_id,
                    'quantity' => $item->quantity,
                ];
            });

            return response()->json($data);
        }

        $pajak = Pajak::findOrFail($id);
        $data = $pajak->toArray();
        // Format date to Y-m-d for date input
        $data['start_date'] = $pajak->start_date ? \Carbon\Carbon::parse($pajak->start_date)->format('Y-m-d') : null;

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $type = $request->input('type', 'pajak');
        $jenis = $request->input('jenis');

        // Handle toggle status
        if ($request->has('toggle_status')) {
            return $this->handleToggleStatus($request, $id, $type);
        }

        if ($type === 'promo') {
            return $this->handleUpdatePromo($request, $id);
        }

        if ($type === 'bundle' || $jenis === 'bundle') {
            return $this->handleUpdateBundle($request, $id);
        }

        return $this->handleUpdatePajak($request, $id);
    }

    /**
     * Handle toggle status
     */
    private function handleToggleStatus(Request $request, string $id, string $type)
    {
        try {
            if ($type === 'promo' || $type === 'bundle') {
                $promo = Promo::findOrFail($id);
                $promo->status = $request->status === 'true' || $request->status === true || $request->status === 1;
                $promo->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Status '.($type === 'bundle' ? 'bundle' : 'promo').' berhasil diubah',
                ]);
            }

            $pajak = Pajak::findOrFail($id);
            $pajak->status = $request->status === 'true' || $request->status === true || $request->status === 1;
            $pajak->save();

            return response()->json([
                'success' => true,
                'message' => 'Status pajak berhasil diubah',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle update pajak
     */
    private function handleUpdatePajak(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_pajak' => 'required|string|max:255',
            'persen' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'status' => 'boolean',
        ], [
            'nama_pajak.required' => 'Nama pajak wajib diisi',
            'persen.required' => 'Persentase wajib diisi',
            'persen.numeric' => 'Persentase harus berupa angka',
            'persen.min' => 'Persentase minimal 0',
            'persen.max' => 'Persentase maksimal 100',
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'start_date.date' => 'Format tanggal tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pajak = Pajak::findOrFail($id);
            $pajak->update([
                'nama_pajak' => $request->nama_pajak,
                'persen' => $request->persen,
                'start_date' => $request->start_date,
                'status' => $request->has('status') ? true : false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pajak berhasil diupdate',
                'data' => $pajak,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle update promo
     */
    private function handleUpdatePromo(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'kode_promo' => 'required|string|max:255|unique:promo,kode_promo,'.$id,
            'nama_promo' => 'required|string|max:255',
            'jenis' => 'required|in:diskon_persen,cashback',
            'nilai' => 'required|numeric|min:0',
            'min_transaksi' => 'nullable|numeric|min:0',
            'maks_potongan' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_stackable' => 'boolean',
            'status' => 'boolean',
        ], [
            'kode_promo.required' => 'Kode promo wajib diisi',
            'kode_promo.unique' => 'Kode promo sudah digunakan',
            'nama_promo.required' => 'Nama promo wajib diisi',
            'jenis.required' => 'Jenis promo wajib dipilih',
            'nilai.required' => 'Nilai wajib diisi',
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'end_date.required' => 'Tanggal selesai wajib diisi',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $promo = Promo::findOrFail($id);
            $promo->update([
                'kode_promo' => strtoupper($request->kode_promo),
                'nama_promo' => $request->nama_promo,
                'jenis' => $request->jenis,
                'nilai' => $request->nilai,
                'min_transaksi' => $request->min_transaksi ?? 0,
                'maks_potongan' => $request->maks_potongan,
                'is_stackable' => $request->input('is_stackable') == '1' || $request->input('is_stackable') == 1 || $request->input('is_stackable') === true,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->input('status') == '1' || $request->input('status') == 1 || $request->input('status') === true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Promo berhasil diupdate',
                'data' => $promo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle update bundle
     */
    private function handleUpdateBundle(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'kode_promo' => 'required|string|max:255|unique:promo,kode_promo,'.$id,
            'nama_promo' => 'required|string|max:255',
            'nilai' => 'required|numeric|min:0',
            'min_transaksi' => 'nullable|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'bundle_products' => 'required|array|min:1',
            'bundle_products.*.produk_id' => 'required|exists:produk,id',
            'bundle_products.*.quantity' => 'required|integer|min:1',
        ], [
            'kode_promo.required' => 'Kode bundle wajib diisi',
            'kode_promo.unique' => 'Kode bundle sudah digunakan',
            'nama_promo.required' => 'Nama bundle wajib diisi',
            'nilai.required' => 'Harga bundle wajib diisi',
            'stok.required' => 'Stok bundle wajib diisi',
            'stok.integer' => 'Stok harus berupa angka',
            'stok.min' => 'Stok minimal 0',
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'end_date.required' => 'Tanggal selesai wajib diisi',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'bundle_products.required' => 'Minimal harus ada 1 produk dalam bundle',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $promo = Promo::findOrFail($id);
            $promo->update([
                'kode_promo' => strtoupper($request->kode_promo),
                'nama_promo' => $request->nama_promo,
                'nilai' => $request->nilai,
                'min_transaksi' => $request->min_transaksi ?? 0,
                'stok' => $request->stok,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->has('status') ? true : false,
            ]);

            // Delete old bundle products and create new ones
            BundleProduct::where('promo_id', $promo->id)->delete();

            foreach ($request->bundle_products as $product) {
                BundleProduct::create([
                    'promo_id' => $promo->id,
                    'produk_id' => $product['produk_id'],
                    'quantity' => $product['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bundle promo berhasil diupdate',
                'data' => $promo->load('bundleProducts.produk'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $type = $request->query('type', 'pajak');

        try {
            if ($type === 'promo' || $type === 'bundle') {
                $promo = Promo::findOrFail($id);
                $promo->delete();

                return response()->json([
                    'success' => true,
                    'message' => ($type === 'bundle' ? 'Bundle' : 'Promo').' berhasil dihapus',
                ]);
            }

            $pajak = Pajak::findOrFail($id);
            $pajak->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pajak berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store bundle promo
     */
    private function storeBundle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_promo' => 'required|string|max:255|unique:promo,kode_promo',
            'nama_promo' => 'required|string|max:255',
            'nilai' => 'required|numeric|min:0',
            'min_transaksi' => 'nullable|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'bundle_products' => 'required|array|min:1',
            'bundle_products.*.produk_id' => 'required|exists:produk,id',
            'bundle_products.*.quantity' => 'required|integer|min:1',
        ], [
            'kode_promo.required' => 'Kode bundle wajib diisi',
            'kode_promo.unique' => 'Kode bundle sudah digunakan',
            'nama_promo.required' => 'Nama bundle wajib diisi',
            'nilai.required' => 'Harga bundle wajib diisi',
            'stok.required' => 'Stok bundle wajib diisi',
            'stok.integer' => 'Stok harus berupa angka',
            'stok.min' => 'Stok minimal 0',
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'end_date.required' => 'Tanggal selesai wajib diisi',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'bundle_products.required' => 'Minimal harus ada 1 produk dalam bundle',
            'bundle_products.*.produk_id.required' => 'Produk wajib dipilih',
            'bundle_products.*.produk_id.exists' => 'Produk tidak ditemukan',
            'bundle_products.*.quantity.required' => 'Quantity wajib diisi',
            'bundle_products.*.quantity.min' => 'Quantity minimal 1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $promo = Promo::create([
                'kode_promo' => strtoupper($request->kode_promo),
                'nama_promo' => $request->nama_promo,
                'jenis' => 'bundle',
                'nilai' => $request->nilai,
                'min_transaksi' => $request->min_transaksi ?? 0,
                'maks_potongan' => null,
                'is_stackable' => false,
                'stok' => $request->stok,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->has('status') ? true : false,
            ]);

            // Save bundle products
            foreach ($request->bundle_products as $product) {
                BundleProduct::create([
                    'promo_id' => $promo->id,
                    'produk_id' => $product['produk_id'],
                    'quantity' => $product['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bundle promo berhasil ditambahkan',
                'data' => $promo->load('bundleProducts.produk'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }
}
