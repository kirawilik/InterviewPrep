<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Concept extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'domain_id',
        'title',
        'slug',
        'explanation',
        'difficulty',
        'status',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function interviewGenerations(): HasMany
    {
        return $this->hasMany(InterviewGeneration::class)->latest();
    }
}
