@extends('layouts.app')

@php
    $seo = $series->seoMeta;
    $seoTitle = $seo?->title ?: $series->title;
    $seoDesc = $seo?->description ?: Str::limit($series->synopsis, 160);
    $seoKeywords = $seo?->keywords ?: '';
    $seoOgImage = $seo?->og_image ?: $series->poster;
@endphp
@section('title', $seoTitle)
@section('meta_description', $seoDesc)

@section('seo_meta')
    @if($seoKeywords)
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    @if($seo?->canonical_url)
        <link rel="canonical" href="{{ $seo->canonical_url }}">
    @endif
    @if($seo?->no_index || $seo?->no_follow)
        <meta name="robots" content="{{ $seo->no_index ? 'noindex' : 'index' }}, {{ $seo->no_follow ? 'nofollow' : 'follow' }}">
    @endif
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDesc }}">
    <meta property="og:type" content="video.tv_show">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($seoOgImage)
        <meta property="og:image" content="{{ Storage::url($seoOgImage) }}">
    @endif
@endsection

@section('content')
    <!-- Backdrop Header -->
    <div class="relative h-[40vh] md:h-[50vh]">
        <div class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('{{ $series->backdrop ? Storage::url($series->backdrop) : ($series->poster ? Storage::url($series->poster) : '') }}')">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-dark-900/60 to-dark-900/30"></div>
    </div>

    <div class="container mx-auto px-4 -mt-32 relative z-10">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Poster -->
            <div class="flex-shrink-0 w-48 md:w-64">
                <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-2xl bg-dark-700">
                    @if($series->poster)
                        <img src="{{ Storage::url($series->poster) }}" alt="{{ $series->title }}"
                            class="w-full h-full object-cover">
                    @endif
                </div>

                <!-- Ad: Sidebar Bottom -->
                @php $adSidebarBottom = \App\Models\Ad::getBySlot('sidebar_bottom'); @endphp
                @if($adSidebarBottom)
                    <div class="ad-container mt-6">{!! $adSidebarBottom !!}</div>
                @endif
            </div>

            <!-- Info -->
            <div class="flex-1">
                <span class="inline-block px-2 py-1 bg-blue-600 text-xs font-medium rounded mb-2">SERIES</span>
                <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $series->title }}</h1>

                @if($series->original_title && $series->original_title !== $series->title)
                    <p class="text-gray-400 mb-4">{{ $series->original_title }}</p>
                @endif

                <div class="flex flex-wrap items-center gap-3 text-sm mb-6">
                    @if($series->rating)
                        <span class="flex items-center gap-1 px-3 py-1 bg-primary text-dark-900 rounded font-bold">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            {{ number_format($series->rating, 1) }}
                        </span>
                    @endif
                    @if($series->year)
                        <span class="px-3 py-1 bg-dark-700 rounded">{{ $series->year }}</span>
                    @endif
                    <span class="px-3 py-1 bg-dark-700 rounded">{{ $series->episodes->count() }} Episodes</span>
                    @if($series->status)
                        <span class="px-3 py-1 bg-green-600 rounded font-medium">{{ ucfirst($series->status) }}</span>
                    @endif
                </div>

                <!-- Genres -->
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach($series->genres as $genre)
                        <a href="/browse?genre={{ $genre->slug }}"
                            class="px-3 py-1 bg-dark-700 hover:bg-dark-600 rounded text-sm transition">
                            {{ $genre->name }}
                        </a>
                    @endforeach
                </div>

                <!-- Synopsis -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-2">Synopsis</h3>
                    <p class="text-gray-300 leading-relaxed">{{ $series->synopsis }}</p>
                </div>
            </div>
        </div>

        <!-- Ad: Before Player -->
        @php $adBeforePlayer = \App\Models\Ad::getBySlot('before_player'); @endphp
        @if($adBeforePlayer)
            <div class="ad-container mt-12">{!! $adBeforePlayer !!}</div>
        @endif

        <!-- Episodes List -->
        <section class="mt-12" x-data="{ activeSeason: {{ $seasons->keys()->first() ?? 1 }} }">
            <h2 class="text-xl font-bold mb-4">Episode List</h2>

            <!-- Season Tabs -->
            @if($seasons->count() > 1)
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach($seasons->keys() as $season)
                        <button @click="activeSeason = {{ $season }}"
                            :class="activeSeason === {{ $season }} ? 'bg-primary text-dark-900' : 'bg-dark-700 hover:bg-dark-600'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition">
                            Season {{ $season }}
                        </button>
                    @endforeach
                </div>
            @endif

            <!-- Episodes -->
            @foreach($seasons as $season => $episodes)
                <div x-show="activeSeason === {{ $season }}" class="space-y-3">
                    @foreach($episodes as $episode)
                        <a href="/series/{{ $series->slug }}/season/{{ $episode->season_number }}/episode/{{ $episode->episode_number }}"
                            class="block p-4 bg-dark-800 hover:bg-dark-700 rounded-lg transition group">
                            <div class="flex items-center gap-4">
                                <!-- Thumbnail -->
                                <div class="flex-shrink-0 w-32 aspect-video bg-dark-700 rounded overflow-hidden">
                                    @if($episode->thumbnail)
                                        <img src="{{ Storage::url($episode->thumbnail) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-600">
                                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium group-hover:text-primary transition">
                                        Episode {{ $episode->episode_number }}: {{ $episode->title }}
                                    </h4>
                                    @if($episode->synopsis)
                                        <p class="text-sm text-gray-400 line-clamp-2 mt-1">{{ $episode->synopsis }}</p>
                                    @endif
                                </div>

                                <!-- Play Icon -->
                                <div class="flex-shrink-0 text-gray-500 group-hover:text-primary transition">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </section>

        <!-- Ad: Between Episodes -->
        @php $adBetweenEp = \App\Models\Ad::getBySlot('between_episodes'); @endphp
        @if($adBetweenEp)
            <div class="ad-container mt-8">{!! $adBetweenEp !!}</div>
        @endif

        <!-- Related Series -->
        @if($relatedSeries->count())
            <section class="mt-12">
                <h2 class="text-xl font-bold mb-4">Related Series</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($relatedSeries as $related)
                        <x-series-card :series="$related" />
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Comments Section -->
        <x-comment-section :comments="$series->comments" :series-id="$series->id" />
    </div>
@endsection