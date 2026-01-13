<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaChat extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'message',
        'context',
        'tokens_used',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
        ];
    }

    /**
     * User who owns this chat message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if message is from user
     */
    public function isUserMessage(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if message is from assistant
     */
    public function isAssistantMessage(): bool
    {
        return $this->role === 'assistant';
    }

    /**
     * Get chat history for a user
     */
    public static function historyForUser(int $userId, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Clear chat history for a user
     */
    public static function clearForUser(int $userId): int
    {
        return static::where('user_id', $userId)->delete();
    }
}
