<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(protected FinanceService $finance)
    {
    }

    /**
     * F-13: Laporan keuangan - rekapitulasi omzet harian/bulanan,
     * total HPP, laba/rugi, dan produk terlaris untuk rentang
     * tanggal yang dipilih Admin.
     */
    public function index(Request $request): View
    {
        [$start, $end] = $this->resolveRange($request);

        return view('admin.reports.index', [
            'summary' => $this->finance->summary($start, $end),
            'topProducts' => $this->finance->topProducts($start, $end, 5),
            'start' => $start,
            'end' => $end,
        ]);
    }

    /**
     * F-13: Mengunduh laporan keuangan dalam format PDF, sesuai
     * rentang tanggal yang sama dengan halaman laporan.
     */
    public function pdf(Request $request): Response
    {
        [$start, $end] = $this->resolveRange($request);

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'summary' => $this->finance->summary($start, $end),
            'topProducts' => $this->finance->topProducts($start, $end, 5),
            'start' => $start,
            'end' => $end,
        ])->setPaper('a4');

        return $pdf->download('laporan-keuangan-jukase-'.$start.'-sd-'.$end.'.pdf');
    }

    /**
     * Mengambil rentang tanggal dari query string (?start=&end=).
     * Default: tanggal 1 bulan ini sampai hari ini.
     *
     * @return array{0: string, 1: string}
     */
    protected function resolveRange(Request $request): array
    {
        $start = $request->input('start') ?: now()->startOfMonth()->toDateString();
        $end = $request->input('end') ?: now()->toDateString();

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        return [$start, $end];
    }
}
