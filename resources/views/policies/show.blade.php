<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-rose-50 via-stone-50 to-amber-50 text-stone-900">
    <main class="mx-auto w-full max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-[28px] border border-rose-200/60 bg-white p-8 shadow-sm">
            <h1 class="mb-3 text-3xl font-semibold text-stone-900">{{ $title }}</h1>
            <div class="h-1 w-20 rounded-full bg-gradient-to-r from-rose-500 to-amber-500"></div>

            <div class="prose mt-8 max-w-none text-stone-700">
                @if(blank($content))
                    <p>Content for this page has not been added yet.</p>
                @else
                    {!! nl2br(e($content)) !!}
                @endif
            </div>
        </div>
    </main>
</body>
</html>
