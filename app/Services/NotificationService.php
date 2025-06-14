<?php

namespace App\Services;

use App\Notifications\EntityModifiedNotification;
use App\Notifications\ProfileModifiedNotification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Service centralisé pour l'envoi des notifications métier Krosmoz JDR.
 *
 * - Respecte les préférences utilisateur (canaux, activation)
 * - Applique la logique métier (créateur, admins, profil)
 */
class NotificationService
{
    /**
     * Notifie le créateur (hors self) et tous les admins (hors self) lors de la modification d'une entité.
     *
     * @param object $entity Entité modifiée (doit avoir created_by, id, name ou title)
     * @param User $modifier Utilisateur ayant fait la modification
     * @param object|null $entityOld Ancienne entité (avant update, optionnel)
     * @param array $changes Tableau des changements (optionnel, surcharge le calcul automatique)
     */
    public static function notifyEntityModified($entity, User $modifier, $entityOld = null, array $changes = [])
    {
        // Si aucun changement fourni mais entityOld présent, on les calcule
        if (empty($changes) && $entityOld) {
            $changes = self::computeChanges($entityOld, $entity);
        }
        // Détermination du type d'entité
        $entityType = class_basename($entity);
        $entityId = $entity->id;
        $entityName = $entity->name ?? $entity->title ?? ('#' . $entityId);

        // Notifier le créateur (hors self)
        if ($entity->created_by && $entity->created_by != $modifier->id) {
            $creator = User::find($entity->created_by);
            if ($creator && $creator->wantsNotification()) {
                $creator->notify(new EntityModifiedNotification(
                    $entityType,
                    $entityId,
                    $entityName,
                    $modifier,
                    $creator->notificationChannels(),
                    $changes
                ));
            }
        }

        // Notifier tous les admins (hors self)
        $admins = User::whereIn('role', ['admin', 'super_admin'])
            ->where('id', '!=', $modifier->id)
            ->where('notifications_enabled', true)
            ->get();
        foreach ($admins as $admin) {
            $admin->notify(new EntityModifiedNotification(
                $entityType,
                $entityId,
                $entityName,
                $modifier,
                $admin->notificationChannels(),
                $changes
            ));
        }
    }

    /**
     * Notifie toujours l'utilisateur dont le profil a été modifié (tous canaux, même si self).
     *
     * @param User $user Utilisateur modifié
     * @param User $modifier Utilisateur ayant fait la modification
     * @param User|null $old Ancien utilisateur (avant update, optionnel)
     */
    public static function notifyProfileModified(User $user, User $modifier, User $old = null)
    {
        $changes = $old ? self::computeChanges($old, $user) : [];
        $user->notify(new ProfileModifiedNotification($user, $modifier, ['database', 'mail'], $changes));
    }

    /**
     * Calcule les changements entre deux entités Eloquent (avant/après update).
     *
     * @param object $old Ancienne entité (avant update)
     * @param object $new Nouvelle entité (après update)
     * @param array $ignore Champs à ignorer (par défaut ['updated_at'])
     * @return array Tableau des changements (clé => [old, new, is_image, image_url])
     */
    public static function computeChanges($old, $new, $ignore = ['updated_at'])
    {
        $changes = [];
        foreach ($new->getChanges() as $field => $newValue) {
            if (in_array($field, $ignore)) continue;
            $isImage = is_string($newValue) && FileService::isImagePath($newValue);
            $changes[$field] = [
                'old' => $old->$field,
                'new' => $new->$field,
                'image_url' => $isImage ? Storage::disk('public')->url($newValue) : null,
            ];
        }
        return $changes;
    }

    /**
     * Notifie tous les admins lors de la création d'une entité.
     *
     * @param object $entity Entité créée
     * @param User $creator Utilisateur ayant créé l'entité
     */
    public static function notifyEntityCreated($entity, User $creator)
    {
        $entityType = class_basename($entity);
        $entityId = $entity->id;
        $entityName = $entity->name ?? $entity->title ?? ('#' . $entityId);
        $message = "L'entité {$entityType} : '{$entityName}' a été créée par {$creator->name}.";

        // Notifier tous les admins (hors self)
        $admins = User::whereIn('role', ['admin', 'super_admin'])
            ->where('id', '!=', $creator->id)
            ->where('notifications_enabled', true)
            ->get();
        foreach ($admins as $admin) {
            $admin->notify(new EntityModifiedNotification(
                $entityType,
                $entityId,
                $entityName,
                $creator,
                $admin->notificationChannels(),
                ['action' => ['old' => null, 'new' => $message]]
            ));
        }
    }

    /**
     * Notifie le créateur (hors self) lors de la suppression d'une entité.
     *
     * @param object $entity Entité supprimée
     * @param User $deleter Utilisateur ayant supprimé l'entité
     */
    public static function notifyEntityDeleted($entity, User $deleter)
    {
        $entityType = class_basename($entity);
        $entityId = $entity->id;
        $entityName = $entity->name ?? $entity->title ?? ('#' . $entityId);
        $message = "L'entité {$entityType} : '{$entityName}' a été supprimée par {$deleter->name}.";

        // Notifier le créateur (hors self)
        if ($entity->created_by && $entity->created_by != $deleter->id) {
            $creator = User::find($entity->created_by);
            if ($creator && $creator->wantsNotification()) {
                $creator->notify(new EntityModifiedNotification(
                    $entityType,
                    $entityId,
                    $entityName,
                    $deleter,
                    $creator->notificationChannels(),
                    ['action' => ['old' => null, 'new' => $message]]
                ));
            }
        }
        // Notifier tous les admins (hors self)
        $admins = User::whereIn('role', ['admin', 'super_admin'])
            ->where('id', '!=', $deleter->id)
            ->where('notifications_enabled', true)
            ->get();
        foreach ($admins as $admin) {
            $admin->notify(new EntityModifiedNotification(
                $entityType,
                $entityId,
                $entityName,
                $deleter,
                $admin->notificationChannels(),
                ['action' => ['old' => null, 'new' => $message]]
            ));
        }
    }

    /**
     * Notifie le créateur (hors self) lors de la restauration d'une entité.
     *
     * @param object $entity Entité restaurée
     * @param User $restorer Utilisateur ayant restauré l'entité
     */
    public static function notifyEntityRestored($entity, User $restorer)
    {
        $entityType = class_basename($entity);
        $entityId = $entity->id;
        $entityName = $entity->name ?? $entity->title ?? ('#' . $entityId);
        $message = "L'entité {$entityType} : '{$entityName}' a été restaurée par {$restorer->name}.";

        // Notifier le créateur (hors self)
        if ($entity->created_by && $entity->created_by != $restorer->id) {
            $creator = User::find($entity->created_by);
            if ($creator && $creator->wantsNotification()) {
                $creator->notify(new EntityModifiedNotification(
                    $entityType,
                    $entityId,
                    $entityName,
                    $restorer,
                    $creator->notificationChannels(),
                    ['action' => ['old' => null, 'new' => $message]]
                ));
            }
        }
        // Notifier tous les admins (hors self)
        $admins = User::whereIn('role', ['admin', 'super_admin'])
            ->where('id', '!=', $restorer->id)
            ->where('notifications_enabled', true)
            ->get();
        foreach ($admins as $admin) {
            $admin->notify(new EntityModifiedNotification(
                $entityType,
                $entityId,
                $entityName,
                $restorer,
                $admin->notificationChannels(),
                ['action' => ['old' => null, 'new' => $message]]
            ));
        }
    }

    /**
     * Notifie le créateur (hors self) lors de la suppression définitive d'une entité.
     *
     * @param object $entity Entité supprimée définitivement
     * @param User $forcer Utilisateur ayant supprimé définitivement l'entité
     */
    public static function notifyEntityForceDeleted($entity, User $forcer)
    {
        $entityType = class_basename($entity);
        $entityId = $entity->id;
        $entityName = $entity->name ?? $entity->title ?? ('#' . $entityId);
        $message = "L'entité {$entityType} : '{$entityName}' a été supprimée définitivement par {$forcer->name}.";

        // Notifier le créateur (hors self)
        if ($entity->created_by && $entity->created_by != $forcer->id) {
            $creator = User::find($entity->created_by);
            if ($creator && $creator->wantsNotification()) {
                $creator->notify(new EntityModifiedNotification(
                    $entityType,
                    $entityId,
                    $entityName,
                    $forcer,
                    $creator->notificationChannels(),
                    ['action' => ['old' => null, 'new' => $message]]
                ));
            }
        }
        // Notifier tous les admins (hors self)
        $admins = User::whereIn('role', ['admin', 'super_admin'])
            ->where('id', '!=', $forcer->id)
            ->where('notifications_enabled', true)
            ->get();
        foreach ($admins as $admin) {
            $admin->notify(new EntityModifiedNotification(
                $entityType,
                $entityId,
                $entityName,
                $forcer,
                $admin->notificationChannels(),
                ['action' => ['old' => null, 'new' => $message]]
            ));
        }
    }

    /**
     * Tronque et nettoie une valeur potentiellement longue ou HTML.
     * @param mixed $value
     * @return string
     */
    public static function truncateAndSanitize($value): string
    {
        $str = is_scalar($value) ? (string)$value : json_encode($value);
        $str = strip_tags($str);
        if (mb_strlen($str) > 120) {
            $str = mb_substr($str, 0, 117) . '...';
        }
        return $str;
    }
}
