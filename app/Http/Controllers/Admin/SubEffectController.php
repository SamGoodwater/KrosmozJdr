<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubEffect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Admin : liste des sous-effets (référentiel en lecture).
 */
class SubEffectController extends Controller
{
    public function index(): InertiaResponse
    {
        $subEffects = SubEffect::withCount('effects')
            ->orderBy('type_slug')
            ->orderBy('slug')
            ->get();

        return Inertia::render('Admin/sub-effects/Index', [
            'subEffects' => $subEffects->map(fn (SubEffect $s) => [
                'id' => $s->id,
                'slug' => $s->slug,
                'type_slug' => $s->type_slug,
                'template_text' => $s->template_text,
                'dofusdb_effect_id' => $s->dofusdb_effect_id,
                'effects_count' => $s->effects_count ?? 0,
            ])->values()->all(),
        ]);
    }
}
