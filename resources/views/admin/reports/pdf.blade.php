<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Jukase Project</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #14130F;
            background: #fff;
            padding: 32px 36px;
            line-height: 1.5;
        }

        /* Header */
        .pdf-header { border-bottom: 2px solid #14130F; padding-bottom: 14px; margin-bottom: 22px; }
        .pdf-header .brand { font-size: 20px; font-weight: 700; letter-spacing: -0.02em; }
        .pdf-header .brand span { background: #CCF000; padding: 2px 6px; border-radius: 4px; color: #14130F; }
        .pdf-header .sub { font-size: 10px; color: #6B675C; margin-top: 3px; }
        .pdf-header .period { float: right; text-align: right; font-size: 11px; margin-top: -36px; }
        .pdf-header .period strong { font-size: 13px; }
        .clearfix::after { content: ''; display: table; clear: both; }

        /* Stat boxes */
        .stat-row { display: table; width: 100%; margin-bottom: 22px; border-collapse: separate; border-spacing: 10px; margin-left: -10px; }
        .stat-box { display: table-cell; background: #F4F0E6; border: 1px solid #E0D8C4; border-radius: 8px; padding: 12px 14px; width: 25%; vertical-align: top; }
        .stat-box.dark { background: #14130F; color: #F4F0E6; }
        .stat-box .lbl { font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; color: #8a8678; margin-bottom: 4px; }
        .stat-box.dark .lbl { color: #8a8678; }
        .stat-box .val { font-size: 16px; font-weight: 700; }
        .stat-box.dark .val { color: #CCF000; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
        th { font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; color: #6B675C; text-align: left; padding: 8px 10px; border-bottom: 1.5px solid #14130F; }
        td { padding: 9px 10px; border-bottom: 1px solid #E0D8C4; font-size: 11px; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }

        /* Section title */
        .section-title { font-size: 13px; font-weight: 700; margin: 22px 0 10px; letter-spacing: -0.01em; }
        .section-title span { background: #CCF000; padding: 1px 6px; border-radius: 3px; font-size: 10px; margin-left: 6px; vertical-align: middle; }

        /* Summary breakdown */
        .breakdown { background: #F4F0E6; border-radius: 8px; padding: 14px 16px; margin-bottom: 22px; }
        .breakdown .row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 11px; }
        .breakdown .row .k { color: #6B675C; }
        .breakdown .divider { border-top: 1px solid #C9C4B5; margin: 6px 0; }
        .breakdown .total-row { display: flex; justify-content: space-between; padding: 8px 0 2px; font-weight: 700; font-size: 14px; }

        /* Footer */
        .pdf-footer { border-top: 1px solid #E0D8C4; margin-top: 30px; padding-top: 10px; font-size: 9px; color: #8a8678; display: flex; justify-content: space-between; }

        .badge { display: inline-block; background: #E0D8C4; border-radius: 4px; padding: 2px 7px; font-size: 9px; font-weight: 700; }
        .badge.ok { background: #DDF3E4; color: #1F9D55; }
        .badge.warn { background: #FBE0E0; color: #D63A3A; }
    </style>
</head>
<body>

<div class="pdf-header clearfix">
    <div class="period">
        <div>Periode Laporan</div>
        <strong>{{ \App\Support\Format::tanggal($start) }} – {{ \App\Support\Format::tanggal($end) }}</strong>
        <div style="margin-top:4px;color:#6B675C">Dicetak: {{ \App\Support\Format::tanggal(now()) }}</div>
    </div>
    <div class="brand"><span>J</span> JUKASE PROJECT</div>
    <div class="sub">Laporan Keuangan – Sistem Informasi Manajemen Pemesanan & Laporan Keuangan (F-13)</div>
    <div class="sub" style="margin-top:2px">Jl. Raya Taman Kopo Indah 2 No.12, Sayati, Margahayu, Kab. Bandung</div>
</div>

{{-- Stat Cards --}}
<div class="stat-row">
    <div class="stat-box dark">
        <div class="lbl">Total Omzet</div>
        <div class="val">{{ \App\Support\Format::rupiah($summary['revenue']) }}</div>
    </div>
    <div class="stat-box">
        <div class="lbl">Total HPP (Moving Avg)</div>
        <div class="val">{{ \App\Support\Format::rupiah($summary['cogs']) }}</div>
    </div>
    <div class="stat-box">
        <div class="lbl">{{ $summary['profit'] >= 0 ? 'Laba Bersih' : 'Rugi' }}</div>
        <div class="val" style="color:{{ $summary['profit'] >= 0 ? '#1F9D55' : '#D63A3A' }}">{{ \App\Support\Format::rupiah($summary['profit']) }}</div>
    </div>
    <div class="stat-box">
        <div class="lbl">Total Transaksi</div>
        <div class="val">{{ $summary['transaction_count'] }}</div>
    </div>
</div>

{{-- Breakdown --}}
<div class="section-title">Rincian Laporan Laba / Rugi</div>
<div class="breakdown">
    <div class="row"><span class="k">Pesanan Website (disetujui)</span><span>{{ $summary['order_count'] }} transaksi</span></div>
    <div class="row"><span class="k">Penjualan Manual (WA/IG/FB/Walk-in)</span><span>{{ $summary['manual_count'] }} transaksi</span></div>
    <div class="divider"></div>
    <div class="row"><span class="k">Total Omzet Penjualan</span><span><strong>{{ \App\Support\Format::rupiah($summary['revenue']) }}</strong></span></div>
    <div class="row"><span class="k">( − ) Total HPP (Metode Moving Average)</span><span>{{ \App\Support\Format::rupiah($summary['cogs']) }}</span></div>
    <div class="divider"></div>
    <div class="total-row">
        <span>{{ $summary['profit'] >= 0 ? 'Laba Bersih' : '⚠ Rugi' }}</span>
        <span style="color:{{ $summary['profit'] >= 0 ? '#1F9D55' : '#D63A3A' }}">{{ \App\Support\Format::rupiah($summary['profit']) }}</span>
    </div>
    @if ($summary['revenue'] > 0)
    <div class="row" style="font-size:10px;color:#8a8678;padding-top:4px">
        <span>Margin Keuntungan</span>
        <span>{{ number_format(($summary['profit'] / $summary['revenue']) * 100, 1) }}%</span>
    </div>
    @endif
</div>

{{-- Produk Terlaris --}}
@if ($topProducts->isNotEmpty())
<div class="section-title">Produk Terlaris <span>TOP 5 UNIT TERJUAL</span></div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nama Produk</th>
            <th>Unit Terjual</th>
            <th>Omzet Produk</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($topProducts as $i => $item)
        <tr>
            <td style="font-weight:700">{{ $i + 1 }}</td>
            <td>{{ $item['product_name'] }}</td>
            <td><span class="badge">{{ $item['qty'] }} unit</span></td>
            <td style="font-weight:700">{{ \App\Support\Format::rupiah($item['total']) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif

<div class="pdf-footer">
    <span>Jukase Project · Sistem Informasi Manajemen Pemesanan &amp; Laporan Keuangan</span>
    <span>HPP dihitung menggunakan Metode Moving Average (Rata-Rata Bergerak) · F-12</span>
</div>

</body>
</html>
