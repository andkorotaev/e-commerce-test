@props(['product', 'ratingStats', 'reviews'])

@php
    $locale = app()->getLocale();
    $slug = $product->translation($locale)?->slug;
    $rating = $ratingStats['average'];
    $count = $ratingStats['count'];
@endphp

<div class="mt-16 border-t border-stone/30 pt-12">
    <h2 class="mb-6 font-serif text-2xl text-ink">Відгуки</h2>

    @if (session('status') === 'review-submitted')
        <p class="mb-6 bg-stone/10 px-4 py-3 text-sm text-ink/70">
            Дякуємо! Ваш відгук з'явиться після модерації.
        </p>
    @endif

    <div class="mb-8 flex items-center gap-3">
        <span class="font-serif text-3xl text-ink">{{ number_format($rating, 1) }}</span>
        <div class="flex items-center gap-0.5 text-madder" aria-hidden="true">
            @for ($i = 1; $i <= 5; $i++)
                <svg viewBox="0 0 20 20" fill="{{ $i <= round($rating) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 1.5l2.6 5.6 6 .8-4.4 4.3 1 6-5.2-2.9-5.2 2.9 1-6L1.4 7.9l6-.8L10 1.5z" />
                </svg>
            @endfor
        </div>
        <span class="font-mono text-xs text-ink/40">{{ $count }}</span>
    </div>

    @if ($reviews->isNotEmpty())
        <div class="mb-12 max-w-2xl space-y-6">
            @foreach ($reviews as $review)
                <div class="border-b border-stone/20 pb-6">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-ink">{{ $review->authorName }}</span>
                        <div class="flex items-center gap-0.5 text-madder" aria-hidden="true">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg viewBox="0 0 20 20" fill="{{ $i <= $review->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1" class="h-3 w-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 1.5l2.6 5.6 6 .8-4.4 4.3 1 6-5.2-2.9-5.2 2.9 1-6L1.4 7.9l6-.8L10 1.5z" />
                                </svg>
                            @endfor
                        </div>
                        <span class="font-mono text-xs text-ink/30">{{ $review->createdAt->format('d.m.Y') }}</span>
                    </div>
                    <p class="mt-2 text-sm text-ink/70">{{ $review->comment }}</p>
                </div>
            @endforeach
        </div>
    @endif

    @auth
        <form method="POST" action="{{ route('front.reviews.store', $slug) }}" class="max-w-lg space-y-4">
            @csrf

            <p class="font-mono text-xs uppercase tracking-widest text-ink/40">
                Ви пишете як {{ auth()->user()->name }}
            </p>

            <div>
                <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Оцінка</label>
                <select name="rating" class="border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none">
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" @selected((int) old('rating', 5) === $i)>{{ $i }} / 5</option>
                    @endfor
                </select>
                @error('rating')
                    <p class="mt-1 text-xs text-madder">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Відгук</label>
                <textarea
                    name="comment"
                    rows="4"
                    required
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >{{ old('comment') }}</textarea>
                @error('comment')
                    <p class="mt-1 text-xs text-madder">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-ink px-6 py-3 font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder">
                Залишити відгук
            </button>
        </form>
    @else
        <p class="font-mono text-xs uppercase tracking-widest text-ink/40">
            <a href="{{ route('front.login') }}" class="underline decoration-dotted underline-offset-4 hover:text-ink">Увійдіть</a>,
            щоб залишити відгук
        </p>
    @endauth
</div>
