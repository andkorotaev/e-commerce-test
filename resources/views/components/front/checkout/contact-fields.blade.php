@props(['prefill'])

<div>
    <h2 class="mb-4 font-mono text-xs uppercase tracking-widest text-ink/40">Контактні дані</h2>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Ім'я</label>
            <input
                type="text"
                name="first_name"
                value="{{ old('first_name', $prefill['first_name']) }}"
                required
                class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
            >
            @error('first_name') <p class="mt-1 text-xs text-madder">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Прізвище</label>
            <input
                type="text"
                name="last_name"
                value="{{ old('last_name', $prefill['last_name']) }}"
                required
                class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
            >
            @error('last_name') <p class="mt-1 text-xs text-madder">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Телефон</label>
            <input
                type="tel"
                name="phone"
                value="{{ old('phone', $prefill['phone'] ?? '') }}"
                required
                placeholder="+380 XX XXX XX XX"
                class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
            >
            @error('phone') <p class="mt-1 text-xs text-madder">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Email</label>
            <input
                type="email"
                name="email"
                value="{{ old('email', $prefill['email']) }}"
                required
                class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
            >
            @error('email') <p class="mt-1 text-xs text-madder">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-4">
        <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Коментар до замовлення</label>
        <textarea
            name="comment"
            rows="3"
            class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
        >{{ old('comment') }}</textarea>
        @error('comment') <p class="mt-1 text-xs text-madder">{{ $message }}</p> @enderror
    </div>
</div>
