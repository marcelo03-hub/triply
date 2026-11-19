<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    /**
     * Display a listing of destinations.
     */
    public function index(Request $request): View
    {
        $query = Destination::query();

        if ($request->filled('wilayah') && $request->wilayah !== 'all') {
            $query->where('wilayah', $request->wilayah);
        }

        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('q')) {
            $searchTerm = $request->input('q');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'like', "%{$searchTerm}%")
                    ->orWhere('lokasi', 'like', "%{$searchTerm}%")
                    ->orWhere('deskripsi', 'like', "%{$searchTerm}%")
                    ->orWhere('highlight', 'like', "%{$searchTerm}%");
            });
        }

        $destinations = $query->orderByDesc('is_popular')->orderBy('nama')->paginate(12)->withQueryString();

        $stats = [
            'total' => Destination::count(),
            'domestik' => Destination::where('wilayah', 'domestik')->count(),
            'internasional' => Destination::where('wilayah', 'internasional')->count(),
        ];

        return view('destinations.index', compact('destinations', 'stats'));
    }

    /**
     * Display the specified destination detail.
     */
    public function show(Destination $destination): View
    {
        $relatedDestinations = Destination::where('id', '!=', $destination->id)
            ->where(function ($q) use ($destination) {
                $q->where('wilayah', $destination->wilayah)
                    ->orWhere('kategori', $destination->kategori);
            })
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('destinations.show', compact('destination', 'relatedDestinations'));
    }
}
