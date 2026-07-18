<form method="GET" action="{{ route('order-tracking.index') }}">
    <div class="field">
        <label>Kode Pesanan</label>
        <input type="text" name="code" value="{{ $code }}" placeholder="mis. JKS-2017" required>
    </div>
    <div class="field">
        <label>Nomor WhatsApp</label>
        <input type="text" name="whatsapp" value="{{ $whatsapp }}" placeholder="mis. 0812xxxxxxxx" required>
    </div>
    <button type="submit" class="btn btn-volt btn-block">Cek Status</button>
</form>