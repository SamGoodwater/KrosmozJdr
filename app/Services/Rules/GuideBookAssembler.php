<?php

declare(strict_types=1);

namespace App\Services\Rules;

use Illuminate\Support\Str;

/**
 * Assemble le PDF public « Le monde des Douze — guide des aventurier·ère·s ».
 *
 * Source : les chapitres 4.1.1 et 4.1.2 du livre de règles (ère Dofus, an 646).
 * Même nettoyage que le livre (kref, liens internes, blocs Sources).
 *
 * @example
 * $html = (new GuideBookAssembler())->toHtml();
 */
class GuideBookAssembler
{
    public const TITLE = 'Le monde des Douze - guide des aventurier·ère·s';

    /** @var list<string> */
    private const CHAPTERS = [
        '4-Le-monde-des-douze/4.1-l-univers/4.1.1-presentation-du-krosmoz.md',
        '4-Le-monde-des-douze/4.1-l-univers/4.1.2-temporalite.md',
    ];

    public function __construct(
        private readonly string $rulesRoot = '',
    ) {}

    public function root(): string
    {
        return $this->rulesRoot !== '' ? $this->rulesRoot : base_path('private/game/rules');
    }

    /**
     * Markdown du guide, prêt à convertir.
     */
    public function assemble(): string
    {
        $chapters = [];
        foreach (self::CHAPTERS as $relative) {
            $path = $this->root().'/'.$relative;
            if (! is_file($path)) {
                continue;
            }
            $raw = file_get_contents($path);
            if (! is_string($raw) || trim($raw) === '') {
                continue;
            }
            $chapters[] = $this->normalize($raw);
        }

        if ($chapters === []) {
            return '';
        }

        $version = (string) env('APP_VERSION', 'dev');
        $date = now()->timezone(config('app.timezone'))->format('d/m/Y');
        $parts = [
            '# '.self::TITLE,
            '',
            'Version '.$version.' · compilé le '.$date.'. An 646, ère de Dofus. Le livre de règles détaille les jets, les kamas et les voyages.',
            '',
            ...$chapters,
        ];

        return trim(implode(PHP_EOL, $parts)).PHP_EOL;
    }

    /**
     * HTML prêt pour {@see RulesPdfWriter} / {@see RulesOdtWriter}.
     */
    public function toHtml(): string
    {
        $markdown = $this->assemble();
        if ($markdown === '') {
            return '';
        }

        return trim((string) Str::markdown($markdown, [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]));
    }

    private function normalize(string $markdown): string
    {
        $markdown = (string) preg_replace(
            '/\[\[kref:[^\]|]+\|([^\]]+)\]\]/u',
            '$1',
            $markdown
        );
        $markdown = (string) preg_replace('/\[\[kref:[^\]]+\]\]/u', '', $markdown);
        $markdown = (string) preg_replace('/\n## Sources?\b.*\z/us', "\n", $markdown);
        $markdown = (string) preg_replace('/^\*\*Description\*\*\s*:\s*/mu', '', $markdown);
        $markdown = (string) preg_replace_callback(
            '/\[([^\]]+)\]\(([^)]+)\)/u',
            static function (array $matches): string {
                $label = (string) $matches[1];
                $url = trim((string) $matches[2]);
                if (preg_match('#^https?://#i', $url) === 1) {
                    return '['.$label.']('.$url.')';
                }

                return $label;
            },
            $markdown
        );

        return trim($markdown);
    }
}
