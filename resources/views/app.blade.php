<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    
    {{-- Self-hosted fonts (resources/css/fonts.css): fetch the Latin files before the CSS asks for them. --}}
    <link rel="preload" href="/fonts/inter-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/oswald-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" as="image" href="/images/dekifit-logo-96.webp" imagesrcset="/images/dekifit-logo-96.webp 96w, /images/dekifit-logo-192.webp 192w" imagesizes="44px" fetchpriority="high">

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Person",
        "name": "Dejan Badzukov",
        "alternateName": "DekiFit",
        "jobTitle": "Bodybuilding & Nutrition Coach",
        "url": "{{ url('/') }}",
        "image": "{{ url('/images/dekifit-logo-512.png') }}",
        "sameAs": [
            "https://www.instagram.com/ddbadzukov3_",
            "https://www.facebook.com/DejanBadzukov"
        ]
    }
    </script>

    @inertiaHead
    {{-- Preload this page's own chunk too, so it downloads alongside app.js instead of after it. --}}
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
</head>
<body class="antialiased">
    @inertia
</body>
</html>
