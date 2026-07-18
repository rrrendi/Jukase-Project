@extends('layouts.admin')

@section('title', 'Riwayat Notifikasi')
@section('page-crumb', 'Sistem / Riwayat Notifikasi')
@section('page-title', 'Riwayat Notifikasi WhatsApp')

@section('content')

    @if ($failedToday > 0)
        <div class="flash error" style="margin-bottom:18px">
            ⚠️ Ada {{ $failedToday }} notifikasi WhatsApp gagal terkirim hari ini. Kemungkinan token Fonnte belum diatur, atau
            nomor WhatsApp Owner sedang tidak tersambung/diblokir — cek menu Konfigurasi.
        </div>
    @endif

    <div class="panel">
        <div class="panel-head">
            <div>
                <h3>Log Pengiriman</h3>
                <div class="sub">F-05/F-07 · Notifikasi pesanan baru ke Admin & konfirmasi status ke pelanggan via Fonnte
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.notification-logs.index') }}" class="panel-body"
            style="padding-bottom:14px;display:flex;gap:12px;flex-wrap:wrap">
            <div class="field" style="margin-bottom:0;min-width:170px;flex:1">
                <label>Status</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="" {{ $status === '' ? 'selected' : '' }}>Semua Status</option>
                    <option value="sent" {{ $status === 'sent' ? 'selected' : '' }}>Terkirim</option>
                    <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
            <div class="field" style="margin-bottom:0;min-width:200px;flex:1">
                <label>Jenis</label>
                <select name="type" onchange="this.form.submit()">
                    <option value="" {{ $type === '' ? 'selected' : '' }}>Semua Jenis</option>
                    <option value="pesanan_baru" {{ $type === 'pesanan_baru' ? 'selected' : '' }}>Pesanan Baru</option>
                    <option value="konfirmasi_pesanan" {{ $type === 'konfirmasi_pesanan' ? 'selected' : '' }}>Konfirmasi
                        Pesanan</option>
                </select>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Jenis</th>
                    <th>Tujuan</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="cellsub">{{ $log->sent_at?->format('d M Y, H:i') ?? '-' }}</td>
                        <td class="cellsub">{{ $log->type === 'pesanan_baru' ? 'Pesanan Baru' : 'Konfirmasi Pesanan' }}</td>
                        <td class="mono" style="font-size:12px">{{ $log->recipient }}</td>
                        <td>
                            <span class="pill {{ $log->status === 'sent' ? 'ok' : 'danger' }}">
                                {{ $log->status === 'sent' ? 'Terkirim' : 'Gagal' }}
                            </span>
                        </td>
                        <td class="cellsub" style="max-width:320px;white-space:normal;word-break:break-word">
                            {{ \Illuminate\Support\Str::limit($log->response ?? '-', 140) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--muted);padding:36px">
                            Belum ada riwayat notifikasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($logs->hasPages())
            <div class="panel-body">{{ $logs->links() }}</div>
        @endif
    </div>

@endsection