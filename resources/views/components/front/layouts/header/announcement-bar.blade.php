@props(['dark' => true, 'left' => null, 'right' => null])

@php
    $left ??= __('header.shipping_notice');
    $right ??= __('header.locale_currency');
    $classes = $dark ? 'bg-indigo-vat text-bone/60' : 'border-b border-ink/10 bg-bone text-ink/60';
@endphp

<div
    class="flex items-center justify-between px-10 py-2.5 font-mono text-[11px] tracking-wide {{ $classes }}"
>
    <span>{{ $left }}</span>
    <span>{{ $right }}</span>
</div>
