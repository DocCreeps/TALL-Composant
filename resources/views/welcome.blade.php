<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mes Composants Laravel</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-gray-100 min-h-screen">
<div class="container mx-auto p-6">
    <h1 class="text-4xl font-extrabold text-gray-900 mb-10 text-center">Mes Composants</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <a href="{{ route('link-preview') }}" class="block">
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-blue-700 mb-2">Aperçu de Lien</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Récupérez et affichez les métadonnées (titre, description, image) de n'importe quel lien. Idéal pour les partages sociaux !
                    </p>
                </div>
                <div class="bg-blue-50 p-4 text-blue-600 text-sm font-semibold">
                    Voir le composant &rarr;
                </div>
            </div>
        </a>

        {{--
        <a href="{{ route('another-component') }}" class="block">
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-green-700 mb-2">Mon Super Formulaire</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Un formulaire interactif pour collecter des informations utilisateurs avec validation en temps réel.
                    </p>
                </div>
                <div class="bg-green-50 p-4 text-green-600 text-sm font-semibold">
                    Voir le composant &rarr;
                </div>
            </div>
        </a>
        --}}

    </div>
</div>

@livewireScripts
</body>
</html>
