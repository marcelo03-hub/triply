<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Destination extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'slug',
        'lokasi',
        'wilayah',
        'kategori',
        'deskripsi',
        'gambar',
        'estimasi_biaya',
        'rekomendasi_durasi',
        'rating',
        'highlight',
        'tips',
        'is_popular',
        'latitude',
        'longitude',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'is_popular' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Get favorited users count/relationship.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(FavoriteDestination::class);
    }

    /**
     * Check if destination is favorited by given or authenticated user.
     */
    public function isFavoritedBy(?User $user = null): bool
    {
        $userId = $user ? $user->id : auth()->id();

        if (! $userId) {
            return false;
        }

        return $this->favorites()->where('user_id', $userId)->exists();
    }

    /**
     * Resolve fallback latitude for map display if null.
     */
    public function getDisplayLatitudeAttribute(): float
    {
        if ($this->latitude !== null) {
            return (float) $this->latitude;
        }

        return match ($this->slug) {
            'pulau-bali' => -8.409518,
            'pulau-lombok' => -8.650000,
            'yogyakarta' => -7.795580,
            'candi-borobudur' => -7.607874,
            'kota-bandung' => -6.917464,
            'labuan-bajo' => -8.4964,
            'raja-ampat' => -0.2333,
            'gunung-bromo' => -7.942494,
            'tokyo-jepang' => 35.6762,
            'seoul-korea-selatan' => 37.5665,
            'paris-prancis' => 48.8566,
            'swiss-alpen' => 46.8182,
            default => -6.2088,
        };
    }

    /**
     * Resolve fallback longitude for map display if null.
     */
    public function getDisplayLongitudeAttribute(): float
    {
        if ($this->longitude !== null) {
            return (float) $this->longitude;
        }

        return match ($this->slug) {
            'pulau-bali' => 115.188919,
            'pulau-lombok' => 116.324900,
            'yogyakarta' => 110.369490,
            'candi-borobudur' => 110.203751,
            'kota-bandung' => 107.619123,
            'labuan-bajo' => 119.8877,
            'raja-ampat' => 130.5167,
            'gunung-bromo' => 112.953012,
            'tokyo-jepang' => 139.6503,
            'seoul-korea-selatan' => 126.9780,
            'paris-prancis' => 2.3522,
            'swiss-alpen' => 8.2275,
            default => 106.8456,
        };
    }

    /**
     * Automatically generate slug on saving if not provided.
     */
    protected static function booted(): void
    {
        static::saving(function (Destination $destination) {
            if (empty($destination->slug)) {
                $destination->slug = Str::slug($destination->nama);
            }
        });
    }

    /**
     * Resolve image URL: if local file in public/ or full URL.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->gambar)) {
            return 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80';
        }

        if (str_starts_with($this->gambar, 'http://') || str_starts_with($this->gambar, 'https://')) {
            return $this->gambar;
        }

        if (str_starts_with($this->gambar, 'destinations/')) {
            return asset('storage/'.$this->gambar);
        }

        return asset($this->gambar);
    }
}
