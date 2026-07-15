@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-crumb', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="stat-grid" style="margin-bottom:18px">
    <div class="stat dark">
        <div class="lbl">Omzet Hari Ini</div>
        <div class="val">{{ \App\Support\Format::rupiah($todayRevenue) }}</div>
    </div>
    <div class="stat">
        <div class="lbl">Pesanan Baru</div>
        <div class="val">{{ $pendingCount }}</div>
    </div>
    <div class="stat">
        <div class="lbl">Stok Kritis</div>
        <div class="val">{{ $lowStockCount }}</div>
    </div>
    <div class="stat">
        <div class="lbl">Laba Bersih (Bulan Ini)</div>
        <div class="val">{{ \App\Support\Format::rupiah($monthProfit) }}</div>
    </div>
</div>

<div class="split">
    <div class="panel">
        <div class="panel-head">
            <div>
                <h3>Penjualan 7 Hari Terakhir</h3>
                <div class="sub">Gabungan Pesanan Website (disetujui) &amp; Penjualan Manual</div>
            </div>
        </div>
        <div class="chart-wrap">
            @php($max = max(1, ...array_column($chart, 'revenue')))
            <div class="bars">
                @foreach ($chart as $day)
                    @php($h = $day['revenue'] > 0 ? max(6, ($day['revenue'] / $max) * 150) : 4)
                    <div class="bar-col">
                        <div class="bar {{ $loop->last ? 'top' : '' }}" style="height:{{ $h }}px" data-v="{{ \App\Support\Format::rupiah($day['revenue']) }}"></div>
                        <div class="d">{{ \App\Support\Format::tanggalSingkat($day['date']) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <div>
                <h3>Stok Menipis / Kritis</h3>
                <div class="sub">F-15 · sesuai batas stok minimum per produk</div>
            </div>
        </div>
        @forelse ($lowStockProducts as $product)
            <div class="alert-row">
                <div class="mini-thumb ph-{{ $product->category?->slug ?? 'default' }}">👟</div>
                <div>
                    <div class="nm">{{ $product->full_name }}</div>
                    <div class="cellsub">{{ $product->category?->name ?? '-' }}</div>
                </div>
                <span class="pill {{ $product->stock <= 0 ? 'danger' : 'warn' }}" style="margin-left:auto">
                    {{ $product->stock <= 0 ? 'Habis' : 'Sisa '.$product->stock }}
                </span>
            </div>
        @empty
            <div class="empty" style="padding:30px 20px">
                <div class="ic">✅</div>
                <p>Semua stok produk dalam kondisi aman.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <div>
            <h3>Pesanan Terbaru</h3>
            <div class="sub">5 pesanan website terakhir, semua status</div>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost btn-sm">Lihat Semua →</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Item</th>
                <th>Total</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($latestOrders as $order)
            <tr>
                <td class="mono">{{ $order->order_code }}</td>
                <td class="cellname">{{ $order->customer_name }}</td>
                <td class="cellsub">{{ $order->items_summary }}</td>
                <td>{{ \App\Support\Format::rupiah($order->total) }}</td>
                <td>@include('admin.orders._status', ['status' => $order->status])</td>
                <td class="cellsub">{{ \App\Support\Format::tanggal($order->created_at) }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:30px">Belum ada pesanan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection
