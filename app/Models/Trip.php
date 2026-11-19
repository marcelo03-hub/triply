<?php

namespace App\Models;

use Database\Factories\TripFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    /** @use HasFactory<TripFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'nama_perjalanan',
        'destinasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'anggaran',
        'catatan',
        'status',
    ];

    /**
     * Get the user that owns the trip.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the itineraries for this trip.
     *
     * @return HasMany<Itinerary, $this>
     */
    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class)->orderBy('hari_ke')->orderBy('waktu');
    }

    /**
     * Total cost from itineraries.
     */
    public function totalItineraryCost(): float
    {
        return (float) $this->itineraries()->sum('estimasi_biaya');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'anggaran' => 'decimal:2',
        ];
    }
}
