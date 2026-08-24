<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight">تعديل اسم الحالة: <span class="text-blue-600">{{ $status->name }}</span></h1>
            <a href="{{ route('statuses.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2 rounded-xl text-sm transition">
                <i class="fa-solid fa-arrow-right ml-1"></i> العودة للحالات
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('statuses.update', $status->id) }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">اسم الحالة *</label>
                    <input type="text" name="name" value="{{ old('name', $status->name) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('statuses.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-6 py-2.5 rounded-xl text-sm transition">إلغاء</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-2.5 rounded-xl text-sm shadow-md transition">
                        <i class="fa-solid fa-floppy-disk ml-1"></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>