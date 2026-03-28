@extends('layouts.app')

@section('title', 'Search: ' . $query)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Search Box -->
        <div class="max-w-2xl mx-auto mb-12">
            <form action="/search" method="GET">
                <div class="relative">
                    <input type="text" name="q" value="{{ $query }}" placeholder="Search movies or TV shows..."
                        class="w-full px-6 py-4 pl-14 bg-dark-800 border border-dark-600 rounded-xl text-lg focus:outline-none focus:border-primary"
                        autofocus>
                    <svg class="w-6 h-6 absolute left-5 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>
        </div>

        @if(strlen($query) >= 2)
            <!-- Results -->
            <h1 class="text-2xl font-bold mb-8">
                Search results for "<span class="text-primary">{{ $query }}</span>"
            </h1>

            @if($films->count())
                <section class="mb-12">
                    <h2 class="text-xl font-semibold mb-4">Movies ({{ $films->count() }})</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($films as $film)
                            <x-film-card :film="$film" />
                        @endforeach
                    </div>
                </section>
            @endif

            @if($series->count())
                <section>
                    <h2 class="text-xl font-semibold mb-4">Series ({{ $series->count() }})</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($series as $s)
                            <x-series-card :series="$s" />
                        @endforeach
                    </div>
                </section>
            @endif

            @if($films->isEmpty() && $series->isEmpty())
                <div class="text-center py-16">
                    <svg class="w-24 h-24 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-xl text-gray-400">No results for "{{ $query }}"</p>
                    <p class="text-sm text-gray-500 mt-2">Try different keywords</p>
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <svg class="w-24 h-24 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <p class="text-xl text-gray-400">Enter a search keyword</p>
                <p class="text-sm text-gray-500 mt-2">Minimum 2 characters</p>
            </div>
        @endif
    </div>
@endsection