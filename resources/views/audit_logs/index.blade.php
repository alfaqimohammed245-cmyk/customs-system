<x-app-layout>
    <div class="container mx-auto p-6">
        <!-- الهيدر وعنوان الصفحة -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">سجل العمليات</h1>
                <p class="text-sm text-gray-500">تتبع أنشطة وتغييرات النظام</p>
            </div>
            
            <a href="{{ route('audit-logs.export-pdf', ['search' => request('search')]) }}" 
               class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow transition">
                📄 تصدير PDF
            </a>
        </div>

        <!-- شريط البحث -->
        <div class="mb-4">
            <form action="{{ route('audit-logs.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="بحث باسم المستخدم، نوع الإجراء، الكيان..."
                       class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">بحث</button>
            </form>
        </div>

        <!-- جدول السجل -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden border">
            <table class="w-full text-right border-collapse">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-3 text-sm font-semibold text-gray-600">التاريخ والوقت</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">المستخدم</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">نوع الإجراء</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">الكيان</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">التفاصيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 text-sm text-gray-600">{{ $log->created_at ? $log->created_at->format('Y-m-d H:i') : '-' }}</td>
                            <td class="p-3 text-sm font-medium text-gray-800">{{ $log->user->name ?? 'النظام' }}</td>
                            <td class="p-3 text-sm">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="p-3 text-sm text-gray-600">{{ $log->entity }}</td>
                            <td class="p-3 text-sm text-gray-600">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500">لا توجد سجلات متوفرة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- الترقيم (Pagination) -->
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>