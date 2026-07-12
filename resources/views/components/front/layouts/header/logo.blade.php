@props(['dark' => false])

<a href="{{ url('/') }}" class="font-serif text-2xl tracking-wide {{ $dark ? 'text-bone' : 'text-ink' }}">
    OCRE
</a>
