@php
    $statusMap = [
        'pending' => ['warn', 'Menunggu Verifikasi'],
        'approved' => ['ok', 'Disetujui'],
        'rejected' => ['danger', 'Ditolak'],
    ];
    [$statusClass, $statusLabel] = $statusMap[$status] ?? ['neutral', $status];
@endphp
<span class="pill {{ $statusClass }}"><span class="dot"></span>{{ $statusLabel }}</span>
