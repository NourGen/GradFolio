<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Portfolio extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'headline',
        'bio',
        'location',
        'phone',
        'whatsapp',
        'linkedin_url',
        'github_url',
        'behance_url',
        'profile_picture_path',
        'cv_path',
        'is_published',
        'hero_prefix',
        'hero_suffix',
        'track',
        'specialization',
        'graduation_year',
        'is_verified',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_verified'  => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(SocialLink::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)->orderBy('created_at', 'desc');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class)->orderBy('sort_order')->orderBy('created_at');
    }

    public function views(): HasMany
    {
        return $this->hasMany(PortfolioView::class);
    }

    public function cvDownloads(): HasMany
    {
        return $this->hasMany(CvDownload::class);
    }

    public function projectClicks()
    {
        return $this->hasManyThrough(ProjectClick::class, Project::class);
    }

    // ── Analytics ──────────────────────────────────────────────

    public function totalViews(): int
    {
        return $this->views()->count();
    }

    // ── Helpers ────────────────────────────────────────────────

    public function techStackArray(): array
    {
        return array_map('trim', explode(',', $this->tech_stack ?? ''));
    }
}
