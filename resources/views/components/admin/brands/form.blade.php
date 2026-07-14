@props(['brand' => null])

@php
    $inputClass = 'w-full rounded-md border border-ink/15 bg-white px-3 py-2.5 text-sm shadow-sm transition-colors focus:border-ink focus:outline-none focus:ring-1 focus:ring-ink/20';
    $labelClass = 'mb-1.5 block text-sm font-medium text-ink/70';
    $errorClass = 'mt-1.5 text-xs text-madder';
@endphp

<form
    method="POST"
    action="{{ $brand ? route('admin.brands.update', $brand->id) : route('admin.brands.store') }}"
    enctype="multipart/form-data"
    class="flex flex-col gap-6"
>
    @csrf
    @if ($brand)
        @method('PUT')
    @endif

    <div class="rounded-lg border border-ink/10 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-2 gap-5">
            <div>
                <label for="name" class="{{ $labelClass }}">Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $brand?->name) }}"
                    class="{{ $inputClass }}"
                >
                @error('name')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="{{ $labelClass }}">Slug</label>
                <input
                    type="text"
                    id="slug"
                    name="slug"
                    value="{{ old('slug', $brand?->slug) }}"
                    class="{{ $inputClass }}"
                >
                @error('slug')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-5">
            <label for="logo" class="{{ $labelClass }}">Logo</label>
            <div class="flex items-center gap-4">
                @if ($brand?->logo)
                    <img src="{{ Storage::url($brand->logo) }}" class="h-16 w-16 rounded-md object-cover ring-1 ring-ink/10" alt="">
                @endif
                <input
                    type="file"
                    id="logo"
                    name="logo"
                    accept="image/*"
                    class="flex-1 text-sm text-ink/70 file:mr-4 file:rounded-md file:border-0 file:bg-ink file:px-4 file:py-2 file:text-sm file:font-medium file:text-bone file:transition-colors hover:file:bg-ink/85"
                >
            </div>
            @error('logo')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
        </div>

        <label class="mt-5 flex items-center gap-2.5 text-sm text-ink/80">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old('is_active', $brand?->isActive ?? true))
                class="h-4 w-4 rounded border-ink/30 text-ink focus:ring-ink/30"
            >
            Active
        </label>
    </div>

    <div class="flex items-center gap-4 pt-2">
        <button type="submit" class="rounded-md bg-ink px-6 py-2.5 text-sm font-medium text-bone transition-colors hover:bg-ink/85">
            {{ $brand ? 'Save changes' : 'Create brand' }}
        </button>
        <a href="{{ route('admin.brands.index') }}" class="text-sm text-ink/60 transition-colors hover:text-ink">
            Cancel
        </a>
    </div>
</form>
