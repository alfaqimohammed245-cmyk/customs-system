<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight">لوحة التحكم الإحصائية وتتبع المراحل</h1>
                <p class="text-sm text-slate-500 mt-1">ملخص فوري ومباشر لمعاملات التخليص الجمركي وتدفق سير العمليات الجمركية</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('transactions.workflow') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-bolt"></i> لوحة سير العمل
                </a>
                <a href="{{ route('transactions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> إضافة معاملة جديدة
                </a>
                <a href="{{ route('transactions.export') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-file-excel"></i> تصدير Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- 1. شريط سير العمليات الجمركية التفاعلي (6-Stage Pipeline Tracker Widget) -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                            <span class="p-1.5 bg-blue-600 text-white rounded-lg text-xs">⚡</span>
                            <span>تدفق المعاملات الجارية عبر سير العمليات الجمركية (Customs Workflow Pipeline)</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">توزيع المعاملات النشطة لحظياً عبر مراحل التخليص والفسح</p>
                    </div>
                    <a href="{{ route('transactions.workflow') }}" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                        فتح اللوحة التفاعلية &larr;
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    @for($s = 1; $s <= 6; $s++)
                    @php
                        $cfg = $stagesConfig[$s];
                        $cnt = $stageCounts[$s] ?? 0;
                    @endphp
                    <a href="{{ route('transactions.index', ['stage' => $s]) }}" 
                       class="group p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-white hover:border-blue-400 hover:shadow-md transition flex flex-col justify-between space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs group-hover:bg-blue-600 transition">
                                {{ $s }}
                            </span>
                            <span class="text-base font-black {{ $cnt > 0 ? 'text-blue-600' : 'text-slate-400' }}">
                                {{ $cnt }}
                            </span>
                        </div>
                        <div>
                            <h4 class="text-xs font-extrabold text-slate-800 leading-tight group-hover:text-blue-600 transition">
                                {{ $cfg['name'] }}
                            </h4>
                            <p class="text-[10px] text-slate-400 mt-0.5 truncate">{{ $cfg['title_en'] }}</p>
                        </div>
                    </a>
                    @endfor
                </div>
            </div>

            <!-- 2. شبكة البطاقات الإحصائية الرئيسية -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                <!-- إجمالي العمليات -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400">إجمالي المعاملات</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                    </div>
                    <span class="text-3xl font-black text-slate-800">{{ number_format($totalTransactions) }}</span>
                </div>

                <!-- المعاملات النشطة الجارية -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400">النشطة والجارية</span>
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </div>
                    </div>
                    <span class="text-3xl font-black text-indigo-600">{{ number_format($activeTransactions) }}</span>
                </div>

                <!-- مكتملة -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400">المكتملة والأرشيف</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <span class="text-3xl font-black text-emerald-600">{{ number_format($completedTransactions) }}</span>
                </div>

                <!-- المتأخرة / تنبيهات التأخير -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400">تنبيهات التأخير</span>
                        <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                    </div>
                    <span class="text-3xl font-black text-rose-600">{{ number_format($delayedTransactions) }}</span>
                </div>

                <!-- التجار والعملاء -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400">التجار والعملاء</span>
                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                    <span class="text-3xl font-black text-slate-800">{{ number_format($totalTraders) }}</span>
                </div>
            </div>

            <!-- 3. قسم الرسوم البيانية التفاعلية -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- الرسم البياني للأداء الشهري -->
                <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-blue-600"></i> نمو المعاملات الجمركية شهرياً
                    </h3>
                    <div class="h-64 relative">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <!-- الرسم البياني لتوزيع الحالات -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-indigo-600"></i> توزيع الحالات الإجمالية
                    </h3>
                    <div class="h-64 relative flex items-center justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 4. الجداول والأقسام الأخيرة -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- أحدث المعاملات النشطة الجارية مع موضع المرحلة -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-blue-600"></i> أحدث المعاملات الجارية ومراحلها
                        </h3>
                        <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-blue-600 hover:underline">عرض الكل &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                                    <th class="p-3">رقم المعاملة</th>
                                    <th class="p-3">التاجر</th>
                                    <th class="p-3">المرحلة الحالية</th>
                                    <th class="p-3">الإنجاز</th>
                                    <th class="p-3">السبب / الملاحظة</th>
                                    <th class="p-3 text-center">التحكم</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($activeLatestTransactions as $tx)
                                @php
                                    $stg = $tx->current_stage ?? 1;
                                    $delay = $tx->{"stage_{$stg}_delay_reason"};
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition {{ !empty($delay) ? 'bg-rose-50/20' : '' }}">
                                    <td class="p-3 font-bold text-blue-600 font-mono">{{ $tx->transaction_number }}</td>
                                    <td class="p-3 font-medium">{{ $tx->trader->name ?? $tx->trader_name ?? '---' }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-blue-50 text-blue-700 border-blue-200">
                                            {{ $stg }}. {{ $stagesConfig[$stg]['name'] ?? '' }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <div class="w-16">
                                            <div class="text-[10px] font-bold text-slate-500 mb-0.5">{{ $tx->progress_percentage }}%</div>
                                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $tx->progress_percentage }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        @if(!empty($delay))
                                        <span class="text-rose-700 text-[10px] font-bold flex items-center gap-1" title="{{ $delay }}">
                                            <i class="fa-solid fa-triangle-exclamation"></i> {{ \Illuminate\Support\Str::limit($delay, 15) }}
                                        </span>
                                        @else
                                        <span class="text-slate-400 text-[10px]">---</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center space-x-1 space-x-reverse">
                                        <a href="{{ route('transactions.show', $tx->id) }}" class="text-slate-600 hover:text-blue-600 font-bold p-1"><i class="fa-solid fa-eye"></i></a>
                                        <a href="{{ route('transactions.edit', $tx->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold p-1"><i class="fa-solid fa-pen-to-square"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-slate-400">لا توجد معاملات جارية مسجلة حالياً.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- أحدث التعديلات والنشاطات (Audit Logs Widget) -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                    <div class="p-5 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-indigo-600"></i> أحدث سجل التعديلات والنشاطات
                        </h3>
                    </div>
                    <div class="p-4 flex-grow space-y-4">
                        @forelse($latestAuditLogs as $log)
                        <div class="flex items-start gap-3 text-xs border-b border-slate-50 pb-3 last:border-none">
                            <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">
                                <i class="fa-solid {{ $log->action == 'إضافة' ? 'fa-plus text-emerald-600' : ($log->action == 'تعديل' ? 'fa-pen text-amber-600' : 'fa-trash text-rose-600') }}"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-slate-800">{{ $log->user->name ?? 'مستخدم' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</span>
                                </div>
                                <p class="text-slate-500 mt-1 leading-tight">{{ $log->description }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-slate-400 text-xs">لا توجد نشاطات مسجلة في السجل حالياً.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- كود تشغيل الرسوم البيانية Chart.js -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // 1. Line Chart للأداء الشهري
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyLabels = {!! json_encode($monthlyChartData->pluck('month')) !!};
            const monthlyData = {!! json_encode($monthlyChartData->pluck('total')) !!};

            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyLabels.length ? monthlyLabels : ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                    datasets: [{
                        label: 'عدد العمليات',
                        data: monthlyData.length ? monthlyData : [5, 12, 18, 24, 30, 42],
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 3,
                        pointBackgroundColor: '#2563eb'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. Doughnut Chart لتوزيع الحالات
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const statusData = {!! json_encode(array_values($statusCounts)) !!};
            const statusLabels = {!! json_encode(array_keys($statusCounts)) !!};

            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: [
                            '#38bdf8', '#f59e0b', '#f43f5e', '#ec4899', '#8b5cf6', '#10b981', '#64748b', '#ef4444'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { family: 'Cairo', size: 10 } } }
                    }
                }
            });
        });
    </script>
</x-app-layout>