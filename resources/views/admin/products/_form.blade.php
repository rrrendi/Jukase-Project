@php
    $isActiveChecked = $errors->any() ? old('is_active', false) : ($product->is_active ?? true);
@endphp

<div class="field-row">
    <div class="field">
        <label>Merek <span class="req">*</span></label>
        <input type="text" name="brand" value="{{ old('brand', $product->brand ?? '') }}" placeholder="mis. Nike" required>
        @error('brand') <span class="error-text">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label>Nama Model <span class="req">*</span></label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" placeholder="mis. Air Force 1 '07" required>
        @error('name') <span class="error-text">{{ $message }}</span> @enderror
    </div>
</div>

<div class="field-row">
    <div class="field">
        <label>Kategori</label>
        <select name="category_id">
            <option value="">-- Tanpa Kategori --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ (string) old('category_id', $product->category_id ?? '') === (string) $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <span class="error-text">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label>Harga Jual (Rp) <span class="req">*</span></label>
        <input type="number" name="price" min="0" step="1" value="{{ old('price', isset($product) ? (int) $product->price : '') }}" required>
        @error('price') <span class="error-text">{{ $message }}</span> @enderror
    </div>
</div>

<div class="field-row">
    <div class="field">
        <label>Ukuran</label>
        <input type="text" name="size_range" value="{{ old('size_range', $product->size_range ?? '') }}" placeholder="mis. 39-44 atau 42">
        @error('size_range') <span class="error-text">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label>Warna</label>
        <input type="text" name="color" value="{{ old('color', $product->color ?? '') }}" placeholder="mis. Hitam/Putih">
        @error('color') <span class="error-text">{{ $message }}</span> @enderror
    </div>
</div>

<div class="field">
    <label>Batas Stok Minimum <span class="req">*</span></label>
    <input type="number" name="min_stock" min="0" step="1"
           value="{{ old('min_stock', $product->min_stock ?? $defaultMinStock ?? 5) }}" required>
    <div class="note" style="margin-top:4px">Produk ditandai "Stok Menipis" pada katalog &amp; dashboard jika stok ≤ nilai ini (F-15).</div>
    @error('min_stock') <span class="error-text">{{ $message }}</span> @enderror
</div>

<div class="field">
    <label>Foto Produk</label>

    @if (! empty($product) && $product->images->isNotEmpty())
        <div style="display:flex;flex-wrap:wrap;gap:14px;margin-bottom:10px">
            @foreach ($product->images as $img)
                <div style="position:relative;width:70px;height:70px;flex:0 0 auto;overflow:visible">
                    <img src="{{ $img->url }}" alt="Galeri {{ $product->full_name }}"
                         style="display:block;width:70px;height:70px;border-radius:10px;object-fit:cover;border:1px solid var(--line-strong)">
                    @if ($loop->first)
                        <span title="Foto sampul di katalog"
                              style="position:absolute;bottom:-6px;left:-6px;background:var(--brand,#2e2e2e);color:#fff;font-size:9px;font-weight:700;padding:2px 5px;border-radius:6px">SAMPUL</span>
                    @endif
                    {{-- Tombol ini menunjuk ke <form id="delete-photo-{{ $img->id }}"> yang dirender
                         di luar form utama (lihat edit.blade.php), lewat atribut HTML "form=".
                         Dengan begitu ia tetap tampil persis di sini tanpa perlu <form> di dalam <form>. --}}
                    <button type="submit" form="delete-photo-{{ $img->id }}" title="Hapus foto"
                            style="position:absolute;top:-8px;right:-8px;display:block;width:22px;height:22px;border-radius:50%;background:#e74c3c;color:#fff;border:none;font-size:11px;line-height:22px;padding:0;cursor:pointer">✕</button>
                </div>
            @endforeach
        </div>
        <div class="note" style="margin-bottom:8px">Foto pertama otomatis jadi sampul di katalog. Hapus foto ini untuk menjadikan foto berikutnya sebagai sampul.</div>
    @endif

    <label class="upload">
        <input type="file" name="images[]" accept="image/png,image/jpeg,image/webp" multiple
               onchange="document.getElementById('gallery-filename').textContent = this.files.length ? this.files.length + ' foto dipilih' : 'Belum ada file dipilih'">
        <div style="font-size:24px;margin-bottom:6px">🖼️</div>
        <div style="font-weight:700;font-size:13px">
            {{ ! empty($product) && $product->images->isNotEmpty() ? 'Klik untuk tambah foto (bisa pilih beberapa sekaligus)' : 'Klik untuk upload foto produk (bisa pilih beberapa sekaligus)' }}
        </div>
        <div id="gallery-filename" class="mono" style="font-size:11px;margin-top:6px">JPG / PNG / WEBP, maks 2MB per foto</div>
    </label>
    @error('images') <span class="error-text">{{ $message }}</span> @enderror
    @error('images.*') <span class="error-text">{{ $message }}</span> @enderror
</div>

<div class="checkbox-row">
    <input type="checkbox" name="is_active" id="is_active" value="1" {{ $isActiveChecked ? 'checked' : '' }}>
    <label for="is_active" style="margin:0">Tampilkan produk ini di katalog online (status aktif)</label>
</div>

<div class="form-actions">
    <a href="{{ route('admin.products.index') }}" class="btn btn-ghost">Batal</a>
    <button type="submit" class="btn btn-volt">{{ isset($product) ? 'Simpan Perubahan' : 'Tambah Produk' }}</button>
</div>