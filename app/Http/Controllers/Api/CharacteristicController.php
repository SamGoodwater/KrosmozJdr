<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Characteristic\CharacteristicMetaByDbColumnService;
use Illuminate\Http\JsonResponse;

/**
 * Expose les métadonnées des caractéristiques pour le frontend.
 * Chargées une fois au démarrage (Inertia share ou fetch).
 */
class CharacteristicController extends Controller
{
    public function __construct(
        private readonly CharacteristicMetaByDbColumnService $characteristicMeta
    ) {
    }

    /**
     * Retourne toutes les caractéristiques (creature, spell, object par entité).
     * Structure compatible avec meta.characteristics des API Table.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->characteristicMeta->buildAllForFrontend());
    }
}
