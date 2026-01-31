<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Models\Type\SpellType;
use Inertia\Inertia;

/**
 * Page d'administration des types de sorts (SpellType).
 *
 * @description
 * Validation via le champ `state` (raw/draft/playable/archived).
 */
class SpellTypeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', SpellType::class);

        $user = request()->user();

        return Inertia::render('Pages/entity/spell-type/Index', [
            'can' => [
                'updateAny' => $user ? $user->can('updateAny', SpellType::class) : false,
            ],
        ]);
    }
}

