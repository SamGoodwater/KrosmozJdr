<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Models\Type\MonsterRace;
use Illuminate\Http\RedirectResponse;

/**
 * Page d'administration des races de monstres (MonsterRace).
 *
 * L’index redirige vers le registre commun `/admin/content/types/race`.
 */
class MonsterRaceController extends Controller
{
    public function index(): RedirectResponse
    {
        $this->authorize('viewAny', MonsterRace::class);

        return redirect()->route('admin.content.types.show', ['kind' => 'race']);
    }
}
