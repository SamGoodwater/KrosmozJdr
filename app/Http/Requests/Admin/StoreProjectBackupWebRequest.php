<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation des options pour lancer `project:backup` depuis l’admin (job file d’attente).
 */
class StoreProjectBackupWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->isInteractiveSuperAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'no_database' => ['sometimes', 'boolean'],
            'no_storage' => ['sometimes', 'boolean'],
            'no_prune' => ['sometimes', 'boolean'],
            'prune_only' => ['sometimes', 'boolean'],
            'dry_run' => ['sometimes', 'boolean'],
            'retention_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
        ];
    }

    /**
     * Options pour Artisan::call (clés avec tirets comme la commande).
     *
     * @return array<string, mixed>
     */
    public function artisanOptions(): array
    {
        $opts = [];

        if ($this->boolean('no_database')) {
            $opts['--no-database'] = true;
        }
        if ($this->boolean('no_storage')) {
            $opts['--no-storage'] = true;
        }
        if ($this->boolean('no_prune')) {
            $opts['--no-prune'] = true;
        }
        if ($this->boolean('prune_only')) {
            $opts['--prune-only'] = true;
        }
        if ($this->boolean('dry_run')) {
            $opts['--dry-run'] = true;
        }
        $days = $this->input('retention_days');
        if ($days !== null && $days !== '') {
            $opts['--retention-days'] = (string) (int) $days;
        }

        return $opts;
    }
}
