<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول - نظام إدارة العمليات</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Tajawal', sans-serif; } </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="fixed top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full sm:max-w-md bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl shadow-2xl p-6 sm:p-10 relative z-10">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 shadow-lg shadow-blue-500/20 mb-4">
                <i class="fa-solid fa-shield-halved text-2xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-white">نظام إدارة العمليات</h2>
            <p class="text-sm text-slate-400 mt-2">سجل دخولك للوصول للوحة التحكم</p>
        </div>

        @if (session('status'))
            <div class="mb-4 text-sm font-medium text-green-400 bg-green-950/50 p-3 rounded-xl border border-green-800 text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="username" class="block text-sm font-medium text-slate-300 mb-2">
                    <i class="fa-solid fa-user text-xs text-slate-400 ml-1"></i> اسم المستخدم / الرقم
                </label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus placeholder="أدخل اسم المستخدم"
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                @if ($errors->has('username'))
                    <p class="mt-2 text-xs text-red-400">{{ $errors->first('username') }}</p>
                @endif
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                    <i class="fa-solid fa-lock text-xs text-slate-400 ml-1"></i> كلمة المرور
                </label>
                <input id="password" type="password" name="password" required placeholder="••••••••"
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                @if ($errors->has('password'))
                    <p class="mt-2 text-xs text-red-400">{{ $errors->first('password') }}</p>
                @endif
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center text-slate-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-slate-800 border-slate-700 text-blue-600 focus:ring-blue-500">
                    <span class="ms-2 text-xs">تذكرني</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-blue-400 hover:text-blue-300 text-xs transition-colors">نسيت كلمة المرور؟</a>
                @endif
            </div>

            <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2">
                <span>تسجيل الدخول</span>
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-slate-500">
            جميع الحقوق محفوظة &copy; {{ date('Y') }} - نظام التخليص الجمركي
        </div>
    </div>

</body>
</html>