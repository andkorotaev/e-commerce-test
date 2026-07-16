@props(['summary'])

@if ($summary->lines->isEmpty())
    <div class="py-12">
        <p class="mb-6 font-mono text-xs uppercase tracking-widest text-ink/40">Кошик порожній</p>
        <a href="{{ route('front.home') }}" class="font-mono text-xs uppercase tracking-widest text-ink underline decoration-dotted underline-offset-4 hover:text-madder">
            До покупок →
        </a>
    </div>
@else
    {{--
        Container query, not a viewport (lg:) breakpoint — this partial is
        shared between the full /cart page (wide) and the header cart modal
        (a ~448px drawer). A viewport breakpoint would switch to the 2-column
        layout on any desktop-width screen regardless of the drawer's actual
        width, cramming a 320px sidebar into a container far too narrow for
        it. @container makes the breakpoint respond to the space this
        partial actually has, so it stays single-column in the narrow modal
        and 2-column on the wide page.
    --}}
    <div class="@container">
        <div class="grid grid-cols-1 gap-10 @lg:grid-cols-[1fr_320px]">
            <div class="divide-y divide-stone/20">
                @foreach ($summary->lines as $line)
                    <x-front.cart.item :line="$line" />
                @endforeach
            </div>

            <x-front.cart.summary :summary="$summary" />
        </div>
    </div>
@endif
