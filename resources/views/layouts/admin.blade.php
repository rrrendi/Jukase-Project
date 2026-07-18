<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') · Jukase Project</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Hanken+Grotesk:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="jk">
    @php
        $pendingBadge = \App\Models\Order::where('status', 'pending')->count();
    @endphp
    <div class="admin-wrap">

        <aside class="sidebar" id="jk-sidebar">
            <div class="sb-brand">
                <span class="sq"><b>J</b></span>
                <div class="t">JUKASE<small>ADMIN PANEL</small></div>
            </div>

            <div class="sb-section">Menu Utama</div>
            <a href="{{ route('admin.dashboard') }}"
                class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
            <a href="{{ route('admin.orders.index') }}"
                class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                🛍️ Pesanan Website
                @if ($pendingBadge > 0)
                    <span class="badge">{{ $pendingBadge }}</span>
                @endif
            </a>
            <a href="{{ route('admin.manual-sales.index') }}"
                class="nav-item {{ request()->routeIs('admin.manual-sales.*') ? 'active' : '' }}">🧾 Penjualan
                Manual</a>
            <a href="{{ route('admin.products.index') }}"
                class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">👟 Kelola Produk</a>
            <a href="{{ route('admin.stock-ins.index') }}"
                class="nav-item {{ request()->routeIs('admin.stock-ins.*') ? 'active' : '' }}">📦 Stok Masuk &amp;
                HPP</a>
            <a href="{{ route('admin.reports.index') }}"
                class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">📈 Laporan Keuangan</a>

            <div class="sb-section">Data Master</div>
            <a href="{{ route('admin.categories.index') }}"
                class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">🏷️ Kategori</a>
            <a href="{{ route('admin.suppliers.index') }}"
                class="nav-item {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">🚚 Supplier</a>

            <div class="sb-section">Sistem</div>
            <a href="{{ route('admin.configuration.edit') }}"
                class="nav-item {{ request()->routeIs('admin.configuration.*') ? 'active' : '' }}">⚙️ Konfigurasi</a>
            <a href="{{ route('admin.notification-logs.index') }}"
                class="nav-item {{ request()->routeIs('admin.notification-logs.*') ? 'active' : '' }}">📨 Riwayat
                Notifikasi</a>

            <div class="sb-foot">
                <div class="av">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                <div style="flex:1;min-width:0">
                    <div class="nm">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="rl">Owner Jukase Project</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Keluar</button>
                </form>
            </div>
        </aside>

        <div class="sidebar-backdrop" id="jk-sidebar-backdrop" onclick="jkToggleSidebar()"></div>
        <div class="admin-main">
            <div class="topbar">
                <button type="button" class="icon-btn sidebar-toggle" onclick="jkToggleSidebar()" aria-label="Menu">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>
                <div class="topbar-title">
                    <div class="crumb">ADMIN · @yield('page-crumb', 'PANEL')</div>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="topbar-buttons">
                    <div class="topbar-actions">
                        @yield('page-actions')
                    </div>
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-ghost btn-sm shop-link-btn">Lihat
                        Toko ↗</a>
                </div>
            </div>

            <div class="admin-page">
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

                @yield('content')
            </div>
        </div>

    </div>
</body>

</html>