<?php

declare(strict_types=1);

namespace App\Console\Commands\Content;

use App\Console\ArtisanExitCode;
use App\Support\KrosmozGameTerms;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Réécrit « désenvoûtable » → « dissipable » dans les textes déjà stockés.
 *
 * @example php artisan game-terms:rewrite-dissipable --dry-run
 * @example php artisan game-terms:rewrite-dissipable
 */
final class RewriteDesenvoutableCommand extends Command
{
    protected $signature = 'game-terms:rewrite-dissipable
                            {--dry-run : N\'écrit pas en base, affiche seulement le volume}';

    protected $description = 'Remplace désenvoûtable par dissipable dans les textes d’entités, d’effets et du CMS.';

    /**
     * Colonnes texte à parcourir (table => champs).
     *
     * @var array<string, list<string>>
     */
    private const TEXT_COLUMNS = [
        'spells' => ['name', 'description', 'effect', 'save_success_note'],
        'items' => ['name', 'description', 'effect', 'bonus'],
        'capabilities' => ['name', 'description', 'effect'],
        'consumables' => ['name', 'description'],
        'resources' => ['name', 'description'],
        'conditions' => ['name', 'description'],
        'creature_traits' => ['name', 'description'],
        'creatures' => ['name', 'description'],
        'specializations' => ['name', 'description', 'short_description'],
        'breeds' => ['name', 'description', 'description_fast'],
        'panoplies' => ['name', 'description'],
        'npcs' => ['name', 'description'],
        'effects' => ['name', 'description'],
        'sub_effects' => ['template_text'],
        'characteristics' => ['name', 'short_name', 'helper', 'descriptions'],
        'pages' => ['title'],
        'sections' => ['title'],
    ];

    /**
     * Colonnes JSON à parcourir récursivement.
     *
     * @var array<string, list<string>>
     */
    private const JSON_COLUMNS = [
        'sections' => ['data', 'settings'],
        'effect_sub_effect' => ['params'],
        'sub_effects' => ['param_schema'],
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $updated = 0;

        foreach (self::TEXT_COLUMNS as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }
                $updated += $this->rewriteTextColumn($table, $column, $dryRun);
            }
        }

        foreach (self::JSON_COLUMNS as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }
                $updated += $this->rewriteJsonColumn($table, $column, $dryRun);
            }
        }

        $this->info($dryRun
            ? "Mode --dry-run : {$updated} champ(s) à réécrire. Relancez sans --dry-run pour appliquer."
            : "{$updated} champ(s) réécrit(s).");

        return ArtisanExitCode::SUCCESS;
    }

    private function rewriteTextColumn(string $table, string $column, bool $dryRun): int
    {
        $count = 0;
        $query = DB::table($table)
            ->select(['id', $column])
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->where(function ($q) use ($column): void {
                $q->where($column, 'like', '%envout%')
                    ->orWhere($column, 'like', '%envoût%')
                    ->orWhere($column, 'like', '%ENVOÛT%');
            });

        foreach ($query->orderBy('id')->cursor() as $row) {
            $original = (string) $row->{$column};
            $next = KrosmozGameTerms::replaceDesenvoutableWithDissipable($original);
            if ($next === $original) {
                continue;
            }
            $count++;
            if (! $dryRun) {
                DB::table($table)->where('id', $row->id)->update([$column => $next]);
            }
        }

        if ($count > 0) {
            $this->line("  {$table}.{$column} : {$count}");
        }

        return $count;
    }

    private function rewriteJsonColumn(string $table, string $column, bool $dryRun): int
    {
        $count = 0;
        $query = DB::table($table)
            ->select(['id', $column])
            ->whereNotNull($column)
            ->where(function ($q) use ($column): void {
                $q->where($column, 'like', '%envout%')
                    ->orWhere($column, 'like', '%envoût%')
                    ->orWhere($column, 'like', '%ENVOÛT%');
            });

        foreach ($query->orderBy('id')->cursor() as $row) {
            $raw = $row->{$column};
            $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
            if (! is_array($decoded)) {
                if (is_string($raw)) {
                    $next = KrosmozGameTerms::replaceDesenvoutableWithDissipable($raw);
                    if ($next === $raw) {
                        continue;
                    }
                    $count++;
                    if (! $dryRun) {
                        DB::table($table)->where('id', $row->id)->update([$column => $next]);
                    }
                }

                continue;
            }

            $next = KrosmozGameTerms::replaceInMixed($decoded);
            if ($next === $decoded) {
                continue;
            }
            $count++;
            if (! $dryRun) {
                DB::table($table)->where('id', $row->id)->update([
                    $column => json_encode($next, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                ]);
            }
        }

        if ($count > 0) {
            $this->line("  {$table}.{$column} : {$count}");
        }

        return $count;
    }
}
