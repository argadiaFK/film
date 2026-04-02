@extends('layouts.app')

@php $seo = $film->effective_seo; @endphp
@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@section('seo_meta')
    <meta name="keywords" content="{{ $seo['keywords'] }}">
    @if($seo['canonical_url'])
        <link rel="canonical" href="{{ $seo['canonical_url'] }}">
    @endif
    @if($seo['no_index'] || $seo['no_follow'])
        <meta name="robots" content="{{ $seo['no_index'] ? 'noindex' : 'index' }}, {{ $seo['no_follow'] ? 'nofollow' : 'follow' }}">
    @endif
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:type" content="video.movie">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($seo['og_image'])
        <meta property="og:image" content="{{ Storage::url($seo['og_image']) }}">
    @endif
@endsection

@section('content')
    <!-- Backdrop Header -->
    <div class="relative h-[40vh] md:h-[50vh]">
        <div class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('{{ $film->backdrop ? Storage::url($film->backdrop) : ($film->poster ? Storage::url($film->poster) : '') }}')">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-dark-900/60 to-dark-900/30"></div>
    </div>

    <div class="container mx-auto px-4 -mt-32 relative z-10">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Poster -->
            <div class="flex-shrink-0 w-48 md:w-64">
                <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-2xl bg-dark-700">
                    @if($film->poster)
                        <img src="{{ Storage::url($film->poster) }}" alt="{{ $film->title }}"
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
                <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $film->title }}</h1>

                @if($film->original_title && $film->original_title !== $film->title)
                    <p class="text-gray-400 mb-4">{{ $film->original_title }}</p>
                @endif

                <div class="flex flex-wrap items-center gap-3 text-sm mb-6">
                    @if($film->rating)
                        <span class="flex items-center gap-1 px-3 py-1 bg-primary text-dark-900 rounded font-bold">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            {{ number_format($film->rating, 1) }}
                        </span>
                    @endif
                    @if($film->year)
                        <span class="px-3 py-1 bg-dark-700 rounded">{{ $film->year }}</span>
                    @endif
                    @if($film->duration_minutes)
                        <span class="px-3 py-1 bg-dark-700 rounded">{{ $film->duration_minutes }} min</span>
                    @endif
                    @if($film->quality)
                        <span class="px-3 py-1 bg-blue-600 rounded font-medium">{{ $film->quality }}</span>
                    @endif
                </div>

                <!-- Genres -->
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach($film->genres as $genre)
                        <a href="/browse?genre={{ $genre->slug }}"
                            class="px-3 py-1 bg-dark-700 hover:bg-dark-600 rounded text-sm transition">
                            {{ $genre->name }}
                        </a>
                    @endforeach
                </div>

                <!-- Synopsis -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-2">Synopsis</h3>
                    <p class="text-gray-300 leading-relaxed">{{ $film->synopsis }}</p>
                </div>

                <!-- Countries -->
                @if($film->countries->count())
                    <p class="text-sm text-gray-400 mb-4">
                        <strong class="text-white">Country:</strong>
                        {{ $film->countries->pluck('name')->join(', ') }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Ad: Before Player -->
        @php $adBeforePlayer = \App\Models\Ad::getBySlot('before_player'); @endphp
        @if($adBeforePlayer)
            <div class="ad-container mt-12">{!! $adBeforePlayer !!}</div>
        @endif

        <!-- Streaming Player -->
        @if($film->streamingSources->count())
            <section class="mt-12" x-data="{ activeSource: 0 }">
                <h2 class="text-xl font-bold mb-4">Watch Streaming</h2>

                <!-- Source Tabs -->
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($film->streamingSources as $index => $source)
                        <button @click="activeSource = {{ $index }}"
                            :class="activeSource === {{ $index }} ? 'bg-primary text-dark-900' : 'bg-dark-700 hover:bg-dark-600'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition">
                            {{ $source->name ?? 'Server ' . ($index + 1) }}
                        </button>
                    @endforeach
                </div>

                <!-- Player -->
                @foreach($film->streamingSources as $index => $source)
                    <div x-show="activeSource === {{ $index }}" class="aspect-video bg-dark-800 rounded-lg overflow-hidden">
                        <iframe src="{{ $source->url }}" class="w-full h-full" frameborder="0" allowfullscreen
                            loading="lazy"></iframe>
                    </div>
                @endforeach
            </section>
        @endif

        <!-- Ad: After Player -->
        @php $adAfterPlayer = \App\Models\Ad::getBySlot('after_player'); @endphp
        @if($adAfterPlayer)
            <div class="ad-container mt-8">{!! $adAfterPlayer !!}</div>
        @endif

        <!-- Ad: Before Download -->
        @php $adBeforeDownload = \App\Models\Ad::getBySlot('before_download'); @endphp
        @if($adBeforeDownload)
            <div class="ad-container mt-8">{!! $adBeforeDownload !!}</div>
        @endif

        <!-- Download Links -->
        @if($film->downloadLinks->count())
            <section class="mt-12">
                <h2 class="text-xl font-bold mb-4">Download</h2>
                <div class="bg-dark-800 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-dark-700">
                            <tr>
                                <th class="text-left py-3 px-4 font-medium">Quality</th>
                                <th class="text-left py-3 px-4 font-medium">Size</th>
                                <th class="text-right py-3 px-4 font-medium">Download</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dark-700">
                            @foreach($film->downloadLinks as $link)
                                <tr class="hover:bg-dark-700/50">
                                    <td class="py-3 px-4 font-medium">{{ $link->quality }}</td>
                                    <td class="py-3 px-4 text-gray-400">{{ $link->size ?? '-' }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('download.track', $link) }}" target="_blank"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 rounded text-white text-xs font-medium transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            {{ $link->name ?? 'Download' }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <!-- Ad: After Download -->
        @php $adAfterDownload = \App\Models\Ad::getBySlot('after_download'); @endphp
        @if($adAfterDownload)
            <div class="ad-container mt-8">{!! $adAfterDownload !!}</div>
        @endif

        <!-- Related Films -->
        @if($relatedFilms->count())
            <section class="mt-12">
                <h2 class="text-xl font-bold mb-4">Related Movies</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($relatedFilms as $related)
                        <x-film-card :film="$related" />
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Comments Section -->
        <x-comment-section :comments="$film->comments" :film-id="$film->id" />
    </div>
@endsection