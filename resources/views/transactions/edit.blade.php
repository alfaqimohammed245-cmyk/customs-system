<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight flex items-center gap-2">
                    <span>تعديل المعاملة الجمركية:</span>
                    <span class="text-blue-600 font-mono">{{ $transaction->transaction_number }}</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">تحديث بيانات ومراحل المعاملة مع تطبيق قيود الصلاحيات المحددة لكل مرحلة</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('transactions.show', $transaction->id) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2 rounded-xl text-sm transition">
                    <i class="fa-solid fa-eye ml-1"></i> معاينة التفاصيل
                </a>
                <a href="{{ route('transactions.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2 rounded-xl text-sm transition">
                    <i class="fa-solid fa-arrow-right ml-1"></i> العودة للقائمة
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- شريط نسبة الإنجاز والمرحلة الحالية -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold text-slate-400 block mb-1">المرحلة الجارية للمعاملة:</span>
                    <span class="text-sm font-extrabold text-slate-800">
                        {{ $transaction->current_stage }}. {{ $stagesConfig[$transaction->current_stage]['name'] ?? '' }}
                    </span>
                </div>
                <div class="w-full sm:w-64">
                    <div class="flex justify-between items-center text-xs font-bold mb-1">
                        <span class="text-slate-500">نسبة التقدم</span>
                        <span class="text-blue-600">{{ $transaction->progress_percentage }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $transaction->progress_percentage }}%"></div>
                    </div>
                </div>
            </div>

            @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs space-y-1">
                <p class="font-bold">يرجى تصحيح الأخطاء التالية:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-8">
                @csrf
                @method('PUT')

                <!-- شريط تبويبات سير العمليات الجمركية (6 Stages Tabs) -->
                <div class="flex border-b border-slate-200 overflow-x-auto gap-2 pb-1">
                    @for($s = 1; $s <= 6; $s++)
                    @php
                        $cfg = $stagesConfig[$s];
                        $isPermitted = $userStagePermissions[$s] ?? false;
                        $isCompleted = $transaction->isStageCompleted($s);
                    @endphp
                    <button type="button" 
                            onclick="switchStageTab({{ $s }}, this)" 
                            id="btn-stage-{{ $s }}" 
                            class="tab-btn px-4 py-3 text-xs font-bold whitespace-nowrap transition flex items-center gap-2 border-b-2 {{ $s === 1 ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] {{ $isCompleted ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                            @if($isCompleted) <i class="fa-solid fa-check text-[9px]"></i> @else {{ $s }} @endif
                        </span>
                        <span>{{ $cfg['name'] }}</span>
                        @if(!$isPermitted)
                        <i class="fa-solid fa-lock text-[10px] text-slate-400" title="مقفل"></i>
                        @endif
                    </button>
                    @endfor
                </div>

                <!-- ==================== Tab 1: الاستلام والترقيم ==================== -->
                <div id="content-stage-1" class="stage-content space-y-6">
                    @if(!($userStagePermissions[1] ?? false))
                    <div class="bg-slate-50 border border-slate-200 text-slate-600 p-3 rounded-xl text-xs flex items-center gap-2">
                        <i class="fa-solid fa-lock text-slate-400"></i>
                        <span>أنت في وضع العرض فقط لهذه المرحلة (تتطلب صلاحية: {{ $stagesConfig[1]['permission'] }}).</span>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">رقم المعاملة *</label>
                            <input type="text" name="transaction_number" value="{{ old('transaction_number', $transaction->transaction_number) }}" required {{ !($userStagePermissions[1] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">التاجر / العميل *</label>
                            <select name="trader_id" required {{ !($userStagePermissions[1] ?? false) ? 'disabled' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                                <option value="">اختر التاجر...</option>
                                @foreach($traders as $trader)
                                <option value="{{ $trader->id }}" {{ old('trader_id', $transaction->trader_id) == $trader->id ? 'selected' : '' }}>{{ $trader->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">عدد الطرود</label>
                            <input type="number" name="packages_count" value="{{ old('packages_count', $transaction->packages_count) }}" min="1" {{ !($userStagePermissions[1] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">تاريخ الاستلام</label>
                            <input type="date" name="receipt_date" value="{{ old('receipt_date', $transaction->receipt_date ? (is_object($transaction->receipt_date) ? $transaction->receipt_date->format('Y-m-d') : \Carbon\Carbon::parse($transaction->receipt_date)->format('Y-m-d')) : '') }}" {{ !($userStagePermissions[1] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">سابر (Saber)</label>
                            <input type="text" name="sabir" value="{{ old('sabir', $transaction->sabir) }}" placeholder="رمز أو حالة سابر" {{ !($userStagePermissions[1] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">الموظف المسؤول</label>
                            <select name="employee_id" {{ !($userStagePermissions[1] ?? false) ? 'disabled' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                                <option value="">اختر الموظف...</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id', $transaction->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">المستندات المرفقة (وصف)</label>
                            <textarea name="documents" rows="2" {{ !($userStagePermissions[1] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('documents', $transaction->documents) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">سبب التأخير للمرحلة 1 (إن وجد)</label>
                            <input type="text" name="stage_1_delay_reason" value="{{ old('stage_1_delay_reason', $transaction->stage_1_delay_reason) }}" placeholder="مثال: بانتظار استلام أصل الفاتورة" {{ !($userStagePermissions[1] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">ملاحظات المرحلة 1</label>
                        <textarea name="stage_1_notes" rows="2" {{ !($userStagePermissions[1] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('stage_1_notes', $transaction->stage_1_notes) }}</textarea>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-slate-700">
                            <input type="checkbox" name="stage_1_completed" value="1" {{ old('stage_1_completed', $transaction->stage_1_completed) ? 'checked' : '' }} {{ !($userStagePermissions[1] ?? false) ? 'disabled' : '' }} class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>اعتماد اكتمال المرحلة الأولى (الاستلام والترقيم)</span>
                        </label>
                        @if($transaction->stage_1_completed)
                        <span class="text-xs font-bold text-emerald-700">✓ مكتملة بواسطة {{ $transaction->stage1User->name ?? 'مستخدم' }} بتاريخ {{ $transaction->stage_1_completed_at ? $transaction->stage_1_completed_at->format('Y-m-d H:i') : '' }}</span>
                        @endif
                    </div>
                </div>

                <!-- ==================== Tab 2: ربط البوليصة بالبيان (الوكيل الملاحي) ==================== -->
                <div id="content-stage-2" class="stage-content space-y-6" style="display: none;">
                    @if(!($userStagePermissions[2] ?? false))
                    <div class="bg-slate-50 border border-slate-200 text-slate-600 p-3 rounded-xl text-xs flex items-center gap-2">
                        <i class="fa-solid fa-lock text-slate-400"></i>
                        <span>أنت في وضع العرض فقط لهذه المرحلة (تتطلب صلاحية: {{ $stagesConfig[2]['permission'] }}).</span>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">الوكيل الملاحي (Shipping Agent) *</label>
                            <input type="text" name="shipping_agent" value="{{ old('shipping_agent', $transaction->shipping_agent) }}" placeholder="اسم الوكيل الملاحي" {{ !($userStagePermissions[2] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">شركة الشحن / الخط الملاحي</label>
                            <select name="company_id" {{ !($userStagePermissions[2] ?? false) ? 'disabled' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                                <option value="">اختر الشركة...</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $transaction->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">رقم بوليصة الشحن (Policy Number)</label>
                            <input type="text" name="policy_number" value="{{ old('policy_number', $transaction->policy_number) }}" placeholder="POL-XXXXXX" {{ !($userStagePermissions[2] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">تاريخ استلام البوليصة</label>
                            <input type="date" name="policy_receipt_date" value="{{ old('policy_receipt_date', $transaction->policy_receipt_date ? (is_object($transaction->policy_receipt_date) ? $transaction->policy_receipt_date->format('Y-m-d') : \Carbon\Carbon::parse($transaction->policy_receipt_date)->format('Y-m-d')) : '') }}" {{ !($userStagePermissions[2] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">فاتورة إذن التسليم (ر.س)</label>
                            <input type="number" step="0.01" name="delivery_order_invoice" value="{{ old('delivery_order_invoice', $transaction->delivery_order_invoice) }}" {{ !($userStagePermissions[2] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-slate-700">
                                <input type="checkbox" name="received_policy" value="1" {{ old('received_policy', $transaction->received_policy) ? 'checked' : '' }} {{ !($userStagePermissions[2] ?? false) ? 'disabled' : '' }} class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span>تم استلام البوليصة من الوكيل</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">سبب التأخير للمرحلة 2 (إن وجد)</label>
                            <input type="text" name="stage_2_delay_reason" value="{{ old('stage_2_delay_reason', $transaction->stage_2_delay_reason) }}" placeholder="مثال: تأخر إصدار إذن التسليم من الوكيل الملاحي" {{ !($userStagePermissions[2] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">ملاحظات المرحلة 2</label>
                            <textarea name="stage_2_notes" rows="2" {{ !($userStagePermissions[2] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('stage_2_notes', $transaction->stage_2_notes) }}</textarea>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-slate-700">
                            <input type="checkbox" name="stage_2_completed" value="1" {{ old('stage_2_completed', $transaction->stage_2_completed) ? 'checked' : '' }} {{ !($userStagePermissions[2] ?? false) ? 'disabled' : '' }} class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span>اعتماد اكتمال المرحلة الثانية (ربط البوليصة بالبيان)</span>
                        </label>
                        @if($transaction->stage_2_completed)
                        <span class="text-xs font-bold text-emerald-700">✓ مكتملة بواسطة {{ $transaction->stage2User->name ?? 'مستخدم' }} بتاريخ {{ $transaction->stage_2_completed_at ? $transaction->stage_2_completed_at->format('Y-m-d H:i') : '' }}</span>
                        @endif
                    </div>
                </div>

                <!-- ==================== Tab 3: التخليص والإجراءات الجمركية ==================== -->
                <div id="content-stage-3" class="stage-content space-y-6" style="display: none;">
                    @if(!($userStagePermissions[3] ?? false))
                    <div class="bg-slate-50 border border-slate-200 text-slate-600 p-3 rounded-xl text-xs flex items-center gap-2">
                        <i class="fa-solid fa-lock text-slate-400"></i>
                        <span>أنت في وضع العرض فقط لهذه المرحلة (تتطلب صلاحية: {{ $stagesConfig[3]['permission'] }}).</span>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">رقم البيان الجمركي</label>
                            <input type="text" name="declaration_number" value="{{ old('declaration_number', $transaction->declaration_number) }}" placeholder="DEC-XXXXXX" {{ !($userStagePermissions[3] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">تاريخ البيان</label>
                            <input type="date" name="declaration_date" value="{{ old('declaration_date', $transaction->declaration_date ? (is_object($transaction->declaration_date) ? $transaction->declaration_date->format('Y-m-d') : \Carbon\Carbon::parse($transaction->declaration_date)->format('Y-m-d')) : '') }}" {{ !($userStagePermissions[3] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">حالة البيان الجمركي</label>
                            <select name="declaration_status" {{ !($userStagePermissions[3] ?? false) ? 'disabled' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                                <option value="معلق" {{ old('declaration_status', $transaction->declaration_status) == 'معلق' ? 'selected' : '' }}>معلق</option>
                                <option value="جاري الفحص" {{ old('declaration_status', $transaction->declaration_status) == 'جاري الفحص' ? 'selected' : '' }}>جاري الفحص والمعاينة</option>
                                <option value="جاري التفسيح" {{ old('declaration_status', $transaction->declaration_status) == 'جاري التفسيح' ? 'selected' : '' }}>جاري التفسيح</option>
                                <option value="تم التفسيح" {{ old('declaration_status', $transaction->declaration_status) == 'تم التفسيح' ? 'selected' : '' }}>تم التفسيح</option>
                                <option value="مكتمل" {{ old('declaration_status', $transaction->declaration_status) == 'مكتمل' ? 'selected' : '' }}>مكتمل</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">تاريخ التفريغ</label>
                            <input type="date" name="unloading_date" value="{{ old('unloading_date', $transaction->unloading_date ? (is_object($transaction->unloading_date) ? $transaction->unloading_date->format('Y-m-d') : \Carbon\Carbon::parse($transaction->unloading_date)->format('Y-m-d')) : '') }}" {{ !($userStagePermissions[3] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">موقع الحاوية / الميناء</label>
                            <input type="text" name="container_location" value="{{ old('container_location', $transaction->container_location) }}" placeholder="ميناء جدة - رصيف 4" {{ !($userStagePermissions[3] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">فاتورة الموانئ (ر.س)</label>
                            <input type="number" step="0.01" name="ports_invoice" value="{{ old('ports_invoice', $transaction->ports_invoice) }}" {{ !($userStagePermissions[3] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">أرقام الحاويات</label>
                            <textarea name="container_numbers" rows="2" placeholder="TGHU1234567, MSCU9876543" {{ !($userStagePermissions[3] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('container_numbers', $transaction->container_numbers) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">سبب التأخير للمرحلة 3 (إن وجد)</label>
                            <input type="text" name="stage_3_delay_reason" value="{{ old('stage_3_delay_reason', $transaction->stage_3_delay_reason) }}" placeholder="مثال: تأخر نتيجة الفحص المخبري لعينات الغذاء" {{ !($userStagePermissions[3] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">ملاحظات المرحلة 3</label>
                        <textarea name="stage_3_notes" rows="2" {{ !($userStagePermissions[3] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('stage_3_notes', $transaction->stage_3_notes) }}</textarea>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-slate-700">
                            <input type="checkbox" name="stage_3_completed" value="1" {{ old('stage_3_completed', $transaction->stage_3_completed) ? 'checked' : '' }} {{ !($userStagePermissions[3] ?? false) ? 'disabled' : '' }} class="w-5 h-5 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <span>اعتماد اكتمال المرحلة الثالثة (التخليص والإجراءات الجمركية)</span>
                        </label>
                        @if($transaction->stage_3_completed)
                        <span class="text-xs font-bold text-emerald-700">✓ مكتملة بواسطة {{ $transaction->stage3User->name ?? 'مستخدم' }} بتاريخ {{ $transaction->stage_3_completed_at ? $transaction->stage_3_completed_at->format('Y-m-d H:i') : '' }}</span>
                        @endif
                    </div>
                </div>

                <!-- ==================== Tab 4: خروج الحاويات والنقل (الفسح) ==================== -->
                <div id="content-stage-4" class="stage-content space-y-6" style="display: none;">
                    @if(!($userStagePermissions[4] ?? false))
                    <div class="bg-slate-50 border border-slate-200 text-slate-600 p-3 rounded-xl text-xs flex items-center gap-2">
                        <i class="fa-solid fa-lock text-slate-400"></i>
                        <span>أنت في وضع العرض فقط لهذه المرحلة (تتطلب صلاحية: {{ $stagesConfig[4]['permission'] }}).</span>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">اسم السائق (driver_name):</label>
                            <input type="text" name="driver_name" value="{{ old('driver_name', $transaction->driver_name ?? $transaction->driver) }}" placeholder="أدخل اسم السائق" {{ !($userStagePermissions[4] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">وسيلة التحميل / الشاحنة</label>
                            <input type="text" name="loading" value="{{ old('loading', $transaction->loading) }}" placeholder="شاحنة نقل / دينا / تريلا" {{ !($userStagePermissions[4] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">فاتورة المشغل (ر.س)</label>
                            <input type="number" step="0.01" name="operator_invoice" value="{{ old('operator_invoice', $transaction->operator_invoice) }}" {{ !($userStagePermissions[4] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">سبب التأخير للمرحلة 4 (إن وجد)</label>
                            <input type="text" name="stage_4_delay_reason" value="{{ old('stage_4_delay_reason', $transaction->stage_4_delay_reason) }}" placeholder="مثال: تأخر الشاحنة في الوصول للميناء" {{ !($userStagePermissions[4] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">ملاحظات المرحلة 4</label>
                            <textarea name="stage_4_notes" rows="2" {{ !($userStagePermissions[4] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('stage_4_notes', $transaction->stage_4_notes) }}</textarea>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-slate-700">
                            <input type="checkbox" name="stage_4_completed" value="1" {{ old('stage_4_completed', $transaction->stage_4_completed) ? 'checked' : '' }} {{ !($userStagePermissions[4] ?? false) ? 'disabled' : '' }} class="w-5 h-5 rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                            <span>اعتماد اكتمال المرحلة الرابعة (خروج الحاويات والنقل)</span>
                        </label>
                        @if($transaction->stage_4_completed)
                        <span class="text-xs font-bold text-emerald-700">✓ مكتملة بواسطة {{ $transaction->stage4User->name ?? 'مستخدم' }} بتاريخ {{ $transaction->stage_4_completed_at ? $transaction->stage_4_completed_at->format('Y-m-d H:i') : '' }}</span>
                        @endif
                    </div>
                </div>

                <!-- ==================== Tab 5: إعادة الحاويات الفارغة ==================== -->
                <div id="content-stage-5" class="stage-content space-y-6" style="display: none;">
                    @if(!($userStagePermissions[5] ?? false))
                    <div class="bg-slate-50 border border-slate-200 text-slate-600 p-3 rounded-xl text-xs flex items-center gap-2">
                        <i class="fa-solid fa-lock text-slate-400"></i>
                        <span>أنت في وضع العرض فقط لهذه المرحلة (تتطلب صلاحية: {{ $stagesConfig[5]['permission'] }}).</span>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">تاريخ ترجيع الفاضي</label>
                            <input type="date" name="empty_return_date" value="{{ old('empty_return_date', $transaction->empty_return_date ? (is_object($transaction->empty_return_date) ? $transaction->empty_return_date->format('Y-m-d') : \Carbon\Carbon::parse($transaction->empty_return_date)->format('Y-m-d')) : '') }}" {{ !($userStagePermissions[5] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">سبب التأخير للمرحلة 5 (إن وجد)</label>
                            <input type="text" name="stage_5_delay_reason" value="{{ old('stage_5_delay_reason', $transaction->stage_5_delay_reason) }}" placeholder="مثال: تأخر تفريغ البضاعة لدى مستودع العميل" {{ !($userStagePermissions[5] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">ملاحظات ترجيع الحاويات الفارغة</label>
                        <textarea name="stage_5_notes" rows="2" {{ !($userStagePermissions[5] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('stage_5_notes', $transaction->stage_5_notes) }}</textarea>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-slate-700">
                            <input type="checkbox" name="stage_5_completed" value="1" {{ old('stage_5_completed', $transaction->stage_5_completed) ? 'checked' : '' }} {{ !($userStagePermissions[5] ?? false) ? 'disabled' : '' }} class="w-5 h-5 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                            <span>اعتماد اكتمال المرحلة الخامسة (إعادة الحاويات الفارغة)</span>
                        </label>
                        @if($transaction->stage_5_completed)
                        <span class="text-xs font-bold text-emerald-700">✓ مكتملة بواسطة {{ $transaction->stage5User->name ?? 'مستخدم' }} بتاريخ {{ $transaction->stage_5_completed_at ? $transaction->stage_5_completed_at->format('Y-m-d H:i') : '' }}</span>
                        @endif
                    </div>
                </div>

                <!-- ==================== Tab 6: الإنجاز والفاتورة والمبيعات (الإغلاق النهائي) ==================== -->
                <div id="content-stage-6" class="stage-content space-y-6" style="display: none;">
                    @if(!($userStagePermissions[6] ?? false))
                    <div class="bg-slate-50 border border-slate-200 text-slate-600 p-3 rounded-xl text-xs flex items-center gap-2">
                        <i class="fa-solid fa-lock text-slate-400"></i>
                        <span>أنت في وضع العرض فقط لهذه المرحلة (تتطلب صلاحية: {{ $stagesConfig[6]['permission'] }}).</span>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">فاتورة العميل الإجمالية (ر.س) *</label>
                            <input type="number" step="0.01" name="client_invoice" value="{{ old('client_invoice', $transaction->client_invoice) }}" placeholder="0.00" {{ !($userStagePermissions[6] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none font-bold text-emerald-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">الحالة العامة للمعاملة</label>
                            <select name="status" {{ !($userStagePermissions[6] ?? false) ? 'disabled' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                                <option value="جديدة" {{ old('status', $transaction->status) == 'جديدة' ? 'selected' : '' }}>جديدة</option>
                                <option value="قيد التنفيذ" {{ old('status', $transaction->status) == 'قيد التنفيذ' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="بانتظار مستندات" {{ old('status', $transaction->status) == 'بانتظار مستندات' ? 'selected' : '' }}>بانتظار مستندات</option>
                                <option value="بانتظار العميل" {{ old('status', $transaction->status) == 'بانتظار العميل' ? 'selected' : '' }}>بانتظار العميل</option>
                                <option value="بانتظار السداد" {{ old('status', $transaction->status) == 'بانتظار السداد' ? 'selected' : '' }}>بانتظار السداد</option>
                                <option value="مكتملة" {{ old('status', $transaction->status) == 'مكتملة' ? 'selected' : '' }}>مكتملة</option>
                                <option value="مؤرشفة" {{ old('status', $transaction->status) == 'مؤرشفة' ? 'selected' : '' }}>مؤرشفة</option>
                                <option value="ملغاة" {{ old('status', $transaction->status) == 'ملغاة' ? 'selected' : '' }}>ملغاة</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">رفع أو استبدال المرفق</label>
                            <input type="file" name="attachment" {{ !($userStagePermissions[6] ?? false) ? 'disabled' : '' }} class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">سبب التأخير للمرحلة 6 (إن وجد)</label>
                            <input type="text" name="stage_6_delay_reason" value="{{ old('stage_6_delay_reason', $transaction->stage_6_delay_reason) }}" placeholder="مثال: بانتظار تحصيل الشيك أو السداد النهائي" {{ !($userStagePermissions[6] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">ملاحظات الإغلاق والمبيعات</label>
                            <textarea name="stage_6_notes" rows="2" {{ !($userStagePermissions[6] ?? false) ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('stage_6_notes', $transaction->stage_6_notes) }}</textarea>
                        </div>
                    </div>

                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-emerald-900">
                            <input type="checkbox" name="stage_6_completed" value="1" {{ old('stage_6_completed', $transaction->stage_6_completed) ? 'checked' : '' }} {{ !($userStagePermissions[6] ?? false) ? 'disabled' : '' }} class="w-5 h-5 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                            <span>اعتماد الإنجاز النهائي والإغلاق (ينقل المعاملة تلقائياً للأرشيف المكتمل)</span>
                        </label>
                        @if($transaction->stage_6_completed)
                        <span class="text-xs font-bold text-emerald-700">✓ مكتملة ومغلقة بتاريخ {{ $transaction->stage_6_completed_at ? $transaction->stage_6_completed_at->format('Y-m-d H:i') : '' }}</span>
                        @endif
                    </div>
                </div>

                <!-- أزرار الحفظ والإلغاء -->
                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-xs text-slate-500 font-medium">
                        <span>💡 يمكنك حفظ التعديلات في أي وقت وسيتم تسجيل الحركة تلقائياً في السجل.</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('transactions.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-6 py-2.5 rounded-xl text-sm transition">إلغاء</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> حفظ كافة التحديثات
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        function switchStageTab(stageNum, btnElement) {
            document.querySelectorAll('.stage-content').forEach(content => {
                content.style.display = 'none';
            });

            const targetContent = document.getElementById('content-stage-' + stageNum);
            if (targetContent) {
                targetContent.style.display = 'block';
            }

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className = "tab-btn px-4 py-3 text-xs font-bold whitespace-nowrap transition flex items-center gap-2 border-b-2 border-transparent text-slate-500 hover:text-slate-800";
            });

            btnElement.className = "tab-btn px-4 py-3 text-xs font-bold whitespace-nowrap transition flex items-center gap-2 border-b-2 border-blue-600 text-blue-600 font-bold";
        }
    </script>
</x-app-layout>