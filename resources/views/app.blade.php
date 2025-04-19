<!DOCTYPE html>
<html lang="en" class="h-full font-sans antialiased">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ \Illuminate\Support\Facades\Vite::useBuildDirectory('vendor/ninshiki')
->useHotFile('vendor/ninshiki/ninshiki.hot')
->asset('resources/assets/logo.png') }}">
    <meta name="robots" content="noindex">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    @routes

    {{-- Inertia --}}
    <script
        src="https://cdnjs.cloudflare.com/polyfill/v3/polyfill.min.js?features=smoothscroll,NodeList.prototype.forEach,Promise,Object.values,Object.assign"
        defer></script>

    <script>
        if (localStorage.ninshikiTheme === 'dark' || (!('ninshikiTheme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    {{ Vite::useBuildDirectory('vendor/ninshiki')
->useHotFile('vendor/ninshiki/ninshiki.hot')
->withEntryPoints(['resources/js/app.js', 'resources/css/ninshiki.css']) }}
    @inertiaHead
</head>
<body class=" text-sm font-medium h-screen max-w-full text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800">
@inertia

<!-- Build Ninshiki Instance -->
<script type="module">

    const config = @json(\MarJose123\Ninshiki\Ninshiki::jsonVariables(request()));
    window.NinshikiApp = createNinshikiApp(config)
    NinshikiApp.engineStart()
</script>
</body>
</html>
