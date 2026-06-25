<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    protected $fillable = ['portfolio_id', 'name', 'level', 'sort_order'];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function levelPercentage(): int
    {
        return match($this->level) {
            'beginner'     => 25,
            'intermediate' => 50,
            'advanced'     => 75,
            'expert'       => 100,
            default        => 50,
        };
    }

    public function levelColor(): string
    {
        return match($this->level) {
            'beginner'     => '#9C7B65',
            'intermediate' => '#D4914E',
            'advanced'     => '#C4783A',
            'expert'       => '#E8B88A',
            default        => '#C4783A',
        };
    }

    public function levelLabel(): string
    {
        return ucfirst($this->level);
    }
}
