@extends('layouts.admin')

@section('title', 'Detail Pesanan '.$order->order_code)
@section('page-crumb', 'Pesanan / Detail')
@section('page-title', $order->order_code)

@section('page-actions')
    <a href="{{ route('admin.orders.index', ['status' => $order->status]) }}" class="btn btn-ghost btn-sm">← Kembali</a>
@endsection

@section('content')

<div class="split">
    <div>
        <div class="panel">
            <div class="panel-head">
                <h3>Informasi Pemesan</h3>
                @include('admin.orders._status', ['status' => $order->status])
            </div>
            <div class="panel-body">
                <div class="field-row">
                    <div>
                        <div class="cellsub">Nama Pelanggan</div>
                        <div class="cellname">{{ $order->customer_name }}</div>
                    </div>
                    <div>
                        <div class="cellsub">Nomor WhatsApp</div>
                        <div class="cellname mono">{{ $order->whatsapp }}</div>
                    </div>
                </div>
                <div style="margin-top:12px">
                    <div class="cellsub">Alamat Lengkap</div>
                    <div class="cellname" style="font-weight:600">{{ $order->address }}</div>
                </div>
                <div style="margin-top:12px">
                    <div class="cellsub">Tanggal Pesanan</div>
                    <div class="cellname" style="font-weight:600">{{ \App\Support\Format::tanggal($order->created_at) }}</div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><h3>Item Pesanan</h3></div>
            <table>
                <thead>
                    <tr><th>Produk</th><th>Qty</th><th>Harga Satuan</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                @foreach ($order->details as $detail)
                    <tr>
                        <td class="cellname">{{ $detail->product_name }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ \App\Support\Format::rupiah($detail->price) }}</td>
                        <td>{{ \App\Support\Format::rupiah($detail->subtotal) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="panel-body">
                <div class="pay-box" style="margin:0">
                    <div class="row"><span class="k">Subtotal</span><span>{{ \App\Support\Format::rupiah($order->subtotal) }}</span></div>
                    <div class="row"><span class="k">Ongkos Kirim</span><span>{{ \App\Support\Format::rupiah($order->shipping_cost) }}</span></div>
                    <div class="row total"><span class="k">Total</span><span class="v">{{ \App\Support\Format::rupiah($order->total) }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="panel">
            <div class="panel-head"><h3>Bukti Pembayaran</h3></div>
            <div class="panel-body">
                @if ($order->payment_proof_url)
                    <a href="{{ $order->payment_proof_url }}" target="_blank">
                        <img src="{{ $order->payment_proof_url }}" alt="Bukti pembayaran {{ $order->order_code }}"
                             style="width:100%;border-radius:12px;border:1px solid var(--line-strong);display:block">
                    </a>
                    <p class="note" style="margin-top:8px">Klik gambar untuk membuka ukuran penuh.</p>
                @else
                    <p class="cellsub">Tidak ada bukti pembayaran.</p>
                @endif
            </div>
        </div>

        @if ($order->status === 'pending')
            <div class="panel">
                <div class="panel-head"><h3>Verifikasi (F-06)</h3></div>
                <div class="panel-body" style="display:flex;gap:10px">
                    <form method="POST" action="{{ route('admin.orders.approve', $order) }}" style="flex:1"
                          onsubmit="return confirm('Setujui pesanan {{ $order->order_code }}? Stok produk akan berkurang & notifikasi WhatsApp akan dikirim ke pelanggan.')">
                        @csrf
                        <button type="submit" class="btn btn-volt btn-block">Setujui</button>
                    </form>
                    <form method="POST" action="{{ route('admin.orders.reject', $order) }}" style="flex:1"
                          onsubmit="return confirm('Tolak pesanan {{ $order->order_code }}? Stok tidak akan berubah.')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-block">Tolak</button>
                    </form>
                </div>
            </div>
        @else
            <div class="panel">
                <div class="panel-head"><h3>Riwayat</h3></div>
                <div class="panel-body">
                    <p style="font-size:13px;margin:0">
                        Pesanan ini telah <b>{{ $order->status === 'approved' ? 'disetujui' : 'ditolak' }}</b>
                        @if ($order->approved_at)
                            pada {{ \App\Support\Format::tanggal($order->approved_at) }}.
                        @else
                            .
                        @endif
                        Notifikasi WhatsApp konfirmasi telah dikirim ke pelanggan (F-07).
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
