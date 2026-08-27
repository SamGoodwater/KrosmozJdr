<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Models\Type\SpellType;
use Illuminate\Http\RedirectResponse;

/**
 * Page d'administration des types de sorts (SpellType).
 *
 * L’index redirige vers le registre commun `/admin/content/types/spell`.
 */
class SpellTypeController extends Controller
{
    public function index(): RedirectResponse
    {
        $this->authorize('viewAny', SpellType::class);

        return redirect()->route('admin.content.types.show', ['kind' => 'spell']);
    }
}
