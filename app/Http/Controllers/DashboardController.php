<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Trader;
use App\Models\Company;
use App\Models\User;
use App\Models\Container;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. البطاقات الإحصائية الرئيسية
        $totalTransactions      = Transaction::count();
        $activeTransactions     = Transaction::active()->count();
        $completedTransactions  = Transaction::completed()->count();
        $newTransactions        = Transaction::where('status', 'جديدة')->count();
        $inProgressTransactions = Transaction::where('status', 'قيد التنفيذ')->count();
        $delayedTransactions    = Transaction::active()->where(function($q) {
            $q->whereNotNull('stage_1_delay_reason')
              ->orWhereNotNull('stage_2_delay_reason')
              ->orWhereNotNull('stage_3_delay_reason')
              ->orWhereNotNull('stage_4_delay_reason')
              ->orWhereNotNull('stage_5_delay_reason')
              ->orWhereNotNull('stage_6_delay_reason')
              ->orWhereIn('status', ['بانتظار مستندات', 'بانتظار العميل', 'بانتظار السداد']);
        })->count();

        $totalTraders    = Trader::count();
        $totalCompanies  = Company::count();
        $totalUsers      = User::count();
        $totalContainers = Container::count();
        $totalPolicies   = Transaction::whereNotNull('policy_number')->where('policy_number', '!=', '')->count();

        // 2. إحصائيات توزيع المعاملات النشطة على المراحل الست
        $stagesConfig = Transaction::STAGES;
        $stageCounts = [];
        for ($i = 1; $i <= 6; $i++) {
            $stageCounts[$i] = Transaction::active()->where('current_stage', $i)->count();
        }

        // 3. تجميع بيانات الرسوم البيانية
        $statusCounts = [
            'جديدة'           => $newTransactions,
            'قيد التنفيذ'     => $inProgressTransactions,
            'بانتظار مستندات' => Transaction::where('status', 'بانتظار مستندات')->count(),
            'بانتظار العميل'  => Transaction::where('status', 'بانتظار العميل')->count(),
            'بانتظار السداد'  => Transaction::where('status', 'بانتظار السداد')->count(),
            'مكتملة'          => $completedTransactions,
            'مؤرشفة'          => Transaction::where('status', 'مؤرشفة')->count(),
            'ملغاة'           => Transaction::where('status', 'ملغاة')->count(),
        ];

        // توزيع العمليات حسب آخر 6 أشهر
        $monthlyChartData = Transaction::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw("count(*) as total")
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->take(6)
        ->get()
        ->reverse();

        // 4. أحدث العمليات النشطة الجارية مع المرحلة الحالية
        $activeLatestTransactions = Transaction::with(['trader', 'company', 'statusModel', 'employee', 'driver'])
            ->active()
            ->latest()
            ->take(6)
            ->get();

        $latestAuditLogs = AuditLog::with(['user', 'transaction'])
            ->latest()
            ->take(6)
            ->get();

        $latestUsers = User::with('roles')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalTransactions',
            'activeTransactions',
            'newTransactions',
            'inProgressTransactions',
            'completedTransactions',
            'delayedTransactions',
            'totalTraders',
            'totalCompanies',
            'totalUsers',
            'totalContainers',
            'totalPolicies',
            'stageCounts',
            'stagesConfig',
            'statusCounts',
            'monthlyChartData',
            'activeLatestTransactions',
            'latestAuditLogs',
            'latestUsers'
        ));
    }
}
