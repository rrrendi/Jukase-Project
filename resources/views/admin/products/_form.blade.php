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
    <label>Foto Produk (Foto Utama Yang Ditampilkan Di Katalog)</label>
    @if (! empty($product) && $product->image_url)
        <div style="margin-bottom:8px">
            <img src="{{ $product->image_url }}" alt="{{ $product->full_name }}"
                 style="width:90px;height:90px;border-radius:12px;object-fit:cover;border:1px solid var(--line-strong)">
        </div>
    @endif
    <label class="upload">
        <input type="file" name="image" accept="image/png,image/jpeg,image/webp"
               onchange="document.getElementById('img-filename').textContent = this.files[0]?.name || 'Belum ada file dipilih'">
        <div style="font-size:24px;margin-bottom:6px">🖼️</div>
        <div style="font-weight:700;font-size:13px">
            {{ ! empty($product) && $product->image ? 'Klik untuk mengganti foto' : 'Klik untuk upload foto produk' }}
        </div>
        <div id="img-filename" class="mono" style="font-size:11px;margin-top:6px">JPG / PNG / WEBP, maks 2MB</div>
    </label>
    @error('image') <span class="error-text">{{ $message }}</span> @enderror
</div>

<div class="field">
    <label>Galeri Foto Tambahan</label>
    @if (! empty($product) && $product->images->isNotEmpty())
        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:10px">
            @foreach ($product->images as $img)
                <div style="position:relative">
                    <img src="{{ $img->url }}" alt="Galeri {{ $product->full_name }}"
                         style="width:70px;height:70px;border-radius:10px;object-fit:cover;border:1px solid var(--line-strong)">
                    <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $img]) }}"
                          onsubmit="return confirm('Hapus foto ini?')" style="position:absolute;top:-8px;right:-8px">
                        @csrf @method('DELETE')
                        <button type="submit" title="Hapus foto"
                                style="width:22px;height:22px;border-radius:50%;background:#e74c3c;color:#fff;border:none;font-size:11px;cursor:pointer">✕</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
    <label class="upload">
        <input type="file" name="images[]" accept="image/png,image/jpeg,image/webp" multiple
               onchange="document.getElementById('gallery-filename').textContent = this.files.length ? this.files.length + ' foto dipilih' : 'Belum ada file dipilih'">
        <div style="font-size:24px;margin-bottom:6px">🖼️</div>
        <div style="font-weight:700;font-size:13px">Klik untuk tambah foto galeri (bisa pilih beberapa sekaligus)</div>
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
