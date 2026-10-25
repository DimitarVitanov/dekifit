<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="/images/dekifit-logo.webp" as="image" fetchpriority="high">

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
    @vite(['resources/js/app.js'])
</head>
<body class="antialiased">
    @inertia
</body>
</html>
