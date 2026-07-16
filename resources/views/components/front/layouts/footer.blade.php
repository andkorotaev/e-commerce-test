@props(['categories'])

<footer class="bg-indigo-vat text-bone/70">
    <div class="mx-auto max-w-6xl px-4 py-16 md:px-10">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <span class="font-serif text-xl text-bone">OCRE</span>
                <p class="mt-3 max-w-xs text-sm text-bone/50">
                    Малі партії, натуральні барвники — індиго, волоський горіх, кошеніль, резеда.
                </p>
            </div>

            <div>
                <h3 class="mb-4 font-mono text-xs uppercase tracking-widest text-bone/40">Контакти</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="mailto:hello@ocre.ua" class="transition-colors hover:text-bone">hello@ocre.ua</a></li>
                    <li><a href="tel:+380441234567" class="transition-colors hover:text-bone">+380 44 123 45 67</a></li>
                    <li class="text-bone/50">Київ, вул. Хрещатик, 1</li>
                </ul>
            </div>

            <div>
                <h3 class="mb-4 font-mono text-xs uppercase tracking-widest text-bone/40">Каталог</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('front.home') }}" class="transition-colors hover:text-bone">Головна</a></li>
                    @foreach ($categories as $category)
                        @php $translation = $category->translation(app()->getLocale()); @endphp
                        <li>
                            <a href="{{ route('front.categories.show', $translation?->slug) }}" class="transition-colors hover:text-bone">
                                {{ $translation?->name }}
                            </a>
                        </li>
                    @endforeach
                    <li><a href="{{ route('front.cart.show') }}" class="transition-colors hover:text-bone">Кошик</a></li>
                </ul>
            </div>

            <div>
                <h3 class="mb-4 font-mono text-xs uppercase tracking-widest text-bone/40">Акаунт</h3>
                <ul class="space-y-2 text-sm">
                    @auth
                        <li><a href="{{ route('front.account.profile') }}" class="transition-colors hover:text-bone">Особистий кабінет</a></li>
                    @else
                        <li><a href="{{ route('front.login') }}" class="transition-colors hover:text-bone">Увійти</a></li>
                        <li><a href="{{ route('front.register') }}" class="transition-colors hover:text-bone">Реєстрація</a></li>
                    @endauth
                </ul>

                <div class="mt-6 flex gap-3">
                    <a href="#" aria-label="Instagram" class="flex h-8 w-8 items-center justify-center border border-bone/20 text-bone/60 transition-colors hover:border-bone hover:text-bone">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                            <rect x="3" y="3" width="18" height="18" rx="5" />
                            <circle cx="12" cy="12" r="4" />
                            <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor" />
                        </svg>
                    </a>
                    <a href="#" aria-label="Facebook" class="flex h-8 w-8 items-center justify-center border border-bone/20 text-bone/60 transition-colors hover:border-bone hover:text-bone">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 9h3V6h-3a3 3 0 00-3 3v2H8v3h3v6h3v-6h3l1-3h-4V9a1 1 0 011-1z" />
                        </svg>
                    </a>
                    <a href="#" aria-label="Pinterest" class="flex h-8 w-8 items-center justify-center border border-bone/20 text-bone/60 transition-colors hover:border-bone hover:text-bone">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l2-9m1 3.5c3 0 5-2 5-5a5 5 0 00-9.5-2.2M6 12a6 6 0 006 6" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-14 border-t border-bone/10 pt-6 text-center font-mono text-xs text-bone/40 sm:text-left">
            © {{ date('Y') }} OCRE. Усі права захищені.
        </div>
    </div>
</footer>
