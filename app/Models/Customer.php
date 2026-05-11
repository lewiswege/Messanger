<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'ulid',
        'name',
        'email',
        'phone_primary',
    ];

    protected $with = ['customerChannelIdentifiers'];

    public function uniqueIds(): array {
        return ['ulid'];
    }

    public function customerChannelIdentifiers(): HasMany
    {
        return $this->hasMany(CustomerChannelIdentifier::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function getIndentifierFor(string $channel): ?string {
        return $this->customerChannelIdentifiers
            ->where('channel', $channel)
            ->first()?->identifier;
    }
}
