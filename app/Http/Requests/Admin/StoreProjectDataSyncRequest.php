<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Console\Concerns\NormalizesProjectSyncEntities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation des options `project:data sync` lancées depuis l’UI super admin.
 */
class StoreProjectDataSyncRequest extends FormRequest
{
    use NormalizesProjectSyncEntities;

    /** @var list<string> */
    public const ENTITY_CHOICES = ['class', 'spell', 'monster', 'panoply', 'resource', 'item', 'consumable'];

    /** @var list<string> */
    public const CATALOG_TYPE_CHOICES = ['all', 'monster', 'spell', 'resource', 'consumable', 'item', 'equipment'];

    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isSuperAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'entities' => ['nullable', 'array'],
            'entities.*' => ['string', Rule::in(self::ENTITY_CHOICES)],
            'catalog_types' => ['nullable', 'array'],
            'catalog_types.*' => ['string', Rule::in(self::CATALOG_TYPE_CHOICES)],
            'races' => ['boolean'],
            'lang' => ['required', 'string', Rule::in(['fr', 'en', 'de', 'es', 'pt'])],
            'noimage' => ['boolean'],
            'skip_cache' => ['boolean'],
            'dry_run' => ['boolean'],
            'skip_clear_queue' => ['boolean'],
            'skip_notify' => ['boolean'],
        ];
    }

    /**
     * Prépare les booléens absents du JSON.
     *
     * @return array<string, mixed>
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'races' => $this->boolean('races'),
            'noimage' => $this->boolean('noimage'),
            'skip_cache' => $this->boolean('skip_cache'),
            'dry_run' => $this->boolean('dry_run'),
            'skip_clear_queue' => $this->boolean('skip_clear_queue'),
            'skip_notify' => $this->boolean('skip_notify'),
        ]);
    }

    /**
     * Paramètres pour `Artisan::call('project:data', ...)`.
     *
     * @return array<string, mixed>
     */
    public function artisanParameters(): array
    {
        $params = [
            'action' => 'sync',
        ];

        $entities = $this->input('entities', []);
        if (is_array($entities) && $entities !== []) {
            $normalized = [];
            foreach ($entities as $raw) {
                $n = $this->normalizeEntityToken((string) $raw);
                if ($n !== '' && ! in_array($n, $normalized, true)) {
                    $normalized[] = $n;
                }
            }
            if ($normalized !== []) {
                $params['--entity'] = implode(',', $normalized);
            }
        }

        $catalogTypes = $this->input('catalog_types', []);
        if (is_array($catalogTypes) && $catalogTypes !== []) {
            $types = array_values(array_unique(array_map('strtolower', array_map('strval', $catalogTypes))));
            if ($types !== []) {
                $params['--type'] = implode(',', $types);
            }
        }

        if ($this->boolean('races')) {
            $params['--races'] = true;
        }

        $params['--lang'] = (string) $this->input('lang', 'fr');

        if ($this->boolean('noimage')) {
            $params['--noimage'] = true;
        }
        if ($this->boolean('skip_cache')) {
            $params['--skip-cache'] = true;
        }
        if ($this->boolean('dry_run')) {
            $params['--dry-run'] = true;
        }
        if ($this->boolean('skip_clear_queue')) {
            $params['--skip-clear-queue'] = true;
        }
        if ($this->boolean('skip_notify')) {
            $params['--skip-notify'] = true;
        }

        return $params;
    }
}
