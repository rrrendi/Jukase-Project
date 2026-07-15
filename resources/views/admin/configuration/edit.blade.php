@extends('layouts.admin')

@section('title', 'Konfigurasi Sistem')
@section('page-crumb', 'Sistem / Konfigurasi')
@section('page-title', 'Konfigurasi Sistem')

@section('content')

<form method="POST" action="{{ route('admin.configuration.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="cfg-grid">

        {{-- WhatsApp Gateway (Fonnte) --}}
        <div class="cfg-card">
            <h4>WhatsApp Gateway (Fonnte)</h4>
            <div class="desc">Digunakan untuk notifikasi pesanan baru ke Owner (F-05) & konfirmasi pesanan ke Pelanggan (F-07). Daftar di <a href="https://fonnte.com" target="_blank">fonnte.com</a> untuk mendapatkan API Token.</div>

            <div class="checkbox-row">
                <input type="checkbox" name="notif_active" id="notif_active" value="1" {{ ($config['notif_active'] ?? '1') === '1' ? 'checked' : '' }}>
                <label for="notif_active" style="margin:0">Aktifkan pengiriman notifikasi WhatsApp</label>
            </div>
            <div class="field">
                <label>API Token Fonnte <span class="req">*</span></label>
                <input type="text" name="fonnte_token" value="{{ $config['fonnte_token'] ?? '' }}" placeholder="Token dari dashboard Fonnte.com">
                @error('fonnte_token') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="field">
                <label>Nomor WhatsApp Owner <span class="req">*</span></label>
                <input type="text" name="owner_whatsapp" value="{{ $config['owner_whatsapp'] ?? '' }}" placeholder="6281234567890 (format internasional)">
                <div class="note" style="margin-top:4px">Format: 62xxxxxxxxxx (tanpa tanda + atau 0 di depan)</div>
                @error('owner_whatsapp') <span class="error-text">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Template Pesan --}}
        <div class="cfg-card">
            <h4>Template Pesan WhatsApp</h4>
            <div class="desc">
                Variabel yang tersedia: <span class="mono" style="background:var(--paper-2);padding:1px 5px;border-radius:4px">{nama}</span>
                <span class="mono" style="background:var(--paper-2);padding:1px 5px;border-radius:4px">{id}</span>
                <span class="mono" style="background:var(--paper-2);padding:1px 5px;border-radius:4px">{total}</span>
                <span class="mono" style="background:var(--paper-2);padding:1px 5px;border-radius:4px">{status}</span>
                <span class="mono" style="background:var(--paper-2);padding:1px 5px;border-radius:4px">{items}</span>
            </div>

            <div class="field">
                <label>Template Notifikasi Pesanan Baru (ke Owner) — F-05</label>
                <textarea name="msg_template_new_order" rows="4">{{ old('msg_template_new_order', $config['msg_template_new_order'] ?? '') }}</textarea>
                @error('msg_template_new_order') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="field">
                <label>Template Konfirmasi Pesanan (ke Pelanggan) — F-07</label>
                <textarea name="msg_template_confirmation" rows="4">{{ old('msg_template_confirmation', $config['msg_template_confirmation'] ?? '') }}</textarea>
                @error('msg_template_confirmation') <span class="error-text">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Informasi Pembayaran --}}
        <div class="cfg-card">
            <h4>Informasi Pembayaran</h4>
            <div class="desc">Ditampilkan di halaman Checkout untuk pelanggan (F-04). Isi nomor rekening / BCA / BRI / GoPay / dll.</div>

            <div class="field">
                <label>Info Rekening Bank / E-Wallet <span class="req">*</span></label>
                <textarea name="payment_bank_info" rows="3">{{ old('payment_bank_info', $config['payment_bank_info'] ?? '') }}</textarea>
                <div class="note" style="margin-top:4px">Contoh: BCA 7220-1234-567 a.n. Jukase Project</div>
                @error('payment_bank_info') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="field">
                <label>Upload Gambar QRIS (PNG / JPG)</label>
                @if (! empty($config['payment_qris_image']))
                    <div style="margin-bottom:8px">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($config['payment_qris_image']) }}" alt="QRIS"
                             style="width:80px;height:80px;border-radius:10px;object-fit:cover;border:1px solid var(--line-strong)">
                    </div>
                @endif
                <label class="upload" style="padding:14px">
                    <input type="file" name="qris_image" accept="image/png,image/jpeg"
                           onchange="document.getElementById('qris-name').textContent = this.files[0]?.name || ''">
                    <div style="font-size:18px">🖼️</div>
                    <div style="font-size:12px;font-weight:700">Klik untuk {{ empty($config['payment_qris_image']) ? 'upload' : 'ganti' }} gambar QRIS</div>
                    <div id="qris-name" class="mono" style="font-size:10px"></div>
                </label>
                @error('qris_image') <span class="error-text">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Stok & Ongkir --}}
        <div class="cfg-card">
            <h4>Stok Minimum &amp; Ongkos Kirim</h4>
            <div class="desc">Batas stok default untuk produk baru. Ongkos kirim flat ditambahkan ke total pesanan (F-15).</div>

            <div class="checkbox-row">
                <input type="checkbox" name="low_stock_alert_active" id="low_stock_alert" value="1" {{ ($config['low_stock_alert_active'] ?? '1') === '1' ? 'checked' : '' }}>
                <label for="low_stock_alert" style="margin:0">Tampilkan peringatan stok kritis di Dashboard (F-14)</label>
            </div>
            <div class="field">
                <label>Batas Stok Minimum Default (untuk produk baru) <span class="req">*</span></label>
                <input type="number" name="default_min_stock" min="0" value="{{ old('default_min_stock', $config['default_min_stock'] ?? 5) }}">
                @error('default_min_stock') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="field">
                <label>Ongkos Kirim Flat (Rp) <span class="req">*</span></label>
                <input type="number" name="shipping_cost" min="0" step="1000" value="{{ old('shipping_cost', $config['shipping_cost'] ?? 0) }}">
                <div class="note" style="margin-top:4px">Set 0 jika ongkos kirim ditentukan manual per pesanan.</div>
                @error('shipping_cost') <span class="error-text">{{ $message }}</span> @enderror
            </div>
        </div>

    </div>

    <div class="form-actions" style="margin-top:6px">
        <button type="submit" class="btn btn-volt" style="min-width:180px">Simpan Semua Konfigurasi</button>
    </div>
</form>

@endsection
