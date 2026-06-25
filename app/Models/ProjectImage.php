<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        $path = ltrim($this->image_path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        return Storage::disk('r2')->url($path);
    }

    public function thumbnailUrl(): string
    {
        if ($this->thumbnail_path) {
            $path = ltrim($this->thumbnail_path, '/');
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            }
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            return Storage::disk('r2')->url($path);
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
