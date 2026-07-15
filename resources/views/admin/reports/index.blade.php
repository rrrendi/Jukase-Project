@extends('layouts.admin')

@section('title', 'Laporan Keuangan')
@section('page-crumb', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@section('page-actions')
    <a href="{{ route('admin.reports.pdf', request()->only(['start','end'])) }}"
       class="btn btn-ink btn-sm"
       style="background:var(--ink);color:var(--paper)">
       ⬇ Unduh PDF
    </a>
@endsection

@section('content')

{{-- Filter Periode --}}
<div class="panel" style="margin-bottom:18px">
    <div class="panel-body">
        <form method="GET" action="{{ route('admin.reports.index') }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
            <div class="field" style="margin:0;flex:1;min-width:160px">
                <label>Dari Tanggal</label>
                <input type="date" name="start" value="{{ $start }}" max="{{ now()->toDateString() }}">
            </div>
            <div class="field" style="margin:0;flex:1;min-width:160px">
                <label>Sampai Tanggal</label>
                <input type="date" name="end" value="{{ $end }}" max="{{ now()->toDateString() }}">
            </div>
            <button type="submit" class="btn btn-volt">Tampilkan</button>
        </form>
    </div>
</div>

{{-- Ringkasan Keuangan --}}
<div class="stat-grid" style="margin-bottom:18px">
    <div class="stat dark">
        <div class="lbl">Total Omzet</div>
        <div class="val">{{ \App\Support\Format::rupiah($summary['revenue']) }}</div>
    </div>
    <div class="stat">
        <div class="lbl">Total HPP</div>
        <div class="val">{{ \App\Support\Format::rupiah($summary['cogs']) }}</div>
    </div>
    <div class="stat {{ $summary['profit'] >= 0 ? '' : 'danger' }}">
        <div class="lbl">Laba / Rugi Bersih</div>
        <div class="val" style="{{ $summary['profit'] < 0 ? 'color:var(--danger)' : '' }}">
            {{ \App\Support\Format::rupiah($summary['profit']) }}
        </div>
    </div>
    <div class="stat">
        <div class="lbl">Jumlah Transaksi</div>
        <div class="val">{{ $summary['transaction_count'] }}</div>
    </div>
</div>

<div class="split">
    {{-- Detail breakdown --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <h3>Rincian Periode</h3>
                <div class="sub">
                    {{ \App\Support\Format::tanggal($start) }} –
                    {{ \App\Support\Format::tanggal($end) }}
                </div>
            </div>
        </div>
        <div class="panel-body">
            <table style="width:100%">
                <tbody>
                    <tr>
                        <td style="padding:10px 0;color:var(--muted);font-size:13px">Pesanan Website (disetujui)</td>
                        <td style="text-align:right;font-weight:700">{{ $summary['order_count'] }} transaksi</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:var(--muted);font-size:13px">Penjualan Manual (WA/IG/FB/Walk-in)</td>
                        <td style="text-align:right;font-weight:700">{{ $summary['manual_count'] }} transaksi</td>
                    </tr>
                    <tr style="border-top:1px solid var(--line-strong)">
                        <td style="padding:12px 0 6px;font-weight:700">Total Omzet</td>
                        <td style="text-align:right;font-weight:800;font-family:var(--disp);font-size:18px">{{ \App\Support\Format::rupiah($summary['revenue']) }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;color:var(--muted);font-size:13px">( − ) Total HPP</td>
                        <td style="text-align:right;color:var(--muted)">{{ \App\Support\Format::rupiah($summary['cogs']) }}</td>
                    </tr>
                    <tr style="border-top:1.5px solid var(--ink)">
                        <td style="padding:12px 0 2px;font-weight:700;font-size:15px">
                            {{ $summary['profit'] >= 0 ? 'Laba Bersih ✅' : 'Rugi ⚠️' }}
                        </td>
                        <td style="text-align:right;font-weight:800;font-family:var(--disp);font-size:22px;
                                   color:{{ $summary['profit'] >= 0 ? 'var(--ok)' : 'var(--danger)' }}">
                            {{ \App\Support\Format::rupiah($summary['profit']) }}
                        </td>
                    </tr>
                    @if ($summary['revenue'] > 0)
                    <tr>
                        <td style="padding:6px 0 0;color:var(--muted);font-size:12px">Margin Keuntungan</td>
                        <td style="text-align:right;font-size:12px;font-weight:700;color:var(--muted)">
                            {{ number_format(($summary['profit'] / $summary['revenue']) * 100, 1) }}%
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="panel-body" style="padding-top:0">
            <p class="note">
                HPP dihitung dari snapshot <em>cost_price</em> tiap detail item saat transaksi disetujui/dicatat,
                berdasarkan HPP Moving Average (Metode Rata-Rata Bergerak) produk pada saat itu (F-12).
                Perubahan HPP setelah tanggal transaksi tidak mempengaruhi laporan ini.
            </p>
        </div>
    </div>

    {{-- Produk terlaris --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <h3>Produk Terlaris</h3>
                <div class="sub">Top 5 berdasarkan total unit terjual</div>
            </div>
        </div>
        @if ($topProducts->isEmpty())
            <div class="empty" style="padding:30px 20px"><div class="ic">📦</div><p>Belum ada data penjualan pada periode ini.</p></div>
        @else
            <table>
                <thead>
                    <tr><th>#</th><th>Produk</th><th>Terjual</th><th>Omzet</th></tr>
                </thead>
                <tbody>
                @foreach ($topProducts as $i => $item)
                    <tr>
                        <td>
                            @if ($i === 0) 🥇 @elseif ($i === 1) 🥈 @elseif ($i === 2) 🥉
                            @else {{ $i + 1 }} @endif
                        </td>
                        <td class="cellname">{{ $item['product_name'] }}</td>
                        <td><span class="pill neutral">{{ $item['qty'] }} unit</span></td>
                        <td>{{ \App\Support\Format::rupiah($item['total']) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@endsection
