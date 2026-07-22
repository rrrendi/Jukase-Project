@extends('layouts.shop')

@section('title', 'Jukase Project - Katalog Sepatu Reseller Bandung')

@section('content')

<section class="hero">
    <div class="hero-glow"></div>
    <div class="hero-shoe">
        <img src="{{ asset('images/sepatu.png') }}" alt="Sepatu">
    </div>
    <div class="hero-inner">
        <div class="eyebrow tag">Reseller Sneakers · Terpercaya Di Bandung</div>
        <h1>STOK <span class="out">REAL-TIME. <br></span>100% ORIGINAL</h1>
        <p>Lihat ketersediaan tiap model langsung dari katalog. Pesan tanpa daftar akun, upload bukti bayar, beres. Konfirmasi otomatis lewat WhatsApp.</p>
    </div>
</section>

<div class="ticker"><div class="track">
    <span>★ GUEST CHECKOUT ★ QRIS &amp; TRANSFER ★ NOTIFIKASI WHATSAPP ★ 100% AMAN & TERPERCAYA ★&nbsp;&nbsp;</span>
    <span>★ GUEST CHECKOUT ★ QRIS &amp; TRANSFER ★ NOTIFIKASI WHATSAPP ★ 100% AMAN & TERPERCAYA ★&nbsp;&nbsp;</span>
    <span>★ GUEST CHECKOUT ★ QRIS &amp; TRANSFER ★ NOTIFIKASI WHATSAPP ★ 100% AMAN & TERPERCAYA ★&nbsp;&nbsp;</span>
    <span>★ GUEST CHECKOUT ★ QRIS &amp; TRANSFER ★ NOTIFIKASI WHATSAPP ★ 100% AMAN & TERPERCAYA ★&nbsp;&nbsp;</span>
</div></div>

<section class="catalog">
    <div class="cat-head">
        <div>
            <h2>Katalog Produk</h2>
            <div class="sub">Status stok diperbarui otomatis tiap ada transaksi — baik dari website maupun penjualan manual.</div>
        </div>
    </div>

    <form method="GET" action="{{ route('home') }}" class="filters">
        <div class="chips">
            <a href="{{ route('home', array_filter(['q' => $search])) }}"
               class="chip {{ $activeCategory === 'all' ? 'active' : '' }}">Semua</a>
            @foreach ($categories as $category)
                <a href="{{ route('home', array_filter(['category' => $category->slug, 'q' => $search])) }}"
                   class="chip {{ $activeCategory === $category->slug ? 'active' : '' }}">{{ $category->name }}</a>
            @endforeach
        </div>
        <div class="search-wrap">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4-4"/></svg>
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari merek atau model…">
            @if ($activeCategory !== 'all')
                <input type="hidden" name="category" value="{{ $activeCategory }}">
            @endif
        </div>
    </form>

    @if ($products->isEmpty())
        <div class="empty">
            <div class="ic">👟</div>
            <h3>Produk tidak ditemukan</h3>
            <p>Coba kata kunci lain atau pilih kategori yang berbeda.</p>
            <a href="{{ route('home') }}" class="btn btn-ghost">Reset Filter</a>
        </div>
    @else
        <div class="grid">
            @foreach ($products as $product)
                @php($status = $product->stock_status)
                <div class="card {{ $status === 'habis' ? 'soldout' : '' }}">
                    <div class="ph ph-{{ $product->category?->slug ?? 'default' }} @if ($product->gallery_urls) has-gallery @endif"
                        @if ($product->gallery_urls) onclick="jkOpenGallery({{ \Illuminate\Support\Js::from($product->gallery_urls) }})" @endif>
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->full_name }}">
                        @else
                            <div class="shoe">👟</div>
                        @endif

                        <span class="stock-badge pill {{ $status === 'ready' ? 'ok' : ($status === 'menipis' ? 'warn' : 'danger') }}">
                            <span class="dot"></span>
                            @if ($status === 'ready')
                                Ready
                            @elseif ($status === 'menipis')
                                Sisa {{ $product->stock }}
                            @else
                                Habis
                            @endif
                        </span>
                    </div>
                    <div class="body">
                        <div class="brand">{{ $product->brand }}</div>
                        <div class="name">{{ $product->name }}</div>
                        <div class="sizes">
                            Ukuran {{ $product->size_range ?? '-' }}
                            @if ($product->color)
                                · {{ $product->color }}
                            @endif
                        </div>
                        <div class="foot">
                            <div class="price">{{ \App\Support\Format::rupiah($product->price) }}</div>
                            @if ($status === 'habis')
                                <span class="add" title="Stok habis">✕</span>
                            @else
                                <form method="POST" action="{{ route('cart.store', $product) }}" class="add-to-cart-form">
                                    @csrf
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="add" title="Tambah ke keranjang">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

<section class="catalog" id="cara-pesan" style="padding-top:6px">
    <div class="cat-head">
        <div>
            <h2>Cara Pesan</h2>
            <div class="sub">Tiga langkah mudah, tanpa perlu mendaftar akun (guest checkout).</div>
        </div>
    </div>
    <div class="grid" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr))">
        <div class="panel" style="margin-bottom:0">
            <div class="panel-body">
                <div class="tag" style="color:var(--volt-deep)">LANGKAH 1</div>
                <h3 style="margin:8px 0 6px;font-size:18px">Pilih Produk</h3>
                <p style="font-size:13px;color:var(--muted);margin:0">Tambahkan sepatu yang kamu mau ke keranjang. Status stok yang tampil selalu sesuai stok terkini.</p>
            </div>
        </div>
        <div class="panel" style="margin-bottom:0">
            <div class="panel-body">
                <div class="tag" style="color:var(--volt-deep)">LANGKAH 2</div>
                <h3 style="margin:8px 0 6px;font-size:18px">Isi Data &amp; Bayar</h3>
                <p style="font-size:13px;color:var(--muted);margin:0">Isi nama, alamat, dan nomor WhatsApp, lalu transfer / QRIS dan unggah bukti pembayaran.</p>
            </div>
        </div>
        <div class="panel" style="margin-bottom:0">
            <div class="panel-body">
                <div class="tag" style="color:var(--volt-deep)">LANGKAH 3</div>
                <h3 style="margin:8px 0 6px;font-size:18px">Konfirmasi WhatsApp</h3>
                <p style="font-size:13px;color:var(--muted);margin:0">Admin memverifikasi bukti pembayaran, lalu kamu menerima konfirmasi pesanan via WhatsApp.</p>
            </div>
        </div>
    </div>
</section>

@endsection
