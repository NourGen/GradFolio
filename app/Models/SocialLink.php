<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'platform',
        'url',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    /**
     * Get the Font Awesome icon class for a given platform.
     */
    public function iconClass(): string
    {
        return match(strtolower($this->platform)) {
            'linkedin'   => 'fab fa-linkedin',
            'github'     => 'fab fa-github',
            'twitter'    => 'fab fa-twitter',
            'instagram'  => 'fab fa-instagram',
            'youtube'    => 'fab fa-youtube',
            'dribbble'   => 'fab fa-dribbble',
            'behance'    => 'fab fa-behance',
            'website'    => 'fas fa-globe',
            'facebook'   => 'fab fa-facebook',
            'tiktok'     => 'fab fa-tiktok',
            'snapchat'   => 'fab fa-snapchat',
            'gmail'      => 'fas fa-envelope',
            'phone'      => 'fas fa-phone-alt',
            'whatsapp'   => 'fab fa-whatsapp',
            'telegram'   => 'fab fa-telegram-plane',
            default      => 'fas fa-link',
        };
    }
}
