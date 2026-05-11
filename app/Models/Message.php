<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory, HasUlids;

    protected $with = ['conversation.customer'];

    protected $fillable = [
        'conversation_id',
        'direction',
        'channel',
        'channel_message_id',
        'content_type',
        'content',
        'metadata',
        'status',
        'status_updated_at',
        'sent_by_agent_id',
    ];


    protected function casts(): array
    {
        return [
            'conversation_id' => 'integer',
            'metadata' => 'array',
            'status_updated_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
