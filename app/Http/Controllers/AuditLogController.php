<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $this->checkPermission('مشاهدة سجل العمليات');
        $logs = AuditLog::with('user')
            ->when($request->search, function($query, $search) {
                $query->where('description', 'like', "%{$search}%")
                      ->orWhere('action', 'like', "%{$search}%")
                      ->orWhere('entity', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(20);

        return view('audit_logs.index', compact('logs'));
    }

    public function exportPdf(Request $request)
    {
        $this->checkPermission(['مشاهدة سجل العمليات', 'طباعة']);

        $logs = AuditLog::with('user')
            ->when($request->search, function($query, $search) {
                $query->where('description', 'like', "%{$search}%")
                      ->orWhere('action', 'like', "%{$search}%")
                      ->orWhere('entity', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        $pdf = Pdf::loadView('audit_logs.pdf', compact('logs'));
        return $pdf->download('سجل_العمليات_'.now()->format('Y-m-d').'.pdf');
    }
}