@props(['title' => null, 'description' => null, 'image' => null, 'type' => 'website'])

@php
    $pageTitle = $title ? $title.' — OCRE' : 'OCRE — одяг ручного фарбування';
    $pageDescription = $description ?? 'OCRE — малі партії одягу, забарвленого натуральними барвниками: індиго, волоський горіх, кошеніль, резеда.';
    $pageImage = $image
        ? (str($image)->startsWith('http') ? $image : Storage::url($image))
        : Storage::url('home/hero-1.jpg');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $pageDescription }}">

        <link rel="icon" href="/favicon.svg" type="image/svg+xml">

        <meta property="og:type" content="{{ $type }}">
        <meta property="og:site_name" content="OCRE">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $pageDescription }}">
        <meta property="og:image" content="{{ $pageImage }}">
        <meta property="og:url" content="{{ url()->current() }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:300,400,500,600|schibsted-grotesk:400,500,600|ibm-plex-mono:400,500" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-screen flex-col bg-bone font-sans text-ink antialiased">
        <x-front.layouts.header />

        <main class="flex-1">
            {{ $slot }}
        </main>

        <x-front.layouts.footer />
    </body>
</html>
