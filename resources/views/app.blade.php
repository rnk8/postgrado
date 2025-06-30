<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="cupcake">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Meta tags para SEO y accesibilidad -->
    <meta name="description" content="Sistema de Gestión de Postgrado - Universidad Autónoma Gabriel René Moreno">
    <meta name="author" content="Dirección de Postgrado UAGRM">
    <meta name="theme-color" content="#1f2937">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ Vite::asset('resources/images/favicon.svg') }}">
    
    <!-- Título dinámico de la página -->
    <title inertia>{{ config('app.name', 'Sistema Postgrado UAGRM') }}</title>
    
    <!-- Fonts para mejor tipografía -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts de Vite para el bundling de assets -->
    @routes
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    
    <!-- Head adicional de Inertia para meta tags dinámicos -->
    @inertiaHead
</head>
<body class="font-inter antialiased">
    <!-- Contenedor principal donde Vue montará la aplicación -->
    @inertia
    
    <!-- Scripts adicionales para mejoras de UX -->
    <script>
        // DaisyUI maneja el tema mediante el atributo data-theme.
        // Guardamos la preferencia en localStorage como 'theme'.
        (function() {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const savedTheme = localStorage.getItem('theme');
            const theme = savedTheme || 'cupcake';
            document.documentElement.setAttribute('data-theme', theme);
            // Exponer función global para cambiar tema desde Vue
            window.setTheme = function(t) {
                document.documentElement.setAttribute('data-theme', t);
                localStorage.setItem('theme', t);
            };
        })();
    </script>
</body>
</html> 