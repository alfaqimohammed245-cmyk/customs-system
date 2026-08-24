<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight">إدارة التجار والعملاء</h1>
                <p class="text-sm text-slate-500 mt-1">عرض ومتابعة بيانات التجار وتفاصيل الاتصال الخاصة بهم</p>
            </div>
            @can('إضافة')
            <a href="{{ route('traders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i> إضافة تاجر جديد
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-100">
                                <th class="p-3.5">اسم التاجر / المرشانت</th>
                                <th class="p-3.5">رقم الجوال / الهاتف</th>
                                <th class="p-3.5">تاريخ الإضافة</th>
                                <th class="p-3.5 text-center">التحكم</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($traders as $trader)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3.5 font-bold text-slate-800 text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-user text-blue-600"></i> {{ $trader->name }}
                                </td>
                                <td class="p-3.5 font-mono text-slate-600">{{ $trader->phone ?? 'غير مسجل' }}</td>
                                <td class="p-3.5 text-slate-400">{{ $trader->created_at ? $trader->created_at->format('Y-m-d') : '---' }}</td>
                                <td class="p-3.5 text-center space-x-2 space-x-reverse">
                                    @can('تعديل')
                                    <a href="{{ route('traders.edit', $trader->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold p-1" title="تعديل"><i class="fa-solid fa-pen-to-square"></i> تعديل</a>
                                    @endcan
                                    @can('حذف')
                                    <form action="{{ route('traders.destroy', $trader->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التاجر؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-900 font-bold p-1 mr-2" title="حذف"><i class="fa-solid fa-trash-can"></i> حذف</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400">لا يوجد تجار مسجلين حتى الآن.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $traders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
