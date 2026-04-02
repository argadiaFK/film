@extends('layouts.app')

@section('title', $series->title . ' - S' . $currentEpisode->season_number . 'E' . $currentEpisode->episode_number)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm">
            <a href="/" class="text-gray-400 hover:text-white">Home</a>
            <span class="mx-2 text-gray-600">/</span>
            <a href="/series/{{ $series->slug }}" class="text-gray-400 hover:text-white">{{ $series->title }}</a>
            <span class="mx-2 text-gray-600">/</span>
            <span class="text-primary">Episode {{ $currentEpisode->episode_number }}</span>
        </nav>

        <!-- Title -->
        <h1 class="text-2xl md:text-3xl font-bold mb-2">{{ $series->title }}</h1>
        <p class="text-gray-400 mb-6">Season {{ $currentEpisode->season_number }} • Episode
            {{ $currentEpisode->episode_number }}:
            {{ $currentEpisode->title }}
        </p>

        <!-- Ad: Before Player -->
        @php $adBeforePlayer = \App\Models\Ad::getBySlot('before_player'); @endphp
        @if($adBeforePlayer)
            <div class="ad-container mb-6">{!! $adBeforePlayer !!}</div>
        @endif

        <!-- Player -->
        @if($currentEpisode->streamingSources->count())
            <section x-data="{ activeSource: 0 }" class="mb-8">
                <!-- Source Tabs -->
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($currentEpisode->streamingSources as $index => $source)
                        <button @click="activeSource = {{ $index }}"
                            :class="activeSource === {{ $index }} ? 'bg-primary text-dark-900' : 'bg-dark-700 hover:bg-dark-600'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition">
                            {{ $source->name ?? 'Server ' . ($index + 1) }}
                        </button>
                    @endforeach
                </div>

                <!-- Player -->
                @foreach($currentEpisode->streamingSources as $index => $source)
                    <div x-show="activeSource === {{ $index }}" class="aspect-video bg-dark-800 rounded-lg overflow-hidden">
                        <iframe src="{{ $source->url }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                    </div>
                @endforeach
            </section>
        @endif

        <!-- Ad: After Player -->
        @php $adAfterPlayer = \App\Models\Ad::getBySlot('after_player'); @endphp
        @if($adAfterPlayer)
            <div class="ad-container mb-6">{!! $adAfterPlayer !!}</div>
        @endif

        <!-- Ad: Before Download -->
        @php $adBeforeDownload = \App\Models\Ad::getBySlot('before_download'); @endphp
        @if($adBeforeDownload)
            <div class="ad-container mb-6">{!! $adBeforeDownload !!}</div>
        @endif

        <!-- Download Links -->
        @if($currentEpisode->downloadLinks->count())
            <section class="mb-8">
                <h2 class="text-lg font-bold mb-4">Download Episode</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($currentEpisode->downloadLinks as $link)
                        <a href="{{ $link->url }}" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 rounded text-sm font-medium transition">
                            {{ $link->quality }} {{ $link->name ? '- ' . $link->name : '' }}
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Ad: After Download -->
        @php $adAfterDownload = \App\Models\Ad::getBySlot('after_download'); @endphp
        @if($adAfterDownload)
            <div class="ad-container mb-6">{!! $adAfterDownload !!}</div>
        @endif

        <!-- Episode Navigation -->
        <div class="flex justify-between items-center mb-8 p-4 bg-dark-800 rounded-lg">
            @php
                $prevEpisode = $episodes->filter(
                    fn($e) =>
                    ($e->season_number == $currentEpisode->season_number && $e->episode_number < $currentEpisode->episode_number) ||
                    ($e->season_number < $currentEpisode->season_number)
                )->last();

                $nextEpisode = $episodes->filter(
                    fn($e) =>
                    ($e->season_number == $currentEpisode->season_number && $e->episode_number > $currentEpisode->episode_number) ||
                    ($e->season_number > $currentEpisode->season_number)
                )->first();
            @endphp

            @if($prevEpisode)
                <a href="/series/{{ $series->slug }}/season/{{ $prevEpisode->season_number }}/episode/{{ $prevEpisode->episode_number }}"
                    class="flex items-center gap-2 text-sm hover:text-primary transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous Episode
                </a>
            @else
                <span></span>
            @endif

            @if($nextEpisode)
                <a href="/series/{{ $series->slug }}/season/{{ $nextEpisode->season_number }}/episode/{{ $nextEpisode->episode_number }}"
                    class="flex items-center gap-2 text-sm hover:text-primary transition">
                    Next Episode
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span></span>
            @endif
        </div>

        <!-- Episode List -->
        <section>
            <h2 class="text-lg font-bold mb-4">All Episodes</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
                @foreach($episodes as $ep)
                    <a href="/series/{{ $series->slug }}/season/{{ $ep->season_number }}/episode/{{ $ep->episode_number }}"
                        class="p-3 rounded text-center text-sm transition {{ $ep->id === $currentEpisode->id ? 'bg-primary text-dark-900 font-bold' : 'bg-dark-700 hover:bg-dark-600' }}">
                        Ep {{ $ep->episode_number }}
                    </a>
                @endforeach
            </div>
        </section>

        <!-- Comments Section -->
        <x-comment-section :comments="$currentEpisode->comments" :episode-id="$currentEpisode->id" />
    </div>
@endsection