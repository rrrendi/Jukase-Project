@extends('layouts.admin')

@section('title', 'Kelola Produk')
@section('page-crumb', 'Kelola Produk')
@section('page-title', 'Kelola Produk')

@section('page-actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-volt">+ Tambah Produk</a>
@endsection

@section('content')

<div class="panel">
    <div class="panel-head">
        <div>
            <h3>Daftar Produk</h3>
            <div class="sub">HPP dihitung otomatis pakai metode Moving Average setiap kali ada stok masuk (F-12).</div>
        </div>
        <form method="GET" action="{{ route('admin.products.index') }}" style="display:flex;gap:8px">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari merek / model…"
                   style="padding:9px 12px;border-radius:10px;border:1.5px solid var(--line-strong);font-size:13px;min-width:220px">
            <button type="submit" class="btn btn-ghost btn-sm">Cari</button>
            @if($search)
                <a href="{{ route('admin.products.index') }}" class="btn btn-ghost btn-sm">Reset</a>
            @endif
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Harga Jual</th>
                <th>HPP (Avg)</th>
                <th>Margin</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($products as $product)
            <tr>
                <td>
                    <div class="table-flex">
                        <div class="mini-thumb ph-{{ $product->category?->slug ?? 'default' }}">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->full_name }}" class="thumb-img" style="margin:0">
                            @else
                                👟
                            @endif
                        </div>
                        <div>
                            <div class="cellname">{{ $product->full_name }}</div>
                            <div class="cellsub">{{ $product->size_range ?? '-' }} · {{ $product->color ?? '-' }}</div>
                        </div>
                    </div>
                </td>
                <td class="cellsub">{{ $product->category?->name ?? '-' }}</td>
                <td>{{ \App\Support\Format::rupiah($product->price) }}</td>
                <td class="mono" style="font-size:12px">{{ \App\Support\Format::rupiah($product->avg_cost) }}</td>
                <td>
                    <span class="pill {{ $product->margin_percent >= 20 ? 'ok' : ($product->margin_percent >= 10 ? 'warn' : 'danger') }}">
                        {{ $product->margin_percent }}%
                    </span>
                </td>
                <td>
                    <span class="pill {{ $product->stock_status === 'ready' ? 'ok' : ($product->stock_status === 'menipis' ? 'warn' : 'danger') }}">
                        {{ $product->stock }}
                    </span>
                </td>
                <td>
                    <span class="pill {{ $product->is_active ? 'ok' : 'neutral' }}">
                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-ghost btn-sm">Ubah</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline-form"
                              onsubmit="return confirm('Hapus produk {{ addslashes($product->full_name) }}? Produk yang sudah bertransaksi tidak bisa dihapus.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;color:var(--muted);padding:36px">
                    {{ $search ? 'Produk tidak ditemukan untuk pencarian "'.$search.'".' : 'Belum ada produk. Tambahkan produk pertama.' }}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    @if ($products->hasPages())
        <div class="panel-body">{{ $products->links() }}</div>
    @endif
</div>

@endsection
