<?php

namespace App\Support\Cms;

use Illuminate\Support\Str;

/**
 * Parse {@code TABLE_DES_MATIERES.md} (même logique que l’import pages règles).
 *
 * @return array<int, array{number:string,title:string,menu_order:int,children:array<int, array{number:string,title:string,menu_order:int,sections:array<int, array{number:string,title:string,order:int}>}>}>
 */
final class RulesTocParser
{
    public static function parse(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if (! is_array($lines)) {
            return [];
        }

        $level1Items = [];
        $currentLevel1Number = null;
        $currentLevel2Number = null;

        foreach ($lines as $rawLine) {
            $line = trim((string) $rawLine);
            if ($line === '' || Str::startsWith($line, ['---', '# Table'])) {
                continue;
            }

            if (preg_match('/^##\s+(\d+)\.\s+(.+)$/u', $line, $m)) {
                $n1 = (string) $m[1];
                $title = trim((string) $m[2]);
                $currentLevel1Number = $n1;
                $currentLevel2Number = null;

                $level1Items[$n1] = [
                    'number' => $n1,
                    'title' => $title,
                    'menu_order' => (int) $n1,
                    'children' => $level1Items[$n1]['children'] ?? [],
                ];

                continue;
            }

            if (preg_match('/^###\s+(\d+\.\d+)\s+(.+)$/u', $line, $m) && $currentLevel1Number !== null) {
                $n2 = (string) $m[1];
                $title = trim((string) $m[2]);
                $currentLevel2Number = $n2;

                $level1Items[$currentLevel1Number]['children'][$n2] = [
                    'number' => $n2,
                    'title' => $title,
                    'menu_order' => self::extractSecondLevelOrder($n2),
                    'sections' => $level1Items[$currentLevel1Number]['children'][$n2]['sections'] ?? [],
                ];

                continue;
            }

            if ($currentLevel1Number !== null && $currentLevel2Number !== null) {
                if (preg_match('/^\-\s*(?:\*\*)?(\d+(?:\.\d+){1,2})\.?(?:\*\*)?\s*(.+)$/u', $line, $m)) {
                    $n3 = (string) $m[1];
                    $title = trim((string) $m[2], " \t\n\r\0\x0B*-");
                    if ($title === '') {
                        continue;
                    }

                    $level1Items[$currentLevel1Number]['children'][$currentLevel2Number]['sections'][] = [
                        'number' => $n3,
                        'title' => $title,
                        'order' => self::extractThirdLevelOrder($n3),
                    ];
                }
            }
        }

        return array_values(array_map(function (array $l1): array {
            $l1['children'] = array_values(array_map(function (array $l2): array {
                return $l2;
            }, $l1['children']));

            return $l1;
        }, $level1Items));
    }

    public static function extractSecondLevelOrder(string $number): int
    {
        $parts = explode('.', $number);

        return isset($parts[1]) ? (int) $parts[1] : 0;
    }

    public static function extractThirdLevelOrder(string $number): int
    {
        $parts = explode('.', $number);
        if (isset($parts[2])) {
            return (int) $parts[2];
        }
        if (isset($parts[1])) {
            return (int) $parts[1];
        }

        return 0;
    }
}
