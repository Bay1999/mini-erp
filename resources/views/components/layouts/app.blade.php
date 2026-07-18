@props(['breadcrumbs' => null, 'title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title . ' | ' . config('app.name', 'Laravel') : config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        <style>
            .toastify {
                border-radius: 0.5rem !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50">
        <div class="flex h-screen overflow-hidden" x-data="{ sidebarCollapsed: false }">
            <x-sidebar />

            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <x-header :breadcrumbs="$breadcrumbs" />

                <main class="flex-1 overflow-y-auto bg-gray-50/50">
                    <div class="mx-auto p-8 w-full">
                        @isset($header)
                            <div class="mb-6">
                                {{ $header }}
                            </div>
                        @endisset
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        @stack('scripts')
        @if(session('success'))
            <script>
                $(document).ready(function() {
                    Toastify({
                        text: "{{ session('success') }}",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#06b6d4",
                    }).showToast();
                });
            </script>
        @endif
        @if(session('error'))
            <script>
                $(document).ready(function() {
                    Toastify({
                        text: "{{ session('error') }}",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#ef4444",
                    }).showToast();
                });
            </script>
        @endif
    </body>
</html>
