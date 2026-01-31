<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Models\Type\ItemType;
use Inertia\Inertia;

/**
 * Page d'administration des types d'équipements (ItemType).
 *
 * @description
 * Gestion de la registry DofusDB (dofusdb_type_id + decision).
 */
class ItemTypeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', ItemType::class);

        $user = request()->user();

        return Inertia::render('Pages/entity/item-type/Index', [
            'can' => [
                'updateAny' => $user ? $user->can('updateAny', ItemType::class) : false,
            ],
        ]);
    }
}

