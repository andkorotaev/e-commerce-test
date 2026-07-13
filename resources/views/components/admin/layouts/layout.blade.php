@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ? $title.' — Admin' : 'Admin' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-stone/10 font-sans text-ink antialiased">
        @auth('admin')
            <div class="flex min-h-screen">
                <x-admin.layouts.sidebar />
                <main class="min-w-0 flex-1">
                    {{ $slot }}
                </main>
            </div>
        @else
            {{ $slot }}
        @endauth
    </body>
</html>
