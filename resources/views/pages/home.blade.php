@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->
    @if($featuredContent->count())
        <section class="relative h-[60vh] md:h-[70vh] overflow-hidden">
            <div x-data="{ current: 0 }"
                x-init="setInterval(() => current = (current + 1) % {{ $featuredContent->count() }}, 5000)" class="h-full">
                @foreach($featuredContent as $index => $content)
                    <div x-show="current === {{ $index }}" x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0">
                        <!-- Backdrop -->
                        <div class="absolute inset-0 bg-cover bg-center"
                            style="background-image: url('{{ $content->backdrop ? Storage::url($content->backdrop) : ($content->poster ? Storage::url($content->poster) : '') }}')">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-dark-900 via-dark-900/80 to-transparent"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-transparent to-transparent"></div>

                        <!-- Content -->
                        <div class="relative h-full container mx-auto px-4 flex items-center">
                            <div class="max-w-2xl">
                                <div class="flex gap-2 mb-4">
                                    <span
                                        class="inline-block px-3 py-1 bg-primary text-dark-900 text-xs font-bold rounded">FEATURED</span>
                                    @if($content->content_type === 'series')
                                        <span
                                            class="inline-block px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded">SERIES</span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-red-600 text-white text-xs font-bold rounded">MOVIE</span>
                                    @endif
                                </div>
                                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4">{{ $content->title }}</h1>
                                <div class="flex items-center gap-4 text-sm text-gray-300 mb-4">
                                    @if($content->year)
                                        <span>{{ $content->year }}</span>
                                    @endif
                                    @if($content->content_type === 'film' && $content->duration_minutes)
                                        <span>{{ $content->duration_minutes }} min</span>
                                    @endif
                                    @if($content->content_type === 'series')
                                        <span>{{ $content->episodes_count ?? $content->episodes->count() }} Episodes</span>
                                    @endif
                                    @if($content->rating)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            {{ number_format($content->rating, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-gray-300 line-clamp-3 mb-6">{{ $content->synopsis }}</p>
                                <div class="flex gap-4">
                                    <a href="/{{ $content->content_type }}/{{ $content->slug }}"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-dark-900 font-semibold rounded-lg hover:bg-primary/90 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                                        </svg>
                                        Watch Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Slider Dots -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2" x-data>
                @foreach($featuredContent as $index => $content)
                    <button @click="$dispatch('set-slide', {{ $index }})"
                        class="w-2 h-2 rounded-full bg-white/50 hover:bg-primary transition"></button>
                @endforeach
            </div>
        </section>
    @endif

    <div class="container mx-auto px-4 py-8 space-y-12">
        <!-- Latest Films -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold">Latest Movies</h2>
                <a href="/browse?type=film" class="text-primary hover:underline text-sm">View All →</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($latestFilms as $film)
                    <x-film-card :film="$film" />
                @endforeach
            </div>
        </section>

        <!-- Latest Series -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold">Latest Series</h2>
                <a href="/browse?type=series" class="text-primary hover:underline text-sm">View All →</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($latestSeries as $series)
                    <x-series-card :series="$series" />
                @endforeach
            </div>
        </section>

        <!-- Genres -->
        <section>
            <h2 class="text-2xl font-bold mb-6">Popular Genres</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4">
                @foreach($genres as $genre)
                    <a href="/browse?genre={{ $genre->slug }}"
                        class="block p-4 bg-dark-800 rounded-lg text-center hover:bg-dark-700 transition group">
                        <span class="text-sm font-medium group-hover:text-primary transition">{{ $genre->name }}</span>
                        <span class="block text-xs text-gray-500 mt-1">{{ $genre->films_count }} Movies</span>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
@endsection