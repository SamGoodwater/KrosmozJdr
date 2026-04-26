<?php

namespace App\Support\Cms;

/**
 * Index TOC : numéro niveau 2 → slug page ; numéro niveau 3 → slug section (fichier règles).
 */
final class RulesTocSlugIndex
{
    /**
     * @param  array<int, array{number:string,title:string,menu_order:int,children:array<int, array{number:string,title:string,menu_order:int,sections:array<int, mixed>}>}>  $tree
     */
    public static function fromTree(array $tree): self
    {
        $l2 = [];
        $l3 = [];
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
                    $l3[$n3] = RulesImportSlugHelper::buildSectionSlug($n3, trim((string) ($l3['title'] ?? '')));
                }
            }
        }

        return new self($l2, $l3);
    }

    /**
     * @param  array<string, string>  $level2SlugByNumber
     * @param  array<string, string>  $sectionSlugByL3Number
     */
    private function __construct(
        private array $level2SlugByNumber,
        private array $sectionSlugByL3Number,
    ) {}

    public function slugForLevel2Number(string $n2): ?string
    {
        return $this->level2SlugByNumber[$n2] ?? null;
    }

    public function sectionSlugForL3Number(string $n3): ?string
    {
        return $this->sectionSlugByL3Number[$n3] ?? null;
    }
}
