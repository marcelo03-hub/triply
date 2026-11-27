<?php

namespace App\Http\Controllers;

use App\Models\PackingItem;
use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PackingItemController extends Controller
{
    /**
     * Store a newly created packing item.
     */
    public function store(Request $request, Trip $trip): RedirectResponse
    {
        Gate::authorize('update', $trip);

        $validated = $request->validate([
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:50'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_packed'] = false;

        $trip->packingItems()->create($validated);

        return redirect()->route('trips.show', ['trip' => $trip, 'tab' => 'packing'])
            ->with('success', 'Barang "'.$validated['nama_barang'].'" berhasil ditambahkan ke checklist!');
    }

    /**
     * Update the packing item details.
     */
    public function update(Request $request, Trip $trip, PackingItem $packingItem): RedirectResponse
    {
        Gate::authorize('update', $trip);
        Gate::authorize('update', $packingItem);

        $validated = $request->validate([
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:50'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $packingItem->update($validated);

        return redirect()->route('trips.show', ['trip' => $trip, 'tab' => 'packing'])
            ->with('success', 'Barang bawaan berhasil diperbarui!');
    }

    /**
     * Toggle the packed status of an item.
     */
    public function toggle(Trip $trip, PackingItem $packingItem): RedirectResponse
    {
        Gate::authorize('update', $trip);
        Gate::authorize('update', $packingItem);

        $packingItem->update([
            'is_packed' => ! $packingItem->is_packed,
        ]);

        $statusMsg = $packingItem->is_packed ? 'sudah disiapkan' : 'dikembalikan ke belum siap';

        return redirect()->route('trips.show', ['trip' => $trip, 'tab' => 'packing'])
            ->with('success', 'Barang "'.$packingItem->nama_barang.'" '.$statusMsg.'!');
    }

    /**
     * Remove the specified packing item.
     */
    public function destroy(Trip $trip, PackingItem $packingItem): RedirectResponse
    {
        Gate::authorize('update', $trip);
        Gate::authorize('delete', $packingItem);

        $nama = $packingItem->nama_barang;
        $packingItem->delete();

        return redirect()->route('trips.show', ['trip' => $trip, 'tab' => 'packing'])
            ->with('success', 'Barang "'.$nama.'" berhasil dihapus.');
    }
}
