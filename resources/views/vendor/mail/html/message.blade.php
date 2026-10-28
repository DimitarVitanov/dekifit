@props(['preheader' => null])
<x-mail::layout :preheader="$preheader">
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{ config('app.name') }}
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
<span class="tagline">Train. Focus. Transform.</span>

[Website]({{ config('app.url') }}) &nbsp;·&nbsp; [Instagram]({{ config('dekifit.instagram_url') }}) &nbsp;·&nbsp; [Facebook]({{ config('dekifit.facebook_url') }}) &nbsp;·&nbsp; [WhatsApp]({{ config('dekifit.whatsapp_url') }})

© {{ date('Y') }} {{ config('app.name') }} · Coach Dejan Badzukov · Strumica, North Macedonia
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
