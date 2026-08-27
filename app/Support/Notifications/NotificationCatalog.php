<?php

declare(strict_types=1);

namespace App\Support\Notifications;

use App\Notifications\DigestNotification;
use App\Notifications\EntityModifiedNotification;
use App\Notifications\FeedbackThreadNotification;
use App\Notifications\LastConnectionNotification;
use App\Notifications\NewUserCreatedNotification;
use App\Notifications\ProfileModifiedNotification;
use App\Notifications\ProjectConsoleJobProgressNotification;
use App\Notifications\ProjectMaintenanceNotification;
use App\Notifications\ScrappingJobProgressNotification;
use App\Notifications\UserDeletedNotification;

/**
 * Métadonnées des notifications in-app (catégorie, type config, actions admin).
 *
 * @see config/notifications.php
 * @see docs/features/notifications/README.md
 */
class NotificationCatalog
{
    /** @var array<string, array{config_type: string|null, category: string, action_label: string|null}> */
    private const CLASS_META = [
        NewUserCreatedNotification::class => [
            'config_type' => 'new_account_registered',
            'category' => 'admin_action',
            'action_label' => 'Examiner le compte',
        ],
        UserDeletedNotification::class => [
            'config_type' => 'user_deleted',
            'category' => 'admin',
            'action_label' => 'Voir les utilisateurs',
        ],
        ProjectMaintenanceNotification::class => [
            'config_type' => 'project_maintenance',
            'category' => 'admin',
            'action_label' => 'Voir l’administration',
        ],
        ProfileModifiedNotification::class => [
            'config_type' => 'profile_modified',
            'category' => 'personal',
            'action_label' => 'Voir mon profil',
        ],
        LastConnectionNotification::class => [
            'config_type' => 'last_connection',
            'category' => 'personal',
            'action_label' => null,
        ],
        DigestNotification::class => [
            'config_type' => null,
            'category' => 'personal',
            'action_label' => 'Voir les notifications',
        ],
        ScrappingJobProgressNotification::class => [
            'config_type' => null,
            'category' => 'system',
            'action_label' => null,
        ],
        ProjectConsoleJobProgressNotification::class => [
            'config_type' => null,
            'category' => 'system',
            'action_label' => 'Voir le job',
        ],
        EntityModifiedNotification::class => [
            'config_type' => null,
            'category' => 'personal',
            'action_label' => 'Voir l’entité',
        ],
        FeedbackThreadNotification::class => [
            'config_type' => null,
            'category' => 'personal',
            'action_label' => 'Voir le retour',
        ],
    ];

    /**
     * Résout le type config et la catégorie à partir de la notification BDD.
     *
     * @param  array<string, mixed>  $data
     * @return array{config_type: string|null, category: string, action_label: string|null, label: string|null}
     */
    public static function resolve(string $notificationClass, array $data): array
    {
        $configType = is_string($data['config_type'] ?? null) ? $data['config_type'] : null;

        if ($notificationClass === DigestNotification::class) {
            $configType = is_string($data['notification_type'] ?? null) ? $data['notification_type'] : $configType;
        }

        if ($configType === null) {
            $configType = self::CLASS_META[$notificationClass]['config_type'] ?? null;
        }

        $category = self::categoryForConfigType($configType)
            ?? self::CLASS_META[$notificationClass]['category']
            ?? 'personal';

        $actionLabel = self::CLASS_META[$notificationClass]['action_label'] ?? null;
        if ($configType !== null) {
            $actionLabel = config('notifications.types.'.$configType.'.action_label', $actionLabel);
        }

        $label = $configType !== null
            ? (config('notifications.types.'.$configType.'.label') ?? null)
            : null;

        return [
            'config_type' => $configType,
            'category' => $category,
            'action_label' => is_string($actionLabel) ? $actionLabel : null,
            'label' => is_string($label) ? $label : null,
        ];
    }

    /**
     * Types config considérés comme « action admin » (à traiter).
     *
     * @return list<string>
     */
    public static function adminActionConfigTypes(): array
    {
        return self::configTypesByCategory('admin_action');
    }

    /**
     * Types config visibles dans la vue admin.
     *
     * @return list<string>
     */
    public static function adminConfigTypes(): array
    {
        return array_values(array_unique(array_merge(
            self::configTypesByCategory('admin_action'),
            self::configTypesByCategory('admin'),
        )));
    }

    /**
     * Classes Laravel Notification correspondant aux types admin.
     *
     * @return list<string>
     */
    public static function adminNotificationClasses(): array
    {
        return [
            NewUserCreatedNotification::class,
            UserDeletedNotification::class,
            ProjectMaintenanceNotification::class,
            EntityModifiedNotification::class,
            FeedbackThreadNotification::class,
        ];
    }

    /**
     * @return list<string>
     */
    private static function configTypesByCategory(string $category): array
    {
        $types = config('notifications.types', []);
        $out = [];
        foreach ($types as $key => $meta) {
            if (($meta['category'] ?? 'personal') === $category) {
                $out[] = $key;
            }
        }

        return $out;
    }

    private static function categoryForConfigType(?string $configType): ?string
    {
        if ($configType === null) {
            return null;
        }

        $category = config('notifications.types.'.$configType.'.category');

        return is_string($category) ? $category : null;
    }
}
