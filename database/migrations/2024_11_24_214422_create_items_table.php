<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('official_id')->nullable();
            $table->string('dofusdb_id')->nullable();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('name');
            $table->integer('level')->default(1);
            $table->string('description')->nullable();
            $table->string('effect');
            $table->string('bonus')->nullable();
            $table->string('recepe')->nullable();
            $table->string('actif')->default('0');
            $table->string('twohands')->default('0');
            $table->string('pa')->default('1');
            $table->string('po')->default('1');
            $table->string('price')->nullable();
            $table->integer('rarity')->default(5);
            $table->boolean('usable')->default(false);
            $table->string('dofus_version')->default('3');
            $table->boolean('is_visible')->default(false);
            $table->string('image')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->softDeletes();

            $table->foreignIdFor(\App\Models\Modules\Itemtype::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\User::class, 'created_by')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('item_resource', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Item::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Modules\Resource::class)->constrained()->cascadeOnDelete();
            $table->string('quantity')->default('1');
            $table->primary(['item_id', 'resource_id']);
            $table->softDeletes();
        });

        Schema::create('item_panoply', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Item::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Modules\Panoply::class)->constrained()->cascadeOnDelete();
            $table->primary(['item_id', 'panoply_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('item_resource');
        Schema::dropIfExists('item_panoply');
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'created_by');
        });
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Modules\Itemtype::class);
        });
    }
};
