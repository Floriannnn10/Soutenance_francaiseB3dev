<?php

namespace App\Http\Controllers;

use App\Models\CustomNotification;
use App\Models\User;
use App\Traits\DaisyUINotifier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\Notification;

class NotificationController extends Controller
{
    use DaisyUINotifier;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = CustomNotification::with(['utilisateurs']);

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

        $notification = CustomNotification::create([
            'message' => $request->message,
            'type' => $request->type,
        ]);

        // Attacher les utilisateurs
        $notification->utilisateurs()->attach($request->utilisateurs);

        return $this->successNotification('Notification créée avec succès !', 'notifications.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomNotification $notification): View
    {
        $notification->load(['utilisateurs']);
        return view('notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomNotification $notification): View
    {
        $utilisateurs = User::all();
        return view('notifications.edit', compact('notification', 'utilisateurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomNotification $notification): RedirectResponse
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

        // Synchroniser les utilisateurs
        $notification->utilisateurs()->sync($request->utilisateurs);

        return $this->successNotification('Notification mise à jour avec succès !', 'notifications.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomNotification $notification): RedirectResponse
    {
        $notification->delete();

        return $this->successNotification('Notification supprimée avec succès !', 'notifications.index');
    }

    /**
     * Marquer une notification comme lue pour l'utilisateur connecté
     */
        public function marquerLue(CustomNotification $notification): JsonResponse
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Utilisateur non connecté']);
        }

        $notification->utilisateurs()->updateExistingPivot($user->id, ['lu_a' => true]);

        return response()->json(['success' => true, 'message' => 'Notification marquée comme lue']);
    }

    /**
     * Marquer toutes les notifications comme lues pour l'utilisateur connecté
     */
    public function marquerToutesLues(): JsonResponse
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Utilisateur non connecté']);
        }

                // Marquer toutes les notifications comme lues
        $notifications = CustomNotification::whereHas('utilisateurs', function($query) use ($user) {
            $query->where('user_id', $user->id)->where('lu_a', false);
        })->get();

        foreach ($notifications as $notification) {
            $notification->utilisateurs()->updateExistingPivot($user->id, ['lu_a' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Toutes les notifications ont été marquées comme lues']);
    }

    /**
     * Obtenir les notifications non lues pour l'utilisateur connecté
     */
    public function getNotificationsNonLues(): JsonResponse
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Utilisateur non connecté']);
        }

        $notifications = CustomNotification::whereHas('utilisateurs', function($query) use ($user) {
            $query->where('user_id', $user->id)->where('lu_a', false);
        })->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }
}
