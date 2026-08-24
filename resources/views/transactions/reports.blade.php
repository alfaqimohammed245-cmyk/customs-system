<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight">التقارير الشاملة والمالية</h1>
                <p class="text-sm text-slate-500 mt-1">تقارير التفريغ، الشحن، الفواتير، والأداء الفعلي للنظام</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition">
                    <i class="fa-solid fa-print ml-1"></i> طباعة التقرير
                </button>
                <a href="{{ route('transactions.export') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition">
                    <i class="fa-solid fa-file-excel ml-1"></i> تصدير البيانات (Excel)
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 print:py-0 print:bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- 1. ملخص البطاقات العامة -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <span class="text-xs font-bold text-slate-400 block mb-1">إجمالي العمليات</span>
                    <span class="text-3xl font-black text-slate-800">{{ number_format($totalTransactions) }}</span>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <span class="text-xs font-bold text-slate-400 block mb-1">العمليات المكتملة</span>
                    <span class="text-3xl font-black text-emerald-600">{{ number_format($completedTransactions) }}</span>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <span class="text-xs font-bold text-slate-400 block mb-1">قيد التنفيذ</span>
                    <span class="text-3xl font-black text-amber-600">{{ number_format($pendingTransactions) }}</span>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <span class="text-xs font-bold text-slate-400 block mb-1">العمليات المتأخرة / بانتظار</span>
                    <span class="text-3xl font-black text-rose-600">{{ number_format($delayedTransactions) }}</span>
                </div>
            </div>

            <!-- 2. التقارير المالية التفصيلية -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
                <h3 class="font-bold text-slate-800 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-coins text-emerald-600"></i> التقارير المجمعة للفواتير والمبالغ
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                    <div class="bg-slate-50 p-4 rounded-xl">
                        <span class="text-xs font-bold text-slate-500 block mb-1">فواتير إذن التسليم</span>
                        <span class="text-xl font-bold text-slate-800">{{ number_format($totalDeliveryOrderInvoices, 2) }} ر.س</span>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl">
                        <span class="text-xs font-bold text-slate-500 block mb-1">فواتير الموانئ</span>
                        <span class="text-xl font-bold text-slate-800">{{ number_format($totalPortsInvoices, 2) }} ر.س</span>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl">
                        <span class="text-xs font-bold text-slate-500 block mb-1">فواتير المشغل</span>
                        <span class="text-xl font-bold text-slate-800">{{ number_format($totalOperatorInvoices, 2) }} ر.س</span>
                    </div>

                    <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                        <span class="text-xs font-bold text-emerald-700 block mb-1">إجمالي فواتير العملاء</span>
                        <span class="text-2xl font-black text-emerald-700">{{ number_format($totalClientInvoices, 2) }} ر.س</span>
                    </div>
                </div>
            </div>

            <!-- 3. تقرير أداء الموظفين -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                        <i class="fa-solid fa-user-check text-blue-600"></i> تقرير إنجاز وأداء الموظفين
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-100">
                                <th class="p-3.5">اسم الموظف</th>
                                <th class="p-3.5">القسم / الوظيفة</th>
                                <th class="p-3.5">العمليات المكتملة</th>
                                <th class="p-3.5">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($employeePerformance as $emp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3.5 font-bold text-slate-800">{{ $emp->name }}</td>
                                <td class="p-3.5 text-slate-500">{{ $emp->department ?? 'عام' }} - {{ $emp->job_title ?? 'موظف' }}</td>
                                <td class="p-3.5 font-bold text-emerald-600 text-sm">{{ number_format($emp->transactions_count) }} عملية مكتملة</td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $emp->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $emp->is_active ? 'نشط' : 'معطل' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400">لا يوجد موظفين مسجلين حالياً.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>