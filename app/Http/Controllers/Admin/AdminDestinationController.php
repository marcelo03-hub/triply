<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminDestinationController extends Controller
{
    /**
     * Display a listing of destinations for admin.
     */
    public function index(Request $request): View
    {
        $query = Destination::query();

        if ($request->filled('q')) {
            $searchTerm = $request->input('q');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'like', "%{$searchTerm}%")
                    ->orWhere('lokasi', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('wilayah')) {
            $query->where('wilayah', $request->input('wilayah'));
        }

        $destinations = $query->orderBy('nama')->paginate(15)->withQueryString();

        return view('admin.destinations.index', compact('destinations'));
    }

    /**
     * Show the form for creating a new destination.
     */
    public function create(): View
    {
        return view('admin.destinations.create');
    }

    /**
     * Store a newly created destination.
     */
    public function store(Request $request): RedirectResponse
    {
        $slug = Str::slug($request->input('nama'));
        $request->merge(['slug' => $slug]);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:destinations,slug'],
            'lokasi' => ['required', 'string', 'max:255'],
            'wilayah' => ['required', 'in:domestik,internasional'],
            'kategori' => ['required', 'in:pantai,alam,budaya,kota,religi'],
            'deskripsi' => ['required', 'string'],
            'gambar' => ['nullable', 'string', 'max:500'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'estimasi_biaya' => ['nullable', 'string', 'max:255'],
            'rekomendasi_durasi' => ['nullable', 'string', 'max:100'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'highlight' => ['nullable', 'string', 'max:1000'],
            'tips' => ['nullable', 'string', 'max:1000'],
            'is_popular' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('destinations', 'public');
            $validated['gambar'] = $path;
        }
        unset($validated['foto']);

        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['rating'] = $validated['rating'] ?? 4.8;

        Destination::create($validated);

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi wisata "'.$validated['nama'].'" berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified destination.
     */
    public function edit(Destination $destination): View
    {
        return view('admin.destinations.edit', compact('destination'));
    }

    /**
     * Update the specified destination.
     */
    public function update(Request $request, Destination $destination): RedirectResponse
    {
        $slug = Str::slug($request->input('nama'));
        $request->merge(['slug' => $slug]);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('destinations', 'slug')->ignore($destination->id)],
            'lokasi' => ['required', 'string', 'max:255'],
            'wilayah' => ['required', 'in:domestik,internasional'],
            'kategori' => ['required', 'in:pantai,alam,budaya,kota,religi'],
            'deskripsi' => ['required', 'string'],
            'gambar' => ['nullable', 'string', 'max:500'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'estimasi_biaya' => ['nullable', 'string', 'max:255'],
            'rekomendasi_durasi' => ['nullable', 'string', 'max:100'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'highlight' => ['nullable', 'string', 'max:1000'],
            'tips' => ['nullable', 'string', 'max:1000'],
            'is_popular' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('foto')) {
            if ($destination->gambar && str_starts_with($destination->gambar, 'destinations/')) {
                Storage::disk('public')->delete($destination->gambar);
            }
            $path = $request->file('foto')->store('destinations', 'public');
            $validated['gambar'] = $path;
        }
        unset($validated['foto']);

        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['rating'] = $validated['rating'] ?? 4.8;

        $destination->update($validated);

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Data destinasi "'.$destination->nama.'" berhasil diperbarui!');
    }

    /**
     * Remove the specified destination.
     */
    public function destroy(Destination $destination): RedirectResponse
    {
        $nama = $destination->nama;
        $destination->delete();

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi "'.$nama.'" berhasil dihapus dari sistem.');
    }
}
