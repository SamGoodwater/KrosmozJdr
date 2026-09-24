<?php

declare(strict_types=1);

namespace App\Http\Requests\GenerativeAi;

use App\Services\GenerativeAi\CostEstimator;
use App\Services\GenerativeAi\ManualJsonPayloadSanitizer;
use App\Services\GenerativeAi\Specializations\SpecializationRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * Injection d’un paquet JSON manuel (admin).
 *
 * Types convertibles : même schéma que la conversion IA (`action` calée sur le type).
 * Autres types du registre : fillable générique (`action` optionnelle / ignorée).
 *
 * @example ['action' => 'spell', 'payload' => ['effect' => '1d6 Terre'], 'force' => false]
 * @example ['payload' => ['name' => 'Campagne', 'description' => '…'], 'force' => false]
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
                'nullable',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || $value === '') {
                        return;
                    }
                    $entityType = (string) $this->route('entityType');
                    $spec = app(SpecializationRegistry::class)->tryForEntityType($entityType);
                    if ($spec === null) {
                        if ($value !== 'inject' && isset(CostEstimator::ACTIONS[$value])) {
                            $fail("L’action IA « {$value} » ne s’applique pas au type « {$entityType} ».");
                        }

                        return;
                    }
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
