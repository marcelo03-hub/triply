<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TripController extends Controller
{
    /**
     * Display a listing of the user's trips.
     */
    public function index(Request $request): View
    {
        $query = Trip::query()->orderBy('tanggal_mulai', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $searchTerm = $request->input('q');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_perjalanan', 'like', "%{$searchTerm}%")
                    ->orWhere('destinasi', 'like', "%{$searchTerm}%");
            });
        }

        $trips = $query->paginate(9)->withQueryString();

        $stats = [
            'total' => Trip::count(),
            'planned' => Trip::where('status', 'planned')->count(),
            'ongoing' => Trip::where('status', 'ongoing')->count(),
            'completed' => Trip::where('status', 'completed')->count(),
        ];

        return view('trips.index', compact('trips', 'stats'));
    }

    /**
     * Show the form for creating a new trip.
     */
    public function create(Request $request): View
    {
        $destinasi = $request->query('destinasi', '');
        $destinations = Destination::orderBy('nama')->get();

        return view('trips.create', compact('destinasi', 'destinations'));
    }

    /**
     * Store a newly created trip in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_perjalanan' => 'required|string|max:255',
            'destinasi' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'anggaran' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:2000',
            'status' => 'nullable|in:planned,ongoing,completed',
        ]);

        if (empty($validated['status'])) {
            $today = Carbon::today();
            $start = Carbon::parse($validated['tanggal_mulai']);
            $end = Carbon::parse($validated['tanggal_selesai']);

            if ($today->lt($start)) {
                $validated['status'] = 'planned';
            } elseif ($today->gt($end)) {
                $validated['status'] = 'completed';
            } else {
                $validated['status'] = 'ongoing';
            }
        }

        if (Auth::check()) {
            $validated['user_id'] = Auth::id();
        }

        $trip = Trip::create($validated);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Rencana perjalanan "'.$trip->nama_perjalanan.'" berhasil disimpan!');
    }

    /**
     * Display the specified trip.
     */
    public function show(Trip $trip): View
    {
        $trip->load('itineraries');
        $start = Carbon::parse($trip->tanggal_mulai);
        $end = Carbon::parse($trip->tanggal_selesai);
        $durationDays = max(1, $start->diffInDays($end) + 1);

        $itinerariesByDay = $trip->itineraries->groupBy('hari_ke');
        $totalItineraryCost = $trip->totalItineraryCost();

        // Simple default budget breakdown suggestion
        $anggaran = (float) $trip->anggaran;
        $budgetBreakdown = [
            'transport' => [
                'name' => 'Transportasi & Tiket',
                'amount' => $anggaran * 0.35,
                'percentage' => 35,
                'icon' => '✈️',
            ],
            'hotel' => [
                'name' => 'Penginapan & Hotel',
                'amount' => $anggaran * 0.30,
                'percentage' => 30,
                'icon' => '🏨',
            ],
            'food' => [
                'name' => 'Kuliner & Makanan',
                'amount' => $anggaran * 0.18,
                'percentage' => 18,
                'icon' => '🍜',
            ],
            'attraction' => [
                'name' => 'Wisata & Aktivitas',
                'amount' => $anggaran * 0.12,
                'percentage' => 12,
                'icon' => '🎟️',
            ],
            'emergency' => [
                'name' => 'Dana Darurat & Lainnya',
                'amount' => $anggaran * 0.05,
                'percentage' => 5,
                'icon' => '🛡️',
            ],
        ];

        return view('trips.show', compact('trip', 'durationDays', 'budgetBreakdown', 'itinerariesByDay', 'totalItineraryCost'));
    }

    /**
     * Show the form for editing the specified trip.
     */
    public function edit(Trip $trip): View
    {
        $destinations = Destination::orderBy('nama')->get();

        return view('trips.edit', compact('trip', 'destinations'));
    }

    /**
     * Update the specified trip in storage.
     */
    public function update(Request $request, Trip $trip): RedirectResponse
    {
        $validated = $request->validate([
            'nama_perjalanan' => 'required|string|max:255',
            'destinasi' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'anggaran' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:2000',
            'status' => 'required|in:planned,ongoing,completed',
        ]);

        $trip->update($validated);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Rencana perjalanan berhasil diperbarui!');
    }

    /**
     * Remove the specified trip from storage.
     */
    public function destroy(Trip $trip): RedirectResponse
    {
        $nama = $trip->nama_perjalanan;
        $trip->delete();

        return redirect()->route('trips.index')
            ->with('success', 'Perjalanan "'.$nama.'" berhasil dihapus.');
    }

    /**
     * Display the Jadwal Perjalanan (Schedule & Itinerary) feature page.
     */
    public function jadwal(): View
    {
        $trips = Trip::with('itineraries')->orderBy('tanggal_mulai', 'asc')->get();

        $ongoingTrips = $trips->where('status', 'ongoing');
        $plannedTrips = $trips->where('status', 'planned');
        $completedTrips = $trips->where('status', 'completed');

        return view('jadwal', compact('trips', 'ongoingTrips', 'plannedTrips', 'completedTrips'));
    }

    /**
     * Display the Anggaran Perjalanan (Budget Tracker & Calculator) feature page.
     */
    public function anggaran(): View
    {
        $trips = Trip::with('itineraries')->orderBy('tanggal_mulai', 'desc')->get();

        $totalAnggaran = $trips->sum('anggaran');
        $averageAnggaran = $trips->count() > 0 ? $totalAnggaran / $trips->count() : 0;
        $maxTrip = $trips->sortByDesc('anggaran')->first();

        // Calculate distribution by destination
        $budgetByDestination = $trips->groupBy('destinasi')->map(function ($group) {
            return [
                'total' => $group->sum('anggaran'),
                'count' => $group->count(),
            ];
        })->sortByDesc('total');

        return view('anggaran', compact('trips', 'totalAnggaran', 'averageAnggaran', 'maxTrip', 'budgetByDestination'));
    }
}
