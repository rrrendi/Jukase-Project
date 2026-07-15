@extends('layouts.shop')

@section('title', 'Keranjang - Jukase Project')

@section('content')
<div class="page">
    <a href="{{ route('home') }}" class="back-link">← Lanjut Belanja</a>

    <div class="page-head">
        <h2>Keranjang</h2>
        <div class="sub">Periksa kembali pilihanmu sebelum lanjut ke form pemesanan.</div>
    </div>

    @if (empty($items))
        <div class="empty">
            <div class="ic">🛒</div>
            <h3>Keranjang masih kosong</h3>
            <p>Yuk pilih sepatu favoritmu dari katalog dulu.</p>
            <a href="{{ route('home') }}" class="btn btn-volt">Mulai Belanja</a>
        </div>
    @else
        <div class="panel">
            <div class="panel-body" style="padding:6px 20px">
                @foreach ($items as $item)
                    @php($product = $item['product'])
                    @php($qty = $item['qty'])
                    <div class="li">
                        <div class="thumb ph-{{ $product->category?->slug ?? 'default' }}">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->full_name }}">
                            @else
                                👟
                            @endif
                        </div>
                        <div class="meta">
                            <div class="n">{{ $product->full_name }}</div>
                            <div class="s">Ukuran {{ $product->size_range ?? '-' }} · Stok tersedia: {{ $product->stock }}</div>
                            <div class="p">{{ \App\Support\Format::rupiah($product->price) }}</div>
                        </div>
                        <div class="right">
                            <form method="POST" action="{{ route('cart.update', $product) }}" style="display:flex;gap:6px;align-items:center">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="qty" value="{{ $qty }}" min="1" max="{{ $product->stock }}"
                                       style="width:56px;padding:7px 8px;border-radius:9px;border:1.5px solid var(--line-strong);text-align:center;font-family:var(--mono)">
                                <button type="submit" class="btn btn-ghost btn-sm">Ubah</button>
                            </form>
                            <form method="POST" action="{{ route('cart.destroy', $product) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="color:var(--danger)">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pay-box">
            <div class="row"><span class="k">Total Sementara</span><span>{{ \App\Support\Format::rupiah($subtotal) }}</span></div>
            <div class="row"><span class="k">Ongkos kirim</span><span>Dihitung di langkah berikutnya</span></div>
            <div class="row total"><span class="k">Estimasi Bayar</span><span class="v">{{ \App\Support\Format::rupiah($subtotal) }}</span></div>
        </div>

        <a href="{{ route('checkout.index') }}" class="btn btn-volt btn-block">Lanjut ke Checkout →</a>
    @endif
</div>
@endsection
