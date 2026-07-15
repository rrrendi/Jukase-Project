@extends('layouts.shop')

@section('title', 'Checkout - Jukase Project')

@section('content')
<div class="page" style="max-width:1080px">
    <a href="{{ route('cart.index') }}" class="back-link">← Kembali ke Keranjang</a>

    <div class="page-head">
        <h2>Checkout</h2>
        <div class="sub">Guest checkout — tidak perlu membuat akun. Pastikan nomor WhatsApp aktif untuk menerima
            konfirmasi pesanan.</div>
    </div>

    <form method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data" class="checkout-grid">
        @csrf

        <div>
            <div class="panel">
                <div class="panel-head">
                    <h3>Data Pemesan</h3>
                </div>
                <div class="panel-body">
                    <div class="field">
                        <label>Nama Lengkap <span class="req">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                            class="{{ $errors->has('customer_name') ? 'invalid' : '' }}"
                            placeholder="Nama sesuai penerima paket" required>
                        @error('customer_name') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label>Nomor WhatsApp <span class="req">*</span></label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp') }}"
                            class="{{ $errors->has('whatsapp') ? 'invalid' : '' }}" placeholder="08xxxxxxxxxx" required>
                        @error('whatsapp') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label>Alamat Lengkap <span class="req">*</span></label>
                        <textarea name="address" rows="3" class="{{ $errors->has('address') ? 'invalid' : '' }}"
                            placeholder="Nama jalan, nomor rumah, kecamatan, kabupaten/kota, kode pos"
                            required>{{ old('address') }}</textarea>
                        @error('address') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h3>Bukti Pembayaran</h3>
                </div>
                <div class="panel-body">
                    <p style="font-size:13px;color:var(--muted);margin:0 0 14px">
                        Transfer/scan QRIS sesuai <b>Total Bayar</b> pada ringkasan, lalu unggah tangkapan layar / foto
                        bukti pembayaran (JPG/PNG, maks 2MB).
                    </p>
                    <label class="upload">
                        <input type="file" name="payment_proof" accept="image/png,image/jpeg" required
                            onchange="document.getElementById('proof-filename').textContent = this.files[0]?.name || 'Belum ada file dipilih'">
                        <div style="font-size:28px;margin-bottom:8px">📎</div>
                        <div style="font-weight:700;font-size:13px">Klik untuk pilih file bukti pembayaran</div>
                        <div id="proof-filename" class="mono" style="font-size:11px;margin-top:6px">Belum ada file
                            dipilih</div>
                    </label>
                    @error('payment_proof') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-volt btn-block">Buat Pesanan</button>
            <p class="note" style="text-align:center;margin-top:10px">
                Status pesanan akan "MENUNGGU VERIFIKASI" sampai Admin memeriksa bukti pembayaran. Notifikasi akan
                dikirim ke WhatsApp kamu setelah diverifikasi.
            </p>
        </div>

        <div>
            <div class="panel">
                <div class="panel-head">
                    <h3>Ringkasan Pesanan</h3>
                </div>
                <div class="panel-body" style="padding:4px 20px">
                    @foreach ($items as $item)
                    @php($product = $item['product'])
                    @php($qty = $item['qty'])
                    <div class="li">
                        <div class="thumb ph-{{ $product->category?->slug ?? 'default' }}"
                            style="width:46px;height:46px">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->full_name }}">
                            @else
                                👟
                            @endif
                        </div>
                        <div class="meta">
                            <div class="n" style="font-size:13px">{{ $product->full_name }}</div>
                            <div class="s">{{ $qty }} × {{ \App\Support\Format::rupiah($product->price) }}</div>
                        </div>
                        <div class="right" style="align-items:flex-end;justify-content:center">
                            <div style="font-weight:800;font-family:var(--disp);font-size:14px">
                                {{ \App\Support\Format::rupiah($product->price * $qty) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="pay-box">
                <div class="row"><span
                        class="k">Subtotal</span><span>{{ \App\Support\Format::rupiah($subtotal) }}</span></div>
                <div class="row"><span class="k">Ongkos
                        Kirim</span><span>{{ \App\Support\Format::rupiah($shipping) }}</span></div>
                <div class="row total"><span class="k">Total Bayar</span><span
                        class="v">{{ \App\Support\Format::rupiah($total) }}</span></div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h3>Info Pembayaran</h3>
                </div>
                <div class="panel-body">

                    <div class="qris" style="cursor: pointer;" onclick="bukaPopupQris()">
                        @if ($paymentQrisImage)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($paymentQrisImage) }}"
                                alt="QRIS Jukase Project">
                        @else
                            <div class="code"></div>
                        @endif

                        <div>
                            <div style="font-weight:800;font-size:13px">QRIS</div>
                            <div style="font-size:12px;color:var(--muted)">Scan untuk bayar instan, Klik gambar untuk memperbesar</div>
                        </div>
                    </div>

                    <div class="field" style="margin-top:14px;margin-bottom:0">
                        <label>Transfer Bank</label>
                        <div class="mono"
                            style="font-size:13px;background:var(--paper-2);padding:10px 12px;border-radius:10px;line-height:1.6">
                            {{ $paymentBankInfo }}
                        </div>
                    </div>
                </div>
            </div>

            @if ($paymentQrisImage)
                <div id="qrisModal"
                    style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); align-items: center; justify-content: center; backdrop-filter: blur(2px);">

                    <span
                        style="position: absolute; top: 20px; right: 30px; color: white; font-size: 40px; font-weight: bold; cursor: pointer;"
                        onclick="tutupPopupQris()">&times;</span>

                    <img src="{{ \Illuminate\Support\Facades\Storage::url($paymentQrisImage) }}"
                        style="max-width: 90%; max-height: 80%; background: white; padding: 15px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">

                    <div
                        style="position: absolute; bottom: 30px; color: white; font-weight: bold; font-family: sans-serif;">
                        Scan QRIS ini untuk membayar
                    </div>
                </div>
            @endif

            <script>
                function bukaPopupQris() {
                    // Mengubah display menjadi flex agar posisinya di tengah layar
                    document.getElementById('qrisModal').style.display = 'flex';
                }

                function tutupPopupQris() {
                    // Sembunyikan kembali modalnya
                    document.getElementById('qrisModal').style.display = 'none';
                }
            </script>
        </div>
    </form>
</div>
@endsection