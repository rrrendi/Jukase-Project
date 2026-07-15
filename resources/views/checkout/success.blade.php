@extends('layouts.shop')

@section('title', 'Pesanan Berhasil - Jukase Project')

@section('content')
<div class="page" style="max-width:560px">
    <div class="panel">
        <div class="panel-body">
            <div class="success">
                <div class="check">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#14130F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <h2 style="font-size:24px">Pesanan Berhasil Dibuat!</h2>
                <p style="color:var(--muted);font-size:14px;margin-top:8px">Kode pesanan kamu adalah</p>
                <div class="mono" style="font-size:22px;font-weight:800;margin:6px 0 16px">{{ $order->order_code }}</div>
                <span class="pill warn" style="font-size:12px;padding:6px 14px">MENUNGGU VERIFIKASI ⏳</span>
            </div>

            <div style="margin-top:22px;border-top:1px solid var(--line);padding-top:6px">
                @foreach ($order->details as $detail)
                    <div class="li">
                        <div class="meta">
                            <div class="n">{{ $detail->product_name }}</div>
                            <div class="s">{{ $detail->quantity }} × {{ \App\Support\Format::rupiah($detail->price) }}</div>
                        </div>
                        <div class="right" style="align-items:flex-end;justify-content:center">
                            <div style="font-weight:800;font-family:var(--disp)">{{ \App\Support\Format::rupiah($detail->subtotal) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pay-box">
                <div class="row"><span class="k">Subtotal</span><span>{{ \App\Support\Format::rupiah($order->subtotal) }}</span></div>
                <div class="row"><span class="k">Ongkos Kirim</span><span>{{ \App\Support\Format::rupiah($order->shipping_cost) }}</span></div>
                <div class="row total"><span class="k">Total Bayar</span><span class="v">{{ \App\Support\Format::rupiah($order->total) }}</span></div>
            </div>

            <p class="note" style="text-align:center">
                Admin akan memeriksa bukti pembayaran kamu. Setelah diverifikasi, konfirmasi status pesanan akan
                dikirim otomatis ke WhatsApp <span class="mono" style="color:var(--ink)">{{ $order->whatsapp }}</span>.
            </p>

            <a href="{{ route('home') }}" class="btn btn-volt btn-block" style="margin-top:6px">Kembali ke Katalog</a>
        </div>
    </div>
</div>
@endsection
