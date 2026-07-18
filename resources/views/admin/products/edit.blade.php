@extends('layouts.admin')

@section('title', 'Ubah Produk')
@section('page-crumb', 'Kelola Produk / Ubah')
@section('page-title', 'Ubah Produk')

@section('page-actions')
    <a href="{{ route('admin.products.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
@endsection

@section('content')

{{-- Satu <form> tersembunyi per foto galeri, untuk aksi hapus foto (DELETE).
     Ini SENGAJA diletakkan di luar & sebelum <form> utama di bawah, karena HTML
     tidak mengizinkan <form> bersarang di dalam <form> lain (kalau dipaksakan,
     browser akan menutup form terluar lebih awal - tombol "Simpan Perubahan"
     jadi tidak bereaksi sama sekali).
     Tombol hapus di galeri (di dalam admin.products._form) tetap bisa tampil
     persis di posisi aslinya berkat atribut HTML `form="delete-photo-{id}"`,
     yang menghubungkan tombol ke form ini walau taruh di tempat lain di DOM. --}}
@foreach ($product->images as $img)
    <form id="delete-photo-{{ $img->id }}" method="POST"
          action="{{ route('admin.products.images.destroy', [$product, $img]) }}"
          onsubmit="return confirm('Hapus foto ini?')" style="display:none">
        @csrf
        @method('DELETE')
    </form>
@endforeach

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