@extends('layouts.admin')

@section('title', 'Ubah Produk')
@section('page-crumb', 'Kelola Produk / Ubah')
@section('page-title', 'Ubah Produk')

@section('page-actions')
    <a href="{{ route('admin.products.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
@endsection

@section('content')

<div class="panel" style="max-width:760px">
    <div class="panel-head">
        <div>
            <h3>{{ $product->full_name }}</h3>
            <div class="sub">
                Stok saat ini: <b>{{ $product->stock }}</b> ·
                HPP (Moving Average): <b>{{ \App\Support\Format::rupiah($product->avg_cost) }}</b>
                — kelola via menu <a href="{{ route('admin.stock-ins.index') }}">Stok Masuk &amp; HPP</a> (F-09/F-12).
            </div>
        </div>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.products._form')
        </form>
    </div>
</div>

@endsection
