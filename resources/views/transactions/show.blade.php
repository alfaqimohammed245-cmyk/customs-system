<style>
@media print {
    /* إخفاء الشريط الجانبي والقوائم العلوية وأزرار التنقل تماماً عند الطباعة */
    aside, nav, header, .sidebar, [class*="sidebar"], [class*="navigation"] {
        display: none !important;
    }
    /* جعل محتوى الصفحة يأخذ العرض الكامل للورقة وبدون هوامش جانبية */
    main, .py-8, body {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
    }
}
</style>

<x-app-layout>
    <!-- إخفاء الهيدر والأزرار أثناء الطباعة -->
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
            <div>
                <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight flex items-center gap-2">
                    <span>تفاصيل المعاملة الجمركية:</span>
                    <span class="text-blue-600 font-mono">{{ $transaction->transaction_number }}</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">تمت الإضافة بتاريخ: {{ $transaction->created_at ? $transaction->created_at->format('Y-m-d H:i') : '---' }} بواسطة {{ $transaction->creator->name ?? $transaction->user->name ?? 'النظام' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-4 py-2 rounded-xl text-sm transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-print"></i> طباعة البيان
                </button>
                @can('تعديل')
                <a href="{{ route('transactions.edit', $transaction->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square"></i> تعديل المعاملة
                </a>
                @endcan
                <a href="{{ route('transactions.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2 rounded-xl text-sm transition">
                    <i class="fa-solid fa-arrow-right ml-1"></i> العودة
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 print:py-0 print:bg-white print:w-full print:m-0 print:p-0">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 print:max-w-none print:px-0 print:space-y-4">

            <!-- عنوان يظهر فقط عند الطباعة -->
            <div class="hidden print:block text-center border-b border-slate-300 pb-4 mb-4">
                <h2 class="text-xl font-bold text-slate-900">تقرير تفاصيل المعاملة الجمركية</h2>
                <p class="text-sm font-mono text-slate-600 mt-1">رقم المعاملة: {{ $transaction->transaction_number }}</p>
            </div>

            <!-- بطاقة الحالة العامة ونسبة الإنجاز -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6 print:border-slate-300 print:shadow-none print:p-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl font-bold print:hidden">
                        <i class="fa-solid fa-ship"></i>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 font-bold block">المرحلة الجمركية الحالية</span>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="text-base font-extrabold text-slate-800">
                                {{ $transaction->current_stage }}. {{ $stagesConfig[$transaction->current_stage]['name'] ?? 'مرحلة غير محددة' }}
                            </span>
                            <span class="px-3 py-0.5 rounded-full text-xs font-bold 
                                @if($transaction->status == 'مكتملة') bg-emerald-100 text-emerald-800
                                @elseif($transaction->status == 'قيد التنفيذ') bg-amber-100 text-amber-800
                                @elseif($transaction->status == 'جديدة') bg-sky-100 text-sky-800
                                @elseif($transaction->status == 'ملغاة') bg-rose-100 text-rose-800
                                @else bg-slate-100 text-slate-800 @endif">
                                {{ $transaction->status ?? 'جديدة' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- شريط نسبة الإنجاز والتحكم -->
                <div class="w-full md:w-80 print:w-64">
                    <div class="flex justify-between items-center text-xs font-bold mb-1.5">
                        <span class="text-slate-600">نسبة التقدم الكلية ({{ $transaction->progress_percentage }}%)</span>
                        <span class="text-blue-600">
                            @php
                                $completedCount = 0;
                                for($i=1; $i<=6; $i++) { if($transaction->isStageCompleted($i)) $completedCount++; }
                            @endphp
                            {{ $completedCount }} من 6 مراحل مكتملة
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200/60">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $transaction->progress_percentage }}%"></div>
                    </div>
                </div>
            </div>

            <!-- خط سير العمليات الجمركية التفاعلي (6-Stage Stepper / Timeline) -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-6 print:border-slate-300 print:shadow-none">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-blue-600 print:hidden"></i>
                        <span>مخطط تقدم سير العمليات الجمركية (Workflow Pipeline)</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 print:grid-cols-3">
                    @for($s = 1; $s <= 6; $s++)
                    @php
                        $cfg = $stagesConfig[$s];
                        $isCompleted = $transaction->isStageCompleted($s);
                        $isCurrent = ($transaction->current_stage == $s && !$isCompleted);
                        $completedAt = $transaction->{"stage_{$s}_completed_at"};
                        $stageUser = $transaction->{"stage{$s}User"};
                        $delayReason = $transaction->{"stage_{$s}_delay_reason"};
                        $notes = $transaction->{"stage_{$s}_notes"};
                        $canEdit = $transaction->canUserEditStage(auth()->user(), $s);
                    @endphp
                    <div class="rounded-xl p-3 border transition relative flex flex-col justify-between
                        @if($isCompleted) bg-emerald-50/60 border-emerald-300
                        @elseif($isCurrent) bg-blue-50 border-blue-400
                        @else bg-slate-50 border-slate-200 @endif">
                        
                        <div>
                            <!-- رأس المرحلة والحالة -->
                            <div class="flex items-center justify-between mb-2">
                                <span class="w-6 h-6 rounded-lg text-xs font-black flex items-center justify-center
                                    @if($isCompleted) bg-emerald-600 text-white
                                    @elseif($isCurrent) bg-blue-600 text-white
                                    @else bg-slate-200 text-slate-600 @endif">
                                    {{ $s }}
                                </span>

                                @if($isCompleted)
                                <span class="text-emerald-700 text-[10px] font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check"></i> مكتملة
                                </span>
                                @elseif($isCurrent)
                                <span class="text-blue-700 text-[10px] font-bold">جارية الآن</span>
                                @else
                                <span class="text-slate-400 text-[10px] font-bold">بانتظار البدء</span>
                                @endif
                            </div>

                            <h4 class="font-extrabold text-xs text-slate-800 leading-tight mb-1">{{ $cfg['name'] }}</h4>
                            <p class="text-[10px] text-slate-400 leading-tight mb-2">{{ $cfg['title_en'] }}</p>

                            <!-- تفاصيل الإنجاز والمسؤول -->
                            @if($isCompleted)
                            <div class="text-[9px] text-emerald-800 space-y-0.5 bg-white/80 p-1.5 rounded-lg border border-emerald-100">
                                <div><strong>بواسطة:</strong> {{ $stageUser->name ?? 'مستخدم' }}</div>
                                <div><strong>التاريخ:</strong> {{ $completedAt ? $completedAt->format('Y-m-d H:i') : '---' }}</div>
                            </div>
                            @endif

                            <!-- تنبيه سبب التأخير -->
                            @if(!empty($delayReason))
                            <div class="mt-2 bg-rose-50 border border-rose-200 text-rose-800 p-1.5 rounded-lg text-[9px]">
                                <strong class="text-rose-900 block font-bold">⚠️ سبب التأخير:</strong>
                                <span>{{ $delayReason }}</span>
                            </div>
                            @endif

                            <!-- الملاحظات -->
                            @if(!empty($notes))
                            <div class="mt-1.5 text-slate-600 text-[9px] bg-slate-100/80 p-1.5 rounded-lg">
                                <strong>ملاحظة:</strong> {{ $notes }}
                            </div>
                            @endif
                        </div>

                        <!-- زر الإجراء السريع للمرحلة (يخفي أثناء الطباعة) -->
                        <div class="mt-3 pt-2 border-t border-slate-200/60 print:hidden">
                            @if($canEdit)
                            <form action="{{ route('transactions.update-stage', [$transaction->id, $s]) }}" method="POST">
                                @csrf
                                @if($isCompleted)
                                <input type="hidden" name="action" value="uncomplete_stage">
                                <button type="submit" class="w-full text-slate-500 hover:text-rose-600 text-[9px] font-bold py-1 bg-white hover:bg-rose-50 rounded border border-slate-200 transition">
                                    إلغاء الاعتماد
                                </button>
                                @else
                                <input type="hidden" name="action" value="complete_stage">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-[9px] font-bold py-1.5 rounded shadow-xs transition flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-check"></i> اعتماد واكتمال
                                </button>
                                @endif
                            </form>
                            @else
                            <span class="block text-center text-[9px] text-slate-400 font-medium">🔒 مقفلة</span>
                            @endif
                        </div>

                    </div>
                    @endfor
                </div>
            </div>

            <!-- شبكة تفاصيل العملية ومراحلها -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:grid-cols-2">

                <!-- 1. مرحلة 1: الاستلام والترقيم -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4 print:border-slate-300 print:shadow-none">
                    <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-clipboard-list text-blue-600 print:hidden"></i> 1. بيانات الاستلام والترقيم
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $transaction->isStageCompleted(1) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $transaction->isStageCompleted(1) ? 'مكتملة' : 'غير مكتملة' }}
                        </span>
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-1">التاجر / العميل:</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $transaction->trader->name ?? $transaction->trader_name ?? '---' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">عدد الطرود:</span>
                            <span class="font-bold text-slate-800">{{ $transaction->packages_count }} طرد</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">تاريخ الاستلام:</span>
                            <span class="font-bold text-slate-800">
                                {{ $transaction->receipt_date ? (is_object($transaction->receipt_date) ? $transaction->receipt_date->format('Y-m-d') : $transaction->receipt_date) : '---' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">رمز / حالة سابر (Saber):</span>
                            <span class="font-bold text-slate-800">{{ $transaction->sabir ?? $transaction->sabir_status ?? '---' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-slate-400 block mb-1">وصف المستندات المرفقة:</span>
                            <p class="text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-xs">{{ $transaction->documents ?? 'لا توجد مستندات مسجلة' }}</p>
                        </div>
                    </div>
                </div>

                <!-- 2. مرحلة 2: ربط البوليصة بالبيان والوكيل الملاحي -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4 print:border-slate-300 print:shadow-none">
                    <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-link text-indigo-600 print:hidden"></i> 2. ربط البوليصة والوكيل الملاحي
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $transaction->isStageCompleted(2) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $transaction->isStageCompleted(2) ? 'مكتملة' : 'غير مكتملة' }}
                        </span>
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-1">الوكيل الملاحي:</span>
                            <span class="font-bold text-indigo-600 text-sm">{{ $transaction->shipping_agent ?? '---' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">شركة الشحن / النقل:</span>
                            <span class="font-bold text-slate-800">{{ $transaction->company->name ?? $transaction->company_name ?? '---' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">رقم بوليصة الشحن (B/L):</span>
                            <span class="font-bold text-slate-800 font-mono">{{ $transaction->policy_number ?? '---' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">حالة استلام البوليصة:</span>
                            <span class="font-bold {{ $transaction->received_policy ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $transaction->received_policy ? 'تم الاستلام' : 'لم تستلم' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 3. مرحلة 3: التخليص والإجراءات الجمركية -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4 print:border-slate-300 print:shadow-none">
                    <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-building-columns text-amber-600 print:hidden"></i> 3. التخليص والبيان الجمركي
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $transaction->isStageCompleted(3) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $transaction->isStageCompleted(3) ? 'مكتملة' : 'غير مكتملة' }}
                        </span>
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-1">رقم البيان الجمركي:</span>
                            <span class="font-bold text-indigo-600 text-sm font-mono">{{ $transaction->declaration_number ?? '---' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">تاريخ البيان:</span>
                            <span class="font-bold text-slate-800">
                                {{ $transaction->declaration_date ? (is_object($transaction->declaration_date) ? $transaction->declaration_date->format('Y-m-d') : $transaction->declaration_date) : '---' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">حالة البيان الجمركي:</span>
                            <span class="font-bold text-amber-600">{{ $transaction->declaration_status ?? '---' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">موقع الحاوية / الميناء:</span>
                            <span class="font-bold text-slate-800">{{ $transaction->container_location ?? '---' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 4. مرحلة 4 و 5: خروج الحاويات وترجيع الفاضي -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4 print:border-slate-300 print:shadow-none">
                    <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-truck-fast text-purple-600 print:hidden"></i> 4 و 5. النقل وترجيع الفاضي
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $transaction->isStageCompleted(4) && $transaction->isStageCompleted(5) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $transaction->isStageCompleted(4) && $transaction->isStageCompleted(5) ? 'مكتملة' : 'قيد المتابعة' }}
                        </span>
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-1">اسم السائق:</span>
                            <span class="font-bold text-slate-800">{{ $transaction->driver_name ?? $transaction->driver ?? 'غير محدد' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">وسيلة التحميل / النقل:</span>
                            <span class="font-bold text-slate-800">{{ $transaction->loading ?? '---' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">تاريخ ترجيع الفاضي:</span>
                            <span class="font-bold text-slate-800">
                                {{ $transaction->empty_return_date ? (is_object($transaction->empty_return_date) ? $transaction->empty_return_date->format('Y-m-d') : $transaction->empty_return_date) : '---' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">حالة ترجيع الحاوية:</span>
                            <span class="font-bold {{ $transaction->isStageCompleted(5) ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $transaction->isStageCompleted(5) ? 'تم الترجيع بنجاح' : 'بانتظار الترجيع' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 5. مرحلة 6: الفواتير والمرفقات وسجل الحركات -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4 md:col-span-2 print:border-slate-300 print:shadow-none">
                    <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice-dollar text-emerald-600 print:hidden"></i> 6. الإنجاز والفاتورة والمبيعات
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $transaction->isStageCompleted(6) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $transaction->isStageCompleted(6) ? 'معاملة مغلقة ومكتملة' : 'غير مغلقة' }}
                        </span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-3">
                            <div>
                                <span class="text-slate-400 text-xs block mb-1">فاتورة العميل الإجمالية:</span>
                                <span class="text-xl font-black text-emerald-600">{{ number_format($transaction->client_invoice ?? 0, 2) }} ر.س</span>
                            </div>
                            <div class="print:hidden">
                                <span class="text-slate-400 text-xs block mb-1">الملف المرفق:</span>
                                @if($transaction->attachment)
                                <a href="{{ asset('storage/' . $transaction->attachment) }}" target="_blank" class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-xl font-bold text-xs hover:bg-blue-100 transition">
                                    <i class="fa-solid fa-file-pdf text-blue-600"></i> معاينة وتحميل المرفق
                                </a>
                                @else
                                <span class="text-xs text-slate-400 font-bold">لا يوجد مرفق لهذه العملية.</span>
                                @endif
                            </div>
                        </div>

                        <div class="md:col-span-2 border-t md:border-t-0 md:border-r border-slate-100 md:pr-6">
                            <span class="text-slate-500 text-xs block mb-2 font-bold flex items-center gap-1.5">
                                <i class="fa-solid fa-clock-rotate-left text-slate-400 print:hidden"></i> سجل حركة ومراحل هذا المستند:
                            </span>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @forelse($transaction->auditLogs ?? [] as $log)
                                <div class="text-[11px] bg-slate-50 p-2.5 rounded-xl flex justify-between items-center border border-slate-100">
                                    <div>
                                        <strong class="text-slate-800">{{ $log->user->name ?? 'مستخدم' }}:</strong>
                                        <span class="text-slate-600">{{ $log->description }}</span>
                                    </div>
                                    <span class="text-slate-400 text-[10px] shrink-0 mr-2 font-mono">{{ $log->created_at ? $log->created_at->format('Y-m-d H:i') : '' }}</span>
                                </div>
                                @empty
                                <p class="text-xs text-slate-400">لا توجد حركات سابقة مسجلة.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>