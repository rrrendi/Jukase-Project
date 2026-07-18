@extends('layouts.shop')

@section('title', 'Lacak Pesanan - Jukase Project')

@section('content')
<div class="page" style="max-width:560px">
    <div class="panel">
        <div class="panel-body">
            <p style="font-family:var(--mono);font-size:11px;color:var(--muted);letter-spacing:.05em;margin-bottom:14px">LACAK PESANAN</p>

            @if ($deviceOrders->isNotEmpty())
                <h2 style="font-size:22px;margin-bottom:4px">Pesanan di Perangkat Ini</h2>
                <p style="color:var(--muted);font-size:13.5px;margin-bottom:6px">
                    Dikenali otomatis dari HP/browser ini — tidak perlu isi kode atau WhatsApp.
                </p>
                <div style="margin-top:10px">
                    @foreach ($deviceOrders as $item)
                        @include('checkout._order-list-item', ['item' => $item])
                    @endforeach
                </div>

                <details style="margin-top:20px">
                    <summary style="cursor:pointer;font-weight:700;font-size:13.5px">Lacak pesanan lain / dari perangkat lain</summary>
                    <div style="margin-top:16px">
                        @include('checkout._track-form')
                    </div>
                </details>
            @else
                <h2 style="font-size:22px;margin-bottom:4px">Lacak Pesanan</h2>
                <p style="color:var(--muted);font-size:13.5px;margin-bottom:20px">Masukkan kode pesanan &amp; nomor WhatsApp yang kamu gunakan saat checkout.</p>
                @include('checkout._track-form')
            @endif

            @if ($notFound)
                <p class="note" style="text-align:center;color:var(--danger);margin-top:18px">
                    Pesanan tidak ditemukan. Pastikan kode pesanan &amp; nomor WhatsApp sudah benar.
                </p>
            @endif

            @if ($order)
                <div style="margin-top:24px;border-top:1px solid var(--line);padding-top:20px">
                    @include('checkout._order-card', ['order' => $order])
                </div>
            @endif

            {{-- Lupa kode pesanan: cari pakai nomor WhatsApp saja --}}
            <details style="margin-top:24px;border-top:1px solid var(--line);padding-top:16px" @if($waSearched) open @endif>
                <summary style="cursor:pointer;font-weight:700;font-size:13.5px">Lupa kode pesanan? Cari pakai nomor WhatsApp saja</summary>
                <div style="margin-top:16px">
                    <form method="GET" action="{{ route('order-tracking.index') }}">
                        <div class="field">
                            <label>Nomor WhatsApp</label>
                            <input type="text" name="whatsapp_only" value="{{ $waOnly }}" placeholder="mis. 0812xxxxxxxx" required>
                        </div>
                        <button type="submit" class="btn btn-ghost btn-block">Cari Pesanan</button>
                    </form>

                    @if ($waSearched)
                        @if ($waResults->isEmpty())
                            <p class="note" style="text-align:center;color:var(--danger);margin-top:16px">
                                Tidak ada pesanan dengan nomor WhatsApp ini.
                            </p>
                        @else
                            <div style="margin-top:16px">
                                @foreach ($waResults as $item)
                                    @include('checkout._order-list-item', ['item' => $item])
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </details>

            {{-- Lupa semuanya: hubungi admin --}}
            @if ($ownerWhatsappUrl)
                <p class="note" style="text-align:center;margin-top:20px">
                    Masih belum ketemu juga? <a href="{{ $ownerWhatsappUrl }}" target="_blank" rel="noopener" style="font-weight:700;color:var(--ink);text-decoration:underline">Hubungi Admin via WhatsApp</a>.
                </p>
            @endif

        </div>
    </div>
</div>
@endsection