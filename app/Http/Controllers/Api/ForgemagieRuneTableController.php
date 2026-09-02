<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Characteristic\Reference\ForgemagieRuneTableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint du tableau des runes de forgemagie (section CMS dédiée).
 */
final class ForgemagieRuneTableController extends Controller
{
    public function __construct(
        private readonly ForgemagieRuneTableService $runeTableService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->runeTableService->build([
            'sort_by' => (string) $request->query('sort_by', 'name'),
            'sort_dir' => (string) $request->query('sort_dir', 'asc'),
        ]));
    }
}
