<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Message;

class Conversation extends Model
{
    use HasFactory, HasUlids;

    protected $with = ['customer'];

    protected $fillable = [
        'ulid',
        'customer_id',
        'assigned_to',
        'status',
        'last_message_at',
        'last_inbound_channel',
        'unread_count',
        'resolved_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'customer_id' => 'integer',
            'assigned_to' => 'integer',
            'last_message_at' => 'datetime',
            'resolved_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function uniqueIds(): array {
        return ['ulid'];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function lastMessage(): HasMany
    {
        return $this->messages()->latest('created_at')->limit(1);
    }
}
