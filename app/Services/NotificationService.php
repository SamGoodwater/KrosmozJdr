<?php

namespace App\Services;

use App\Models\NotificationDigestQueue;
use App\Models\Page;
use App\Models\Section;
use App\Notifications\EntityModifiedNotification;
use App\Notifications\LastConnectionNotification;
use App\Notifications\NewUserCreatedNotification;
use App\Notifications\ProfileModifiedNotification;
use App\Notifications\UserDeletedNotification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Service centralisé pour l'envoi des notifications métier Krosmoz JDR.
 *
 * - Respecte les préférences utilisateur (canaux, fréquence : instant / digest)
 * - Applique la logique métier (créateur, droits page/section, admins, profil)
 * - Les payloads destinés au digest sont rendus JSON-serialisables (Carbon, Enum, etc.)
 */
class NotificationService
{
    /** Fréquences digest supportées (hors "instant" = envoi immédiat). */
    private const DIGEST_FREQUENCIES = ['daily', 'weekly', 'monthly'];

    /**
     * Enfile une notification pour envoi en digest (quotidien, hebdo, mensuel).
     * Le payload est normalisé pour être stocké en JSON (scalaires, tableaux, pas d'objets).
     *
     * @param int $userId
     * @param string $notificationType Clé du type (config notifications.types)
     * @param string $frequency daily|weekly|monthly
     * @param array<string, mixed> $payload Données à inclure dans le digest (seront sérialisées en JSON)
     */
    public static function pushToDigestQueue(int $userId, string $notificationType, string $frequency, array $payload): void
    {
        if (! in_array($frequency, self::DIGEST_FREQUENCIES, true)) {
            return;
        }
        NotificationDigestQueue::create([
            'user_id' => $userId,
            'notification_type' => $notificationType,
            'frequency' => $frequency,
            'payload' => self::payloadForJson($payload),
        ]);
    }

    /**
     * Rend un tableau sérialisable en JSON (Carbon → chaîne ISO, Enum → value, objets → tableau).
     * Évite les erreurs lors de l'écriture en colonne JSON.
     *
     * @param mixed $data
     * @return mixed
     */
    private static function payloadForJson($data)
    {
        if ($data instanceof \Carbon\CarbonInterface) {
            return $data->toIso8601String();
        }
        if ($data instanceof \BackedEnum) {
            return $data->value;
        }
        if ($data instanceof \UnitEnum) {
            return $data->name;
        }
        if (! is_array($data)) {
            return is_object($data) ? null : $data;
        }
        $out = [];
        foreach ($data as $k => $v) {
            $out[$k] = self::payloadForJson($v);
        }
        return $out;
    }

    /**
     * Notifie le créateur (hors self), les users avec droits (page/section), et les admins lors de la modification.
     * Pour Page/Section : types page_section_modified / page_section_modified_admin et destinataires étendus.
     *
     * @param object $entity Entité modifiée (doit avoir created_by, id, name ou title)
     * @param User $modifier Utilisateur ayant fait la modification
     * @param object|null $entityOld Ancienne entité (avant update, optionnel)
     * @param array $changes Tableau des changements (optionnel, surcharge le calcul automatique)
     */
    public static function notifyEntityModified($entity, User $modifier, $entityOld = null, array $changes = [])
    {
        if (empty($changes) && $entityOld) {
            $changes = self::computeChanges($entityOld, $entity);
        }
        $entityType = class_basename($entity);
        $entityId = $entity->id;
        $entityName = $entity->name ?? $entity->title ?? ('#' . $entityId);
        $isPageOrSection = $entity instanceof Page || $entity instanceof Section;
        $typeCreator = $isPageOrSection ? 'page_section_modified' : 'entity_modified';
        $typeAdmin = $isPageOrSection ? 'page_section_modified_admin' : 'entity_modified_admin';

        $entityUrl = self::entityUrl($entity);
        $notifyOne = function (User $user, string $type) use ($entityType, $entityId, $entityName, $modifier, $changes, $entityUrl) {
            $channels = $user->getChannelsForNotificationType($type);
            if (empty($channels)) {
                return;
            }
            $frequency = $user->getFrequencyForNotificationType($type);
            if ($frequency === 'instant') {
                $user->notify(new EntityModifiedNotification(
                    $entityType,
                    $entityId,
                    $entityName,
                    $modifier,
                    $channels,
                    $changes,
                    $entityUrl
                ));
                return;
            }
            self::pushToDigestQueue($user->id, $type, $frequency, [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'entity_name' => $entityName,
                'modifier_name' => $modifier->name,
                'message' => "L'entité {$entityType} : '{$entityName}' a été modifiée par {$modifier->name}.",
                'url' => $entityUrl,
                'changes' => $changes,
            ]);
        };

        if ($entity->created_by && $entity->created_by != $modifier->id) {
            $creator = User::find($entity->created_by);
            if ($creator && $creator->wantsNotificationForType($typeCreator)) {
                $notifyOne($creator, $typeCreator);
            }
        }

        if ($isPageOrSection && method_exists($entity, 'users')) {
            foreach ($entity->users()->get() as $u) {
                if ($u->id === $modifier->id || ($entity->created_by && $u->id === $entity->created_by)) {
                    continue;
                }
                if ($u->wantsNotificationForType($typeCreator)) {
                    $notifyOne($u, $typeCreator);
                }
            }
        }

        $admins = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])
            ->where('id', '!=', $modifier->id)
            ->get();
        foreach ($admins as $admin) {
            if (!$admin->wantsNotificationForType($typeAdmin)) {
                continue;
            }
            $notifyOne($admin, $typeAdmin);
        }
    }

    /**
     * Notifie l'utilisateur dont le profil a été modifié (respecte fréquence instant / digest).
     *
     * @param User $user Utilisateur modifié
     * @param User $modifier Utilisateur ayant fait la modification
     * @param User|null $old Ancien utilisateur (avant update, optionnel)
     */
    public static function notifyProfileModified(User $user, User $modifier, User $old = null)
    {
        $channels = $user->getChannelsForNotificationType('profile_modified');
        if (empty($channels)) {
            return;
        }
        $changes = $old ? self::computeChanges($old, $user) : [];
        $frequency = $user->getFrequencyForNotificationType('profile_modified');
        if ($frequency === 'instant') {
            $user->notify(new ProfileModifiedNotification($user, $modifier, $channels, $changes));
            return;
        }
        $payload = [
            'modified_user_id' => $user->id,
            'modifier_name' => $modifier->name,
            'message' => "Votre profil a été modifié par {$modifier->name}.",
            'url' => url('/users/' . $user->id),
            'changes' => $changes,
        ];
        self::pushToDigestQueue($user->id, 'profile_modified', $frequency, $payload);
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
            
            // Récupérer l'ancienne valeur de manière sécurisée
            $oldValue = null;
            if (is_object($old) && method_exists($old, 'getAttribute')) {
                $oldValue = $old->getAttribute($field);
            } elseif (is_object($old) && isset($old->$field)) {
                $oldValue = $old->$field;
            } elseif (is_array($old) && isset($old[$field])) {
                $oldValue = $old[$field];
            }
            
            // Convertir les enums en valeurs si nécessaire
            if ($oldValue instanceof \BackedEnum) {
                $oldValue = $oldValue->value;
            }
            $newValueForChange = $new->getAttribute($field);
            if ($newValueForChange instanceof \BackedEnum) {
                $newValueForChange = $newValueForChange->value;
            }
            
            $changes[$field] = [
                'old' => $oldValue,
                'new' => $newValueForChange,
                'image_url' => $isImage ? Storage::disk('public')->url($newValue) : null,
            ];
        }
        return $changes;
    }

    /**
     * Notifie tous les admins lors de la création d'une entité.
     * Envoi immédiat uniquement (pas de digest pour ce type).
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
        $admins = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])
            ->where('id', '!=', $creator->id)
            ->get();
        foreach ($admins as $admin) {
            if (!$admin->wantsNotificationForType('entity_created')) {
                continue;
            }
            $channels = $admin->getChannelsForNotificationType('entity_created');
            $admin->notify(new EntityModifiedNotification(
                $entityType,
                $entityId,
                $entityName,
                $creator,
                $channels,
                ['action' => ['old' => null, 'new' => $message]]
            ));
        }
    }

    /**
     * Notifie le créateur (hors self), les users avec droits (page/section), et les admins lors de la suppression.
     */
    public static function notifyEntityDeleted($entity, User $deleter)
    {
        $entityType = class_basename($entity);
        $entityId = $entity->id;
        $entityName = $entity->name ?? $entity->title ?? ('#' . $entityId);
        $message = "L'entité {$entityType} : '{$entityName}' a été supprimée par {$deleter->name}.";
        $isPageOrSection = $entity instanceof Page || $entity instanceof Section;
        $typeCreator = $isPageOrSection ? 'page_section_deleted' : 'entity_deleted';
        $typeAdmin = $isPageOrSection ? 'page_section_deleted_admin' : 'entity_deleted_admin';
        $changes = ['action' => ['old' => null, 'new' => $message]];

        $entityUrl = self::entityUrl($entity);
        $notifyOne = function (User $user, string $type) use ($entityType, $entityId, $entityName, $deleter, $message, $entityUrl) {
            $channels = $user->getChannelsForNotificationType($type);
            if (empty($channels)) {
                return;
            }
            $frequency = $user->getFrequencyForNotificationType($type);
            if ($frequency === 'instant') {
                $user->notify(new EntityModifiedNotification(
                    $entityType,
                    $entityId,
                    $entityName,
                    $deleter,
                    $channels,
                    ['action' => ['old' => null, 'new' => $message]],
                    $entityUrl
                ));
                return;
            }
            self::pushToDigestQueue($user->id, $type, $frequency, [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'entity_name' => $entityName,
                'modifier_name' => $deleter->name,
                'message' => $message,
                'url' => $entityUrl,
            ]);
        };

        if ($entity->created_by && $entity->created_by != $deleter->id) {
            $creator = User::find($entity->created_by);
            if ($creator && $creator->wantsNotificationForType($typeCreator)) {
                $notifyOne($creator, $typeCreator);
            }
        }

        if ($isPageOrSection && method_exists($entity, 'users')) {
            foreach ($entity->users()->get() as $u) {
                if ($u->id === $deleter->id || ($entity->created_by && $u->id === $entity->created_by)) {
                    continue;
                }
                if ($u->wantsNotificationForType($typeCreator)) {
                    $notifyOne($u, $typeCreator);
                }
            }
        }

        $admins = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])
            ->where('id', '!=', $deleter->id)
            ->get();
        foreach ($admins as $admin) {
            if ($admin->wantsNotificationForType($typeAdmin)) {
                $notifyOne($admin, $typeAdmin);
            }
        }
    }

    /**
     * Notifie le créateur (hors self) et les admins lors de la restauration d'une entité.
     * Envoi immédiat uniquement (pas de digest).
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
            if ($creator && $creator->wantsNotificationForType('entity_restored')) {
                $channels = $creator->getChannelsForNotificationType('entity_restored');
                $creator->notify(new EntityModifiedNotification(
                    $entityType,
                    $entityId,
                    $entityName,
                    $restorer,
                    $channels,
                    ['action' => ['old' => null, 'new' => $message]]
                ));
            }
        }
        // Notifier tous les admins (hors self)
        $admins = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])
            ->where('id', '!=', $restorer->id)
            ->get();
        foreach ($admins as $admin) {
            if (!$admin->wantsNotificationForType('entity_restored')) {
                continue;
            }
            $channels = $admin->getChannelsForNotificationType('entity_restored');
            $admin->notify(new EntityModifiedNotification(
                $entityType,
                $entityId,
                $entityName,
                $restorer,
                $channels,
                ['action' => ['old' => null, 'new' => $message]]
            ));
        }
    }

    /**
     * Notifie le créateur (hors self) et les admins lors de la suppression définitive d'une entité.
     * Envoi immédiat uniquement (pas de digest).
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
            if ($creator && $creator->wantsNotificationForType('entity_force_deleted')) {
                $channels = $creator->getChannelsForNotificationType('entity_force_deleted');
                $creator->notify(new EntityModifiedNotification(
                    $entityType,
                    $entityId,
                    $entityName,
                    $forcer,
                    $channels,
                    ['action' => ['old' => null, 'new' => $message]]
                ));
            }
        }
        // Notifier tous les admins (hors self)
        $admins = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])
            ->where('id', '!=', $forcer->id)
            ->get();
        foreach ($admins as $admin) {
            if (!$admin->wantsNotificationForType('entity_force_deleted')) {
                continue;
            }
            $channels = $admin->getChannelsForNotificationType('entity_force_deleted');
            $admin->notify(new EntityModifiedNotification(
                $entityType,
                $entityId,
                $entityName,
                $forcer,
                $channels,
                ['action' => ['old' => null, 'new' => $message]]
            ));
        }
    }

    /**
     * Construit l'URL d'accès à l'entité (pour le lien dans la notification).
     * Pour Section : utilise la relation page si chargée, sinon une requête est exécutée.
     *
     * @param object $entity Page, Section ou autre modèle avec id (et optionnellement slug, page_id)
     * @return string URL absolue
     */
    public static function entityUrl($entity): string
    {
        if ($entity instanceof Page && ! empty($entity->slug)) {
            return url('/pages/' . $entity->slug);
        }
        if ($entity instanceof Section && isset($entity->page_id)) {
            $page = $entity->relationLoaded('page') ? $entity->page : $entity->page()->first();
            return $page ? url('/pages/' . ($page->slug ?? $page->id)) : url('/pages');
        }
        $type = strtolower(class_basename($entity));
        return url('/' . $type . 's/' . ($entity->id ?? ''));
    }

    /**
     * Notifie les admins de la création d'un nouveau compte (inscription).
     */
    public static function notifyNewUserCreated(User $newUser): void
    {
        $admins = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])->get();
        foreach ($admins as $admin) {
            if (! $admin->wantsNotificationForType('new_account_registered')) {
                continue;
            }
            $frequency = $admin->getFrequencyForNotificationType('new_account_registered');
            if ($frequency === 'instant') {
                $admin->notify(new NewUserCreatedNotification($newUser));
                continue;
            }
            self::pushToDigestQueue($admin->id, 'new_account_registered', $frequency, [
                'new_user_id' => $newUser->id,
                'new_user_name' => $newUser->name,
                'new_user_email' => $newUser->email,
                'message' => "Nouveau compte créé : {$newUser->name} ({$newUser->email}).",
                'url' => url('/users'),
            ]);
        }
    }

    /**
     * Notifie les admins de la suppression d'un utilisateur (appeler avant le delete).
     */
    public static function notifyUserDeleted(User $deletedUser, User $deleter): void
    {
        $admins = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])
            ->where('id', '!=', $deleter->id)
            ->get();
        foreach ($admins as $admin) {
            if (! $admin->wantsNotificationForType('user_deleted')) {
                continue;
            }
            $frequency = $admin->getFrequencyForNotificationType('user_deleted');
            if ($frequency === 'instant') {
                $admin->notify(new UserDeletedNotification(
                    $deletedUser->id,
                    $deletedUser->name,
                    $deletedUser->email,
                    $deleter
                ));
                continue;
            }
            self::pushToDigestQueue($admin->id, 'user_deleted', $frequency, [
                'deleted_user_id' => $deletedUser->id,
                'deleted_user_name' => $deletedUser->name,
                'deleted_user_email' => $deletedUser->email,
                'deleter_name' => $deleter->name,
                'message' => "L'utilisateur {$deletedUser->name} a été supprimé par {$deleter->name}.",
                'url' => url('/users'),
            ]);
        }
    }

    /**
     * Notifie l'utilisateur de sa dernière connexion (enregistrée).
     */
    public static function notifyLastConnection(User $user): void
    {
        if (! $user->wantsNotificationForType('last_connection')) {
            return;
        }
        $loggedAt = $user->last_login_at ?? now();
        $loggedAtIso = $loggedAt->format('d/m/Y à H:i');
        $frequency = $user->getFrequencyForNotificationType('last_connection');
        if ($frequency === 'instant') {
            $user->notify(new LastConnectionNotification($loggedAtIso));
            return;
        }
        self::pushToDigestQueue($user->id, 'last_connection', $frequency, [
            'logged_at' => $loggedAtIso,
            'message' => 'Connexion enregistrée le ' . $loggedAtIso . '.',
            'url' => url('/user'),
        ]);
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
