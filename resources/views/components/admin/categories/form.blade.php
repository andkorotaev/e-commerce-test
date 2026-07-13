@props(['category' => null, 'parentOptions' => []])

@php
    $inputClass = 'w-full rounded-md border border-ink/15 bg-white px-3 py-2.5 text-sm shadow-sm transition-colors focus:border-ink focus:outline-none focus:ring-1 focus:ring-ink/20';
    $labelClass = 'mb-1.5 block text-sm font-medium text-ink/70';
    $errorClass = 'mt-1.5 text-xs text-madder';
@endphp

<form
    method="POST"
    action="{{ $category ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}"
    enctype="multipart/form-data"
    class="flex flex-col gap-6"
>
    @csrf
    @if ($category)
        @method('PUT')
    @endif

    <div class="rounded-lg border border-ink/10 bg-white p-6 shadow-sm">
        <h2 class="mb-5 text-sm font-semibold text-ink">General</h2>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label for="parent_id" class="{{ $labelClass }}">Parent category</label>
                <select id="parent_id" name="parent_id" class="{{ $inputClass }}">
                    <option value="">— None (top level) —</option>
                    @foreach ($parentOptions as $id => $label)
                        <option value="{{ $id }}" @selected(old('parent_id', $category?->parentId) == $id)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sort_order" class="{{ $labelClass }}">Sort order</label>
                <input
                    type="number"
                    id="sort_order"
                    name="sort_order"
                    value="{{ old('sort_order', $category?->sortOrder ?? 0) }}"
                    class="{{ $inputClass }}"
                >
                @error('sort_order')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-5">
            <label for="image" class="{{ $labelClass }}">Image</label>
            <div class="flex items-center gap-4">
                @if ($category?->image)
                    <img src="{{ Storage::url($category->image) }}" class="h-16 w-16 rounded-md object-cover ring-1 ring-ink/10" alt="">
                @endif
                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/*"
                    class="flex-1 text-sm text-ink/70 file:mr-4 file:rounded-md file:border-0 file:bg-ink file:px-4 file:py-2 file:text-sm file:font-medium file:text-bone file:transition-colors hover:file:bg-ink/85"
                >
            </div>
            @error('image')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
        </div>

        <label class="mt-5 flex items-center gap-2.5 text-sm text-ink/80">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old('is_active', $category?->isActive ?? true))
                class="h-4 w-4 rounded border-ink/30 text-ink focus:ring-ink/30"
            >
            Active
        </label>
    </div>

    @foreach (config('localization.locales') as $locale => $label)
        @php $translation = $category?->translation($locale); @endphp
        <div class="rounded-lg border border-ink/10 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center gap-2">
                <h2 class="text-sm font-semibold text-ink">{{ $label }}</h2>
                <span class="rounded-full bg-stone/20 px-2 py-0.5 font-mono text-[11px] uppercase text-ink/50">{{ $locale }}</span>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="{{ $labelClass }}">Name</label>
                    <input
                        type="text"
                        name="translations[{{ $locale }}][name]"
                        value="{{ old("translations.$locale.name", $translation?->name) }}"
                        class="{{ $inputClass }}"
                    >
                    @error("translations.$locale.name")
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="{{ $labelClass }}">Slug</label>
                    <input
                        type="text"
                        name="translations[{{ $locale }}][slug]"
                        value="{{ old("translations.$locale.slug", $translation?->slug) }}"
                        class="{{ $inputClass }}"
                    >
                    @error("translations.$locale.slug")
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 border-t border-ink/10 pt-5">
                <p class="mb-4 font-mono text-xs uppercase tracking-wide text-ink/40">SEO</p>

                <div class="mb-4">
                    <label class="{{ $labelClass }}">H1 <span class="font-normal text-ink/40">(if different from name)</span></label>
                    <input
                        type="text"
                        name="translations[{{ $locale }}][h1]"
                        value="{{ old("translations.$locale.h1", $translation?->h1) }}"
                        class="{{ $inputClass }}"
                    >
                    @error("translations.$locale.h1")
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="{{ $labelClass }}">Meta title</label>
                    <input
                        type="text"
                        name="translations[{{ $locale }}][meta_title]"
                        value="{{ old("translations.$locale.meta_title", $translation?->metaTitle) }}"
                        class="{{ $inputClass }}"
                    >
                    @error("translations.$locale.meta_title")
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="{{ $labelClass }}">Meta description</label>
                    <textarea
                        name="translations[{{ $locale }}][meta_description]"
                        rows="2"
                        class="{{ $inputClass }}"
                    >{{ old("translations.$locale.meta_description", $translation?->metaDescription) }}</textarea>
                    @error("translations.$locale.meta_description")
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Description</label>
                    <textarea
                        name="translations[{{ $locale }}][description]"
                        rows="4"
                        class="{{ $inputClass }}"
                    >{{ old("translations.$locale.description", $translation?->description) }}</textarea>
                    @error("translations.$locale.description")
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    @endforeach

    <div class="flex items-center gap-4 pt-2">
        <button type="submit" class="rounded-md bg-ink px-6 py-2.5 text-sm font-medium text-bone transition-colors hover:bg-ink/85">
            {{ $category ? 'Save changes' : 'Create category' }}
        </button>
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-ink/60 transition-colors hover:text-ink">
            Cancel
        </a>
    </div>
</form>
