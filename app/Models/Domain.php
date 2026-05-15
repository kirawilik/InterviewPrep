<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concept;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domain extends Model
{
    protected $fillable = ['name', 'color'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function concepts(): HasMany
{
    return $this->hasMany(Concept::class);
}
}
