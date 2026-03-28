@props(['film'])

<a href="/film/{{ $film->slug }}" class="group block">
    <div class="relative aspect-[2/3] rounded-lg overflow-hidden bg-dark-700">
        @if($film->poster)
            <img src="{{ Storage::url($film->poster) }}" alt="{{ $film->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-600">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                </svg>
            </div>
        @endif

        <!-- Overlay -->
        <div
            class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition">
            <div class="absolute bottom-0 left-0 right-0 p-3">
                <span class="inline-flex items-center gap-1 text-xs text-primary">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                    </svg>
                    Watch Now
                </span>
            </div>
        </div>

        <!-- Rating Badge -->
        @if($film->rating)
            <div class="absolute top-2 right-2 px-2 py-1 bg-primary/90 rounded text-xs font-bold text-dark-900">
                {{ number_format($film->rating, 1) }}
            </div>
        @endif

        <!-- Quality Badge -->
        @if($film->quality)
            <div class="absolute top-2 left-2 px-2 py-1 bg-dark-800/90 rounded text-xs font-medium">
                {{ $film->quality }}
            </div>
        @endif
    </div>

    <div class="mt-2">
        <h3 class="font-medium text-sm line-clamp-2 group-hover:text-primary transition">
            {{ $film->title }}
        </h3>
        <p class="text-xs text-gray-500 mt-1">
            {{ $film->year ?? '' }}
            @if($film->genres->count())
                • {{ $film->genres->first()->name }}
            @endif
        </p>
    </div>
</a>