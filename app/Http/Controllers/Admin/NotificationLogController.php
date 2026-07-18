<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationLogController extends Controller
{
    /**
     * Riwayat pengiriman notifikasi WhatsApp (F-05/F-07) via Fonnte,
     * supaya Admin bisa memantau notifikasi yang gagal terkirim
     * (mis. nomor WhatsApp Owner sedang diblokir) tanpa perlu
     * membuka database secara langsung.
     */
    public function index(Request $request): View
    {
        $logs = NotificationLog::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->orderByDesc('sent_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.notification-logs.index', [
            'logs' => $logs,
            'status' => (string) $request->input('status', ''),
            'type' => (string) $request->input('type', ''),
            'failedToday' => NotificationLog::where('status', 'failed')
                ->whereDate('created_at', today())
                ->count(),
        ]);
    }
}