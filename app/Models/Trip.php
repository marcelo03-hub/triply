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
     * Get the expenses for this trip.
     *
     * @return HasMany<Expense, $this>
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class)->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Get the packing items for this trip.
     *
     * @return HasMany<PackingItem, $this>
     */
    public function packingItems(): HasMany
    {
        return $this->hasMany(PackingItem::class)->orderBy('is_packed')->orderBy('kategori');
    }

    /**
     * Total cost from itineraries.
     */
    public function totalItineraryCost(): float
    {
        return (float) $this->itineraries()->sum('estimasi_biaya');
    }

    /**
     * Total actual expense.
     */
    public function totalActualExpense(): float
    {
        return (float) $this->expenses()->sum('nominal');
    }

    /**
     * Find matched destination by trip destination name.
     */
    public function findDestination(): ?Destination
    {
        return Destination::where('nama', 'like', "%{$this->destinasi}%")
            ->orWhere('lokasi', 'like', "%{$this->destinasi}%")
            ->first();
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
