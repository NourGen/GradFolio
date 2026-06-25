<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'title',
        'description',
        'tech_stack',
        'project_url',
        'github_url',
        'sort_order',
        'cover_image_path',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(ProjectClick::class);
    }

    // ── Helpers ────────────────────────────────────────────────

    public function techStackArray(): array
    {
        if (!$this->tech_stack) return [];
        return array_map('trim', explode(',', $this->tech_stack));
    }

    /**
     * Returns the URL of the cover image, or null if none.
     */
    public function coverUrl(): ?string
    {
        if ($this->cover_image_path) {
            $path = ltrim($this->cover_image_path, '/');
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            }
            if (Storage::disk('r2')->exists($path)) {
                return Storage::disk('r2')->url($path);
            }
        }

        // Fall back to first gallery image
        $first = $this->images()->where('file_type', 'image')->first();
        if ($first) {
            return $first->url();
        }

        return null;
    }

    public function hasCover(): bool
    {
        return $this->coverUrl() !== null;
    }

    /**
     * Gallery images only (excludes PDFs).
     */
    public function galleryImages(): HasMany
    {
        return $this->hasMany(ProjectImage::class)
                    ->where('file_type', 'image')
                    ->orderBy('sort_order');
    }

    /**
     * PDF files attached to this project.
     */
    public function pdfFiles(): HasMany
    {
        return $this->hasMany(ProjectImage::class)
                    ->where('file_type', 'pdf')
                    ->orderBy('sort_order');
    }
}
