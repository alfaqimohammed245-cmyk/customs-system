<style>
@media print {
    /* إخفاء العناصر غير المرغوبة عند الطباعة */
    aside, nav, header, form, .print\:hidden, [class*="sidebar"], [class*="navigation"] {
        display: none !important;
    }
    
    /* ضبط الصفحة والـ body */
    body, html {
        direction: rtl !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* إظهار الجدول ورأس الجدول بوضوح تام */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    thead {
        display: table-header-group !important;
    }
    
    tr {
        display: table-row !important;
        page-break-inside: avoid !important;
    }
    
    th {
        display: table-cell !important;
        background-color: #f8fafc !important;
        color: #334155 !important;
        font-weight: bold !important;
        padding: 8px !important;
        font-size: 11px !important;
        text-align: right !important;
        border-bottom: 2px solid #cbd5e1 !important;
    }

    td {
        display: table-cell !important;
        padding: 8px !important;
        font-size: 10px !important;
        text-align: right !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    /* إخفاء عمود التحكم الأخير أثناء الطباعة */
    th:last-child, td:last-child {
        display: none !important;
    }

    /* إلغاء الـ overflows التي تمنع ظهور العناوين */
    .overflow-x-auto, .bg-white, .rounded-2xl {
        overflow: visible !important;
        box-shadow: none !important;
        border: none !important;
    }
}
</style>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 print:hidden">
            <div>
                <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight">إدارة المعاملات الجمركية</h1>
                <p class="text-sm text-slate-500 mt-1">متابعة ومعالجة كافة معاملات الشحن والتخليص الجمركي عبر سير العمليات الجمركية</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- زر الطباعة العامة للجدول -->
                <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> طباعة الجدول
                </button>
                @can('تصدير Excel')
                <a href="{{ route('transactions.export', request()->query()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-file-excel"></i> تصدير Excel
                </a>
                @endcan
                @can('سير العمل والمراحل')
                <a href="{{ route('transactions.workflow') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-bolt"></i> لوحة سير العمل
                </a>
                @endcan
                @can('إضافة')
                <a href="{{ route('transactions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> معاملة جديدة
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8 print:py-0 print:bg-white print:w-full print:m-0 print:p-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 print:max-w-none print:px-0 print:space-y-4">

            <!-- عنوان التقرير يظهر فقط عند الطباعة -->
            <div class="hidden print:block text-center border-b border-slate-300 pb-4 mb-4">
                <h2 class="text-xl font-bold text-slate-900">تقرير إجمالي العمليات الجمركية</h2>
                <p class="text-sm text-slate-600 mt-1">تاريخ الطباعة: {{ date('Y-m-d H:i') }}</p>
            </div>

            <!-- تبويبات التصفية الرئيسية للمقاطع (تختفي عند الطباعة) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4 print:hidden">
                <div class="flex items-center gap-2 overflow-x-auto">
                    <a href="{{ route('transactions.index', array_merge(request()->except('tab', 'page'), ['tab' => 'active'])) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'active' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                        <span>⚡ المعاملات الجارية والنشطة</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'active' ? 'bg-blue-800 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $activeCount }}</span>
                    </a>

                    <a href="{{ route('transactions.completed') }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'completed' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                        <span>✅ المعاملات المكتملة والأرشيف</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-50 text-emerald-700">{{ $completedCount }}</span>
                    </a>

                    <a href="{{ route('transactions.index', array_merge(request()->except('tab', 'page'), ['tab' => 'all'])) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                        <span>📋 كافة المعاملات</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'all' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $totalCount }}</span>
                    </a>
                </div>

                <!-- أزرار التصفية السريعة حسب سير العمليات الجمركية -->
                <div class="flex items-center gap-1.5 overflow-x-auto text-[11px]">
                    <span class="text-xs font-bold text-slate-400 ml-1">المرحلة:</span>
                    @for($s = 1; $s <= 6; $s++)
                    <a href="{{ route('transactions.index', array_merge(request()->except('stage', 'page'), ['stage' => request('stage') == $s ? '' : $s])) }}" 
                       class="px-2.5 py-1 rounded-lg font-bold transition flex items-center gap-1 {{ request('stage') == $s ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                        <span>{{ $s }}</span>
                        @if(isset($stageCounts[$s]) && $stageCounts[$s] > 0)
                        <span class="w-4 h-4 rounded-full text-[9px] flex items-center justify-center {{ request('stage') == $s ? 'bg-white text-blue-600' : 'bg-blue-100 text-blue-800' }}">{{ $stageCounts[$s] }}</span>
                        @endif
                    </a>
                    @endfor
                </div>
            </div>

            <!-- نموذج البحث والفلاتر المتقدمة (يختفي عند الطباعة) -->
            <form method="GET" action="{{ route('transactions.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 print:hidden">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">بحث شامل</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم المعاملة، البوليصة، البيان، الوكيل..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">المرحلة الحالية</label>
                    <select name="stage" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        <option value="">جميع مراحل سير العمليات الجمركية</option>
                        @foreach($stagesConfig as $sId => $sCfg)
                        <option value="{{ $sId }}" {{ request('stage') == $sId ? 'selected' : '' }}>{{ $sId }}. {{ $sCfg['name'] }}</option>
                        @endforeach
                    </select>
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
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">الوكيل الملاحي / الشركة</label>
                    <input type="text" name="shipping_agent" value="{{ request('shipping_agent') }}" placeholder="اسم الوكيل الملاحي..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-xl text-xs w-full shadow transition">
                        <i class="fa-solid fa-magnifying-glass ml-1"></i> بحث
                    </button>
                    <a href="{{ route('transactions.index', ['tab' => $tab]) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-2 rounded-xl text-xs transition text-center shrink-0">
                        إعادة ضبط
                    </a>
                </div>
            </form>

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center justify-between print:hidden">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
            </div>
            @endif

            <!-- جدول عرض المعاملات الجمركية -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden print:border-none print:shadow-none">
                <div class="overflow-x-auto print:overflow-visible">
                    <table class="w-full text-right border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-100 print:bg-slate-200">
                                <th class="p-3.5">رقم المعاملة</th>
                                <th class="p-3.5">التاجر</th>
                                <th class="p-3.5">الوكيل الملاحي</th>
                                <th class="p-3.5">البوليصة</th>
                                <th class="p-3.5">البيان الجمركي</th>
                                <th class="p-3.5">المرحلة الحالية</th>
                                <th class="p-3.5">الإنجاز</th>
                                <th class="p-3.5">الحالة</th>
                                <th class="p-3.5">سبب التأخير / ملاحظات</th>
                                <th class="p-3.5">التاريخ</th>
                                <th class="p-3.5 text-center">التحكم</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($transactions as $tx)
                            @php
                                $curStage = $tx->current_stage ?? 1;
                                $curDelay = $tx->{"stage_{$curStage}_delay_reason"};
                                $curNotes = $tx->{"stage_{$curStage}_notes"};
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition {{ !empty($curDelay) ? 'bg-rose-50/30' : '' }}">
                                <td class="p-3.5 font-extrabold text-blue-600 font-mono">
                                    <a href="{{ route('transactions.show', $tx->id) }}" class="hover:underline">{{ $tx->transaction_number }}</a>
                                </td>
                                <td class="p-3.5 font-medium text-slate-800">{{ $tx->trader->name ?? $tx->trader_name ?? '---' }}</td>
                                <td class="p-3.5 text-slate-600">{{ $tx->shipping_agent ?? $tx->company->name ?? '---' }}</td>
                                <td class="p-3.5 font-mono text-slate-700">{{ $tx->policy_number ?? '---' }}</td>
                                <td class="p-3.5 font-mono text-indigo-600 font-bold">{{ $tx->declaration_number ?? '---' }}</td>
                                <td class="p-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border
                                        @if($curStage == 1) bg-blue-50 text-blue-700 border-blue-200
                                        @elseif($curStage == 2) bg-indigo-50 text-indigo-700 border-indigo-200
                                        @elseif($curStage == 3) bg-amber-50 text-amber-700 border-amber-200
                                        @elseif($curStage == 4) bg-purple-50 text-purple-700 border-purple-200
                                        @elseif($curStage == 5) bg-teal-50 text-teal-700 border-teal-200
                                        @elseif($curStage == 6) bg-emerald-50 text-emerald-700 border-emerald-200
                                        @endif">
                                        <span class="w-2 h-2 rounded-full 
                                            @if($curStage == 1) bg-blue-500 @elseif($curStage == 2) bg-indigo-500 @elseif($curStage == 3) bg-amber-500 @elseif($curStage == 4) bg-purple-500 @elseif($curStage == 5) bg-teal-500 @elseif($curStage == 6) bg-emerald-500 @endif"></span>
                                        {{ $curStage }}. {{ $stagesConfig[$curStage]['name'] ?? 'مرحلة ' . $curStage }}
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    <div class="w-16">
                                        <div class="flex justify-between text-[10px] font-bold text-slate-500 mb-0.5">
                                            <span>{{ $tx->progress_percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $tx->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                        @if($tx->status == 'مكتملة') bg-emerald-100 text-emerald-800
                                        @elseif($tx->status == 'قيد التنفيذ') bg-amber-100 text-amber-800
                                        @elseif($tx->status == 'جديدة') bg-sky-100 text-sky-800
                                        @elseif($tx->status == 'ملغاة') bg-rose-100 text-rose-800
                                        @else bg-slate-100 text-slate-700 @endif">
                                        {{ $tx->status ?? 'جديدة' }}
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    @if(!empty($curDelay))
                                    <span class="inline-flex items-center gap-1 bg-rose-100 text-rose-800 text-[10px] font-bold px-2 py-0.5 rounded-md" title="{{ $curDelay }}">
                                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> {{ \Illuminate\Support\Str::limit($curDelay, 20) }}
                                    </span>
                                    @elseif(!empty($curNotes))
                                    <span class="text-slate-500 text-[10px]" title="{{ $curNotes }}">📝 {{ \Illuminate\Support\Str::limit($curNotes, 20) }}</span>
                                    @else
                                    <span class="text-slate-300">---</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-slate-400">{{ $tx->created_at ? $tx->created_at->format('Y-m-d') : '---' }}</td>
                                <td class="p-3.5 text-center space-x-1 space-x-reverse">
                                    <a href="{{ route('transactions.show', $tx->id) }}" class="text-slate-600 hover:text-blue-600 p-1 font-bold" title="عرض"><i class="fa-solid fa-eye"></i></a>
                                    @can('تعديل')
                                    <a href="{{ route('transactions.edit', $tx->id) }}" class="text-indigo-600 hover:text-indigo-900 p-1 font-bold" title="تعديل"><i class="fa-solid fa-pen-to-square"></i></a>
                                    @endcan
                                    @can('حذف')
                                    <form action="{{ route('transactions.destroy', $tx->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه العملية الجمركية؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-900 p-1 font-bold" title="حذف"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="p-8 text-center text-slate-400 font-medium">لا توجد معاملات جمركية مطابقة للبحث حالياً.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- الترقيم والتنقل بين الصفحات (يختفي عند الطباعة) -->
                <div class="p-4 border-t border-slate-100 print:hidden">
                    {{ $transactions->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>