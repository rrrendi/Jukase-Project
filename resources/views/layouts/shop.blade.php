<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Jukase Project')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Hanken+Grotesk:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="jk shop-body">

    <header class="shop-header">
        <div class="bar">
            <a href="{{ route('home') }}" class="wordmark">
                <span class="sq"><b>J</b></span>JUKASE<span style="color:var(--volt-deep)">·</span>
            </a>
            <nav class="shop-nav">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Katalog</a>
                <a href="{{ route('home') }}#cara-pesan">Cara Pesan</a>
                <a href="{{ route('order-tracking.index') }}"
                    class="{{ request()->routeIs('order-tracking.*') ? 'active' : '' }}">Lacak Pesanan</a>
            </nav>
            <div class="header-spacer"></div>
            <button type="button" class="icon-btn nav-toggle" onclick="jkToggleMenu()" aria-label="Menu">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>
            <a href="{{ route('cart.index') }}" class="icon-btn" title="Keranjang">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 6h15l-1.5 9h-12z" />
                    <circle cx="9" cy="20" r="1.4" />
                    <circle cx="18" cy="20" r="1.4" />
                    <path d="M6 6 5 3H2" />
                </svg>
                @php($cartCount = array_sum(session('cart', [])))
                @if ($cartCount > 0)
                    <span class="cart-count">{{ $cartCount }}</span>
                @endif
            </a>
        </div>
        <div id="jk-mobile-menu" class="jk-mobile-menu">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Katalog</a>
            <a href="{{ route('home') }}#cara-pesan">Cara Pesan</a>
            <a href="{{ route('order-tracking.index') }}"
                class="{{ request()->routeIs('order-tracking.*') ? 'active' : '' }}">Lacak Pesanan</a>
        </div>
    </header>

    @if (session('success') || session('error') || $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if (session('success'))
                    window.jkToast('success', @json(session('success')));
                @endif
                @if (session('error'))
                    window.jkToast('error', @json(session('error')));
                @endif
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        window.jkToast('error', @json($error));
                    @endforeach
                @endif
                                });
        </script>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="shop-footer">
        <div class="inner">
            <div class="brand">
                <span class="sq" style="background:var(--volt)"><b style="color:var(--ink)">J</b></span>
                Jukase Project
            </div>
            <div>Gg. Pamoyanan, Wr. Muncang, Kec. Bandung Kulon, Kota Bandung, Jawa Barat 40211</div>
            <div class="mono">© {{ date('Y') }} Jukase Project</div>
        </div>
    </footer>

    <div id="jk-lightbox" class="jk-lightbox" onclick="if(event.target===this) jkCloseGallery()">
        <button type="button" class="jk-lightbox-close" onclick="jkCloseGallery()" aria-label="Tutup">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6 6 18M6 6l12 12" />
            </svg>
        </button>
        <button type="button" class="jk-lightbox-prev" onclick="jkPrevImage()" aria-label="Sebelumnya">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 18l-6-6 6-6" />
            </svg>
        </button>
        <img id="jk-lightbox-img" src="" alt="Foto produk">
        <button type="button" class="jk-lightbox-next" onclick="jkNextImage()" aria-label="Berikutnya">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 6l6 6-6 6" />
            </svg>
        </button>
        <div id="jk-lightbox-counter" class="jk-lightbox-counter"></div>
    </div>

</body>

</html>