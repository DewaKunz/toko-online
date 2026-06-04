<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // GET /api/transactions
    public function index()
    {
        $transactions = Transaction::with(['user', 'product'])->latest()->get();

        return response()->json([
            'status'  => true,
            'message' => 'Daftar transaksi',
            'data'    => $transactions,
        ]);
    }

    // POST /api/transactions
    public function store(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        // Cek stok produk
        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'status'  => false,
                'message' => 'Stok produk tidak mencukupi. Stok tersedia: ' . $product->stock,
            ], 400);
        }

        // Hitung total harga
        $totalPrice = $product->price * $request->quantity;

        // Kurangi stok
        $product->decrement('stock', $request->quantity);

        // Simpan transaksi
        $transaction = Transaction::create([
            'user_id'     => $request->user_id,
            'product_id'  => $request->product_id,
            'quantity'    => $request->quantity,
            'total_price' => $totalPrice,
            'status'      => 'pending',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Transaksi berhasil dibuat',
            'data'    => $transaction->load(['user', 'product']),
        ], 201);
    }

    // GET /api/transactions/{id}
    public function show(string $id)
    {
        $transaction = Transaction::with(['user', 'product'])->find($id);

        if (!$transaction) {
            return response()->json([
                'status'  => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail transaksi',
            'data'    => $transaction,
        ]);
    }

    // PUT /api/transactions/{id}
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'status'  => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,success,cancelled',
        ]);

        $transaction->update(['status' => $request->status]);

        return response()->json([
            'status'  => true,
            'message' => 'Status transaksi diperbarui',
            'data'    => $transaction->load(['user', 'product']),
        ]);
    }

    // DELETE /api/transactions/{id}
    public function destroy(string $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'status'  => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        $transaction->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Transaksi berhasil dihapus',
        ]);
    }
}