<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VetFlow - Gestão que cuida</title>
    
    <!-- Esta linha conecta o Laravel ao Vue e ao Tailwind -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    
    <!-- O Vue vai desenhar a tela dentro desta div -->
    <div id="app"></div>

</body>
</html>