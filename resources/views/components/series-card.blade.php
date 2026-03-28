@props(['series'])

<a href="/series/{{ $series->slug }}" class="group block">
    <div class="relative aspect-[2/3] rounded-lg overflow-hidden bg-dark-700">
        @if($series->poster)
            <img src="{{ Storage::url($series->poster) }}" alt="{{ $series->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-600">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                </svg>
            </div>
        @endif

        <!-- Series Badge -->
        <div class="absolute top-2 left-2 px-2 py-1 bg-blue-600/90 rounded text-xs font-medium">
            Series
        </div>

        <!-- Rating Badge -->
        @if($series->rating)
            <div class="absolute top-2 right-2 px-2 py-1 bg-primary/90 rounded text-xs font-bold text-dark-900">
                {{ number_format($series->rating, 1) }}
            </div>
        @endif

        <!-- Episodes Count -->
        <div class="absolute bottom-2 left-2 px-2 py-1 bg-dark-800/90 rounded text-xs">
            {{ $series->episodes_count ?? $series->episodes->count() }} Eps
        </div>
    </div>

    <div class="mt-2">
        <h3 class="font-medium text-sm line-clamp-2 group-hover:text-primary transition">
            {{ $series->title }}
        </h3>
        <p class="text-xs text-gray-500 mt-1">
            {{ $series->year ?? '' }}
            @if($series->genres->count())
                • {{ $series->genres->first()->name }}
            @endif
        </p>
    </div>
</a>