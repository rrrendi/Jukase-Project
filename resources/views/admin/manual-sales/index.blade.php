@extends('layouts.admin')

@section('title', 'Penjualan Manual')
@section('page-crumb', 'Penjualan Manual')
@section('page-title', 'Penjualan Manual')

@section('content')

<div class="split">
    <div class="panel">
        <div class="panel-head">
            <div>
                <h3>Riwayat Penjualan Manual</h3>
                <div class="sub">Transaksi dari WhatsApp, Instagram, Facebook, atau Walk-in (F-10)</div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Kanal</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td class="cellsub">{{ \App\Support\Format::tanggal($sale->sale_date) }}</td>
                    <td class="cellname">{{ $sale->items_summary }}</td>
                    <td><span class="pill neutral">{{ $sale->channel }}</span></td>
                    <td class="cellsub">{{ $sale->customer_name ?? '-' }}</td>
                    <td>{{ \App\Support\Format::rupiah($sale->total) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:36px">Belum ada penjualan manual yang dicatat.</td></tr>
            @endforelse
            </tbody>
        </table>
        @if ($sales->hasPages())
            <div class="panel-body">{{ $sales->links() }}</div>
        @endif
    </div>

    <div class="panel">
        <div class="panel-head">
            <div>
                <h3>Catat Penjualan Baru</h3>
                <div class="sub">Stok berkurang &amp; omzet tercatat otomatis</div>
            </div>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.manual-sales.store') }}">
                @csrf

                <div class="field">
                    <label>Produk <span class="req">*</span></label>
                    <select name="product_id" id="product_id" required
                            onchange="document.getElementById('price').value = this.options[this.selectedIndex].dataset.price ?? ''; document.getElementById('quantity').max = this.options[this.selectedIndex].dataset.stock ?? 1">
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                    data-price="{{ (int) $product->price }}"
                                    data-stock="{{ $product->stock }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->full_name }} — Stok: {{ $product->stock }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field-row">
                    <div class="field">
                        <label>Jumlah <span class="req">*</span></label>
                        <input type="number" id="quantity" name="quantity" min="1" value="{{ old('quantity', 1) }}" required>
                        @error('quantity') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label>Harga Jual (Rp) <span class="req">*</span></label>
                        <input type="number" id="price" name="price" min="0" value="{{ old('price') }}" required>
                        @error('price') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label>Kanal Penjualan <span class="req">*</span></label>
                        <select name="channel" required>
                            @foreach (['WhatsApp', 'Instagram', 'Facebook', 'Walk-in'] as $channel)
                                <option value="{{ $channel }}" {{ old('channel') === $channel ? 'selected' : '' }}>{{ $channel }}</option>
                            @endforeach
                        </select>
                        @error('channel') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label>Tanggal <span class="req">*</span></label>
                        <input type="date" name="sale_date" value="{{ old('sale_date', $today) }}" max="{{ $today }}" required>
                        @error('sale_date') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="field">
                    <label>Nama Pelanggan (opsional)</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="mis. Dimas - IG @dimasstyle">
                    @error('customer_name') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn-volt btn-block">Simpan Penjualan</button>
            </form>
        </div>
    </div>
</div>

@endsection
