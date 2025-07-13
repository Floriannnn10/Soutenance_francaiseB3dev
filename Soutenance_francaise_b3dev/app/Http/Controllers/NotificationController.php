<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\TypeNotification;
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
        $query = Notification::with(['typeNotification', 'utilisateurs']);

        if ($request->filled('type_id')) {
            $query->where('type_notification_id', $request->type_id);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(15);
        $typesNotification = TypeNotification::all();

        return view('notifications.index', compact('notifications', 'typesNotification'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $typesNotification = TypeNotification::all();
        $utilisateurs = User::all();
        return view('notifications.create', compact('typesNotification', 'utilisateurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'type_notification_id' => 'required|exists:types_notification,id',
            'utilisateurs' => 'required|array',
            'utilisateurs.*' => 'exists:users,id',
            'date_envoi' => 'nullable|date|after_or_equal:today',
        ]);

        $notification = Notification::create([
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'type_notification_id' => $request->type_notification_id,
            'date_envoi' => $request->date_envoi ?? now(),
            'est_envoyee' => false,
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
        $notification->load(['typeNotification', 'utilisateurs']);
        return view('notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification): View
    {
        $typesNotification = TypeNotification::all();
        $utilisateurs = User::all();
        return view('notifications.edit', compact('notification', 'typesNotification', 'utilisateurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification): RedirectResponse
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'type_notification_id' => 'required|exists:types_notification,id',
            'utilisateurs' => 'required|array',
            'utilisateurs.*' => 'exists:users,id',
            'date_envoi' => 'nullable|date',
        ]);

        $notification->update([
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'type_notification_id' => $request->type_notification_id,
            'date_envoi' => $request->date_envoi,
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
            'est_lue' => true,
            'lu_a' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Envoyer une notification.
     */
    public function envoyer(Notification $notification): RedirectResponse
    {
        if ($notification->est_envoyee) {
            return redirect()->back()
                ->with('error', 'Cette notification a déjà été envoyée.');
        }

        $notification->update([
            'est_envoyee' => true,
            'envoyee_a' => now(),
        ]);

        return redirect()->route('notifications.index')
            ->with('success', 'Notification envoyée avec succès.');
    }
}
