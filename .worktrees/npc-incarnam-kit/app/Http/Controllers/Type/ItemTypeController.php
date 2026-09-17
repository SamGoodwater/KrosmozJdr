<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Models\Type\ItemType;
use Illuminate\Http\RedirectResponse;

/**
 * Page d'administration des types d'équipements (ItemType).
 *
 * L’index redirige vers le registre commun `/admin/content/types/equipment`.
 */
class ItemTypeController extends Controller
{
    public function index(): RedirectResponse
    {
        $this->authorize('viewAny', ItemType::class);

        return redirect()->route('admin.content.types.show', ['kind' => 'equipment']);
    }
}
