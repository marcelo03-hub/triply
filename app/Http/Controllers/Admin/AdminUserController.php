<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a listing of registered users.
     */
    public function index(Request $request): View
    {
        $query = User::withCount('trips');

        if ($request->filled('q')) {
            $searchTerm = $request->input('q');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('role')) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'user') {
                $query->where('is_admin', false);
            }
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'admins' => User::where('is_admin', true)->count(),
            'travelers' => User::where('is_admin', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Toggle the admin status of a user.
     */
    public function toggleAdmin(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah status role akun Anda sendiri.');
        }

        $user->is_admin = ! $user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'dijadikan Administrator' : 'diubah menjadi Pengguna Biasa';

        return back()->with('success', 'Role akun '.$user->name.' berhasil '.$status.'!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        $nama = $user->name;
        $user->delete();

        return back()->with('success', 'Akun pengguna "'.$nama.'" beserta data perjalanannya berhasil dihapus.');
    }
}
