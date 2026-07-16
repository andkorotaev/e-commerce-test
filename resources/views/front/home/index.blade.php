<x-front.layouts.layout>
    <x-front.home.hero />
    <x-front.home.new-arrivals :products="$newArrivals" />
    <x-front.home.popular-products :products="$popularProducts" />
    <x-front.home.categories :categories="$categories" />
    <x-front.benefits />
</x-front.layouts.layout>
