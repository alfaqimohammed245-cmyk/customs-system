<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-blue-600 text-white rounded-xl text-lg shadow-sm">⚡</span>
                    لوحة متابعة سير العمليات الجمركية
                </h1>
                <p class="text-sm text-slate-500 mt-1">متابعة لحظية ودقيقة لموقع كل معاملة جمركية عبر سير العمليات الجمركية مع رصد أسباب التأخير والملاحظات</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('transactions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> معاملة جديدة
                </a>
                <a href="{{ route('transactions.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2.5 rounded-xl text-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-table-list"></i> عرض الجدول
                </a>
                <a href="{{ route('transactions.completed') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> المكتملة ({{ $totalCompleted }})
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- شريط الفلترة والبحث -->
            <form method="GET" action="{{ route('transactions.workflow') }}" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex-1 w-full flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative w-full sm:w-80">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث برقم المعاملة، البوليصة، البيان، التاجر..." class="w-full rounded-xl border border-slate-300 pl-3 pr-9 py-2 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        <i class="fa-solid fa-magnifying-glass absolute right-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>

                    <select name="trader_id" onchange="this.form.submit()" class="w-full sm:w-60 rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        <option value="">جميع التجار</option>
                        @foreach($traders as $trader)
                        <option value="{{ $trader->id }}" {{ request('trader_id') == $trader->id ? 'selected' : '' }}>{{ $trader->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-xl text-xs shadow transition">
                        تصفية
                    </button>
                    @if(request()->hasAny(['search', 'trader_id']))
                    <a href="{{ route('transactions.workflow') }}" class="text-xs font-bold text-rose-600 hover:underline">إلغاء الفلتر</a>
                    @endif
                </div>

                <div class="flex items-center gap-4 text-xs font-bold text-slate-600">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> إجمالي الجارية: <strong class="text-slate-900">{{ $totalActive }}</strong></span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> المكتملة: <strong class="text-slate-900">{{ $totalCompleted }}</strong></span>
                </div>
            </form>

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
            </div>
            @endif

            <!-- أعمدة سير العمليات الجمركية (Kanban / Step Board) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 items-start">
                @for($stage = 1; $stage <= 6; $stage++)
                @php
                    $cfg = $stagesConfig[$stage];
                    $items = $stagesData[$stage] ?? collect();
                    $canEditStage = auth()->user()->hasAnyRole(['admin', 'Admin', 'super-admin', 'Super Admin', 'مسؤول النظام']) || auth()->user()->can('تعديل كافة المراحل') || auth()->user()->can($cfg['permission']);
                @endphp
                <div class="bg-slate-50/90 rounded-2xl border border-slate-200/80 p-3.5 flex flex-col min-h-[550px] shadow-sm">
                    <!-- رأس المرحلة -->
                    <div class="border-b border-slate-200 pb-3 mb-3">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs">{{ $stage }}</span>
                            <span class="text-[11px] font-black px-2.5 py-0.5 rounded-full bg-white border border-slate-200 text-slate-700 shadow-xs">
                                {{ $items->count() }} معاملة
                            </span>
                        </div>
                        <h3 class="font-extrabold text-slate-800 text-xs leading-tight flex items-center gap-1.5">
                            <i class="fa-solid {{ $cfg['icon'] }} text-blue-600 text-[11px]"></i>
                            <span>{{ $cfg['name'] }}</span>
                        </h3>
                        <p class="text-[10px] text-slate-400 font-medium mt-1 truncate" title="{{ $cfg['subtitle'] }}">{{ $cfg['subtitle'] }}</p>

                        <!-- شارة الصلاحية -->
                        <div class="mt-2">
                            @if($canEditStage)
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                <i class="fa-solid fa-lock-open text-[8px]"></i> مصرح لك بالتحديث
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200" title="مطلوب صلاحية: {{ $cfg['permission'] }}">
                                <i class="fa-solid fa-lock text-[8px]"></i> للعرض فقط
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- بطاقات المعاملات داخل المرحلة -->
                    <div class="space-y-3 flex-grow overflow-y-auto max-h-[75vh] pr-0.5">
                        @forelse($items as $tx)
                        <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-xs hover:shadow-md hover:border-blue-300 transition space-y-2.5 group">
                            <!-- رقم المعاملة والنسبة -->
                            <div class="flex items-center justify-between">
                                <a href="{{ route('transactions.show', $tx->id) }}" class="font-black text-xs text-blue-600 hover:underline">
                                    {{ $tx->transaction_number }}
                                </a>
                                <span class="text-[10px] font-bold text-slate-500 font-mono">
                                    {{ $tx->progress_percentage }}%
                                </span>
                            </div>

                            <!-- اسم التاجر -->
                            <div class="text-[11px] font-bold text-slate-800 truncate" title="{{ $tx->trader->name ?? $tx->trader_name }}">
                                👤 {{ $tx->trader->name ?? $tx->trader_name ?? 'تاجر غير محدد' }}
                            </div>

                            <!-- تفاصيل الربط والمستندات الخاصة بالمرحلة -->
                            <div class="text-[10px] text-slate-600 space-y-1 bg-slate-50 p-2 rounded-lg border border-slate-100">
                                @if($tx->policy_number)
                                <div class="truncate"><strong class="text-slate-400">بوليصة:</strong> <span class="font-mono">{{ $tx->policy_number }}</span></div>
                                @endif
                                @if($tx->shipping_agent)
                                <div class="truncate"><strong class="text-slate-400">الوكيل:</strong> {{ $tx->shipping_agent }}</div>
                                @endif
                                @if($tx->declaration_number)
                                <div class="truncate"><strong class="text-slate-400">البيان:</strong> <span class="font-mono text-indigo-600">{{ $tx->declaration_number }}</span></div>
                                @endif
                                @if($tx->driver_name ?? $tx->driver)
                                <div class="truncate"><strong class="text-slate-400">السائق:</strong> {{ $tx->driver_name ?? $tx->driver }}</div>
                                @endif
                                @if($tx->created_at)
                                <div class="text-slate-400 text-[9px]">🕒 منذ {{ $tx->created_at->diffForHumans() }}</div>
                                @endif
                            </div>

                            <!-- تنبيه سبب التأخير إن وجد -->
                            @php
                                $currentDelay = $tx->{"stage_{$stage}_delay_reason"};
                                $currentNotes = $tx->{"stage_{$stage}_notes"};
                            @endphp

                            @if(!empty($currentDelay))
                            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-2 rounded-lg text-[10px] font-medium flex items-start gap-1.5">
                                <i class="fa-solid fa-triangle-exclamation text-rose-600 shrink-0 mt-0.5"></i>
                                <div class="overflow-hidden">
                                    <strong class="block text-rose-900 font-bold">سبب التأخير:</strong>
                                    <p class="truncate">{{ $currentDelay }}</p>
                                </div>
                            </div>
                            @endif

                            @if(!empty($currentNotes))
                            <div class="bg-amber-50 border border-amber-200 text-amber-800 p-2 rounded-lg text-[10px] font-medium truncate" title="{{ $currentNotes }}">
                                📝 <strong>ملاحظة:</strong> {{ $currentNotes }}
                            </div>
                            @endif

                            <!-- أزرار الإجراءات السريعة -->
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-1">
                                <a href="{{ route('transactions.show', $tx->id) }}" class="text-[10px] text-slate-500 hover:text-blue-600 font-bold p-1" title="معاينة">
                                    <i class="fa-solid fa-eye"></i> التفاصيل
                                </a>

                                @if($canEditStage)
                                <button type="button" 
                                        onclick="openStageModal('{{ $tx->id }}', '{{ $tx->transaction_number }}', '{{ $stage }}', '{{ addslashes($cfg['name']) }}', '{{ addslashes($currentNotes ?? '') }}', '{{ addslashes($currentDelay ?? '') }}')"
                                        class="bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 font-bold px-2.5 py-1 rounded-lg text-[10px] transition flex items-center gap-1">
                                    <i class="fa-solid fa-bolt text-[9px]"></i> تحديث المرحلة
                                </button>
                                @else
                                <span class="text-[9px] text-slate-400 font-medium">🔒 مقفل</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-12 text-slate-400 text-xs">
                            <i class="fa-solid fa-inbox text-2xl mb-2 text-slate-300 block"></i>
                            لا توجد معاملات في هذه المرحلة حالياً
                        </div>
                        @endforelse
                    </div>
                </div>
                @endfor
            </div>

        </div>
    </div>

    <!-- نافذة منبثقة تفاعلية لتحديث المرحلة والملاحظات وسبب التأخير (Modal) -->
    <div id="stageModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-5 animate-in fade-in zoom-in duration-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base" id="modalTitle">تحديث المرحلة</h3>
                    <p class="text-xs text-blue-600 font-bold font-mono mt-0.5" id="modalTransNumber">TRX-XXXX</p>
                </div>
                <button type="button" onclick="closeStageModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-sm transition">
                    &times;
                </button>
            </div>

            <form id="stageForm" method="POST" action="" class="space-y-4">
                @csrf
                <input type="hidden" name="action" id="modalAction" value="save_notes">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">سبب التأخير (Delay Reason):</label>
                    <input type="text" name="delay_reason" id="modalDelayReason" placeholder="مثال: بانتظار استلام إذن التسليم، تأخر الفحص المخبري..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <button type="button" onclick="setQuickDelay('بانتظار مستندات العميل')" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md font-medium transition">بانتظار العميل</button>
                        <button type="button" onclick="setQuickDelay('تأخر الفحص والمعاينة بالميناء')" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md font-medium transition">تأخر الفحص</button>
                        <button type="button" onclick="setQuickDelay('بانتظار سداد الفواتير')" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md font-medium transition">بانتظار السداد</button>
                        <button type="button" onclick="setQuickDelay('تأخر وصول الشاحنة/السائق')" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md font-medium transition">تأخر الشاحنة</button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">ملاحظات المرحلة (Stage Notes):</label>
                    <textarea name="notes" id="modalNotes" rows="3" placeholder="أدخل أي تفاصيل أو ملاحظات تخص هذه المرحلة..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <button type="submit" onclick="document.getElementById('modalAction').value='save_notes'" class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">
                        <i class="fa-solid fa-floppy-disk ml-1"></i> حفظ الملاحظات فقط
                    </button>
                    <button type="submit" onclick="document.getElementById('modalAction').value='complete_stage'" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-md transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-check"></i> اعتماد وإكمال المرحلة للانتقال للتالية &larr;
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStageModal(transId, transNumber, stageNum, stageName, notes, delayReason) {
            const form = document.getElementById('stageForm');
            form.action = `/transactions/${transId}/stage/${stageNum}`;
            document.getElementById('modalTitle').textContent = `تحديث: ${stageName}`;
            document.getElementById('modalTransNumber').textContent = transNumber;
            document.getElementById('modalNotes').value = notes || '';
            document.getElementById('modalDelayReason').value = delayReason || '';
            document.getElementById('stageModal').style.display = 'flex';
        }

        function closeStageModal() {
            document.getElementById('stageModal').style.display = 'none';
        }

        function setQuickDelay(text) {
            document.getElementById('modalDelayReason').value = text;
        }

        // إغلاق النافذة عند الضغط خارجها
        window.onclick = function(event) {
            const modal = document.getElementById('stageModal');
            if (event.target === modal) {
                closeStageModal();
            }
        }
    </script>
</x-app-layout>
