<?php

namespace App;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'year',
        'make',
        'model',
    ];

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function tripCount(): int
    {
        return $this->trips()->count();
    }

    public function tripMiles(): int
    {
        return $this->trips()->sum('miles');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new UserScope());
    }
}
