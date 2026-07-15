@extends('layouts.admin')

@section('title', 'Tambah Produk')
@section('page-crumb', 'Kelola Produk / Tambah')
@section('page-title', 'Tambah Produk')

@section('page-actions')
    <a href="{{ route('admin.products.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
@endsection

@section('content')

<div class="panel" style="max-width:760px">
    <div class="panel-head">
        <div>
            <h3>Data Produk Baru</h3>
            <div class="sub">Stok &amp; HPP dimulai dari 0 - tambahkan via menu Stok Masuk setelah produk dibuat (F-09).</div>
        </div>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.products._form')
        </form>
    </div>
</div>

@endsection
