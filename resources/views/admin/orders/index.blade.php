@extends('layouts.admin')

@section('title', 'Pesanan Website')
@section('page-crumb', 'Pesanan')
@section('page-title', 'Pesanan Website')

@section('content')

<div class="seg" style="margin-bottom:18px">
    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="{{ $status === 'pending' ? 'active' : '' }}">Menunggu ({{ $counts['pending'] }})</a>
    <a href="{{ route('admin.orders.index', ['status' => 'approved']) }}" class="{{ $status === 'approved' ? 'active' : '' }}">Disetujui ({{ $counts['approved'] }})</a>
    <a href="{{ route('admin.orders.index', ['status' => 'rejected']) }}" class="{{ $status === 'rejected' ? 'active' : '' }}">Ditolak ({{ $counts['rejected'] }})</a>
</div>

<div class="panel">
    <div class="panel-head">
        <div>
            <h3>
                @if ($status === 'pending') Menunggu Verifikasi
                @elseif ($status === 'approved') Pesanan Disetujui
                @else Pesanan Ditolak
                @endif
            </h3>
            <div class="sub">F-06 · periksa bukti pembayaran sebelum menyetujui pesanan</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Item</th>
                <th>Total</th>
                <th>Bukti Bayar</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($orders as $order)
            <tr>
                <td class="mono"><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_code }}</a></td>
                <td>
                    <div class="cellname">{{ $order->customer_name }}</div>
                    <div class="cellsub mono">{{ $order->whatsapp }}</div>
                </td>
                <td class="cellsub col-item">{{ $order->items_summary }}</td>
                <td>{{ \App\Support\Format::rupiah($order->total) }}</td>
                <td>
                    @if ($order->payment_proof_url)
                        <a href="{{ $order->payment_proof_url }}" target="_blank">
                            <img src="{{ $order->payment_proof_url }}" class="proof" alt="Bukti pembayaran {{ $order->order_code }}">
                        </a>
                    @else
                        <span class="cellsub">-</span>
                    @endif
                </td>
                <td class="cellsub">{{ \App\Support\Format::tanggal($order->created_at) }}</td>
                <td>
                    @if ($status === 'pending')
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-ghost btn-sm" title="Lihat detail pesanan" aria-label="Detail pesanan {{ $order->order_code }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('admin.orders.approve', $order) }}" class="inline-form"
                                  onsubmit="return confirm('Setujui pesanan {{ $order->order_code }}? Stok produk akan berkurang & notifikasi WhatsApp akan dikirim ke pelanggan.')">
                                @csrf
                                <button type="submit" class="btn btn-volt btn-sm">Setujui</button>
                            </form>
                            <form method="POST" action="{{ route('admin.orders.reject', $order) }}" class="inline-form"
                                  onsubmit="return confirm('Tolak pesanan {{ $order->order_code }}? Stok tidak akan berubah.')">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-ghost btn-sm">Detail</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:36px">Belum ada pesanan pada status ini.</td></tr>
        @endforelse
        </tbody>
    </table>

    @if ($orders->hasPages())
        <div class="panel-body">{{ $orders->links() }}</div>
    @endif
</div>

@endsection