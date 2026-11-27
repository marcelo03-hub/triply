<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\FavoriteDestination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FavoriteDestinationController extends Controller
{
    /**
     * Display a listing of user's favorite destinations.
     */
    public function index(): View
    {
        $favorites = Auth::user()->favorites()
            ->with('destination')
            ->latest()
            ->paginate(12);

        return view('destinations.favorites', compact('favorites'));
    }

    /**
     * Toggle favorite status for a destination.
     */
    public function toggle(Request $request, Destination $destination): RedirectResponse
    {
        $user = Auth::user();

        $existing = FavoriteDestination::where('user_id', $user->id)
            ->where('destination_id', $destination->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Destinasi "'.$destination->nama.'" dihapus dari favorit.';
        } else {
            FavoriteDestination::create([
                'user_id' => $user->id,
                'destination_id' => $destination->id,
            ]);
            $message = 'Destinasi "'.$destination->nama.'" berhasil ditambahkan ke favorit!';
        }

        return back()->with('success', $message);
    }
}
