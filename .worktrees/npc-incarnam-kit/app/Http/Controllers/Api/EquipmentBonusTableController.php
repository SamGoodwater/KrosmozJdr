<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Characteristic\Reference\EquipmentBonusTableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Tableau vivant des plafonds de bonus d’équipement (atelier MJ).
 */
final class EquipmentBonusTableController extends Controller
{
    public function __construct(
        private readonly EquipmentBonusTableService $equipmentBonusTableService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $role = (int) ($request->user()?->role ?? User::ROLE_GUEST);
        if ($role < User::ROLE_GAME_MASTER) {
            abort(403, 'Réservé aux meneurs de jeu.');
        }

        return response()->json($this->equipmentBonusTableService->build());
    }
}
