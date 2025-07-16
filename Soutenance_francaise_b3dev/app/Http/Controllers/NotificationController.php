<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Notification::with(['utilisateurs']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $utilisateurs = User::all();
        return view('notifications.create', compact('utilisateurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string',
            'type' => 'required|string|max:255',
            'utilisateurs' => 'required|array',
            'utilisateurs.*' => 'exists:users,id',
        ]);

        $notification = Notification::create([
            'message' => $request->message,
            'type' => $request->type,
        ]);

        // Attacher les utilisateurs
        $notification->utilisateurs()->attach($request->utilisateurs);

        return redirect()->route('notifications.index')
            ->with('success', 'Notification créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification): View
    {
        $notification->load(['utilisateurs']);
        return view('notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification): View
    {
        $utilisateurs = User::all();
        return view('notifications.edit', compact('notification', 'utilisateurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string',
            'type' => 'required|string|max:255',
            'utilisateurs' => 'required|array',
            'utilisateurs.*' => 'exists:users,id',
        ]);

        $notification->update([
            'message' => $request->message,
            'type' => $request->type,
        ]);

        // Mettre à jour les utilisateurs
        $notification->utilisateurs()->sync($request->utilisateurs);

        return redirect()->route('notifications.index')
            ->with('success', 'Notification mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification supprimée avec succès.');
    }

    /**
     * Marquer une notification comme lue.
     */
    public function marquerLue(Notification $notification): RedirectResponse
    {
        $notification->utilisateurs()->updateExistingPivot(auth()->id(), [
            'read_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Notification marquée comme lue.');
    }
}
