<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Contrôleur des notifications (centre de notifications).
 *
 * Expose la liste des notifications de l'utilisateur connecté (lu/non lu, archivées, épinglées),
 * marquage lu, archivage, épinglage et suppression.
 */
class NotificationController extends Controller
{
    /**
     * Liste des notifications (API JSON) ou page plein écran (Inertia).
     * En JSON : filtre archived=0|1, pagination, unread_count.
     *
     * @return JsonResponse|Response
     */
    public function index(Request $request): JsonResponse|Response
    {
        if (! $request->wantsJson()) {
            return Inertia::render('Pages/notifications/Index', [
                'unreadCount' => $request->user()->unreadNotifications()->whereNull('archived_at')->count(),
            ]);
        }

        $user = $request->user();
        $perPage = (int) $request->input('per_page', 15);
        $perPage = min(max($perPage, 5), 50);
        $archived = $request->boolean('archived', false);

        $query = $user->notifications();
        if ($archived) {
            $query->whereNotNull('archived_at');
        } else {
            $query->whereNull('archived_at');
        }
        $notifications = $query->orderByRaw('pinned_at IS NOT NULL DESC')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $items = $notifications->getCollection()->map(fn (DatabaseNotification $n) => $this->formatNotification($n));

        $unreadCount = $user->unreadNotifications()->whereNull('archived_at')->count();

        return response()->json([
            'data' => $items,
            'unread_count' => $unreadCount,
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Formate une notification pour l'API (tableau associatif).
     *
     * @return array<string, mixed>
     */
    private function formatNotification(DatabaseNotification $notification): array
    {
        $data = is_array($notification->data) ? $notification->data : [];
        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'message' => $data['message'] ?? '',
            'url' => $data['url'] ?? null,
            'read_at' => isset($notification->read_at) ? $notification->read_at->toIso8601String() : null,
            'archived_at' => isset($notification->archived_at) ? $notification->archived_at->toIso8601String() : null,
            'pinned_at' => isset($notification->pinned_at) ? $notification->pinned_at->toIso8601String() : null,
            'created_at' => $notification->created_at->toIso8601String(),
            'data' => $data,
        ];
    }

    /**
     * Marquer une notification comme lue.
     *
     * @param string $id UUID de la notification
     * @return JsonResponse
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification introuvable.'], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues (non archivées).
     *
     * @return JsonResponse
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications()
            ->whereNull('archived_at')
            ->get()
            ->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    /**
     * Archiver une notification.
     *
     * @param string $id UUID de la notification
     * @return JsonResponse
     */
    public function archive(Request $request, string $id): JsonResponse
    {
        $notification = $this->findNotification($request, $id);
        if (! $notification) {
            return response()->json(['message' => 'Notification introuvable.'], 404);
        }
        $notification->archived_at = now();
        $notification->save();

        return response()->json([
            'success' => true,
            'unread_count' => $request->user()->unreadNotifications()->whereNull('archived_at')->count(),
        ]);
    }

    /**
     * Désarchiver une notification.
     *
     * @param string $id UUID de la notification
     * @return JsonResponse
     */
    public function unarchive(Request $request, string $id): JsonResponse
    {
        $notification = $this->findNotification($request, $id);
        if (! $notification) {
            return response()->json(['message' => 'Notification introuvable.'], 404);
        }
        $notification->archived_at = null;
        $notification->save();

        return response()->json([
            'success' => true,
            'unread_count' => $request->user()->unreadNotifications()->whereNull('archived_at')->count(),
        ]);
    }

    /**
     * Épingler une notification (mise en avant).
     *
     * @param string $id UUID de la notification
     * @return JsonResponse
     */
    public function pin(Request $request, string $id): JsonResponse
    {
        $notification = $this->findNotification($request, $id);
        if (! $notification) {
            return response()->json(['message' => 'Notification introuvable.'], 404);
        }
        $notification->pinned_at = now();
        $notification->save();

        return response()->json(['success' => true]);
    }

    /**
     * Désépingler une notification.
     *
     * @param string $id UUID de la notification
     * @return JsonResponse
     */
    public function unpin(Request $request, string $id): JsonResponse
    {
        $notification = $this->findNotification($request, $id);
        if (! $notification) {
            return response()->json(['message' => 'Notification introuvable.'], 404);
        }
        $notification->pinned_at = null;
        $notification->save();

        return response()->json(['success' => true]);
    }

    /**
     * Supprimer définitivement une notification.
     *
     * @param string $id UUID de la notification
     * @return JsonResponse
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = $this->findNotification($request, $id);
        if (! $notification) {
            return response()->json(['message' => 'Notification introuvable.'], 404);
        }
        $notification->delete();

        return response()->json([
            'success' => true,
            'unread_count' => $request->user()->unreadNotifications()->whereNull('archived_at')->count(),
        ]);
    }

    /**
     * Récupère une notification appartenant à l'utilisateur connecté.
     *
     * @return DatabaseNotification|null
     */
    private function findNotification(Request $request, string $id): ?DatabaseNotification
    {
        return $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();
    }
}
