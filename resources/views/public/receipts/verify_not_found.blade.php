<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إيصال غير موجود</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8 max-w-lg w-full text-center">
            <div class="text-2xl font-extrabold">الإيصال غير موجود</div>
            <p class="text-slate-500 mt-3">
                الرابط غير صحيح أو تم حذف الإيصال.
            </p>
            <a href="{{ url('/') }}"
               class="inline-flex items-center justify-center mt-6 px-5 py-3 rounded-2xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">
                العودة للموقع
            </a>
        </div>
    </div>
</body>
</html>
