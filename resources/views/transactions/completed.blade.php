<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-emerald-600 text-white rounded-xl text-lg shadow-sm">✅</span>
                    سجل المعاملات المكتملة والأرشيف النهائي
                </h1>
                <p class="text-sm text-slate-500 mt-1">عرض وتحليل المعاملات الجمركية التي أتمت سير العمليات الجمركية بنجاح وتم إغلاقها وإصدار فواتيرها</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('transactions.export', array_merge(request()->query(), ['tab' => 'completed'])) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-file-excel"></i> تصدير المكتملة Excel
                </a>
                <a href="{{ route('transactions.workflow') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-bolt"></i> لوحة سير العمل
                </a>
                <a href="{{ route('transactions.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2.5 rounded-xl text-sm transition">
                    <i class="fa-solid fa-arrow-right ml-1"></i> كافة المعاملات
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- بطاقات الإحصائيات السريعة للعمليات المكتملة -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 block mb-1">إجمالي المعاملات المنجزة</span>
                        <span class="text-3xl font-black text-slate-800">{{ number_format($totalCount) }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 block mb-1">إجمالي فواتير العملاء المحصلة</span>
                        <span class="text-2xl font-black text-emerald-600">{{ number_format($totalRevenue, 2) }} <span class="text-xs font-bold text-slate-400">ر.س</span></span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 block mb-1">رسوم وفواتير الموانئ المسددة</span>
                        <span class="text-2xl font-black text-slate-700">{{ number_format($totalPorts, 2) }} <span class="text-xs font-bold text-slate-400">ر.س</span></span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-ship"></i>
                    </div>
                </div>
            </div>

            <!-- نموذج البحث والفلترة للعمليات المكتملة -->
            <form method="GET" action="{{ route('transactions.completed') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">بحث شامل</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم المعاملة، البوليصة، البيان..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">التاجر / العميل</label>
                    <select name="trader_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        <option value="">جميع التجار</option>
                        @foreach($traders as $trader)
                        <option value="{{ $trader->id }}" {{ request('trader_id') == $trader->id ? 'selected' : '' }}>{{ $trader->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">من تاريخ الإنجاز</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">إلى تاريخ الإنجاز</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-xl text-xs w-full shadow transition">
                        <i class="fa-solid fa-magnifying-glass ml-1"></i> بحث
                    </button>
                    <a href="{{ route('transactions.completed') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-2 rounded-xl text-xs transition text-center shrink-0">
                        إعادة ضبط
                    </a>
                </div>
            </form>

            <!-- جدول المعاملات المكتملة -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-100">
                                <th class="p-3.5">رقم المعاملة</th>
                                <th class="p-3.5">التاجر</th>
                                <th class="p-3.5">شركة الشحن</th>
                                <th class="p-3.5">الوكيل الملاحي</th>
                                <th class="p-3.5">البوليصة</th>
                                <th class="p-3.5">البيان الجمركي</th>
                                <th class="p-3.5">فاتورة العميل</th>
                                <th class="p-3.5">تاريخ الإنجاز</th>
                                <th class="p-3.5">منفذ الإغلاق</th>
                                <th class="p-3.5 text-center">التحكم</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($completedTransactions as $tx)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3.5 font-extrabold text-blue-600">
                                    <a href="{{ route('transactions.show', $tx->id) }}" class="hover:underline">{{ $tx->transaction_number }}</a>
                                </td>
                                <td class="p-3.5 font-bold text-slate-800">{{ $tx->trader->name ?? $tx->trader_name ?? '---' }}</td>
                                <td class="p-3.5 text-slate-600">{{ $tx->company->name ?? $tx->company_name ?? '---' }}</td>
                                <td class="p-3.5 text-slate-600">{{ $tx->shipping_agent ?? '---' }}</td>
                                <td class="p-3.5 font-mono text-slate-700">{{ $tx->policy_number ?? '---' }}</td>
                                <td class="p-3.5 font-mono text-indigo-600 font-bold">{{ $tx->declaration_number ?? '---' }}</td>
                                <td class="p-3.5 font-bold text-emerald-600">{{ number_format($tx->client_invoice ?? 0, 2) }} ر.س</td>
                                <td class="p-3.5 text-slate-600">
                                    {{ $tx->completed_at ? (is_object($tx->completed_at) ? $tx->completed_at->format('Y-m-d H:i') : $tx->completed_at) : ($tx->updated_at ? $tx->updated_at->format('Y-m-d H:i') : '---') }}
                                </td>
                                <td class="p-3.5 text-slate-500">
                                    {{ $tx->stage6User->name ?? $tx->employee->name ?? $tx->user->name ?? 'النظام' }}
                                </td>
                                <td class="p-3.5 text-center space-x-1 space-x-reverse">
                                    <a href="{{ route('transactions.show', $tx->id) }}" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-2.5 py-1 rounded-lg font-bold transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-eye"></i> التفاصيل
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="p-12 text-center text-slate-400">
                                    <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                                    لا توجد معاملات مكتملة مطابقة لمعايير البحث.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $completedTransactions->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
