<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Support\Cms\RulesCharacteristicKrefReplacementCatalog;

/**
 * Transforme une fiche classe rédigée en une section CMS de présentation.
 *
 * Le passif, les voies et les sorts restent sur {@see BreedViewFull} ;
 * ce texte raconte comment on joue la classe.
 *
 * Gabarit validé : clé `presentation` (quelques paragraphes simples, sans intertitres).
 * Ancien gabarit, tant qu’une fiche n’est pas réécrite : `identity` + Repères,
 * « Ce que cette classe n’est pas » et Synergies.
 *
 * @example (new ClassSheetContentRenderer)->sections(['name' => 'Iop', 'passive' => 'Tête brûlée', 'presentation' => ['…']]);
 */
final class ClassSheetContentRenderer
{
    /**
     * @param  array<string, mixed>  $sheet
     * @return list<array{title: string, level: int, content: string, capabilities?: list<string>}>
     */
    public function sections(array $sheet): array
    {
        $passive = trim((string) ($sheet['passive'] ?? ''));

        return [
            [
                'title' => 'Texte',
                'level' => 1,
                'content' => $this->presentationHtml($sheet),
                'capabilities' => $passive !== '' ? [$passive] : [],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $sheet
     */
    private function presentationHtml(array $sheet): string
    {
        $presentation = $sheet['presentation'] ?? null;
        if (is_array($presentation) && $presentation !== []) {
            $paragraphs = array_filter(array_map(
                static fn (mixed $text): string => trim((string) $text),
                $presentation,
            ));

            return implode('', array_map(
                fn (string $text): string => '<p>'.$this->richText($text).'</p>',
                $paragraphs,
            ));
        }

        $parts = [];

        $identity = trim((string) ($sheet['identity'] ?? ''));
        if ($identity !== '') {
            $parts[] = '<p>'.$this->richText($identity).'</p>';
        }

        $passive = trim((string) ($sheet['passive'] ?? ''));
        $passiveLine = $passive !== ''
            ? 'Passif : [[kref:entity:capabilities:'.$passive.'|'.$passive.']]'
            : '';

        $parts[] = '<h2>Repères</h2><ul>'
            .$this->li('Focus', (string) ($sheet['focus'] ?? ''))
            .$this->li('Caractéristiques', (string) ($sheet['characteristics'] ?? ''))
            .$this->li('Idéal pour', (string) ($sheet['idealFor'] ?? ''))
            .($passiveLine !== '' ? '<li>'.$this->richText($passiveLine).'</li>' : '')
            .'</ul>';

        $difference = trim((string) ($sheet['difference'] ?? ''));
        if ($difference !== '') {
            $parts[] = '<h2>Ce que cette classe n’est pas</h2><p>'.$this->richText($difference).'</p>';
        }

        $synergies = $sheet['synergies'] ?? [];
        if (is_array($synergies) && $synergies !== []) {
            $items = '';
            foreach ($synergies as $label => $text) {
                $items .= $this->li((string) $label, (string) $text);
            }
            $parts[] = '<h2>Synergies</h2><ul>'.$items.'</ul>';
        }

        return implode('', $parts);
    }

    private function richText(string $text): string
    {
        return RulesCharacteristicKrefReplacementCatalog::applyToMarkdown(e($text));
    }

    private function li(string $label, string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        return '<li><strong>'.e($label).'</strong> — '.$this->richText($text).'</li>';
    }
}
