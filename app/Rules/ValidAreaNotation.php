<?php

declare(strict_types=1);

namespace App\Rules;

use App\Support\AreaNotation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valide la notation de zone (point, line-1xL, cross-a-b, circle-a-b, rect-WxH, shape-id, shape-id-p1-p2).
 */
final class ValidAreaNotation implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }
        if (! is_string($value)) {
            $fail(__('validation.area_notation'));

            return;
        }
        if (! AreaNotation::isValid($value)) {
            $fail(__('validation.area_notation'));
        }
    }
}
