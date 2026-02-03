<?php

namespace App\Services\Scrapping\Core\Conversion;

use App\Services\Characteristic\DofusConversion\DofusDbConversionFormulas;
use App\Services\Scrapping\Core\Config\ConfigLoader;

/**
 * Service de conversion : applique le mapping (propriété source → cible + formatter).
 *
 * Lit la config « mapping » pour une entité, extrait les valeurs par chemin,
 * applique les formatters, produit une structure au format KrosmozJDR (ex. creatures + monsters).
 * Si la config entité a "resistanceBatch": true et entityType monster/class/item, fusionne
 * le résultat de convertResistancesBatch() dans la sortie (ex. creatures).
 * Réutilisable hors scrapping (autres imports).
 */
final class ConversionService
{
    public function __construct(
        private ConfigLoader $configLoader,
        private FormatterApplicator $formatterApplicator,
        private ?DofusDbConversionFormulas $conversionFormulas = null
    ) {
    }

    /**
     * Convertit des données brutes en structure KrosmozJDR.
     *
     * @param array<string, mixed> $raw Données brutes (ex. réponse API DofusDB)
     * @param array{lang?: string} $context Contexte (lang pour pickLang, etc.)
     * @return array<string, array<string, mixed>> Structure par modèle (ex. ['creatures' => [...], 'monsters' => [...]])
     */
    public function convert(string $source, string $entity, array $raw, array $context = []): array
    {
        $entityConfig = $this->configLoader->loadEntity($source, $entity);
        $lang = (string) ($context['lang'] ?? 'fr');

        $out = [];
        $mapping = $entityConfig['mapping'] ?? [];
        if (!is_array($mapping)) {
            return $out;
        }

        foreach ($mapping as $map) {
            if (!is_array($map)) {
                continue;
            }

            $from = (array) ($map['from'] ?? []);
            $path = (string) ($from['path'] ?? '');
            if ($path === '') {
                continue;
            }

            $value = $this->getByPath($raw, $path);

            $formatters = $map['formatters'] ?? [];
            if (is_array($formatters)) {
                foreach ($formatters as $fmt) {
                    if (!is_array($fmt) || !isset($fmt['name']) || !is_string($fmt['name'])) {
                        continue;
                    }
                    if (!$this->formatterApplicator->supports($fmt['name'])) {
                        continue;
                    }
                    $args = $this->interpolateArgs($fmt['args'] ?? [], ['lang' => $lang]);
                    $value = $this->formatterApplicator->apply($fmt['name'], $value, $args, $raw, $context);
                }
            }

            $targets = $map['to'] ?? [];
            if (!is_array($targets)) {
                continue;
            }

            foreach ($targets as $target) {
                if (!is_array($target)) {
                    continue;
                }
                $model = $target['model'] ?? null;
                $field = $target['field'] ?? null;
                if (!is_string($model) || $model === '' || !is_string($field) || $field === '') {
                    continue;
                }

                if (!isset($out[$model]) || !is_array($out[$model])) {
                    $out[$model] = [];
                }
                $out[$model][$field] = $value;
            }
        }

        $entityType = (string) ($context['entityType'] ?? $entity);
        if ($this->conversionFormulas !== null && in_array($entityType, ['monster', 'class', 'item'], true)) {
            $useResistanceBatch = (bool) ($entityConfig['resistanceBatch'] ?? false);
            if ($useResistanceBatch) {
                $resMap = $this->conversionFormulas->convertResistancesBatch($raw, $entityType);
                $targetModel = $entityType === 'monster' ? 'creatures' : ($entityType === 'class' ? 'breeds' : 'items');
                if (!isset($out[$targetModel]) || !is_array($out[$targetModel])) {
                    $out[$targetModel] = [];
                }
                foreach ($resMap as $field => $value) {
                    $out[$targetModel][$field] = is_numeric($value) ? (int) $value : (string) $value;
                }
            }
        }

        return $out;
    }

    /**
     * Extraction par chemin dot (ex. grades.0.level).
     *
     * @param array<string, mixed> $data
     * @return mixed
     */
    private function getByPath(array $data, string $path): mixed
    {
        $parts = explode('.', $path);
        $cur = $data;
        foreach ($parts as $part) {
            if (!is_array($cur)) {
                return null;
            }
            if (ctype_digit($part)) {
                $cur = $cur[(int) $part] ?? null;
                continue;
            }
            $cur = $cur[$part] ?? null;
        }

        return $cur;
    }

    /**
     * Remplace {lang} etc. dans les arguments.
     *
     * @param array<string, mixed> $args
     * @param array<string, string> $vars
     * @return array<string, mixed>
     */
    private function interpolateArgs(array $args, array $vars): array
    {
        foreach ($args as $k => $v) {
            if (is_string($v)) {
                foreach ($vars as $var => $value) {
                    $args[$k] = str_replace('{' . $var . '}', $value, $args[$k]);
                }
            }
        }

        return $args;
    }
}
