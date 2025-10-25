<!-- PWA Meta Tags -->
<meta name="theme-color" content="#4F46E5">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Zubaidi Accountant">

<!-- Web App Manifest -->
<link rel="manifest" href="{{ asset('manifest.json') }}">

<!-- PWA Icons -->
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/logo-192.png') }}">
<link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/logo-512.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/logo-192.png') }}">

@yield('css')
@vite(['resources/scss/icons.scss','resources/scss/app.scss'])
@vite(['resources/js/config.js'])
