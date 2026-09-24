<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight">إضافة معاملة جمركية جديدة</h1>
                <p class="text-xs text-slate-500 mt-1">بدء معاملة جديدة في المرحلة الأولى (الاستلام والترقيم) مع ربط تلقائي بالبيانات</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2 rounded-xl text-sm transition">
                <i class="fa-solid fa-arrow-right ml-1"></i> العودة للقائمة
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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

            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-8">
                @csrf

                <!-- شريط التبويبات Tabs Bar للمراحل الست -->
                <div class="flex border-b border-slate-200 overflow-x-auto gap-2 pb-1">
                    @for($s = 1; $s <= 6; $s++)
                    @php $cfg = $stagesConfig[$s]; @endphp
                    <button type="button" 
                            onclick="switchCreateTab({{ $s }}, this)" 
                            id="btn-create-tab-{{ $s }}" 
                            class="tab-btn px-4 py-3 text-xs font-bold whitespace-nowrap transition flex items-center gap-2 border-b-2 {{ $s === 1 ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] {{ $s === 1 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $s }}</span>
                        <span>{{ $cfg['name'] }}</span>
                    </button>
                    @endfor
                </div>

                <!-- ==================== Tab 1: الاستلام والترقيم ==================== -->
                <div id="content-create-1" class="stage-content space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">رقم المعاملة * (توليد تلقائي)</label>
                            <input type="text" name="transaction_number" value="{{ old('transaction_number', $autoNumber) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none font-mono">
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-xs font-bold text-slate-700">التاجر / العميل *</label>
                                <a href="{{ route('traders.create') }}" target="_blank" class="text-[11px] font-bold text-blue-600 hover:underline">+ تاجر جديد</a>
                            </div>
                            <select name="trader_id" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                                <option value="">اختر التاجر...</option>
                                @foreach($traders as $trader)
                                <option value="{{ $trader->id }}" {{ old('trader_id') == $trader->id ? 'selected' : '' }}>{{ $trader->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">عدد الطرود</label>
                            <input type="number" name="packages_count" value="{{ old('packages_count', 1) }}" min="1" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">تاريخ الاستلام</label>
                            <input type="date" name="receipt_date" value="{{ old('receipt_date', date('Y-m-d')) }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">سابر (Saber)</label>
                            <input type="text" name="sabir" value="{{ old('sabir') }}" placeholder="رمز أو حالة سابر" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">الموظف المسؤول</label>
                            <select name="employee_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                                <option value="">اختر الموظف...</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id', auth()->id()) == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">المستندات المرفقة (وصف)</label>
                            <textarea name="documents" rows="2" placeholder="الفاتورة الأصلية، شهادة المنشأ، بوليصة التأمين..." class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('documents') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">سبب التأخير للمرحلة 1 (إن وجد)</label>
                            <input type="text" name="stage_1_delay_reason" value="{{ old('stage_1_delay_reason') }}" placeholder="مثال: بانتظار استلام أصل المستندات من العميل" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">ملاحظات المرحلة 1 (الاستلام والترقيم)</label>
                        <textarea name="stage_1_notes" rows="2" placeholder="أي ملاحظات إضافية بخصوص استلام الطرود والمستندات..." class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('stage_1_notes') }}</textarea>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-slate-700">
                            <input type="checkbox" name="stage_1_completed" value="1" {{ old('stage_1_completed') ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>اعتماد اكتمال المرحلة الأولى فور الإنشاء والانتقال للمرحلة الثانية</span>
                        </label>
                    </div>
                </div>

                <!-- ==================== Tab 2: ربط البوليصة والوكيل الملاحي ==================== -->
                <div id="content-create-2" class="stage-content space-y-6" style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">الوكيل الملاحي (Shipping Agent)</label>
                            <input type="text" name="shipping_agent" value="{{ old('shipping_agent') }}" placeholder="اسم الوكيل الملاحي" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">شركة الشحن / الخط الملاحي</label>
                            <select name="company_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                                <option value="">اختر الشركة...</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">رقم بوليصة الشحن (B/L Number)</label>
                            <input type="text" name="policy_number" value="{{ old('policy_number') }}" placeholder="POL-XXXXXX" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">تاريخ استلام البوليصة</label>
                            <input type="date" name="policy_receipt_date" value="{{ old('policy_receipt_date') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">فاتورة إذن التسليم (ر.س)</label>
                            <input type="number" step="0.01" name="delivery_order_invoice" value="{{ old('delivery_order_invoice') }}" placeholder="0.00" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-slate-700">
                                <input type="checkbox" name="received_policy" value="1" {{ old('received_policy') ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span>تم استلام البوليصة من الوكيل</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">ملاحظات المرحلة 2</label>
                        <textarea name="stage_2_notes" rows="2" placeholder="ملاحظات ربط البوليصة والوكيل الملاحي..." class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('stage_2_notes') }}</textarea>
                    </div>
                </div>

                <!-- ==================== Tab 3: التخليص والبيان الجمركي ==================== -->
                <div id="content-create-3" class="stage-content space-y-6" style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">رقم البيان الجمركي</label>
                            <input type="text" name="declaration_number" value="{{ old('declaration_number') }}" placeholder="DEC-XXXXXX" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">تاريخ البيان</label>
                            <input type="date" name="declaration_date" value="{{ old('declaration_date') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">حالة البيان الجمركي</label>
                            <select name="declaration_status" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                                <option value="معلق" {{ old('declaration_status') == 'معلق' ? 'selected' : '' }}>معلق</option>
                                <option value="جاري الفحص والمعاينة" {{ old('declaration_status') == 'جاري الفحص والمعاينة' ? 'selected' : '' }}>جاري الفحص والمعاينة</option>
                                <option value="تحصيل الرسوم" {{ old('declaration_status') == 'تحصيل الرسوم' ? 'selected' : '' }}>تحصيل الرسوم</option>
                                <option value=" جاهز" {{ old('declaration_status') == 'جاهز' ? 'selected' : '' }}>جاهز</option>
                                <option value="فسح كلي" {{ old('declaration_status') == 'فسح كلي' ? 'selected' : '' }}>فسح كلي</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">تاريخ التفريغ</label>
                            <input type="date" name="unloading_date" value="{{ old('unloading_date') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">موقع الحاوية / الميناء</label>
                            <input type="text" name="container_location" value="{{ old('container_location') }}" placeholder="ميناء جدة - رصيف 4" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">فاتورة الموانئ (ر.س)</label>
                            <input type="number" step="0.01" name="ports_invoice" value="{{ old('ports_invoice') }}" placeholder="0.00" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">أرقام الحاويات</label>
                            <textarea name="container_numbers" rows="2" placeholder="TGHU1234567, MSCU9876543" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('container_numbers') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">ملاحظات التخليص والإجراءات</label>
                            <textarea name="stage_3_notes" rows="2" placeholder="تفاصيل الفحص والمعاينة والرسوم..." class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('stage_3_notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- ==================== Tab 4: خروج الحاويات والنقل ==================== -->
                <div id="content-create-4" class="stage-content space-y-6" style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">اسم السائق (driver_name):</label>
                            <input type="text" name="driver_name" value="{{ old('driver_name') }}" placeholder="أدخل اسم السائق" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">وسيلة التحميل / الشاحنة</label>
                            <input type="text" name="loading" value="{{ old('loading') }}" placeholder="شاحنة نقل / دينا / تريلا" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">فاتورة المشغل (ر.س)</label>
                            <input type="number" step="0.01" name="operator_invoice" value="{{ old('operator_invoice') }}" placeholder="0.00" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                    </div>
                </div>

                <!-- ==================== Tab 5: إعادة الحاويات الفارغة والمرفقات ==================== -->
                <div id="content-create-5" class="stage-content space-y-6" style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">تاريخ ترجيع الفاضي المتوقع / الفعلي</label>
                            <input type="date" name="empty_return_date" value="{{ old('empty_return_date') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">رفع مستند إرجاع الحاوية الفارغة (PDF، صور)</label>
                            <!-- تم ربطه باسم حقل آمن ومتاح مسبقاً لمنع خطأ قاعدة البيانات -->
                            <input type="file" name="attachment" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">ملاحظات ترجيع الحاويات الفارغة</label>
                        <textarea name="stage_5_notes" rows="2" placeholder="ملاحظات ساحة الإرجاع وإيصال الاستلام..." class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">{{ old('stage_5_notes') }}</textarea>
                    </div>
                </div>

                <!-- ==================== Tab 6: الفواتير والمرفقات النهائية ==================== -->
                <div id="content-create-6" class="stage-content space-y-6" style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">فاتورة العميل الإجمالية (ر.س)</label>
                            <input type="number" step="0.01" name="client_invoice" value="{{ old('client_invoice') }}" placeholder="0.00" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none font-bold text-emerald-600">
                        </div>
                    </div>
                </div>

                <!-- أزرار الإرسال والحفظ -->
                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-xs text-slate-500 font-medium">
                        <span>💡 تبدأ المعاملة افتراضياً في المرحلة 1، ويمكنك الانتقال وتحديث المراحل اللاحقة لاحقاً.</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('transactions.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-6 py-2.5 rounded-xl text-sm transition">إلغاء</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-plus"></i> إنشاء المعاملة والبدء
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        function switchCreateTab(stageNum, btnElement) {
            document.querySelectorAll('.stage-content').forEach(content => {
                content.style.display = 'none';
            });

            const targetContent = document.getElementById('content-create-' + stageNum);
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
