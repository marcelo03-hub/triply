<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItineraryController extends Controller
{
    /**
     * Store a newly created itinerary activity.
     */
    public function store(Request $request, Trip $trip): RedirectResponse
    {
        $validated = $request->validate([
            'hari_ke' => ['required', 'integer', 'min:1'],
            'waktu' => ['nullable', 'string', 'max:50'],
            'kegiatan' => ['required', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'estimasi_biaya' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validated['estimasi_biaya'] = $validated['estimasi_biaya'] ?? 0;
        $validated['status'] = 'pending';

        $trip->itineraries()->create($validated);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Kegiatan itinerary hari ke-'.$validated['hari_ke'].' berhasil ditambahkan!');
    }

    /**
     * Update the specified itinerary activity.
     */
    public function update(Request $request, Trip $trip, Itinerary $itinerary): RedirectResponse
    {
        $validated = $request->validate([
            'hari_ke' => ['required', 'integer', 'min:1'],
            'waktu' => ['nullable', 'string', 'max:50'],
            'kegiatan' => ['required', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'estimasi_biaya' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,completed'],
        ]);

        $validated['estimasi_biaya'] = $validated['estimasi_biaya'] ?? 0;

        $itinerary->update($validated);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Kegiatan itinerary berhasil diperbarui!');
    }

    /**
     * Toggle status (pending/completed).
     */
    public function toggle(Trip $trip, Itinerary $itinerary): RedirectResponse
    {
        $newStatus = $itinerary->status === 'completed' ? 'pending' : 'completed';
        $itinerary->update(['status' => $newStatus]);

        $statusText = $newStatus === 'completed' ? 'ditandai selesai' : 'dikembalikan ke pending';

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Kegiatan "'.$itinerary->kegiatan.'" '.$statusText.'!');
    }

    /**
     * Remove the specified itinerary activity.
     */
    public function destroy(Trip $trip, Itinerary $itinerary): RedirectResponse
    {
        $kegiatan = $itinerary->kegiatan;
        $itinerary->delete();

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Kegiatan "'.$kegiatan.'" berhasil dihapus dari itinerary.');
    }
}
