<?php

declare(strict_types=1);

namespace App\Http\Requests\GenerativeAi;

use App\Services\GenerativeAi\CostEstimator;
use App\Services\GenerativeAi\ManualJsonPayloadSanitizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * Injection d’un paquet JSON manuel (admin), même schéma que la conversion IA.
 *
 * @example ['action' => 'spell', 'payload' => ['effect' => '1d6 Terre'], 'force' => false]
 */
class InjectEntityJsonRequest extends FormRequest
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
            'payload' => ['required'],
            'force' => ['sometimes', 'boolean'],
        ];
    }

    public function action(string $fallback): string
    {
        $action = $this->validated('action') ?? $fallback;

        return is_string($action) && $action !== '' ? $action : $fallback;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        try {
            return app(ManualJsonPayloadSanitizer::class)->parseAndSanitize($this->input('payload'));
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'payload' => [$exception->getMessage()],
            ]);
        }
    }

    public function force(): bool
    {
        return $this->boolean('force');
    }
}
