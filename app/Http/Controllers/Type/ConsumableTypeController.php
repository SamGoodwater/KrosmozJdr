<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Models\Type\ConsumableType;
use Inertia\Inertia;

/**
 * Page d'administration des types de consommables (ConsumableType).
 *
 * @description
 * Gestion de la registry DofusDB (dofusdb_type_id + decision).
 */
class ConsumableTypeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', ConsumableType::class);

        $user = request()->user();

        return Inertia::render('Pages/entity/consumable-type/Index', [
            'can' => [
                'updateAny' => $user ? $user->can('updateAny', ConsumableType::class) : false,
            ],
        ]);
    }
}

