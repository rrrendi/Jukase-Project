@extends('layouts.admin')

@section('title', 'Stok Masuk & HPP')
@section('page-crumb', 'Stok Masuk & HPP')
@section('page-title', 'Stok Masuk & HPP Moving Average')

@section('content')

    <div class="split">
        <div>
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <h3>Riwayat Stok Masuk</h3>
                        <div class="sub">F-09 · Setiap stok masuk memperbarui HPP Moving Average produk (F-12)</div>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Supplier</th>
                            <th>Qty Masuk</th>
                            <th>Harga Modal/Satuan</th>
                            <th>Total Modal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockIns as $s)
                            <tr>
                                <td class="cellsub">{{ \App\Support\Format::tanggal($s->date) }}</td>
                                <td>
                                    <div class="cellname">{{ $s->product?->full_name ?? '(produk dihapus)' }}</div>
                                    <div class="cellsub">HPP baru produk:
                                        {{ $s->product ? \App\Support\Format::rupiah($s->product->avg_cost) : '-' }}</div>
                                </td>
                                <td class="cellsub">{{ $s->supplier?->name ?? '-' }}</td>
                                <td><span class="pill ok">+{{ $s->quantity }}</span></td>
                                <td>{{ \App\Support\Format::rupiah($s->unit_cost) }}</td>
                                <td>{{ \App\Support\Format::rupiah($s->unit_cost * $s->quantity) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--muted);padding:36px">Belum ada riwayat stok
                                    masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($stockIns->hasPages())
                    <div class="panel-body">{{ $stockIns->links() }}</div>
                @endif
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <div>
                    <h3>Catat Stok Masuk</h3>
                    <div class="sub">HPP Moving Average produk dihitung ulang otomatis</div>
                </div>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('admin.stock-ins.store') }}">
                    @csrf

                    <div class="field jk-combobox">
                        <label>Produk <span class="req">*</span></label>
                        <input type="text" class="combobox-input" placeholder="Ketik merek/model untuk mencari…"
                            autocomplete="off"
                            value="{{ old('product_id') ? ($products->firstWhere('id', (int) old('product_id'))->full_name ?? '') : '' }}">
                        <input type="hidden" name="product_id" value="{{ old('product_id') }}">
                        <div class="combobox-panel">
                            @foreach ($products as $product)
                                <div class="combobox-option" data-id="{{ $product->id }}"
                                    data-label="{{ $product->full_name }}">
                                    <div class="cb-name">{{ $product->full_name }}</div>
                                    <div class="cb-sub">Stok: {{ $product->stock }} · HPP:
                                        {{ \App\Support\Format::rupiah($product->avg_cost) }}</div>
                                </div>
                            @endforeach
                        </div>
                        @error('product_id') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="field jk-combobox">
                        <label>Supplier</label>
                        <input type="text" class="combobox-input" placeholder="Ketik untuk mencari supplier… (opsional)" autocomplete="off"
                            value="{{ old('supplier_id') ? ($suppliers->firstWhere('id', (int) old('supplier_id'))->name ?? '') : '' }}">
                        <input type="hidden" name="supplier_id" value="{{ old('supplier_id') }}">
                        <div class="combobox-panel">
                            <div class="combobox-option" data-id="" data-label="-- Tanpa Supplier --">
                                <div class="cb-name">-- Tanpa Supplier --</div>
                            </div>
                            @foreach ($suppliers as $supplier)
                                <div class="combobox-option" data-id="{{ $supplier->id }}" data-label="{{ $supplier->name }}">
                                    <div class="cb-name">{{ $supplier->name }}</div>
                                </div>
                            @endforeach
                        </div>
                        @error('supplier_id') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label>Jumlah Masuk <span class="req">*</span></label>
                            <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required>
                            @error('quantity') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label>Harga Modal/Satuan (Rp) <span class="req">*</span></label>
                            <input type="number" name="unit_cost" min="0" step="1" value="{{ old('unit_cost') }}" required>
                            @error('unit_cost') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label>Tanggal <span class="req">*</span></label>
                        <input type="date" name="date" value="{{ old('date', $today) }}" max="{{ $today }}" required>
                        @error('date') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="panel" style="background:var(--paper-2);border:none;margin-bottom:14px;padding:14px">
                        <div class="note">
                            <b>Rumus HPP Moving Average:</b><br>
                            HPP baru = (Stok lama × HPP lama + Qty masuk × Harga modal masuk) ÷ (Stok lama + Qty masuk)
                        </div>
                    </div>

                    <button type="submit" class="btn btn-volt btn-block">Simpan Stok Masuk</button>
                </form>
            </div>
        </div>
    </div>

@endsection