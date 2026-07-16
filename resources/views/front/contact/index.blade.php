<x-front.layouts.layout
    title="Контакти"
    description="Адреса, телефон, email та форма зворотного зв'язку OCRE."
>
    <div class="mx-auto max-w-6xl px-4 py-16 md:px-10 md:py-20">
        <p class="font-mono text-xs uppercase tracking-widest text-stone">Контакти</p>
        <h1 class="mt-2 mb-10 font-serif text-4xl text-ink md:text-5xl">Зв'яжіться з нами</h1>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
            <div class="space-y-10">
                <dl class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <dt class="font-mono text-xs uppercase tracking-widest text-ink/40">Адреса</dt>
                        <dd class="mt-1 text-sm text-ink">{{ config('shop.contact.address') }}</dd>
                    </div>
                    <div>
                        <dt class="font-mono text-xs uppercase tracking-widest text-ink/40">Телефон</dt>
                        <dd class="mt-1 text-sm text-ink">
                            <a href="tel:{{ config('shop.contact.phone') }}" class="hover:text-madder">{{ config('shop.contact.phone_display') }}</a>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-mono text-xs uppercase tracking-widest text-ink/40">Email</dt>
                        <dd class="mt-1 text-sm text-ink">
                            <a href="mailto:{{ config('shop.contact.email') }}" class="hover:text-madder">{{ config('shop.contact.email') }}</a>
                        </dd>
                    </div>
                </dl>

                <div class="aspect-[4/3] w-full overflow-hidden border border-stone/30 sm:aspect-[16/10]">
                    <iframe
                        src="https://www.google.com/maps?q={{ urlencode(config('shop.contact.map_query')) }}&output=embed"
                        class="h-full w-full border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Розташування на карті"
                    ></iframe>
                </div>
            </div>

            <div>
                <h2 class="mb-6 font-serif text-2xl text-ink">Форма зворотного зв'язку</h2>

                @if (session('status') === 'contact-message-sent')
                    <div class="mb-6 border border-ink/10 bg-stone/10 px-4 py-3 text-sm text-ink">
                        Дякуємо! Ваше повідомлення надіслано, ми відповімо найближчим часом.
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 border border-madder/30 bg-madder/5 px-4 py-3 text-sm text-madder">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('front.contact.store') }}" class="flex flex-col gap-4">
                    @csrf

                    <div>
                        <label for="name" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Ім'я</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', auth()->user()->name ?? '') }}"
                            required
                            class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                        >
                    </div>

                    <div>
                        <label for="email" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Email</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', auth()->user()->email ?? '') }}"
                            required
                            class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                        >
                    </div>

                    <div>
                        <label for="phone" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Телефон (необов'язково)</label>
                        <input
                            id="phone"
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                        >
                    </div>

                    <div>
                        <label for="message" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Повідомлення</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="5"
                            required
                            class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                        >{{ old('message') }}</textarea>
                    </div>

                    <button
                        type="submit"
                        class="mt-2 bg-ink px-6 py-3 font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder"
                    >
                        Надіслати
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-front.layouts.layout>
