<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight">إدارة الأدوار والصلاحيات</h1>
                <p class="text-sm text-slate-500 mt-1">تحديد الأدوار الرئيسية وتخصيص الصلاحيات المطلقة للمسؤولين</p>
            </div>
            <a href="{{ route('roles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> إضافة دور جديد
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- تنبيه نظامي -->
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-2xl text-sm flex items-center gap-3">
                <i class="fa-solid fa-shield-halved text-lg text-blue-600"></i>
                <div>
                    <span class="font-bold">دور المسؤول (Admin):</span>
                    هذا الدور هو المسؤول الرئيسي في النظام ويحمل كافة الصلاحيات المطلقة لضمان إدارة العمليات الجمركية دون قيود.
                </div>
            </div>

            <!-- جدول الأدوار والصلاحيات -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-100">
                                <th class="p-3.5">اسم الدور</th>
                                <th class="p-3.5">الصلاحيات الممنوحة</th>
                                <th class="p-3.5 text-center">التحكم</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($roles as $role)
                            <tr class="hover:bg-slate-50/80 transition {{ strtolower($role->name) === 'admin' ? 'bg-slate-50/50' : '' }}">
                                <td class="p-3.5 font-bold text-slate-800 text-sm flex items-center gap-2">
                                    @if(strtolower($role->name) === 'admin')
                                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                    @endif
                                    {{ $role->name }}
                                </td>
                                <td class="p-3.5">
                                    <div class="flex flex-wrap gap-1 max-h-24 overflow-y-auto p-1">
                                        @if(strtolower($role->name) === 'admin')
                                        <span class="bg-blue-600 text-white font-bold text-[10px] px-2.5 py-1 rounded-lg shadow-sm">صلاحيات كاملة ومطلقة (Admin Access)</span>
                                        @else
                                        @forelse($role->permissions as $perm)
                                        <span class="bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-bold px-2 py-0.5 rounded-lg">{{ $perm->name }}</span>
                                        @empty
                                        <span class="text-slate-400 text-[10px]">لا توجد صلاحيات مخصصة</span>
                                        @endforelse
                                        @endif
                                    </div>
                                </td>
                                <td class="p-3.5 text-center space-x-2 space-x-reverse">
                                    <a href="{{ route('roles.edit', $role->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold p-1 inline-flex items-center gap-1" title="تعديل الصلاحيات">
                                        <i class="fa-solid fa-sliders"></i> تعديل
                                    </a>

                                    @if(strtolower($role->name) !== 'admin')
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-900 font-bold p-1 mr-2 inline-flex items-center gap-1" title="حذف">
                                            <i class="fa-solid fa-trash-can"></i> حذف
                                        </button>
                                    </form>
                                    @else
                                    <span class="bg-slate-200 text-slate-600 text-[10px] font-bold px-3 py-1 rounded-full mr-2">محمي نظامياً</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-6 text-center text-slate-400">لا توجد أدوار مضافة حتى الآن.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>