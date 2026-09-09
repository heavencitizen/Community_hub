<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityProduct;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketplaceController extends Controller
{
    /**
     * Katalog Publik Etalase Jual Beli Komunitas
     */
    public function index(Request $request)
    {
        $query = CommunityProduct::with(['community', 'seller'])
            ->where('status', 'available');

        // 1. Filter Pencarian Nama Produk, Deskripsi, atau Nama Komunitas
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('community', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // 2. Filter Kategori Komunitas
        if ($request->filled('category') && $request->input('category') !== 'all') {
            $category = $request->input('category');
            $query->whereHas('community', function ($cq) use ($category) {
                $cq->where('category', $category);
            });
        }

        // 3. Filter Komunitas Spesifik
        if ($request->filled('community_id')) {
            $query->where('community_id', $request->input('community_id'));
        }

        // 4. Pengurutan (Sorting)
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'stock_low' => $query->orderBy('stock', 'asc'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        // 5. Statistik Etalase
        $stats = [
            'total_products' => CommunityProduct::where('status', 'available')->count(),
            'total_communities' => CommunityProduct::where('status', 'available')->distinct('community_id')->count('community_id'),
            'total_in_stock' => CommunityProduct::where('status', 'available')->where('stock', '>', 0)->count(),
        ];

        // 6. Daftar Kategori Unik yang Tersedia
        $categories = Community::whereHas('products', function ($q) {
            $q->where('status', 'available');
        })->distinct()->pluck('category')->filter()->values();

        return view('marketplace.index', compact('products', 'stats', 'categories', 'sort'));
    }

    /**
     * Tambah produk baru (Hanya Ketua Komunitas / Super Admin)
     */
    public function store(Request $request, Community $community)
    {
        // Pastikan hanya ketua komunitas atau super admin yang bisa menambah produk
        if ($community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Hanya ketua komunitas yang berhak menambahkan produk untuk dijual.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:1000',
            'stock' => 'required|integer|min:1',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $slug = Str::slug($request->name).'-'.Str::random(5);

        $community->products()->create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'stock' => $request->stock,
            'status' => 'available',
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan ke etalase komunitas!');
    }

    /**
     * Hapus produk (Ketua Komunitas / Super Admin)
     */
    public function destroy(CommunityProduct $product)
    {
        if ($product->community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Anda tidak memiliki otoritas menghapus produk ini.');
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus dari etalase.');
    }

    /**
     * Inisiasi pembelian produk menuju checkout
     */
    public function buy(Request $request, CommunityProduct $product)
    {
        if ($product->stock <= 0 || $product->status !== 'available') {
            return back()->with('error', 'Maaf, stok produk ini telah habis.');
        }

        // Cek apakah pembeli adalah anggota komunitas
        $userId = auth()->id();
        $isMember = $userId ? $product->community->hasActiveMember($userId) : false;

        // Fee: 1% jika anggota komunitas, 2% jika umum
        $feePercent = $isMember ? 1.0 : 2.0;
        $feeAmount = round(($product->price * $feePercent) / 100, 2);
        $totalAmount = $product->price + $feeAmount;

        $transactionCode = 'PROD-'.strtoupper(Str::random(10));

        $transaction = Transaction::create([
            'transaction_code' => $transactionCode,
            'user_id' => $userId,
            'type' => 'product',
            'reference_id' => $product->id,
            'amount' => $product->price,
            'platform_fee_percent' => $feePercent,
            'platform_fee_amount' => $feeAmount,
            'total_amount' => $totalAmount,
            'payment_method' => 'qris',
            'payment_status' => 'pending',
            'notes' => 'Pembelian produk '.$product->name.' di komunitas '.$product->community->name,
        ]);

        return redirect()->route('payment.checkout', $transaction->transaction_code);
    }
}
