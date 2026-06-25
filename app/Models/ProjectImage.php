<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'image_path',
        'file_type',
        'caption',
        'alt_text',
        'sort_order',
        'thumbnail_path',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // ── URL Helpers ────────────────────────────────────────────

    public function url(): string
    {
        return asset('storage/' . $this->image_path);
    }

    public function thumbnailUrl(): string
    {
        if ($this->thumbnail_path) {
            return asset('storage/' . $this->thumbnail_path);
        }
        return $this->url();
    }

    // ── Type Helpers ───────────────────────────────────────────

    public function isImage(): bool
    {
        return ($this->file_type ?? 'image') === 'image';
    }

    public function isPdf(): bool
    {
        return ($this->file_type ?? 'image') === 'pdf';
    }
}
