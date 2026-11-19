<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'is_popular' => 'boolean',
        ];
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

        return asset($this->gambar);
    }
}
