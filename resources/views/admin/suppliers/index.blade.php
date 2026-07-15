@extends('layouts.admin')

@section('title', 'Data Supplier')
@section('page-crumb', 'Data Master / Supplier')
@section('page-title', 'Data Supplier')

@section('content')

<div class="split">
    {{-- Tabel daftar supplier --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <h3>Daftar Supplier</h3>
                <div class="sub">Digunakan pada pencatatan Stok Masuk (F-09)</div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nama Supplier</th>
                    <th>Kontak</th>
                    <th>Alamat</th>
                    <th>Stok Masuk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($suppliers as $supplier)
                <tr>
                    <td class="cellname">{{ $supplier->name }}</td>
                    <td class="cellsub mono">{{ $supplier->contact ?? '-' }}</td>
                    <td class="cellsub">{{ $supplier->address ?? '-' }}</td>
                    <td>{{ $supplier->stock_ins_count }} kali</td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            {{-- Trigger modal edit --}}
                            <button type="button" class="btn btn-ghost btn-sm"
                                    onclick="openEdit({{ $supplier->id }}, '{{ addslashes($supplier->name) }}', '{{ addslashes($supplier->contact ?? '') }}', '{{ addslashes($supplier->address ?? '') }}')">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}" class="inline-form"
                                  onsubmit="return confirm('Hapus supplier {{ $supplier->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:36px">Belum ada supplier. Tambahkan di form sebelah.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{-- Form Tambah --}}
        <div class="panel">
            <div class="panel-head"><h3 id="form-title">Tambah Supplier</h3></div>
            <div class="panel-body">
                <form method="POST" id="supplier-form" action="{{ route('admin.suppliers.store') }}">
                    @csrf
                    <span id="method-field"></span>

                    <div class="field">
                        <label>Nama Supplier <span class="req">*</span></label>
                        <input type="text" name="name" id="s-name" value="{{ old('name') }}" required placeholder="mis. Distro Kicks Jakarta">
                        @error('name') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label>Kontak (HP / WA)</label>
                        <input type="text" name="contact" id="s-contact" value="{{ old('contact') }}" placeholder="08xxxxxxxxxx">
                        @error('contact') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label>Alamat</label>
                        <input type="text" name="address" id="s-address" value="{{ old('address') }}" placeholder="Kota / Kabupaten">
                        @error('address') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-actions">
                        <button type="button" id="btn-reset" class="btn btn-ghost" style="display:none" onclick="resetForm()">Batal</button>
                        <button type="submit" class="btn btn-volt">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const formEl   = document.getElementById('supplier-form');
const titleEl  = document.getElementById('form-title');
const methodEl = document.getElementById('method-field');
const resetBtn = document.getElementById('btn-reset');

function openEdit(id, name, contact, address) {
    formEl.action = '/admin/suppliers/' + id;
    methodEl.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('s-name').value    = name;
    document.getElementById('s-contact').value = contact;
    document.getElementById('s-address').value = address;
    titleEl.textContent = 'Ubah Supplier';
    resetBtn.style.display = 'inline-flex';
    document.getElementById('s-name').focus();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    formEl.action = '{{ route('admin.suppliers.store') }}';
    methodEl.innerHTML = '';
    formEl.reset();
    titleEl.textContent = 'Tambah Supplier';
    resetBtn.style.display = 'none';
}
</script>

@endsection
