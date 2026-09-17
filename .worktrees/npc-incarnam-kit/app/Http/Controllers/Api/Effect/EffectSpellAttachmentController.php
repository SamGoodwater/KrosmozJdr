<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Effect;

use App\Http\Controllers\Controller;
use App\Models\Entity\Spell;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Liaison sort ↔ définition d’effet (pivot effect_spell).
 */
class EffectSpellAttachmentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'spell_id' => 'required|integer|exists:spells,id',
            'effect_id' => 'required|integer|exists:effects,id',
        ]);
        $spell = Spell::query()->findOrFail($data['spell_id']);
        $this->authorize('update', $spell);

        $effectId = (int) $data['effect_id'];
        $spell->effects()->syncWithoutDetaching([$effectId]);

        return response()->json(['ok' => true], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'spell_id' => 'required|integer|exists:spells,id',
            'effect_id' => 'required|integer|exists:effects,id',
        ]);
        $spell = Spell::query()->findOrFail($data['spell_id']);
        $this->authorize('update', $spell);

        $spell->effects()->detach((int) $data['effect_id']);

        return response()->json(null, 204);
    }
}
