<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // Check if comments are enabled
        if (!Setting::get('enable_comments', true)) {
            return response()->json([
                'message' => 'Komentar dinonaktifkan'
            ], 403);
        }

        // Check if guest comments are allowed
        $enableGuestComments = Setting::get('enable_guest_comments', true);
        if (!$enableGuestComments && !auth()->check()) {
            return response()->json([
                'message' => 'Silakan login untuk berkomentar'
            ], 403);
        }

        // Anti-spam: rate limit by IP (max 3 comments per minute)
        $ip = $request->ip();
        $rateLimitKey = 'comment_rate_' . md5($ip);
        $commentCount = Cache::get($rateLimitKey, 0);
        
        if ($commentCount >= 3) {
            return response()->json([
                'message' => 'Terlalu banyak komentar. Silakan coba lagi dalam 1 menit.'
            ], 429);
        }

        // Anti-spam: check for duplicate content within last 5 minutes
        $duplicateCheck = Comment::where('content', $request->input('content'))
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();
        
        if ($duplicateCheck) {
            return response()->json([
                'message' => 'Komentar duplikat terdeteksi. Silakan tulis komentar yang berbeda.'
            ], 422);
        }

        // Anti-spam: honeypot check (hidden field that bots fill)
        if ($request->filled('website_url')) {
            return response()->json([
                'message' => 'Komentar berhasil dikirim!'
            ]); // Silent fail for bots
        }

        // Anti-spam: minimum content length
        $content = trim($request->input('content', ''));
        if (strlen($content) < 3) {
            return response()->json([
                'message' => 'Komentar terlalu pendek. Minimum 3 karakter.'
            ], 422);
        }

        // Determine author name and email
        $user = auth()->user();
        if ($user) {
            $authorName = $user->name;
            $authorEmail = $user->email;
        } else {
            $request->validate([
                'author_name' => 'required|string|max:100',
                'author_email' => 'required|email|max:255',
            ]);
            $authorName = $request->input('author_name');
            $authorEmail = $request->input('author_email');
        }

        $validated = $request->validate([
            'film_id' => 'nullable|uuid|exists:films,id',
            'series_id' => 'nullable|uuid|exists:series,id',
            'episode_id' => 'nullable|uuid|exists:episodes,id',
            'parent_id' => 'nullable|uuid|exists:comments,id',
            'content' => 'required|string|min:3|max:2000',
        ]);

        // At least one of film_id, series_id, or episode_id must be provided (unless replying)
        if (empty($validated['film_id']) && empty($validated['series_id']) && empty($validated['episode_id']) && empty($validated['parent_id'])) {
            return response()->json([
                'message' => 'Target (Film, Series, atau Episode) harus dipilih'
            ], 422);
        }

        $filmId = $validated['film_id'] ?? null;
        $seriesId = $validated['series_id'] ?? null;
        $episodeId = $validated['episode_id'] ?? null;

        if (!empty($validated['parent_id'])) {
            $parentComment = Comment::find($validated['parent_id']);
            if ($parentComment) {
                $filmId = $filmId ?: $parentComment->film_id;
                $seriesId = $seriesId ?: $parentComment->series_id;
                $episodeId = $episodeId ?: $parentComment->episode_id;
            }
        }

        // Check if approval is required
        $requireApproval = Setting::get('comments_require_approval', false);
        
        // Admin comments are auto-approved
        $isAdmin = $user && $user->hasAnyRole(['super_admin', 'admin']);
        $status = ($isAdmin || !$requireApproval) ? 'approved' : 'pending';

        Comment::create([
            'film_id' => $filmId,
            'series_id' => $seriesId,
            'episode_id' => $episodeId,
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => $user?->id,
            'author_name' => $authorName,
            'author_email' => $authorEmail,
            'content' => $validated['content'],
            'status' => $status,
        ]);

        // Update rate limit counter (expires in 60 seconds)
        Cache::put($rateLimitKey, $commentCount + 1, 60);

        $message = ($requireApproval && !$isAdmin)
            ? 'Komentar berhasil dikirim dan akan ditampilkan setelah disetujui.'
            : 'Komentar berhasil dikirim!';

        return response()->json([
            'message' => $message
        ]);
    }
}
