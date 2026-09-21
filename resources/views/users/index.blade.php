<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight">إدارة المستخدمين والموظفين</h1>
                <p class="text-sm text-slate-500 mt-1">التحكم في بيانات الموظفين وتعيين الصلاحيات والأدوار</p>
            </div>
            @can('إدارة المستخدمين')
            <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i> إضافة مستخدم جديد
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
                                <th class="p-3.5">المستخدم</th>
                                <th class="p-3.5">اسم المستخدم</th>
                                <th class="p-3.5">البريد الإلكتروني</th>
                                <th class="p-3.5">القسم والوظيفة</th>
                                <th class="p-3.5">الدور / الصلاحية</th>
                                <th class="p-3.5">الحالة</th>
                                <th class="p-3.5 text-center">التحكم</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $user)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3.5 font-bold text-slate-800 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full text-white font-bold flex items-center justify-center text-xs shrink-0 shadow" style="background-color: {{ $user->color ?? '#2563eb' }}">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                    <span>{{ $user->name }}</span>
                                </td>
                                <td class="p-3.5 font-mono text-slate-600">{{ $user->username }}</td>
                                <td class="p-3.5 text-slate-500">{{ $user->email ?? '---' }}</td>
                                <td class="p-3.5 text-slate-500">{{ $user->department ?? '---' }} - {{ $user->job_title ?? '---' }}</td>
                                <td class="p-3.5">
                                    @forelse($user->roles as $role)
                                    <span class="bg-purple-100 text-purple-800 text-[10px] font-bold px-2.5 py-0.5 rounded-full">{{ $role->name }}</span>
                                    @empty
                                    <span class="text-slate-400 text-[10px]">بدون دور</span>
                                    @endforelse
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $user->is_active ? 'نشط' : 'معطل' }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center space-x-2 space-x-reverse">
                                    @can('إدارة المستخدمين')
                                    <a href="{{ route('users.edit', $user->id) }}" class="text-indigo-600 hover:bg-indigo-100 font-bold px-2.5 py-1 bg-indigo-50 rounded-lg text-xs inline-flex items-center gap-1 transition" title="تعديل المستخدم">
                                        <i class="fa-solid fa-user-pen"></i> تعديل
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:bg-rose-100 font-bold px-2.5 py-1 bg-rose-50 rounded-lg text-xs inline-flex items-center gap-1 transition" title="حذف">
                                            <i class="fa-solid fa-trash-can"></i> حذف
                                        </button>
                                    </form>
                                    @endif
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-slate-400">لا يوجد مستخدمين مسجلين حالياً.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- أزرار التنقل بين الصفحات (مع ضبط الاتجاه والتنسيق الشامل) -->
                <div class="p-4 border-t border-slate-100 bg-slate-50/50" dir="ltr">
                    <div dir="rtl">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>