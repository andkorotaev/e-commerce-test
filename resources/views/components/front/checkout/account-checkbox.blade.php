<div data-account-section>
    <label class="flex items-center gap-2 text-sm text-ink/70">
        <input
            type="checkbox"
            name="create_account"
            value="1"
            data-create-account-checkbox
            class="h-3.5 w-3.5 rounded-none border-stone text-madder focus:ring-madder"
            @checked(old('create_account'))
        >
        Створити акаунт, щоб відстежувати замовлення
    </label>

    <div data-password-fields class="{{ old('create_account') ? '' : 'hidden' }} mt-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Пароль</label>
                <input
                    type="password"
                    name="password"
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
                @error('password') <p class="mt-1 text-xs text-madder">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">
                    Підтвердження пароля
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>
        </div>
    </div>
</div>
