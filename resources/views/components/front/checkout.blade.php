@props(['summary', 'prefill'])

<form
    method="POST"
    action="{{ route('front.checkout.store') }}"
    data-component="front/checkout"
    class="grid grid-cols-1 gap-10 lg:grid-cols-[1fr_360px]"
>
    @csrf

    <div class="space-y-10">
        @if ($errors->any())
            <div class="border border-madder/30 bg-madder/5 px-4 py-3 text-sm text-madder">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <x-front.checkout.contact-fields :prefill="$prefill" />
        <x-front.checkout.delivery />
        <x-front.checkout.payment />

        @guest
            <x-front.checkout.account-checkbox />
        @endguest
    </div>

    <x-front.checkout.review :summary="$summary" />
</form>
