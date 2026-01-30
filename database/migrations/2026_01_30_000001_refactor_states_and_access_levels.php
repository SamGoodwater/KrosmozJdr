<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cycle de vie unifié (remplace `usable` + anciens états de pages/sections).
     */
    private const STATES = ['raw', 'draft', 'playable', 'archived'];

    /**
     * Mapping rôle (string historique) -> niveau (int).
     */
    private const ROLE_LEVELS = [
        'guest' => 0,
        'user' => 1,
        'player' => 2,
        'game_master' => 3,
        'admin' => 4,
        'super_admin' => 5,
    ];

    private function roleCase(string $column): string
    {
        $cases = [];
        foreach (self::ROLE_LEVELS as $role => $level) {
            $cases[] = "WHEN '{$role}' THEN {$level}";
        }
        $casesSql = implode(' ', $cases);

        return "CASE {$column} {$casesSql} ELSE 0 END";
    }

    private function ensureWriteGteRead(string $table): void
    {
        // Évite GREATEST() (portable) : write_level = max(write_level, read_level)
        DB::statement("
            UPDATE {$table}
            SET write_level = CASE
                WHEN write_level < read_level THEN read_level
                ELSE write_level
            END
        ");
    }

    public function up(): void
    {
        /**
         * Tables "entity/type" historiques : `usable` (tinyint) + `is_visible` (string role)
         * -> `state` (string) + `read_level`/`write_level` (tinyint)
         */
        $usableVisibleTables = [
            'attributes',
            'capabilities',
            'classes',
            'consumables',
            'consumable_types',
            'creatures',
            'item_types',
            'items',
            'monster_races',
            'panoplies',
            'resources',
            'resource_types',
            'shops',
            'specializations',
            'spell_types',
            'spells',
        ];

        foreach ($usableVisibleTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('state')->default('draft')->after('is_visible');
                $table->tinyInteger('read_level')->default(0)->after('state');
                $table->tinyInteger('write_level')->default(3)->after('read_level');
            });

            DB::statement("UPDATE {$tableName} SET state = CASE WHEN usable = 1 THEN 'playable' ELSE 'draft' END");
            DB::statement("UPDATE {$tableName} SET read_level = " . $this->roleCase('is_visible'));
            // défaut GM (3) puis clamp write>=read
            DB::statement("UPDATE {$tableName} SET write_level = 3");
            $this->ensureWriteGteRead($tableName);

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['usable', 'is_visible']);
            });
        }

        /**
         * Pages / Sections : `is_visible` + `can_edit_role` + état PageState
         * -> `read_level` + `write_level` + état unifié (draft/playable/archived/raw)
         */
        $pagesHasCanEditRole = Schema::hasColumn('pages', 'can_edit_role');
        Schema::table('pages', function (Blueprint $table) {
            $table->tinyInteger('read_level')->default(0)->after('slug');
            $table->tinyInteger('write_level')->default(4)->after('read_level');
        });
        // read_level depuis is_visible
        DB::statement("UPDATE pages SET read_level = " . $this->roleCase('is_visible'));
        // write_level depuis can_edit_role (si présent) sinon admin (4)
        if ($pagesHasCanEditRole) {
            DB::statement("UPDATE pages SET write_level = " . $this->roleCase('can_edit_role'));
        } else {
            DB::statement("UPDATE pages SET write_level = 4");
        }
        $this->ensureWriteGteRead('pages');
        // Migration d'état: published -> playable, preview -> draft
        DB::statement("
            UPDATE pages
            SET state = CASE
                WHEN state = 'published' THEN 'playable'
                WHEN state = 'preview' THEN 'draft'
                ELSE state
            END
        ");
        Schema::table('pages', function (Blueprint $table) use ($pagesHasCanEditRole) {
            if ($pagesHasCanEditRole) {
                $table->dropColumn(['is_visible', 'can_edit_role']);
            } else {
                $table->dropColumn(['is_visible']);
            }
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->tinyInteger('read_level')->default(0)->after('data');
            $table->tinyInteger('write_level')->default(4)->after('read_level');
        });
        DB::statement("UPDATE sections SET read_level = " . $this->roleCase('is_visible'));
        DB::statement("UPDATE sections SET write_level = " . $this->roleCase('can_edit_role'));
        $this->ensureWriteGteRead('sections');
        DB::statement("
            UPDATE sections
            SET state = CASE
                WHEN state = 'published' THEN 'playable'
                WHEN state = 'preview' THEN 'draft'
                ELSE state
            END
        ");
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['is_visible', 'can_edit_role']);
        });

        /**
         * Campaigns / Scenarios : `state` = état de progression (int) + `usable` + `is_visible`
         * -> `progress_state` (int) + `state` (string cycle de vie) + `read_level`/`write_level`
         */
        Schema::table('campaigns', function (Blueprint $table) {
            $table->integer('progress_state')->default(0)->after('is_public');
        });
        DB::statement("UPDATE campaigns SET progress_state = state");
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['state']);
        });
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('state')->default('draft')->after('is_visible');
            $table->tinyInteger('read_level')->default(0)->after('state');
            $table->tinyInteger('write_level')->default(3)->after('read_level');
        });
        DB::statement("UPDATE campaigns SET state = CASE WHEN usable = 1 THEN 'playable' ELSE 'draft' END");
        DB::statement("UPDATE campaigns SET read_level = " . $this->roleCase('is_visible'));
        DB::statement("UPDATE campaigns SET write_level = 3");
        $this->ensureWriteGteRead('campaigns');
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['usable', 'is_visible']);
        });

        Schema::table('scenarios', function (Blueprint $table) {
            $table->integer('progress_state')->default(0)->after('is_public');
        });
        DB::statement("UPDATE scenarios SET progress_state = state");
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropColumn(['state']);
        });
        Schema::table('scenarios', function (Blueprint $table) {
            $table->string('state')->default('draft')->after('is_visible');
            $table->tinyInteger('read_level')->default(0)->after('state');
            $table->tinyInteger('write_level')->default(3)->after('read_level');
        });
        DB::statement("UPDATE scenarios SET state = CASE WHEN usable = 1 THEN 'playable' ELSE 'draft' END");
        DB::statement("UPDATE scenarios SET read_level = " . $this->roleCase('is_visible'));
        DB::statement("UPDATE scenarios SET write_level = 3");
        $this->ensureWriteGteRead('scenarios');
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropColumn(['usable', 'is_visible']);
        });
    }

    public function down(): void
    {
        // Down migration volontairement non supportée : refactor destructif (suppression colonnes).
        // En cas de rollback, repartir d'un backup DB ou d'un commit antérieur.
    }
};

