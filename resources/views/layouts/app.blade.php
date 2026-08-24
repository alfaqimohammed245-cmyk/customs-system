<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'نظام التخليص الجمركي') }}</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
        
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- 🚀 استخدام Tailwind CSS عبر CDN لضمان ظهور التصميم والألوان فوراً بدون مشاكل Vite -->
        <script src="https://cdn.tailwindcss.com"></script>

        <style>
            body { font-family: 'Cairo', sans-serif; }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-900">
        <div class="min-h-screen flex">
            
            <!-- 📌 1. القائمة الجانبية (Sidebar) على اليمين -->
            <aside class="w-72 bg-slate-900 text-white flex flex-col justify-between shadow-2xl flex-shrink-0 z-50 min-h-screen">
                <div>
                    <!-- الشعار ورأس القائمة الجانبية -->
                    <div class="p-6 border-b border-slate-800 flex items-center gap-3">
                        <div class="bg-blue-600 p-3 rounded-2xl shadow-lg shadow-blue-500/30 flex items-center justify-center text-white text-2xl">
                            🏢
                        </div>
                        <div>
                            <h2 class="text-lg font-black tracking-wide text-white">التخليص الجمركي</h2>
                            <p class="text-xs text-blue-400 font-bold">لوحة إدارة العمليات</p>
                        </div>
                    </div>

                    <!-- روابط الملاحة الرأسية الجانبية -->
                    <nav class="p-4 space-y-2">
                        
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-base transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="text-xl">📊</span>
                            <span>لوحة التحكم</span>
                        </a>

                        @if(Route::has('transactions.index'))
                        <a href="{{ route('transactions.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-base transition-all duration-200 {{ request()->routeIs('transactions.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="text-xl">📦</span>
                            <span>العمليات الجمركية</span>
                        </a>
                        @endif

                        @if(Route::has('reports.index'))
                        <a href="{{ route('reports.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-base transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="text-xl">📈</span>
                            <span>التقارير</span>
                        </a>
                        @endif

                        @if(Route::has('traders.index'))
                        <a href="{{ route('traders.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-base transition-all duration-200 {{ request()->routeIs('traders.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="text-xl">👥</span>
                            <span>التجار</span>
                        </a>
                        @endif

                        @if(Route::has('companies.index'))
                        <a href="{{ route('companies.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-base transition-all duration-200 {{ request()->routeIs('companies.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="text-xl">🏢</span>
                            <span>الشركات</span>
                        </a>
                        @endif

                        @if(Route::has('statuses.index'))
                        <a href="{{ route('statuses.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-base transition-all duration-200 {{ request()->routeIs('statuses.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="text-xl">🏷️</span>
                            <span>الحالات</span>
                        </a>
                        @endif

                        @if(Route::has('users.index'))
                        <a href="{{ route('users.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-base transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="text-xl">👤</span>
                            <span>المستخدمين</span>
                        </a>
                        @endif

                        @if(Route::has('roles.index'))
                        <a href="{{ route('roles.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-base transition-all duration-200 {{ request()->routeIs('roles.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="text-xl">🔑</span>
                            <span>الصلاحيات</span>
                        </a>
                        @endif

                        @if(Route::has('audit-logs.index'))
                        <a href="{{ route('audit-logs.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-base transition-all duration-200 {{ request()->routeIs('audit-logs.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="text-xl">📜</span>
                            <span>سجل العمليات</span>
                        </a>
                        @endif

                    </nav>
                </div>

                <!-- أسفل القائمة الجانبية: كارت المستخدم وزر الخروج -->
                <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white shadow">
                                {{ mb_substr(Auth::user()->name ?? 'م', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">{{ Auth::user()->name ?? 'مدير النظام' }}</h4>
                                <span class="text-xs text-slate-400">مسؤول النظام</span>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-right flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold text-red-400 hover:bg-red-500/10 transition">
                            🚪 تسجيل الخروج
                        </button>
                    </form>
                </div>
            </aside>

            <!-- 📌 2. منطقة المحتوى الرئيسي -->
            <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
                
                @if (isset($header))
                    <header class="bg-white border-b border-slate-200 shadow-sm py-5 px-8">
                        <div class="max-w-7xl mx-auto">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main class="flex-1 p-8">
                    {{ $slot }}
                </main>
            </div>

        </div>
    </body>
</html>