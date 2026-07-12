@props(['categories', 'depth' => 0])

<ul class="{{ $depth > 0 ? 'ml-6 border-l border-ink/10 pl-4' : '' }}">
    @foreach ($categories as $category)
        <li class="py-2">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    @if ($category->image)
                        <img src="{{ Storage::url($category->image) }}" class="h-8 w-8 rounded-sm object-cover" alt="">
                    @endif
                    <span class="{{ $category->isActive ? '' : 'text-ink/40 line-through' }}">
                        {{ $category->translation('uk')?->name ?? '—' }}
                    </span>
                    <span class="font-mono text-xs text-ink/40">#{{ $category->sortOrder }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-ink/60 underline hover:text-ink">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" onsubmit="return confirm('Delete this category?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-madder hover:underline">Delete</button>
                    </form>
                </div>
            </div>

            @if ($category->children->isNotEmpty())
                <x-admin.categories.tree :categories="$category->children" :depth="$depth + 1" />
            @endif
        </li>
    @endforeach
</ul>
