<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Models\Type\MonsterRace;
use Inertia\Inertia;

/**
 * Page d'administration des races de monstres (MonsterRace).
 *
 * @description
 * Validation via le champ `state` (raw/draft/playable/archived).
 */
class MonsterRaceController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', MonsterRace::class);

        $user = request()->user();

        return Inertia::render('Pages/entity/monster-race/Index', [
            'can' => [
                'updateAny' => $user ? $user->can('updateAny', MonsterRace::class) : false,
            ],
        ]);
    }
}

