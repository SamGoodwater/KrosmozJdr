<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Support\ElementConstants;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capabilities', function (Blueprint $table) {
            $table->unsignedTinyInteger('element_new')->default(0)->after('duration');
        });

        // Conversion des valeurs existantes
        $rows = DB::table('capabilities')->get(['id', 'element']);

        foreach ($rows as $row) {
            $old = $row->element;
            $new = $this->convertElement($old);
            DB::table('capabilities')
                ->where('id', $row->id)
                ->update(['element_new' => $new]);
        }

        Schema::table('capabilities', function (Blueprint $table) {
            $table->dropColumn('element');
        });

        Schema::table('capabilities', function (Blueprint $table) {
            $table->unsignedTinyInteger('element')->default(0)->after('duration');
        });

        DB::table('capabilities')->update(['element' => DB::raw('element_new')]);

        Schema::table('capabilities', function (Blueprint $table) {
            $table->dropColumn('element_new');
        });
    }

    private function convertElement(mixed $old): int
    {
        if ($old === null || $old === '') {
            return 0;
        }

        $key = is_numeric($old) ? (string) (int) $old : strtolower(trim((string) $old));
        $mapping = ElementConstants::LEGACY_STRING_TO_INT;

        return $mapping[$key] ?? 0;
    }

    public function down(): void
    {
        Schema::table('capabilities', function (Blueprint $table) {
            $table->string('element_old')->default('0')->after('duration');
        });

        $rows = DB::table('capabilities')->get(['id', 'element']);
        foreach ($rows as $row) {
            $val = (int) $row->element;
            $old = (string) $val;
            DB::table('capabilities')
                ->where('id', $row->id)
                ->update(['element_old' => $old]);
        }

        Schema::table('capabilities', function (Blueprint $table) {
            $table->dropColumn('element');
        });

        Schema::table('capabilities', function (Blueprint $table) {
            $table->string('element')->default('0')->after('duration');
        });

        DB::table('capabilities')->update(['element' => DB::raw('element_old')]);

        Schema::table('capabilities', function (Blueprint $table) {
            $table->dropColumn('element_old');
        });
    }
};
