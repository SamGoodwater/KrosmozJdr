<?php

namespace App\Http\Requests\Entity\Concerns;

use App\Models\Entity\Capability;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;

/**
 * Aligne les champs avec les colonnes NOT NULL de {@see Capability}
 * après {@see ConvertEmptyStringsToNull} (chaînes vides → null, ou JSON null explicite).
 *
 * À appeler depuis {@see FormRequest::prepareForValidation()} (pas après validation :
 * {@see FormRequest::validated()} ne reflète pas un merge fait dans {@see FormRequest::passedValidation()}).
 */
trait NormalizesCapabilityStringDefaults
{
    /**
     * Valeurs par défaut alignées sur database/migrations/2025_06_01_100130_entity_capabilities_table.php
     * et migrations ultérieures (booléens, is_passive).
     */
    protected function normalizeCapabilityNotNullDefaultsForDatabase(): void
    {
        $merge = [];

        $stringDefaults = [
            'time_before_use_again' => '0',
            'casting_time' => '0',
            'duration' => '0',
            'level' => '1',
            'pa' => '3',
            'po' => '0',
            'state' => 'draft',
        ];

        foreach ($stringDefaults as $field => $default) {
            if ($this->exists($field) && $this->input($field) === null) {
                $merge[$field] = $default;
            }
        }

        if ($this->exists('element') && $this->input('element') === null) {
            $merge['element'] = 0;
        }

        if ($this->exists('read_level') && $this->input('read_level') === null) {
            $merge['read_level'] = 0;
        }

        $booleanDefaults = [
            'po_editable' => true,
            'is_magic' => true,
            'ritual_available' => true,
            'is_passive' => false,
        ];

        foreach ($booleanDefaults as $field => $default) {
            if ($this->exists($field) && $this->input($field) === null) {
                $merge[$field] = $default;
            }
        }

        if ($this->exists('write_level') && $this->input('write_level') === null) {
            $read = array_key_exists('read_level', $merge)
                ? $merge['read_level']
                : $this->input('read_level');
            if ($read === null) {
                $read = 0;
            }
            $merge['write_level'] = max(3, (int) $read);
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}
