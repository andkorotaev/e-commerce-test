@props(['cartCount' => 0, 'wishlistCount' => 0])

<div>
    <x-front.layouts.header.announcement-bar />
    <header class="flex items-center justify-between border-b border-ink/10 bg-bone px-4 py-4 md:px-10 md:py-5">
        <div class="flex items-center gap-3">
            <x-front.layouts.header.mobile-menu :categories="$categories" />
            <x-front.layouts.header.logo />
        </div>

        <x-front.layouts.header.nav :categories="$categories" />

        <div class="flex items-center gap-4 md:gap-6">
            <x-front.layouts.header.search />
            <x-front.layouts.header.account />
            <x-front.layouts.header.wishlist :count="$wishlistCount" />
            <x-front.layouts.header.cart :count="$cartCount" />
        </div>
    </header>
</div>
