@extends('layouts.admin')

@section('title', 'Kategori Produk')
@section('page-crumb', 'Data Master / Kategori')
@section('page-title', 'Kategori Produk')

@section('content')

<div class="split">
    <div class="panel">
        <div class="panel-head">
            <div>
                <h3>Daftar Kategori</h3>
                <div class="sub">Digunakan untuk mengelompokkan produk di katalog (Tabel 1.5 No.2)</div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Slug</th>
                    <th style="text-align:right">Jumlah Produk</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td class="cellname">{{ $category->name }}</td>
                    <td class="cellsub mono">{{ $category->slug }}</td>
                    <td style="text-align:right">{{ $category->products_count }}</td>
                    <td style="text-align:right">
                        <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap">
                            <button type="button" class="btn btn-ghost btn-sm"
                                onclick="document.getElementById('edit-name-{{ $category->id }}').value='{{ $category->name }}';document.getElementById('edit-form-{{ $category->id }}').style.display='block';this.closest('tr').querySelector('.view-row').style.display='none'">
                                Ubah
                            </button>
                            @if ($category->products_count == 0)
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline-form"
                                      onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            @endif
                        </div>
                        {{-- Inline edit form --}}
                        <form method="POST" action="{{ route('admin.categories.update', $category) }}"
                              id="edit-form-{{ $category->id }}" style="display:none;margin-top:8px">
                            @csrf @method('PUT')
                            <div style="display:flex;gap:6px">
                                <input type="text" id="edit-name-{{ $category->id }}" name="name"
                                       style="padding:7px 10px;border-radius:9px;border:1.5px solid var(--line-strong);flex:1;font-family:inherit">
                                <button type="submit" class="btn btn-volt btn-sm">Simpan</button>
                                <button type="button" class="btn btn-ghost btn-sm"
                                    onclick="this.closest('form').style.display='none'">Batal</button>
                            </div>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:30px">Belum ada kategori. Tambahkan di bawah.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h3>Tambah Kategori</h3>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="field">
                    <label>Nama Kategori <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="mis. Sneakers, Casual, Sport…" required>
                    @error('name') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="btn btn-volt btn-block">Tambah Kategori</button>
            </form>
        </div>
    </div>
</div>

@endsection
