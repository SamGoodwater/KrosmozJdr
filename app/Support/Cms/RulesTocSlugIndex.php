<?php

declare(strict_types=1);

namespace App\Support\Cms;

/**
 * Index TOC : numéros → slugs pages / sections + résolution de références internes.
 */
final class RulesTocSlugIndex
{
    /**
     * @param  array<string, string>  $level2SlugByNumber
     * @param  array<string, string>  $sectionSlugByL3Number
     * @param  array<string, string>  $sectionTitleByL3Number
     */
    private function __construct(
        private array $level2SlugByNumber,
        private array $sectionSlugByL3Number,
        private array $sectionTitleByL3Number,
    ) {}

    /**
     * @param  array<int, array{number:string,title:string,menu_order:int,children:array<int, array{number:string,title:string,menu_order:int,sections:array<int, mixed>}>}>  $tree
     */
    public static function fromTree(array $tree): self
    {
        $l2 = [];
        $l3 = [];
        $l3Titles = [];
        foreach ($tree as $l1) {
            foreach ($l1['children'] ?? [] as $l2Item) {
                $n2 = trim((string) ($l2Item['number'] ?? ''));
                if ($n2 !== '') {
                    $l2[$n2] = RulesImportSlugHelper::buildPageSlug($n2, trim((string) ($l2Item['title'] ?? '')));
                }
                foreach ($l2Item['sections'] ?? [] as $l3) {
                    $n3 = trim((string) ($l3['number'] ?? ''));
                    if ($n3 === '') {
                        continue;
                    }
                    $title = trim((string) ($l3['title'] ?? ''));
                    $l3[$n3] = RulesImportSlugHelper::buildSectionSlug($n3, $title);
                    $l3Titles[$n3] = $title;
                }
            }
        }

        return new self($l2, $l3, $l3Titles);
    }

    public function slugForLevel2Number(string $n2): ?string
    {
        return $this->level2SlugByNumber[$n2] ?? null;
    }

    public function sectionSlugForL3Number(string $n3): ?string
    {
        return $this->sectionSlugByL3Number[$n3] ?? null;
    }

    /**
     * @deprecated Préférer {@see krefForSectionNumber()} — alias conservé pour compatibilité.
     */
    public function pageKrefForSectionNumber(string $number, string $label, ?string $subKeyword = null): ?string
    {
        return $this->krefForSectionNumber($number, $label, $subKeyword);
    }

    /**
     * Shortcode riche {@code pageSection} (L3 connu) ou {@code page} (chapitre L2).
     */
    public function krefForSectionNumber(string $number, string $label, ?string $subKeyword = null): ?string
    {
        $number = trim($number);
        if ($number === '') {
            return null;
        }

        if ($subKeyword !== null && trim($subKeyword) !== '') {
            $resolved = $this->findL3NumberByParentAndKeyword($number, $subKeyword);
            if ($resolved !== null) {
                $number = $resolved;
            }
        }

        $display = trim($label) !== '' ? trim($label) : ($this->sectionTitleByL3Number[$number] ?? $number);
        $parts = explode('.', $number);

        if (count($parts) >= 3 && isset($this->sectionSlugByL3Number[$number])) {
            $pageSlug = $this->slugForLevel2Number($parts[0].'.'.$parts[1]);
            $sectionSlug = $this->sectionSlugByL3Number[$number];
            if ($pageSlug !== null && $sectionSlug !== '') {
                return '[[kref:pageSection:'.$pageSlug.'@'.$sectionSlug.'|'.$display.']]';
            }
        }

        $pageSlug = $this->pageSlugForSectionNumber($number);
        if ($pageSlug === null) {
            return null;
        }

        return '[[kref:page:'.$pageSlug.'|'.$display.']]';
    }

    public function pageSlugForSectionNumber(string $number): ?string
    {
        $parts = explode('.', trim($number));
        if (count($parts) >= 3) {
            return $this->slugForLevel2Number($parts[0].'.'.$parts[1]);
        }
        if (count($parts) === 2) {
            return $this->slugForLevel2Number($number);
        }

        return null;
    }

    public function findL3NumberByParentAndKeyword(string $l2Number, string $keyword): ?string
    {
        $keywordNorm = self::normalizeText($keyword);
        if ($keywordNorm === '') {
            return null;
        }

        $prefix = trim($l2Number).'.';
        $best = null;
        $bestLen = 0;

        foreach ($this->sectionTitleByL3Number as $n3 => $title) {
            if (! str_starts_with($n3, $prefix)) {
                continue;
            }
            $titleNorm = self::normalizeText($title);
            if ($titleNorm === '' || ! str_contains($titleNorm, $keywordNorm)) {
                continue;
            }
            $len = strlen($n3);
            if ($len > $bestLen) {
                $best = $n3;
                $bestLen = $len;
            }
        }

        return $best;
    }

    private static function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = str_replace(['’', "'"], '', $text);

        return $text;
    }
}
