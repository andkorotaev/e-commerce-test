<x-front.layouts.layout
    description="OCRE — малі партії одягу, забарвленого натуральними барвниками: індиго, волоський горіх, кошеніль, резеда."
    image="home/hero-1.jpg"
>
    <x-front.home.hero />
    <x-front.home.new-arrivals :products="$newArrivals" />
    <x-front.home.popular-products :products="$popularProducts" />
    <x-front.home.categories :categories="$categories" />
    <x-front.benefits />
</x-front.layouts.layout>
