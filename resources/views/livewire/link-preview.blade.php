<div class="max-w-xl mx-auto p-4 bg-white shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-4 text-center">Aperçu de Lien</h2>

    <form wire:submit.prevent="getLinkPreview" class="mb-6"
          x-data="{ urlInput: @entangle('url'), showError: false }" {{-- REMOVED .defer HERE --}}
          x-init="$watch('urlInput', value => {
          // ... (rest of your x-init logic remains the same)
          if (value === '') {
              showError = false;
          } else {
              showError = !/^https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/i.test(value);
          }
      })">
        <div class="flex items-center border-b border-b-2 border-blue-500 py-2">
            {{-- Utiliser x-model pour la liaison Alpine.js --}}
            <input x-model="urlInput"
                   class="appearance-none bg-transparent border-none w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none"
                   :class="{ 'border-red-500': showError }"
                   type="text"
                   placeholder="Collez votre lien ici"
                   aria-label="Lien à prévisualiser">
            <button class="flex-shrink-0 bg-blue-500 hover:bg-blue-700 border-blue-500 hover:border-blue-700 text-sm border-4 text-white py-1 px-2 rounded"
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="getLinkPreview"
                    :disabled="showError || urlInput === ''"> {{-- Désactive si erreur ou champ vide --}}
                <span wire:loading.remove wire:target="getLinkPreview">Prévisualiser</span>
                <span wire:loading wire:target="getLinkPreview">Chargement...</span>
            </button>
        </div>
        {{-- Afficher l'erreur Alpine.js si présente --}}
        <div x-show="showError" class="text-red-500 text-sm mt-2">
            Veuillez entrer une URL valide (doit commencer par http:// ou https://).
        </div>
        {{-- Afficher l'erreur Livewire/Laravel si présente --}}
        @error('url') <span class="text-red-500 text-sm mt-2">{{ $message }}</span> @enderror
    </form>

    @if ($previewing)
        <div class="flex justify-center items-center h-32">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
        </div>
    @elseif ($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Erreur!</strong>
            <span class="block sm:inline">{{ $error }}</span>
        </div>
    @elseif ($title || $description || (!empty($image) && $image !== $defaultImage))
        {{-- Cette condition assure que la carte n'est affichée que si:
             - Il y a un titre OU une description
             - OU s'il y a une image ET que cette image n'est PAS l'image par défaut
             Ceci évite d'afficher une carte vide avec juste l'image par défaut.
        --}}
        <div class="border border-gray-300 rounded-lg overflow-hidden shadow-md flex flex-col sm:flex-row cursor-pointer transition-all duration-300 hover:shadow-lg">
            <div class="sm:w-1/3 flex-shrink-0">
                <img src="{{ $image }}" alt="{{ $title ?? 'Image' }}" class="w-full h-48 sm:h-full object-cover">
            </div>
            <div class="p-4 sm:p-6 flex-grow">
                @if ($title)
                    <h3 class="font-bold text-lg text-gray-900 mb-2">{{ $title }}</h3>
                @endif
                @if ($description)
                    <p class="text-gray-700 text-sm mb-2 line-clamp-3">{{ $description }}</p>
                @endif
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline text-sm break-all">
                    {{ $url }}
                </a>
            </div>
        </div>
    @else
        {{-- Ce bloc s'affiche s'il n'y a pas de prévisualisation, pas d'erreur, et que les données sont vides --}}
        <div class="text-center text-gray-500 py-8">
            Collez un lien dans le champ ci-dessus pour voir un aperçu.
        </div>
    @endif
</div>
