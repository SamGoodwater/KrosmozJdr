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
            'with_system' => ['sometimes', 'boolean'],
            'apt' => ['sometimes', 'boolean'],
            'composer' => ['sometimes', 'boolean'],
            'pnpm' => ['sometimes', 'boolean'],
            'css' => ['sometimes', 'boolean'],
            'docs' => ['sometimes', 'boolean'],
            'dump' => ['sometimes', 'boolean'],
            'migrate' => ['sometimes', 'boolean'],
            'optimize' => ['sometimes', 'boolean'],
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
            if ($this->boolean('with_system')) {
                $opts['--with-system'] = true;
            }

            return $opts;
        }
        foreach ([
            'with_system' => '--with-system',
            'apt' => '--apt',
            'composer' => '--composer',
            'pnpm' => '--pnpm',
            'css' => '--css',
            'docs' => '--docs',
            'dump' => '--dump',
            'migrate' => '--migrate',
            'optimize' => '--optimize',
        ] as $key => $flag) {
            if ($this->boolean($key)) {
                $opts[$flag] = true;
            }
        }

        return $opts;
    }
}
