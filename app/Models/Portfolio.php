<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Get the public URL for the profile picture (via R2 or local fallback).
     */
    public function profilePictureUrl(): ?string
    {
        if (!$this->profile_picture_path) {
            return null;
        }

        $path = ltrim($this->profile_picture_path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return Storage::disk('r2')->url($path);
    }

    /**
     * Get the URL/path for the CV (via R2 private disk or local private fallback).
     */
    public function cvUrl(): ?string
    {
        if (!$this->cv_path) {
            return null;
        }

        $path = ltrim($this->cv_path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return Storage::disk('r2_private')->url($path);
    }
}
