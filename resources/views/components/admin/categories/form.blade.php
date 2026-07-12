@props(['category' => null, 'parentOptions' => []])

<form
    method="POST"
    action="{{ $category ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}"
    enctype="multipart/form-data"
    class="flex flex-col gap-8"
>
    @csrf
    @if ($category)
        @method('PUT')
    @endif

    <div class="grid grid-cols-2 gap-6">
        <div>
            <label for="parent_id" class="mb-1 block text-xs uppercase tracking-wide text-ink/60">Parent category</label>
            <select id="parent_id" name="parent_id" class="w-full border border-ink/15 bg-white px-3 py-2 text-sm">
                <option value="">— None (top level) —</option>
                @foreach ($parentOptions as $id => $label)
                    <option value="{{ $id }}" @selected(old('parent_id', $category?->parentId) == $id)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="sort_order" class="mb-1 block text-xs uppercase tracking-wide text-ink/60">Sort order</label>
            <input
                type="number"
                id="sort_order"
                name="sort_order"
                value="{{ old('sort_order', $category?->sortOrder ?? 0) }}"
                class="w-full border border-ink/15 bg-white px-3 py-2 text-sm"
            >
        </div>
    </div>

    <div>
        <label for="image" class="mb-1 block text-xs uppercase tracking-wide text-ink/60">Image</label>
        @if ($category?->image)
            <img src="{{ Storage::url($category->image) }}" class="mb-2 h-24 w-24 rounded-sm object-cover" alt="">
        @endif
        <input type="file" id="image" name="image" accept="image/*" class="w-full text-sm">
    </div>

    <label class="flex items-center gap-2 text-sm text-ink/70">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category?->isActive ?? true))>
        Active
    </label>

    @foreach (config('localization.locales') as $locale => $label)
        @php $translation = $category?->translation($locale); @endphp
        <fieldset class="border border-ink/10 p-5">
            <legend class="px-2 font-mono text-xs uppercase tracking-widest text-ink/50">{{ $label }}</legend>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs uppercase tracking-wide text-ink/60">Name</label>
                    <input
                        type="text"
                        name="translations[{{ $locale }}][name]"
                        value="{{ old("translations.$locale.name", $translation?->name) }}"
                        class="w-full border border-ink/15 bg-white px-3 py-2 text-sm"
                    >
                </div>
                <div>
                    <label class="mb-1 block text-xs uppercase tracking-wide text-ink/60">Slug</label>
                    <input
                        type="text"
                        name="translations[{{ $locale }}][slug]"
                        value="{{ old("translations.$locale.slug", $translation?->slug) }}"
                        class="w-full border border-ink/15 bg-white px-3 py-2 text-sm"
                    >
                </div>
            </div>

            <div class="mt-4">
                <label class="mb-1 block text-xs uppercase tracking-wide text-ink/60">H1 (if different from name)</label>
                <input
                    type="text"
                    name="translations[{{ $locale }}][h1]"
                    value="{{ old("translations.$locale.h1", $translation?->h1) }}"
                    class="w-full border border-ink/15 bg-white px-3 py-2 text-sm"
                >
            </div>

            <div class="mt-4">
                <label class="mb-1 block text-xs uppercase tracking-wide text-ink/60">Meta title</label>
                <input
                    type="text"
                    name="translations[{{ $locale }}][meta_title]"
                    value="{{ old("translations.$locale.meta_title", $translation?->metaTitle) }}"
                    class="w-full border border-ink/15 bg-white px-3 py-2 text-sm"
                >
            </div>

            <div class="mt-4">
                <label class="mb-1 block text-xs uppercase tracking-wide text-ink/60">Meta description</label>
                <textarea
                    name="translations[{{ $locale }}][meta_description]"
                    rows="2"
                    class="w-full border border-ink/15 bg-white px-3 py-2 text-sm"
                >{{ old("translations.$locale.meta_description", $translation?->metaDescription) }}</textarea>
            </div>

            <div class="mt-4">
                <label class="mb-1 block text-xs uppercase tracking-wide text-ink/60">Description</label>
                <textarea
                    name="translations[{{ $locale }}][description]"
                    rows="4"
                    class="w-full border border-ink/15 bg-white px-3 py-2 text-sm"
                >{{ old("translations.$locale.description", $translation?->description) }}</textarea>
            </div>
        </fieldset>
    @endforeach

    @if ($errors->any())
        <div class="border border-madder/30 bg-madder/5 px-4 py-3 text-sm text-madder">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <button type="submit" class="self-start bg-ink px-6 py-2.5 text-sm font-medium text-bone hover:bg-ink/85">
        {{ $category ? 'Save changes' : 'Create category' }}
    </button>
</form>
