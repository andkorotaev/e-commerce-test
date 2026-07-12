@props(['cartCount' => 0])

<div>
    <x-front.layouts.header.announcement-bar />
    <header class="flex items-center justify-between border-b border-ink/10 bg-bone px-10 py-5">
        <x-front.layouts.header.logo />
        <x-front.layouts.header.nav />
        <div class="flex items-center gap-6">
            <x-front.layouts.header.search />
            <x-front.layouts.header.cart :count="$cartCount" />
        </div>
    </header>
</div>
