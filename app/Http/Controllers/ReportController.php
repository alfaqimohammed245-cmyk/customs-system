<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Trader;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $this->checkPermission('مشاهدة التقارير');
        $totalTransactions     = Transaction::count();
        $completedTransactions = Transaction::where('status', 'مكتملة')->count();
        $pendingTransactions   = Transaction::where('status', 'قيد التنفيذ')->count();
        $newTransactions       = Transaction::where('status', 'جديدة')->count();
        $delayedTransactions   = Transaction::whereIn('status', ['بانتظار مستندات', 'بانتظار العميل', 'بانتظار السداد'])->count();

        // إحصائيات مالية
        $totalClientInvoices        = Transaction::sum('client_invoice') ?? 0;
        $totalPortsInvoices         = Transaction::sum('ports_invoice') ?? 0;
        $totalOperatorInvoices      = Transaction::sum('operator_invoice') ?? 0;
        $totalDeliveryOrderInvoices = Transaction::sum('delivery_order_invoice') ?? 0;

        // إحصائيات أداء الموظفين
        $employeePerformance = User::withCount(['transactions' => function($q) {
            $q->where('status', 'مكتملة');
        }])->get();

        return view('transactions.reports', compact(
            'totalTransactions',
            'completedTransactions',
            'pendingTransactions',
            'newTransactions',
            'delayedTransactions',
            'totalClientInvoices',
            'totalPortsInvoices',
            'totalOperatorInvoices',
            'totalDeliveryOrderInvoices',
            'employeePerformance'
        ));
    }
}
