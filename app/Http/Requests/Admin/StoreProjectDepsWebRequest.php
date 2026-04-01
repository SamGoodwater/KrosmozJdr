<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation des options pour lancer `project:deps` depuis l’admin (job, hors production).
 */
class StoreProjectDepsWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->isSuperAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'all' => ['sometimes', 'boolean'],
            'apt' => ['sometimes', 'boolean'],
            'composer' => ['sometimes', 'boolean'],
            'pnpm' => ['sometimes', 'boolean'],
            'css' => ['sometimes', 'boolean'],
            'docs' => ['sometimes', 'boolean'],
            'dump' => ['sometimes', 'boolean'],
            'migrate' => ['sometimes', 'boolean'],
            'ide' => ['sometimes', 'boolean'],
            'laravel_clear' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function artisanOptions(): array
    {
        $opts = [];
        if ($this->boolean('all')) {
            $opts['--all'] = true;

            return $opts;
        }
        foreach ([
            'apt' => '--apt',
            'composer' => '--composer',
            'pnpm' => '--pnpm',
            'css' => '--css',
            'docs' => '--docs',
            'dump' => '--dump',
            'migrate' => '--migrate',
            'ide' => '--ide',
            'laravel_clear' => '--laravel-clear',
        ] as $key => $flag) {
            if ($this->boolean($key)) {
                $opts[$flag] = true;
            }
        }

        return $opts;
    }
}
