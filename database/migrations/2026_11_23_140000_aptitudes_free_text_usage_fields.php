<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Remplace les enums structurés (reuse/duration/range) par trois textes libres.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('aptitudes')) {
            return;
        }

        if (! Schema::hasColumn('aptitudes', 'reuse')) {
            Schema::table('aptitudes', function (Blueprint $table) {
                $table->text('reuse')->nullable()->after('effect');
                $table->text('duration')->nullable()->after('reuse');
                $table->text('range')->nullable()->after('duration');
            });
        }

        if (Schema::hasColumn('aptitudes', 'reuse_type')) {
            $rows = DB::table('aptitudes')->select([
                'id',
                'reuse_type', 'reuse_amount', 'reuse_unit',
                'duration_type', 'duration_amount', 'duration_unit', 'duration_note',
                'range_type', 'range_amount', 'range_unit', 'range_sense', 'range_note',
            ])->get();

            foreach ($rows as $row) {
                DB::table('aptitudes')->where('id', $row->id)->update([
                    'reuse' => $this->composeReuse($row),
                    'duration' => $this->composeDuration($row),
                    'range' => $this->composeRange($row),
                ]);
            }

            Schema::table('aptitudes', function (Blueprint $table) {
                $table->dropIndex(['reuse_type']);
                $table->dropIndex(['duration_type']);
                $table->dropIndex(['range_type']);
                $table->dropColumn([
                    'reuse_type', 'reuse_amount', 'reuse_unit',
                    'duration_type', 'duration_amount', 'duration_unit', 'duration_note',
                    'range_type', 'range_amount', 'range_unit', 'range_sense', 'range_note',
                ]);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('aptitudes') || ! Schema::hasColumn('aptitudes', 'reuse')) {
            return;
        }

        Schema::table('aptitudes', function (Blueprint $table) {
            $table->string('reuse_type', 32)->default('unlimited')->after('effect');
            $table->unsignedInteger('reuse_amount')->nullable()->after('reuse_type');
            $table->string('reuse_unit', 16)->nullable()->after('reuse_amount');
            $table->string('duration_type', 32)->default('permanent')->after('reuse_unit');
            $table->unsignedInteger('duration_amount')->nullable()->after('duration_type');
            $table->string('duration_unit', 16)->nullable()->after('duration_amount');
            $table->string('duration_note')->nullable()->after('duration_unit');
            $table->string('range_type', 32)->default('self')->after('duration_note');
            $table->unsignedInteger('range_amount')->nullable()->after('range_type');
            $table->string('range_unit', 8)->nullable()->after('range_amount');
            $table->string('range_sense', 32)->nullable()->after('range_unit');
            $table->string('range_note')->nullable()->after('range_sense');
            $table->index('reuse_type');
            $table->index('duration_type');
            $table->index('range_type');
        });

        $rows = DB::table('aptitudes')->select(['id', 'reuse', 'duration', 'range'])->get();
        foreach ($rows as $row) {
            DB::table('aptitudes')->where('id', $row->id)->update([
                'reuse_type' => 'unlimited',
                'duration_type' => 'permanent',
                'range_type' => 'self',
                'duration_note' => $row->duration,
                'range_note' => $row->range,
            ]);
        }

        Schema::table('aptitudes', function (Blueprint $table) {
            $table->dropColumn(['reuse', 'duration', 'range']);
        });
    }

    private function composeReuse(object $row): string
    {
        return match ((string) ($row->reuse_type ?? '')) {
            'short_rest' => 'Après un repos court',
            'long_rest' => 'Après un repos long',
            'timed' => $this->composeAmountUnit($row->reuse_amount, $row->reuse_unit, 'Temporisée'),
            default => 'Illimitée',
        };
    }

    private function composeDuration(object $row): string
    {
        $note = trim((string) ($row->duration_note ?? ''));

        return match ((string) ($row->duration_type ?? '')) {
            'instant' => 'Instantanée',
            'timed' => $this->composeAmountUnit($row->duration_amount, $row->duration_unit, 'Temporisée'),
            'while_condition' => $note !== '' ? $note : 'Tant que la condition est vraie',
            default => $note !== '' ? $note : 'Permanente',
        };
    }

    private function composeRange(object $row): string
    {
        $note = trim((string) ($row->range_note ?? ''));
        $sense = match ((string) ($row->range_sense ?? '')) {
            'hearing' => 'Ouïe',
            'sight' => 'Vue',
            'touch' => 'Toucher',
            'custom' => 'Sens personnalisé',
            default => 'À portée d\'un sens',
        };

        return match ((string) ($row->range_type ?? '')) {
            'distance' => $this->composeAmountUnit($row->range_amount, $row->range_unit ?: 'm', 'Distance'),
            'sense' => $note !== '' ? "{$sense} ({$note})" : $sense,
            default => 'Soi',
        };
    }

    private function composeAmountUnit(mixed $amount, mixed $unit, string $fallback): string
    {
        $a = is_numeric($amount) ? (int) $amount : null;
        $u = trim((string) ($unit ?? ''));
        if ($a === null || $a < 1) {
            return $fallback;
        }
        $unitLabel = match ($u) {
            'minute' => $a > 1 ? 'minutes' : 'minute',
            'hour' => $a > 1 ? 'heures' : 'heure',
            'day' => $a > 1 ? 'jours' : 'jour',
            'round' => $a > 1 ? 'tours' : 'tour',
            'scene' => $a > 1 ? 'scènes' : 'scène',
            'km' => 'km',
            'm' => 'm',
            default => $u !== '' ? $u : '',
        };

        return trim($a.' '.$unitLabel);
    }
};
