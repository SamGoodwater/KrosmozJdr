<?php

declare(strict_types=1);

namespace App\Http\Requests\GenerativeAi;

use App\Services\GenerativeAi\CostEstimator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Lancement d’une conversion IA (admin, 1 paquet).
 *
 * @example ['action' => 'encounter', 'brief' => 'chef Bouftou niveau 10']
 */
class ConvertEntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => [
                'sometimes',
                'string',
                Rule::in(array_keys(CostEstimator::ACTIONS)),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || $value === '') {
                        return;
                    }
                    $entityType = (string) $this->route('entityType');
                    $expected = app(CostEstimator::class)->actionForEntityType($entityType);
                    if ($value !== $expected) {
                        $fail("L’action IA « {$value} » ne correspond pas au type « {$entityType} » (attendu : {$expected}).");
                    }
                },
            ],
            'brief' => ['nullable', 'string', 'max:2000'],
            'force' => ['sometimes', 'boolean'],
        ];
    }

    public function action(string $fallback): string
    {
        $action = $this->validated('action') ?? $fallback;

        return is_string($action) && $action !== '' ? $action : $fallback;
    }

    public function brief(): ?string
    {
        $brief = $this->validated('brief') ?? null;

        return is_string($brief) && trim($brief) !== '' ? trim($brief) : null;
    }

    public function force(): bool
    {
        return $this->boolean('force');
    }
}
