<?php

namespace App\Services\Privacy;

use App\Models\DataSubjectRequest;
use App\Models\PrivacyAuditLog;
use App\Models\PrivacyExport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service d'effacement RGPD : anonymisation et purge des données personnelles.
 *
 * - Dissocie created_by sur le contenu collaboratif (contenu conservé, auteur anonymisé)
 * - Supprime pivots, sessions, notifications, exports, médias
 * - Anonymise le profil utilisateur puis soft-delete
 */
class UserErasureService
{
    public const ANONYMOUS_NAME = 'Utilisateur supprimé';

    /**
     * Exécute l'effacement complet pour un utilisateur.
     *
     * @param  User  $user  Utilisateur à effacer (doit exister, non système, pas dernier super_admin)
     * @param  DataSubjectRequest|null  $request  Demande RGPD associée (optionnel)
     * @param  int|null  $actorId  ID de l'acteur (null = auto-demandé)
     * @return void
     *
     * @throws \RuntimeException Si l'utilisateur est système ou dernier super_admin
     */
    public function execute(User $user, ?DataSubjectRequest $request = null, ?int $actorId = null): void
    {
        $this->guardErasure($user);

        $userId = $user->id;
        $actorId = $actorId ?? $userId;

        DB::transaction(function () use ($user, $userId, $request): void {
            // 1. Dissocier created_by sur le contenu (conserver le contenu, anonymiser l'auteur)
            $this->dissociateCreatedBy($userId);

            // 2. Supprimer les pivots (accès personnalisés)
            $this->deletePivotRelations($userId);

            // 3. Supprimer sessions, notifications, exports, filter presets
            $this->deleteUserArtifacts($userId);

            // 4. Supprimer les exports RGPD et leurs fichiers
            $this->deletePrivacyExports($userId);

            // 5. Médias (avatar)
            $user->clearMediaCollection('avatars');

            // 6. Anonymiser le profil puis soft-delete
            $this->anonymizeAndSoftDelete($user);
        });

        if ($request) {
            $request->update([
                'status' => DataSubjectRequest::STATUS_COMPLETED,
                'processed_at' => now(),
            ]);
        }

        PrivacyAuditLog::log(
            PrivacyAuditLog::ACTION_ERASURE_EXECUTED,
            $userId,
            $actorId,
            ['request_id' => $request?->id],
            null,
            null
        );
    }

    /**
     * Vérifie que l'effacement est autorisé.
     */
    protected function guardErasure(User $user): void
    {
        if ($user->is_system ?? false) {
            throw new \RuntimeException('Impossible de supprimer un compte système.');
        }

        if ($user->role === User::ROLE_SUPER_ADMIN) {
            $otherSuperAdmins = User::query()
                ->where('role', User::ROLE_SUPER_ADMIN)
                ->where('id', '!=', $user->id)
                ->exists();

            if (! $otherSuperAdmins) {
                throw new \RuntimeException('Impossible de supprimer le dernier super administrateur.');
            }
        }
    }

    /**
     * Dissocie created_by sur toutes les tables concernées.
     */
    protected function dissociateCreatedBy(int $userId): void
    {
        $tables = [
            'campaigns',
            'scenarios',
            'creatures',
            'pages',
            'sections',
            'attributes',
            'items',
            'consumables',
            'resources',
            'capabilities',
            'breeds',
            'specializations',
            'shops',
            'panoplies',
            'spells',
            'monster_races',
            'item_types',
            'consumable_types',
            'resource_types',
            'spell_types',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'created_by')) {
                DB::table($table)->where('created_by', $userId)->update(['created_by' => null]);
            }
        }
    }

    protected function deletePivotRelations(int $userId): void
    {
        $pivots = [
            'campaign_user' => 'user_id',
            'scenario_user' => 'user_id',
            'page_user' => 'user_id',
            'section_user' => 'user_id',
        ];

        foreach ($pivots as $table => $column) {
            if (Schema::hasTable($table)) {
                DB::table($table)->where($column, $userId)->delete();
            }
        }
    }

    protected function deleteUserArtifacts(int $userId): void
    {
        if (Schema::hasTable('sessions')) {
            DB::table('sessions')->where('user_id', $userId)->delete();
        }
        if (Schema::hasTable('notifications')) {
            DB::table('notifications')
                ->where('notifiable_type', User::class)
                ->where('notifiable_id', $userId)
                ->delete();
        }
        if (Schema::hasTable('notification_digest_queue')) {
            DB::table('notification_digest_queue')->where('user_id', $userId)->delete();
        }
        if (Schema::hasTable('table_filter_presets')) {
            DB::table('table_filter_presets')->where('user_id', $userId)->delete();
        }
        if (Schema::hasTable('scrapping_jobs')) {
            DB::table('scrapping_jobs')->where('requested_by', $userId)->update(['requested_by' => null]);
        }
    }

    protected function deletePrivacyExports(int $userId): void
    {
        $exports = PrivacyExport::query()->where('user_id', $userId)->get();
        foreach ($exports as $export) {
            if ($export->path && Storage::disk('local')->exists($export->path)) {
                Storage::disk('local')->delete($export->path);
            }
            $export->delete();
        }
    }

    protected function anonymizeAndSoftDelete(User $user): void
    {
        $user->update([
            'name' => self::ANONYMOUS_NAME,
            'email' => sprintf('deleted-%d@%s.anonymized', $user->id, Str::random(8)),
            'avatar' => null,
            'password' => Hash::make(Str::random(64)),
            'remember_token' => null,
            'notifications_enabled' => false,
            'notification_channels' => [],
            'notification_preferences' => [],
        ]);

        $user->delete();
    }

}
