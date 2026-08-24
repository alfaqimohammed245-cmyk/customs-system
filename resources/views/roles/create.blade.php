<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight">إضافة دور وصلاحيات جديدة</h1>
            <a href="{{ route('roles.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2 rounded-xl text-sm transition">
                <i class="fa-solid fa-arrow-right ml-1"></i> العودة للأدوار
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('roles.store') }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-6">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">اسم الدور *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: موظف إدخال، محاسب..." class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-3">اختر الصلاحيات المتاحة لهذا الدور:</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($permissions as $permission)
                        <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-blue-50/50 hover:border-blue-200 transition">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-bold text-slate-800">{{ $permission->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('roles.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-6 py-2.5 rounded-xl text-sm transition">إلغاء</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-2.5 rounded-xl text-sm shadow-md transition">
                        <i class="fa-solid fa-plus ml-1"></i> حفظ الدور
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>