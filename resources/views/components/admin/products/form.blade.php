@props(['product' => null, 'categoryOptions' => []])

@php
    $inputClass = 'w-full rounded-md border border-ink/15 bg-white px-3 py-2.5 text-sm shadow-sm transition-colors focus:border-ink focus:outline-none focus:ring-1 focus:ring-ink/20';
    $labelClass = 'mb-1.5 block text-sm font-medium text-ink/70';
    $errorClass = 'mt-1.5 text-xs text-madder';
@endphp

<form
    method="POST"
    action="{{ $product ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
    enctype="multipart/form-data"
    class="flex flex-col gap-6"
>
    @csrf
    @if ($product)
        @method('PUT')
    @endif

    <div class="rounded-lg border border-ink/10 bg-white p-6 shadow-sm">
        <h2 class="mb-5 text-sm font-semibold text-ink">General</h2>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label for="category_id" class="{{ $labelClass }}">Category</label>
                <select id="category_id" name="category_id" class="{{ $inputClass }}">
                    <option value="">— Select —</option>
                    @foreach ($categoryOptions as $id => $label)
                        <option value="{{ $id }}" @selected(old('category_id', $product?->categoryId) == $id)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sku" class="{{ $labelClass }}">SKU</label>
                <input
                    type="text"
                    id="sku"
                    name="sku"
                    value="{{ old('sku', $product?->sku) }}"
                    class="{{ $inputClass }}"
                >
                @error('sku')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-5 grid grid-cols-4 gap-5">
            <div>
                <label for="price" class="{{ $labelClass }}">Price</label>
                <input
                    type="number"
                    step="0.01"
                    id="price"
                    name="price"
                    value="{{ old('price', $product?->price) }}"
                    class="{{ $inputClass }}"
                >
                @error('price')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="old_price" class="{{ $labelClass }}">Old price <span class="font-normal text-ink/40">(optional)</span></label>
                <input
                    type="number"
                    step="0.01"
                    id="old_price"
                    name="old_price"
                    value="{{ old('old_price', $product?->oldPrice) }}"
                    class="{{ $inputClass }}"
                >
                @error('old_price')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="stock" class="{{ $labelClass }}">Stock</label>
                <input
                    type="number"
                    id="stock"
                    name="stock"
                    value="{{ old('stock', $product?->stock ?? 0) }}"
                    class="{{ $inputClass }}"
                >
                @error('stock')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sort_order" class="{{ $labelClass }}">Sort order</label>
                <input
                    type="number"
                    id="sort_order"
                    name="sort_order"
                    value="{{ old('sort_order', $product?->sortOrder ?? 0) }}"
                    class="{{ $inputClass }}"
                >
                @error('sort_order')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <label class="mt-5 flex items-center gap-2.5 text-sm text-ink/80">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old('is_active', $product?->isActive ?? true))
                class="h-4 w-4 rounded border-ink/30 text-ink focus:ring-ink/30"
            >
            Active
        </label>
    </div>

    <div class="rounded-lg border border-ink/10 bg-white p-6 shadow-sm">
        <h2 class="mb-5 text-sm font-semibold text-ink">Images</h2>

        @if ($product && $product->images->isNotEmpty())
            <div class="mb-5 grid grid-cols-4 gap-4 sm:grid-cols-6">
                @foreach ($product->images as $image)
                    <label class="group relative block cursor-pointer">
                        <img
                            src="{{ Storage::url($image->path) }}"
                            alt=""
                            class="aspect-square w-full rounded-md object-cover ring-1 ring-ink/10 transition-opacity group-has-[:checked]:opacity-30"
                        >
                        <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="absolute right-1.5 top-1.5 h-4 w-4 rounded border-ink/30 bg-white/90">
                    </label>
                @endforeach
            </div>
            <p class="mb-4 text-xs text-ink/40">Check an image to remove it when you save.</p>
        @endif

        <label for="images" class="{{ $labelClass }}">Add images</label>
        <input
            type="file"
            id="images"
            name="images[]"
            accept="image/*"
            multiple
            class="w-full text-sm text-ink/70 file:mr-4 file:rounded-md file:border-0 file:bg-ink file:px-4 file:py-2 file:text-sm file:font-medium file:text-bone file:transition-colors hover:file:bg-ink/85"
        >
        @error('images.*')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    @foreach (config('localization.locales') as $locale => $label)
        @php $translation = $product?->translation($locale); @endphp
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
            {{ $product ? 'Save changes' : 'Create product' }}
        </button>
        <a href="{{ route('admin.products.index') }}" class="text-sm text-ink/60 transition-colors hover:text-ink">
            Cancel
        </a>
    </div>
</form>
