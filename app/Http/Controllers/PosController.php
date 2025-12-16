<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PosController extends Controller
{
    // Halaman Utama POS
    public function index(Request $request)
    {
        // Load all products for client-side filtering with Alpine.js
        $products = Product::latest()->get();
        $categories = Product::distinct()->pluck('category');

        return view('pos.index', compact('products', 'categories'));
    }

    public function adminIndex(Request $request)
    {
        // Load all products for client-side filtering with Alpine.js
        $products = Product::latest()->get();
        $categories = Product::distinct()->pluck('category');

        return view('products.manage', compact('products', 'categories'));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'category' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $data = $request->all();

        // Cek jika ada upload gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Simpan gambar baru
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return back()->with('success', 'Produk berhasil diperbarui!');
    }

    // 3. Logic Hapus
    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);

        // Hapus gambar dari folder agar tidak menumpuk sampah
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus!');
    }

    // Proses Simpan Transaksi
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.id' => ['required', 'integer', 'exists:products,id'],
            'cart.*.qty' => ['required', 'integer', 'min:1'],
            'cart.*.price' => ['required', 'integer', 'min:0'],
            'total_amount' => ['required', 'integer', 'min:0'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'in:qris,cash'],
            'amount_paid' => ['nullable', 'integer', 'min:0'],
        ]);

        // Aturan pembayaran cash: amount_paid wajib dan harus >= total_amount
        if ($validated['payment_method'] === 'cash') {
            if (!isset($validated['amount_paid'])) {
                return response()->json([
                    'message' => 'Uang diterima wajib diisi untuk pembayaran cash.'
                ], 422);
            }

            if ((int) $validated['amount_paid'] < (int) $validated['total_amount']) {
                return response()->json([
                    'message' => 'Uang diterima kurang.'
                ], 422);
            }
        }

        try {
            DB::transaction(function () use ($validated) {
                // Buat Order
                $changeAmount = null;
                $amountPaid = null;
                if ($validated['payment_method'] === 'cash') {
                    $amountPaid = (int) $validated['amount_paid'];
                    $changeAmount = $amountPaid - (int) $validated['total_amount'];
                }

                $order = Order::create([
                    'invoice_number' => 'INV-' . time(),
                    'customer_name' => $validated['customer_name'] ?? 'Guest',
                    'total_price' => (int) $validated['total_amount'],
                    'status' => 'paid',
                    'payment_method' => $validated['payment_method'],
                    'amount_paid' => $amountPaid,
                    'change_amount' => $changeAmount,
                ]);

                foreach ($validated['cart'] as $item) {
                    // AMBIL DATA PRODUK ASLI DARI DB
                    $product = Product::lockForUpdate()->find($item['id']);

                    // CEK STOK
                    if (!$product || $product->stock < $item['qty']) {
                        throw new \Exception("Stok " . ($product->name ?? 'Produk') . " habis atau kurang!");
                    }

                    // KURANGI STOK
                    $product->decrement('stock', $item['qty']);

                    // Simpan Item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'quantity' => $item['qty'],
                        'price' => $item['price'],
                    ]);
                }
            });

            return response()->json(['message' => 'Transaksi Berhasil!', 'status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'category' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            // Simpan gambar ke folder storage/app/public/products
            $path = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'category' => $request->category,
            'image' => $path
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan!');
    }

    // History Transaksi
    public function history(Request $request)
    {
        // Load all orders for client-side filtering with Alpine.js
        $orders = Order::with('items.product')->latest()->get();

        // Wrap in a paginator-like object for compatibility
        $orders = new \Illuminate\Pagination\LengthAwarePaginator(
            $orders,
            $orders->count(),
            $orders->count() > 0 ? $orders->count() : 1,
            1
        );

        return view('orders.history', compact('orders'));
    }

    // Detail Order untuk API
    public function historyDetail($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return response()->json($order);
    }
}
