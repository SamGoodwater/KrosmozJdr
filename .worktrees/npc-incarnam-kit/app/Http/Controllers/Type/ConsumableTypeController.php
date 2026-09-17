<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Models\Type\ConsumableType;
use Illuminate\Http\RedirectResponse;

/**
 * Page d'administration des types de consommables (ConsumableType).
 *
 * L’index redirige vers le registre commun `/admin/content/types/consumable`.
 */
class ConsumableTypeController extends Controller
{
    public function index(): RedirectResponse
    {
        $this->authorize('viewAny', ConsumableType::class);

        return redirect()->route('admin.content.types.show', ['kind' => 'consumable']);
    }
}
