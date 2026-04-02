@props(['comments', 'filmId' => null, 'seriesId' => null, 'episodeId' => null])

@php
    $enableComments = \App\Models\Setting::get('enable_comments', true);
    $enableGuestComments = \App\Models\Setting::get('enable_guest_comments', true);
    $requireApproval = \App\Models\Setting::get('comments_require_approval', true);

    // Get root comments only (no parent)
    $rootComments = $comments->whereNull('parent_id');
@endphp

@if($enableComments)
    <section class="mt-12 pb-8" x-data="commentSystem()">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">Comments ({{ $comments->count() }})</h2>
            <button @click="openForm()"
                class="px-4 py-2 bg-primary text-dark-900 font-medium rounded-lg hover:bg-primary/90 transition text-sm">
                Write a Comment
            </button>
        </div>

        <!-- Success/Error Message -->
        <div x-show="message" x-transition
            :class="error ? 'bg-red-600/20 border-red-600 text-red-400' : 'bg-green-600/20 border-green-600 text-green-400'"
            class="p-4 rounded-lg border mb-6">
            <p x-text="message"></p>
        </div>

        <!-- Comment Form -->
        <div x-show="showForm" x-transition class="bg-dark-800 rounded-lg p-6 mb-6" id="comment-form">
            <div x-show="replyTo" class="mb-4 p-3 bg-dark-700 rounded-lg flex items-center justify-between">
                <span class="text-sm text-gray-400">Replying to <strong class="text-white"
                        x-text="replyToName"></strong></span>
                <button @click="cancelReply()" class="text-gray-500 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @if($requireApproval)
                <p class="text-sm text-yellow-500 mb-4">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Comments will be displayed after moderator approval.
                </p>
            @endif
            <form @submit.prevent="submitComment()" class="space-y-4">
                @auth
                    {{-- Logged in user: auto-fill name/email --}}
                    <div class="p-3 bg-dark-700/50 rounded-lg flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-primary font-bold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-sm">{{ auth()->user()->name }}</span>
                            @if(auth()->user()->hasAnyRole(['super_admin', 'admin']))
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-red-600 text-white">Admin</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Name <span class="text-red-500">*</span></label>
                            <input type="text" x-model="name" required
                                class="w-full px-4 py-3 bg-dark-700 border border-dark-600 rounded-lg focus:outline-none focus:border-primary"
                                placeholder="Your Name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" x-model="email" required
                                class="w-full px-4 py-3 bg-dark-700 border border-dark-600 rounded-lg focus:outline-none focus:border-primary"
                                placeholder="email@example.com">
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <input type="checkbox" id="saveInfo" x-model="saveInfo" class="rounded border-dark-600 bg-dark-700 text-primary focus:ring-primary focus:ring-offset-dark-800">
                        <label for="saveInfo" class="text-sm text-gray-400 cursor-pointer">Save my name and email in this browser for the next time I comment</label>
                    </div>
                @endauth
                <div>
                    <label class="block text-sm font-medium mb-2">Comment <span class="text-red-500">*</span></label>
                    <textarea x-model="content" required rows="4"
                        class="w-full px-4 py-3 bg-dark-700 border border-dark-600 rounded-lg focus:outline-none focus:border-primary resize-none"
                        placeholder="Write your comment..."></textarea>
                </div>
                <!-- Honeypot anti-spam (hidden from humans) -->
                <div class="absolute opacity-0 -z-10" style="position:absolute;left:-9999px" aria-hidden="true">
                    <input type="text" x-model="website_url" tabindex="-1" autocomplete="off">
                </div>
                <div class="flex gap-3">
                    <button type="submit" :disabled="loading"
                        class="px-6 py-3 bg-primary text-dark-900 font-semibold rounded-lg hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!loading" x-text="replyTo ? 'Post Reply' : 'Post Comment'"></span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Posting...
                        </span>
                    </button>
                    <button type="button" @click="closeForm()"
                        class="px-6 py-3 bg-dark-700 text-gray-300 font-semibold rounded-lg hover:bg-dark-600 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Comments List -->
        @if($rootComments->count())
            <div class="space-y-4">
                @foreach($rootComments as $comment)
                    <!-- Parent Comment -->
                    <div class="bg-dark-800 rounded-lg p-5">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full {{ $comment->isAdmin() ? 'bg-red-600/20' : 'bg-primary/20' }} flex items-center justify-center flex-shrink-0">
                                <span class="{{ $comment->isAdmin() ? 'text-red-400' : 'text-primary' }} font-bold text-sm">
                                    {{ strtoupper(substr($comment->author_name ?? $comment->user?->name ?? 'A', 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="font-medium">{{ $comment->author_name ?? $comment->user?->name ?? 'Anonymous' }}</span>
                                    @if($comment->isAdmin())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-red-600 text-white">Admin</span>
                                    @endif
                                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-300 text-sm leading-relaxed mb-3">{{ $comment->content }}</p>
                                <button
                                    @click="replyToComment('{{ $comment->id }}', '{{ addslashes($comment->author_name ?? $comment->user?->name ?? 'Anonymous') }}')"
                                    class="text-xs text-primary hover:underline flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                    Reply
                                </button>
                            </div>
                        </div>

                        <!-- Replies -->
                        @php
                            $replies = $comments->where('parent_id', $comment->id)->sortBy('created_at');
                        @endphp
                        @if($replies->count())
                            <div class="mt-4 ml-14 space-y-3 border-l-2 border-dark-600 pl-4">
                                @foreach($replies as $reply)
                                    <div class="bg-dark-700/50 rounded-lg p-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-full {{ $reply->isAdmin() ? 'bg-red-600/20' : 'bg-blue-600/20' }} flex items-center justify-center flex-shrink-0">
                                                <span class="{{ $reply->isAdmin() ? 'text-red-400' : 'text-blue-400' }} font-bold text-xs">
                                                    {{ strtoupper(substr($reply->author_name ?? $reply->user?->name ?? 'A', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                    <span class="font-medium text-sm">{{ $reply->author_name ?? $reply->user?->name ?? 'Anonymous' }}</span>
                                                    @if($reply->isAdmin())
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-red-600 text-white">Admin</span>
                                                    @endif
                                                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-gray-300 text-sm leading-relaxed mb-2">{{ $reply->content }}</p>
                                                <button
                                                    @click="replyToComment('{{ $comment->id }}', '{{ addslashes($reply->author_name ?? $reply->user?->name ?? 'Anonymous') }}')"
                                                    class="text-xs text-primary hover:underline flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                    </svg>
                                                    Reply
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-dark-800 rounded-lg p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-gray-400">No comments yet.</p>
                <p class="text-gray-500 text-sm mt-1">Be the first to comment!</p>
            </div>
        @endif
    </section>

    <script>
        function commentSystem() {
            return {
                showForm: false,
                loading: false,
                name: '{{ auth()->user()?->name ?? '' }}' || localStorage.getItem('comment_name') || '',
                email: '{{ auth()->user()?->email ?? '' }}' || localStorage.getItem('comment_email') || '',
                saveInfo: localStorage.getItem('comment_save_info') === 'true',
                content: '',
                website_url: '',
                message: '',
                error: false,
                replyTo: null,
                replyToName: '',

                openForm() {
                    this.showForm = true;
                    this.replyTo = null;
                    this.replyToName = '';
                    this.$nextTick(() => {
                        document.getElementById('comment-form')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                },

                closeForm() {
                    this.showForm = false;
                    this.replyTo = null;
                    this.replyToName = '';
                },

                cancelReply() {
                    this.replyTo = null;
                    this.replyToName = '';
                },

                replyToComment(commentId, authorName) {
                    this.showForm = true;
                    this.replyTo = commentId;
                    this.replyToName = authorName;
                    this.$nextTick(() => {
                        document.getElementById('comment-form')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                },

                async submitComment() {
                    if (!this.content.trim()) {
                        this.error = true;
                        this.message = 'Comment cannot be empty';
                        return;
                    }
                    this.loading = true;
                    this.error = false;
                    
                    if (!{{ auth()->check() ? 'true' : 'false' }}) {
                        if (this.saveInfo) {
                            localStorage.setItem('comment_save_info', 'true');
                            localStorage.setItem('comment_name', this.name);
                            localStorage.setItem('comment_email', this.email);
                        } else {
                            localStorage.removeItem('comment_save_info');
                            localStorage.removeItem('comment_name');
                            localStorage.removeItem('comment_email');
                        }
                    }

                    try {
                        const response = await fetch('/api/comments', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                            },
                            body: JSON.stringify({
                                film_id: '{{ $filmId }}' || null,
                                series_id: '{{ $seriesId }}' || null,
                                episode_id: '{{ $episodeId }}' || null,
                                parent_id: this.replyTo,
                                author_name: this.name,
                                author_email: this.email,
                                content: this.content,
                                website_url: this.website_url
                            })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.message = data.message || 'Comment posted successfully!';
                            this.error = false;
                            this.content = '';
                            this.showForm = false;
                            this.replyTo = null;
                            this.replyToName = '';
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            this.message = data.message || 'Failed to post comment';
                            this.error = true;
                        }
                    } catch (e) {
                        this.message = 'An error occurred. Please try again later.';
                        this.error = true;
                    }
                    this.loading = false;
                }
            }
        }
    </script>
@endif