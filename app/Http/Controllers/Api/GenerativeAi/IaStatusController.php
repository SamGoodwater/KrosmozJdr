<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\GenerativeAi;

use App\Http\Controllers\Controller;
use App\Services\GenerativeAi\AnthropicUsageService;
use App\Services\GenerativeAi\CostEstimator;
use App\Services\GenerativeAi\GenerativeAiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Usage local + estimations de coût (admin). Pas d’appel org Anthropic : le modal Sources reste instantané.
 *
 * @example GET /api/ia/status
 */
class IaStatusController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        abort_unless($request->user()?->isAdmin() === true, 403);

        return response()->json([
            'usage' => app(AnthropicUsageService::class)->snapshot(false),
            'estimates' => app(CostEstimator::class)->all(app(GenerativeAiClient::class)->model()),
        ]);
    }
}
