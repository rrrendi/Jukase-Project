@extends('layouts.shop')

@section('title', 'Pesanan Berhasil - Jukase Project')

@section('content')
<div class="page" style="max-width:560px">
    <div class="panel">
        <div class="panel-body">
            @include('checkout._order-card', ['order' => $order])

            <p class="note" style="text-align:center;margin-top:4px">
                Simpan kode pesanan ini — kamu bisa cek statusnya kapan saja lewat menu
                <a href="{{ route('order-tracking.index') }}" style="font-weight:700;color:var(--ink);text-decoration:underline">Lacak Pesanan</a>.
            </p>

            <a href="{{ route('home') }}" class="btn btn-volt btn-block" style="margin-top:14px">Kembali ke Katalog</a>
        </div>
    </div>
</div>
@endsection