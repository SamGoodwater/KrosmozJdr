<?php

declare(strict_types=1);

use App\Models\Section;
use Illuminate\Database\Migrations\Migration;

/**
 * Active les références riches TipTap sur les sections texte des pages « Règles » et « L'Essentiels ».
 */
return new class extends Migration
{
    public function up(): void
    {
        Section::query()
            ->where('template', 'text')
            ->whereHas('page', function ($q) {
                $q->whereIn('menu_group', ['Règles', 'L\'Essentiels']);
            })
            ->orderBy('id')
            ->chunkById(100, function ($sections) {
                foreach ($sections as $section) {
                    $settings = is_array($section->settings) ? $section->settings : [];
                    $settings['enableRichReferences'] = true;
                    $section->settings = $settings;
                    $section->saveQuietly();
                }
            });
    }

    public function down(): void
    {
        Section::query()
            ->where('template', 'text')
            ->whereHas('page', function ($q) {
                $q->whereIn('menu_group', ['Règles', 'L\'Essentiels']);
            })
            ->orderBy('id')
            ->chunkById(100, function ($sections) {
                foreach ($sections as $section) {
                    $settings = is_array($section->settings) ? $section->settings : [];
                    unset($settings['enableRichReferences']);
                    $section->settings = $settings;
                    $section->saveQuietly();
                }
            });
    }
};
