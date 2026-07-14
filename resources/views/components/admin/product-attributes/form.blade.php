@props(['attribute' => null])

@php
    $inputClass = 'w-full rounded-md border border-ink/15 bg-white px-3 py-2.5 text-sm shadow-sm transition-colors focus:border-ink focus:outline-none focus:ring-1 focus:ring-ink/20';
    $labelClass = 'mb-1.5 block text-sm font-medium text-ink/70';
    $errorClass = 'mt-1.5 text-xs text-madder';
@endphp

<form
    method="POST"
    action="{{ $attribute ? route('admin.product-attributes.update', $attribute->id) : route('admin.product-attributes.store') }}"
    class="flex flex-col gap-6"
>
    @csrf
    @if ($attribute)
        @method('PUT')
    @endif

    <div class="rounded-lg border border-ink/10 bg-white p-6 shadow-sm">
        <h2 class="mb-5 text-sm font-semibold text-ink">General</h2>

        <div>
            <label for="slug" class="{{ $labelClass }}">Slug <span class="font-normal text-ink/40">(e.g. "color", "size")</span></label>
            <input
                type="text"
                id="slug"
                name="slug"
                value="{{ old('slug', $attribute?->slug) }}"
                class="{{ $inputClass }}"
            >
            @error('slug')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-5 grid grid-cols-2 gap-5">
            @foreach (config('localization.locales') as $locale => $label)
                <div>
                    <label class="{{ $labelClass }}">{{ $label }} name <span class="font-mono text-[10px] uppercase text-ink/40">{{ $locale }}</span></label>
                    <input
                        type="text"
                        name="translations[{{ $locale }}][name]"
                        value="{{ old("translations.$locale.name", $attribute?->translation($locale)?->name) }}"
                        class="{{ $inputClass }}"
                    >
                    @error("translations.$locale.name")
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </div>
    </div>

    <div data-component="admin/product-attributes/form" class="rounded-lg border border-ink/10 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-ink">Values</h2>
            <button type="button" data-add-value class="rounded-md border border-ink/15 px-3 py-1.5 text-xs font-medium text-ink/70 transition-colors hover:bg-ink/5">
                Add value
            </button>
        </div>

        <div data-values-container class="flex flex-col gap-3">
            @foreach ($attribute?->values ?? [] as $value)
                <x-admin.product-attributes.value-row :row-key="$value->id" :value="$value" />
            @endforeach
        </div>

        <template data-value-row-template>
            <x-admin.product-attributes.value-row row-key="__KEY__" />
        </template>
    </div>

    <div class="flex items-center gap-4 pt-2">
        <button type="submit" class="rounded-md bg-ink px-6 py-2.5 text-sm font-medium text-bone transition-colors hover:bg-ink/85">
            {{ $attribute ? 'Save changes' : 'Create attribute' }}
        </button>
        <a href="{{ route('admin.product-attributes.index') }}" class="text-sm text-ink/60 transition-colors hover:text-ink">
            Cancel
        </a>
    </div>
</form>
