<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight">إضافة موظف / مستخدم جديد</h1>
            <a href="{{ route('users.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2 rounded-xl text-sm transition">
                <i class="fa-solid fa-arrow-right ml-1"></i> العودة لقائمة المستخدمين
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('users.store') }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">الاسم الكامل *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">اسم المستخدم (الدخول) *</label>
                        <input type="text" name="username" value="{{ old('username') }}" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">البريد الإلكتروني</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">رقم الجوال</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="05XXXXXXXX" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">القسم</label>
                        <input type="text" name="department" value="{{ old('department') }}" placeholder="التخليص الجمركي" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">الوظيفة</label>
                        <input type="text" name="job_title" value="{{ old('job_title') }}" placeholder="مخلص جمركي" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">الدور (الصلاحية)</label>
                        <select name="role" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                            <option value="">اختر الدور...</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">كلمة المرور *</label>
                        <input type="password" name="password" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                    </div>

                    <div class="flex items-center gap-6 pt-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">اللون المميز</label>
                            <input type="color" name="color" value="{{ old('color', '#2563eb') }}" class="h-10 w-16 rounded-xl border border-slate-300 cursor-pointer p-1">
                        </div>

                        <div class="flex items-center mt-4">
                            <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-slate-700">
                                <input type="checkbox" name="is_active" value="1" checked class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span>حساب نشط</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6">
                    <h3 class="font-bold text-slate-800 text-xs mb-3 flex items-center gap-1.5">
                        🔑 <span>الصلاحيات المباشرة للمستخدم</span>
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200/60">
                        @foreach($permissions as $perm)
                        <label class="flex items-center gap-2.5 cursor-pointer p-2 hover:bg-white rounded-lg transition border border-transparent hover:border-slate-200">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" 
                                   {{ in_array($perm->name, old('permissions', [])) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-semibold text-slate-700">{{ $perm->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-6 py-2.5 rounded-xl text-sm transition">إلغاء</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-2.5 rounded-xl text-sm shadow-md transition">
                        <i class="fa-solid fa-user-plus ml-1"></i> حفظ المستخدم
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>