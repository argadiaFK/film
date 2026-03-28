<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', $siteSettings['site_tagline'] ?? 'Watch movies and TV shows online for free')">
    @yield('seo_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home') - {{ $siteSettings['site_name'] ?? 'FilmKu' }}</title>

    @if($siteSettings['site_favicon'] ?? false)
        <link rel="icon" href="{{ Storage::url($siteSettings['site_favicon']) }}" type="image/x-icon">
    @endif

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#f59e0b',
                        dark: {
                            900: '#0f0f0f',
                            800: '#171717',
                            700: '#262626',
                            600: '#404040',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Ad Styles */
        .ad-container {
            text-align: center;
            overflow: hidden;
        }
        .ad-container img,
        .ad-container .ad-image {
            max-width: 100%;
            height: auto;
            display: inline-block;
        }
        .ad-container .ad-link {
            display: inline-block;
        }
    </style>

    @stack('styles')

    {!! $siteSettings['header_scripts'] ?? '' !!}
</head>

<body class="bg-dark-900 text-gray-100 min-h-screen">
    <!-- Header -->
    <header x-data="{ mobileSearchOpen: false }" class="sticky top-0 z-50 bg-dark-800/95 backdrop-blur border-b border-dark-700">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2">
                    @if($siteSettings['site_logo'] ?? false)
                        <img src="{{ Storage::url($siteSettings['site_logo']) }}" alt="{{ $siteSettings['site_name'] ?? 'Logo' }}" class="h-8">
                    @else
                        <img src="/images/logo.svg" alt="Logo" class="h-8 w-8">
                    @endif
                    <span class="text-2xl font-bold text-primary">{{ $siteSettings['site_name'] ?? 'FilmKu' }}</span>
                </a>

                <!-- Navigation -->
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="/" class="text-gray-300 hover:text-primary transition">Home</a>
                    <a href="/browse?type=film" class="text-gray-300 hover:text-primary transition">Movies</a>
                    <a href="/browse?type=series" class="text-gray-300 hover:text-primary transition">Series</a>
                    <a href="/browse" class="text-gray-300 hover:text-primary transition">Browse</a>
                    @if(!empty($siteSettings['donation_link']))
                        <a href="{{ $siteSettings['donation_link'] }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-gradient-to-r from-pink-500 to-red-500 hover:from-pink-600 hover:to-red-600 text-white font-semibold rounded-full transition-all transform hover:scale-105 shadow-md text-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            {{ $siteSettings['donation_text'] ?? 'Support Us' }}
                        </a>
                    @endif
                </nav>

                <!-- Search with Dropdown -->
                <div class="hidden sm:block relative" 
                    x-data="{ 
                        query: '', 
                        results: [], 
                        loading: false, 
                        open: false,
                        async search() {
                            if (this.query.length < 2) {
                                this.results = [];
                                this.open = false;
                                return;
                            }
                            this.loading = true;
                            try {
                                const response = await fetch('/api/search?q=' + encodeURIComponent(this.query));
                                this.results = await response.json();
                                this.open = true;
                            } catch (e) {
                                console.error(e);
                            }
                            this.loading = false;
                        }
                    }"
                    @click.outside="open = false"
                >
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Search movies or series..."
                            x-model="query"
                            @input.debounce.300ms="search()"
                            @focus="if(results.length) open = true"
                            @keydown.enter.prevent="if(query.length >= 2) window.location.href = '/search?q=' + encodeURIComponent(query)"
                            class="w-64 px-4 py-2 pl-10 bg-dark-700 border border-dark-600 rounded-lg text-sm focus:outline-none focus:border-primary"
                        >
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <svg x-show="loading" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                    
                    <!-- Dropdown Results -->
                    <div 
                        x-show="open && results.length > 0" 
                        x-transition
                        class="absolute top-full left-0 right-0 mt-2 bg-dark-800 border border-dark-600 rounded-lg shadow-xl overflow-hidden z-50 max-h-96 overflow-y-auto"
                    >
                        <template x-for="item in results" :key="item.id">
                            <a 
                                :href="'/' + item.type + '/' + item.slug"
                                class="flex items-center gap-3 p-3 hover:bg-dark-700 transition border-b border-dark-700 last:border-0"
                            >
                                <img 
                                    :src="item.poster ? '/storage/' + item.poster : '/images/no-poster.jpg'" 
                                    :alt="item.title"
                                    class="w-10 h-14 object-cover rounded"
                                    onerror="this.src='/images/no-poster.jpg'"
                                >
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm truncate" x-text="item.title"></p>
                                    <div class="flex items-center gap-2 text-xs text-gray-400">
                                        <span x-text="item.year || '-'"></span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase"
                                            :class="item.type === 'film' ? 'bg-red-600 text-white' : 'bg-blue-600 text-white'"
                                            x-text="item.type"></span>
                                    </div>
                                </div>
                            </a>
                        </template>
                        
                        <!-- View All Results -->
                        <a 
                            :href="'/search?q=' + encodeURIComponent(query)"
                            class="block p-3 text-center text-sm text-primary hover:bg-dark-700 transition"
                        >
                            See all results →
                        </a>
                    </div>
                    
                    <!-- No Results -->
                    <div 
                        x-show="open && results.length === 0 && query.length >= 2 && !loading" 
                        class="absolute top-full left-0 right-0 mt-2 bg-dark-800 border border-dark-600 rounded-lg shadow-xl p-4 text-center text-sm text-gray-400 z-50"
                    >
                        No results found for "<span x-text="query"></span>"
                    </div>
                </div>

                <!-- Mobile Icons (Donation, Search, Menu) -->
                <div class="flex items-center gap-2 sm:gap-3 md:hidden">
                    @if(!empty($siteSettings['donation_link']))
                        <a href="{{ $siteSettings['donation_link'] }}" target="_blank" rel="noopener noreferrer" 
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-pink-500 to-red-500 hover:from-pink-600 hover:to-red-600 text-white font-semibold rounded-full shadow-md text-xs transition-transform transform hover:scale-105" aria-label="Dukung Kami">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ $siteSettings['donation_text'] ?? 'Support Us' }}</span>
                        </a>
                    @endif
                    
                    <button @click="mobileSearchOpen = !mobileSearchOpen" class="text-gray-400 hover:text-white p-2" aria-label="Search">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    <button x-data @click="$dispatch('toggle-menu')" class="text-gray-400 hover:text-white p-1 sm:p-2">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Search Bar Expansion -->
            <div x-show="mobileSearchOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="pb-4 md:hidden relative" x-cloak
                 x-data="{ 
                     query: '', 
                     results: [], 
                     loading: false, 
                     open: false,
                     async search() {
                         if (this.query.length < 2) {
                             this.results = [];
                             this.open = false;
                             return;
                         }
                         this.loading = true;
                         try {
                             const response = await fetch('/api/search?q=' + encodeURIComponent(this.query));
                             this.results = await response.json();
                             this.open = true;
                         } catch (e) {
                             console.error(e);
                         }
                         this.loading = false;
                     }
                 }"
                 @click.outside="open = false"
            >
                <form action="/search" method="GET" class="relative">
                    <input type="text" name="q" placeholder="Search movies or series..."
                        x-model="query"
                        @input.debounce.300ms="search()"
                        @focus="if(results.length) open = true"
                        @keydown.enter.prevent="if(query.length >= 2) window.location.href = '/search?q=' + encodeURIComponent(query)"
                        autocomplete="off"
                        class="w-full bg-dark-700 text-gray-100 px-4 py-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-primary border border-dark-600 pl-10 text-sm">
                    <svg class="w-4 h-4 text-gray-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <svg x-show="loading" class="w-4 h-4 absolute right-4 top-3.5 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </form>

                <!-- Dropdown Results -->
                <div 
                    x-show="open && results.length > 0" 
                    x-transition
                    class="absolute top-full left-0 right-0 mt-1 bg-dark-800 border border-dark-600 rounded-lg shadow-xl overflow-hidden z-50 max-h-80 overflow-y-auto"
                >
                    <template x-for="item in results" :key="item.id">
                        <a 
                            :href="'/' + item.type + '/' + item.slug"
                            class="flex items-center gap-3 p-3 hover:bg-dark-700 transition border-b border-dark-700 last:border-0"
                        >
                            <img 
                                :src="item.poster ? '/storage/' + item.poster : '/images/no-poster.jpg'" 
                                :alt="item.title"
                                class="w-10 h-14 object-cover rounded"
                                onerror="this.src='/images/no-poster.jpg'"
                            >
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm truncate" x-text="item.title"></p>
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <span x-text="item.year || '-'"></span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase"
                                        :class="item.type === 'film' ? 'bg-red-600 text-white' : 'bg-blue-600 text-white'"
                                        x-text="item.type"></span>
                                </div>
                            </div>
                        </a>
                    </template>
                    
                    <a 
                        :href="'/search?q=' + encodeURIComponent(query)"
                        class="block p-3 text-center text-sm text-primary hover:bg-dark-700 transition"
                    >
                        See all results →
                    </a>
                </div>
                
                <!-- No Results -->
                <div 
                    x-show="open && results.length === 0 && query.length >= 2 && !loading" 
                    class="absolute top-full left-0 right-0 mt-1 bg-dark-800 border border-dark-600 rounded-lg shadow-xl p-4 text-center text-sm text-gray-400 z-50"
                >
                    No results found for "<span x-text="query"></span>"
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div x-data="{ open: false }" @toggle-menu.window="open = !open" x-show="open" x-cloak
        class="md:hidden fixed inset-0 z-40 bg-dark-900/95">
        <div class="p-4 space-y-4">
            <a href="/" class="block py-3 border-b border-dark-700 text-lg">Home</a>
            <a href="/browse?type=film" class="block py-3 border-b border-dark-700 text-lg">Movies</a>
            <a href="/browse?type=series" class="block py-3 border-b border-dark-700 text-lg">Series</a>
            <a href="/browse" class="block py-3 border-b border-dark-700 text-lg">Browse</a>
        </div>
    </div>

    <!-- Header Banner Ad -->
    @php $headerAd = \App\Models\Ad::getBySlot('header_banner'); @endphp
    @if($headerAd)
        <div class="ad-container bg-dark-800 border-b border-dark-700">
            <div class="container mx-auto px-4 py-2">
                {!! $headerAd !!}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <!-- Footer Ad -->
    @php $footerAd = \App\Models\Ad::getBySlot('footer'); @endphp
    @if($footerAd)
        <div class="ad-container bg-dark-900 py-4">
            <div class="container mx-auto px-4">
                {!! $footerAd !!}
            </div>
        </div>
    @endif

    <footer class="bg-dark-800 border-t border-dark-700 mt-16">
        <div class="container mx-auto px-4 py-8 flex flex-col md:flex-row items-center justify-between text-sm text-gray-500 gap-4">
            <div class="text-center md:text-left">
                {!! $siteSettings['footer_text'] ?? '&copy; ' . date('Y') . ' FilmKu. All rights reserved.' !!}
            </div>
            
            <!-- Social Links -->
            <div class="flex gap-4">
                @if($siteSettings['social_facebook'] ?? false)
                    <a href="{{ $siteSettings['social_facebook'] }}" target="_blank" class="text-gray-400 hover:text-primary">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                @endif
                @if($siteSettings['social_twitter'] ?? false)
                    <a href="{{ $siteSettings['social_twitter'] }}" target="_blank" class="text-gray-400 hover:text-primary">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                @endif
                @if($siteSettings['social_instagram'] ?? false)
                    <a href="{{ $siteSettings['social_instagram'] }}" target="_blank" class="text-gray-400 hover:text-primary">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                @endif
                @if($siteSettings['social_youtube'] ?? false)
                    <a href="{{ $siteSettings['social_youtube'] }}" target="_blank" class="text-gray-400 hover:text-primary">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                @endif
            </div>
        </div>
    </footer>

    @stack('scripts')

    <script>
        function trackAdClick(adId) {
            fetch('/ad/click/' + adId, { method: 'GET' }).catch(function() {});
        }
    </script>

    {{-- Popup Ad --}}
    @php $popupAd = \App\Models\Ad::getBySlot('popup'); @endphp
    @if($popupAd)
        <div x-data="{ 
                showPopup: false,
                init() {
                    if (!sessionStorage.getItem('popup_dismissed')) {
                        setTimeout(() => this.showPopup = true, 500);
                    }
                },
                closePopup() {
                    this.showPopup = false;
                    sessionStorage.setItem('popup_dismissed', '1');
                }
             }" x-show="showPopup" x-cloak
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 p-4"
             @keydown.escape.window="closePopup()">
            <div class="relative bg-dark-800 rounded-xl shadow-2xl max-w-lg w-full p-6 border border-dark-600">
                <button @click="closePopup()"
                    class="absolute -top-3 -right-3 w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded-full flex items-center justify-center text-lg font-bold shadow-lg transition">&times;</button>
                <div class="ad-container">
                    {!! $popupAd !!}
                </div>
            </div>
        </div>
    @endif
    
    {!! $siteSettings['footer_scripts'] ?? '' !!}
    {!! $siteSettings['analytics_code'] ?? '' !!}
</body>

</html>