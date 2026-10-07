<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Spell;

use App\Http\Controllers\Controller;
use App\Http\Requests\Spell\StoreSpellDegreeRequest;
use App\Http\Requests\Spell\SyncSpellDegreeEffectsRequest;
use App\Http\Requests\Spell\SyncSpellDegreesBulkRequest;
use App\Http\Requests\Spell\UpdateSpellDegreeRequest;
use App\Models\Entity\Spell;
use App\Models\SpellDegree;
use App\Services\Spell\SpellDegreeService;
use App\Services\Spell\SpellDegreesSerializer;
use Illuminate\Http\JsonResponse;

/**
 * API degrés / effets d’un sort.
 */
class SpellDegreeController extends Controller
{
    public function __construct(
        private readonly SpellDegreeService $service,
        private readonly SpellDegreesSerializer $serializer
    ) {}

    public function index(Spell $spell): JsonResponse
    {
        $this->authorize('view', $spell);

        return response()->json([
            'data' => $this->serializer->serialize($spell),
        ]);
    }

    public function store(StoreSpellDegreeRequest $request, Spell $spell): JsonResponse
    {
        $this->authorize('update', $spell);
        $data = $request->validated();
        $effects = $data['effects'] ?? null;
        unset($data['effects']);

        $degree = $this->service->createDegree($spell, $data);
        if (is_array($effects)) {
            $degree = $this->service->syncEffects($degree, $effects);
        }

        return response()->json([
            'data' => $this->serializer->serialize($spell->fresh()),
            'degree_id' => $degree->id,
        ], 201);
    }

    /**
     * Enregistrement groupé de tous les degrés modifiés (un seul bouton Enregistrer).
     */
    public function syncBulk(SyncSpellDegreesBulkRequest $request, Spell $spell): JsonResponse
    {
        $this->authorize('update', $spell);
        $this->service->syncDegreesBulk($spell, $request->validated('degrees') ?? []);

        return response()->json([
            'data' => $this->serializer->serialize($spell->fresh()),
        ]);
    }

    public function update(UpdateSpellDegreeRequest $request, Spell $spell, SpellDegree $degree): JsonResponse
    {
        $this->authorize('update', $spell);
        $this->assertDegreeBelongsToSpell($spell, $degree);

        $data = $request->validated();
        $effects = $data['effects'] ?? null;
        unset($data['effects']);

        $degree = $this->service->updateDegree($degree, $data);
        if (is_array($effects)) {
            $degree = $this->service->syncEffects($degree, $effects);
        }

        return response()->json([
            'data' => $this->serializer->serialize($spell->fresh()),
            'degree_id' => $degree->id,
        ]);
    }

    public function destroy(Spell $spell, SpellDegree $degree): JsonResponse
    {
        $this->authorize('update', $spell);
        $this->assertDegreeBelongsToSpell($spell, $degree);
        $this->service->deleteDegree($degree);

        return response()->json([
            'data' => $this->serializer->serialize($spell->fresh()),
        ]);
    }

    public function materializeEffects(Spell $spell, SpellDegree $degree): JsonResponse
    {
        $this->authorize('update', $spell);
        $this->assertDegreeBelongsToSpell($spell, $degree);
        $degree = $this->service->materializeEffects($degree);

        return response()->json([
            'data' => $this->serializer->serialize($spell->fresh()),
            'degree_id' => $degree->id,
        ]);
    }

    public function syncEffects(SyncSpellDegreeEffectsRequest $request, Spell $spell, SpellDegree $degree): JsonResponse
    {
        $this->authorize('update', $spell);
        $this->assertDegreeBelongsToSpell($spell, $degree);
        $degree = $this->service->syncEffects($degree, $request->validated('effects') ?? []);

        return response()->json([
            'data' => $this->serializer->serialize($spell->fresh()),
            'degree_id' => $degree->id,
        ]);
    }

    private function assertDegreeBelongsToSpell(Spell $spell, SpellDegree $degree): void
    {
        abort_unless((int) $degree->spell_id === (int) $spell->id, 404);
    }
}
