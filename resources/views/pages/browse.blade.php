@extends('layouts.app')

@section('title', 'Browse')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Browse</h1>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <aside class="lg:w-64 flex-shrink-0">
                <!-- Ad: Sidebar Top -->
                @php $adSidebarTop = \App\Models\Ad::getBySlot('sidebar_top'); @endphp
                @if($adSidebarTop)
                    <div class="ad-container mb-6">{!! $adSidebarTop !!}</div>
                @endif

                <form action="/browse" method="GET" class="space-y-6">
                    <!-- Type -->
                    <div>
                        <h3 class="font-semibold mb-3">Type</h3>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="all" {{ $type === 'all' ? 'checked' : '' }}
                                    class="text-primary">
                                <span>All</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="film" {{ $type === 'film' ? 'checked' : '' }}
                                    class="text-primary">
                                <span>Movies</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="series" {{ $type === 'series' ? 'checked' : '' }}
                                    class="text-primary">
                                <span>Series</span>
                            </label>
                        </div>
                    </div>

                    <!-- Genre -->
                    <div>
                        <h3 class="font-semibold mb-3">Genre</h3>
                        <select name="genre" class="w-full px-3 py-2 bg-dark-700 border border-dark-600 rounded-lg text-sm">
                            <option value="">All Genres</option>
                            @foreach($genres as $genre)
                                <option value="{{ $genre->slug }}" {{ $genreSlug === $genre->slug ? 'selected' : '' }}>
                                    {{ $genre->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Country -->
                    <div>
                        <h3 class="font-semibold mb-3">Country</h3>
                        <select name="country"
                            class="w-full px-3 py-2 bg-dark-700 border border-dark-600 rounded-lg text-sm">
                            <option value="">All Countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->code }}" {{ $countrySlug === $country->code ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Year -->
                    <div>
                        <h3 class="font-semibold mb-3">Year</h3>
                        <select name="year" class="w-full px-3 py-2 bg-dark-700 border border-dark-600 rounded-lg text-sm">
                            <option value="">All Years</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <h3 class="font-semibold mb-3">Sort By</h3>
                        <select name="sort" class="w-full px-3 py-2 bg-dark-700 border border-dark-600 rounded-lg text-sm">
                            <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Newest Added</option>
                            <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest Added</option>
                            <option value="year_desc" {{ $sort === 'year_desc' ? 'selected' : '' }}>Newest Release Year</option>
                            <option value="year_asc" {{ $sort === 'year_asc' ? 'selected' : '' }}>Oldest Release Year</option>
                            <option value="title" {{ $sort === 'title' ? 'selected' : '' }}>Title A-Z</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-primary text-dark-900 font-semibold rounded-lg hover:bg-primary/90 transition">
                        Apply Filters
                    </button>
                </form>

                <!-- Ad: Sidebar Bottom -->
                @php $adSidebarBottom = \App\Models\Ad::getBySlot('sidebar_bottom'); @endphp
                @if($adSidebarBottom)
                    <div class="ad-container mt-6">{!! $adSidebarBottom !!}</div>
                @endif
            </aside>

            <!-- Results -->
            <div class="flex-1">
                @if($type === 'film' || $type === 'all')
                    @if($films instanceof \Illuminate\Pagination\LengthAwarePaginator ? $films->count() : $films->count())
                        <section class="mb-12">
                            @if($type === 'all')
                                <h2 class="text-xl font-bold mb-4">Movies</h2>
                            @endif
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                @foreach($films as $film)
                                    <x-film-card :film="$film" />
                                @endforeach
                            </div>

                            @if($films instanceof \Illuminate\Pagination\LengthAwarePaginator && $type === 'film')
                                <div class="mt-8">
                                    {{ $films->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </section>
                    @endif
                @endif

                @if($type === 'series' || $type === 'all')
                    @if($series instanceof \Illuminate\Pagination\LengthAwarePaginator ? $series->count() : $series->count())
                        <section>
                            @if($type === 'all')
                                <h2 class="text-xl font-bold mb-4">Series</h2>
                            @endif
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                @foreach($series as $s)
                                    <x-series-card :series="$s" />
                                @endforeach
                            </div>

                            @if($series instanceof \Illuminate\Pagination\LengthAwarePaginator && $type === 'series')
                                <div class="mt-8">
                                    {{ $series->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </section>
                    @endif
                @endif

                @if(
                        ($type === 'all' && $films->isEmpty() && $series->isEmpty()) ||
                        ($type === 'film' && $films->isEmpty()) ||
                        ($type === 'series' && $series->isEmpty())
                    )
                    <div class="text-center py-16">
                        <p class="text-xl text-gray-400">No results found</p>
                        <p class="text-sm text-gray-500 mt-2">Try changing your search filters</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection