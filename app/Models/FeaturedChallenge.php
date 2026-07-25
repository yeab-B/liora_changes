<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedChallenge extends Model
{
    protected $fillable = [
        'challenge_id',
        'display_priority',
        'publish_date',
        'expiration_date',
        'is_visible',
        'campaign_support',
    ];

    protected $casts = [
        'publish_date' => 'datetime',
        'expiration_date' => 'datetime',
        'is_visible' => 'boolean',
        'display_priority' => 'integer',
    ];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }
}
