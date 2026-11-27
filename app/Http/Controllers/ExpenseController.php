<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    /**
     * Store a newly created expense for a trip.
     */
    public function store(Request $request, Trip $trip): RedirectResponse
    {
        Gate::authorize('update', $trip);

        if ($request->has('kategori')) {
            $request->merge([
                'kategori' => strtolower(trim((string) $request->input('kategori'))),
            ]);
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'kategori' => ['required', 'string', 'in:transportasi,akomodasi,makanan,tiket,tiket wisata,belanja,lainnya'],
            'tanggal' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['user_id'] = Auth::id();

        $trip->expenses()->create($validated);

        return redirect()->route('trips.show', ['trip' => $trip, 'tab' => 'expenses'])
            ->with('success', 'Pengeluaran "'.$validated['judul'].'" berhasil dicatat!');
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, Trip $trip, Expense $expense): RedirectResponse
    {
        Gate::authorize('update', $trip);
        Gate::authorize('update', $expense);

        if ($request->has('kategori')) {
            $request->merge([
                'kategori' => strtolower(trim((string) $request->input('kategori'))),
            ]);
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'kategori' => ['required', 'string', 'in:transportasi,akomodasi,makanan,tiket,tiket wisata,belanja,lainnya'],
            'tanggal' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $expense->update($validated);

        return redirect()->route('trips.show', ['trip' => $trip, 'tab' => 'expenses'])
            ->with('success', 'Pengeluaran "'.$expense->judul.'" berhasil diperbarui!');
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Trip $trip, Expense $expense): RedirectResponse
    {
        Gate::authorize('update', $trip);
        Gate::authorize('delete', $expense);

        $judul = $expense->judul;
        $expense->delete();

        return redirect()->route('trips.show', ['trip' => $trip, 'tab' => 'expenses'])
            ->with('success', 'Pengeluaran "'.$judul.'" berhasil dihapus.');
    }
}
