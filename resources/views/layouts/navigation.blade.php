<nav class="bg-slate-900 border-l border-slate-800 text-white shadow-xl w-64 min-h-screen p-4 flex flex-col justify-between" dir="rtl">
    <div>
        <!-- الشعار / اسم النظام -->
        <div class="flex items-center gap-3 border-b border-slate-700 pb-5 mb-6">
            <div class="bg-blue-600 p-2.5 rounded-xl shadow-lg shadow-blue-500/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div>
                <span class="text-lg font-black tracking-wide text-white block">نظام التخليص الجمركي</span>
                <span class="text-xs text-blue-400 font-medium">لوحة إدارة العمليات</span>
            </div>
        </div>

        <!-- الروابط والقوائم الجانبية مع شروط الصلاحيات المطابقة لصصورتك -->
        <div class="space-y-1.5 flex flex-col">

            <!-- لوحة التحكم -->
            @can('مشاهدة')
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                📊 <span>لوحة التحكم</span>
            </a>
            @endcan

            <!-- العمليات الجمركية ومراحل سير العمل -->
            @can('مشاهدة')
            <div class="space-y-1">
                <a href="{{ route('transactions.index') }}"
                    class="flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('transactions.index') || request()->routeIs('transactions.create') || request()->routeIs('transactions.edit') || request()->routeIs('transactions.show') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <span>📦</span> <span>العمليات الجمركية</span>
                    </div>
                </a>

                <!-- لوحة سير العمل والمراحل (مربوطة بصلاحية مرحلة 1 حسب اختيارك) -->
                @can('مرحلة 1: الاستلام والترقيم')
                <a href="{{ route('transactions.workflow') }}"
                    class="flex items-center justify-between pr-8 pl-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('transactions.workflow') ? 'bg-blue-600/30 text-blue-300 font-bold border border-blue-500/40' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-200' }}">
                    <div class="flex items-center gap-2">
                        <span>⚡</span> <span>لوحة المراحل وسير العمل</span>
                    </div>
                    <span class="bg-blue-900/60 text-blue-300 text-[10px] px-2 py-0.5 rounded-full font-mono font-bold">مفعل</span>
                </a>
                @endcan

                <!-- العمليات المكتملة -->
                @can('مشاهدة العمليات المكتملة')
                <a href="{{ route('transactions.completed') }}"
                    class="flex items-center justify-between pr-8 pl-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('transactions.completed') ? 'bg-emerald-600/30 text-emerald-300 font-bold border border-emerald-500/40' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-200' }}">
                    <div class="flex items-center gap-2">
                        <span>✅</span> <span>العمليات المكتملة</span>
                    </div>
                </a>
                @endcan
            </div>
            @endcan

            <!-- التقارير -->
            @can('مشاهدة التقارير')
            <a href="{{ route('reports.index') ?? '#' }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                📈 <span>التقارير</span>
            </a>
            @endcan

            <!-- التجار -->
            @can('مشاهدة التجار')
            <a href="{{ route('traders.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('traders.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                👥 <span>التجار</span>
            </a>
            @endcan

            <!-- الشركات -->
            @can('مشاهدة الشركات')
            <a href="{{ route('companies.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('companies.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                🏢 <span>الشركات</span>
            </a>
            @endcan

            <!-- الحالات -->
            @can('مشاهدة الحالات')
            <a href="{{ route('statuses.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('statuses.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                🏷️ <span>الحالات</span>
            </a>
            @endcan

            <!-- المستخدمين -->
            @can('إدارة المستخدمين')
            <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                👤 <span>المستخدمين</span>
            </a>
            @endcan

            <!-- الصلاحيات -->
            @can('إدارة الصلاحيات')
            <a href="{{ route('roles.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('roles.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                🔑 <span>الصلاحيات</span>
            </a>
            @endcan

            <!-- سجل العمليات -->
            @can('مشاهدة سجل العمليات')
            <a href="{{ route('audit-logs.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('audit-logs.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                📜 <span>سجل العمليات</span>
            </a>
            @endcan

        </div>
    </div>

    <!-- أسفل القائمة الجانبية: بروفايل المستخدم وتسجيل الخروج -->
    <div class="border-t border-slate-800 pt-4 mt-4">
        <div class="flex items-center gap-3 px-3 py-2 bg-slate-800/60 rounded-xl border border-slate-700/50 mb-3">
            <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white shadow">
                {{ mb_substr(Auth::user()->name ?? 'م', 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <span class="block text-sm font-bold text-white truncate">{{ Auth::user()->name ?? 'مستخدم' }}</span>
                <span class="block text-xs text-blue-400 truncate">
                    {{ Auth::user()->getRoleNames()->first() ?? 'مستخدم' }}
                </span>
            </div>
        </div>

        <div class="flex items-center justify-between gap-2">
            <a href="{{ route('profile.edit') }}" class="flex-1 text-center py-2 px-3 bg-slate-800 hover:bg-slate-700 rounded-lg text-xs font-bold text-slate-300 transition">
                ⚙️ الإعدادات
            </a>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full text-center py-2 px-3 bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white rounded-lg text-xs font-bold transition">
                    🚪 خروج
                </button>
            </form>
        </div>
    </div>
</nav>