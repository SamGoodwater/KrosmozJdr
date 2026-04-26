<?php

namespace App\Support\Cms;

/**
 * Insère des shortcodes [[kref:characteristic:…]] pour les libellés reconnus (catalogue partagé).
 *
 * @example
 * « Force (Terre) » → shortcode → span `.kref` après {@see PagesImportRulesTocCommand::replaceKrefShortcodes}.
 *
 * @see RulesCharacteristicKrefReplacementCatalog
 */
final class RulesMarkdownCharacteristicKrefAutowrap
{
    public static function apply(string $markdown): string
    {
        $markdown = trim($markdown);
        if ($markdown === '') {
            return $markdown;
        }

        return RulesCharacteristicKrefReplacementCatalog::applyToMarkdown($markdown);
    }
}
