<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Characteristic\Reference\CharacteristicReferenceTableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint du tableau de référence des caractéristiques (section CMS dédiée).
 */
final class CharacteristicReferenceTableController extends Controller
{
    public function __construct(
        private readonly CharacteristicReferenceTableService $referenceTableService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $role = (int) ($request->user()?->role ?? User::ROLE_GUEST);
        $canSeeStatus = $role >= User::ROLE_GAME_MASTER;

        $payload = $this->referenceTableService->build([
            'group' => (string) $request->query('group', 'all'),
            'entity' => (string) $request->query('entity', '*'),
            'search' => (string) $request->query('search', ''),
            'sort_by' => (string) $request->query('sort_by', 'group'),
            'sort_dir' => (string) $request->query('sort_dir', 'asc'),
            'status_filter' => $canSeeStatus ? (string) $request->query('status_filter', 'all') : 'all',
            'include_status' => $canSeeStatus,
            'show_only_with_equipment' => (bool) $request->boolean('show_only_with_equipment', false),
        ]);

        return response()->json($payload);
    }
}

