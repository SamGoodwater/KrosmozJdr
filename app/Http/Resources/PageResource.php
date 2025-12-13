<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Page.
 *
 * Structure et expose les champs principaux, relations et droits d'accès pour le frontend/API.
 * Permet d'inclure dynamiquement les relations si chargées.
 */
class PageResource extends JsonResource
{
    /** @mixin \App\Models\Page */
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        /** @var \App\Models\Page $page */
        $page = $this->resource;
        return [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'is_visible' => $page->is_visible instanceof \App\Enums\Visibility ? $page->is_visible->value : $page->is_visible,
            'can_edit_role' => $page->can_edit_role instanceof \App\Enums\Visibility ? $page->can_edit_role->value : $page->can_edit_role,
            'in_menu' => $page->in_menu,
            'state' => $page->state instanceof \App\Enums\PageState ? $page->state->value : $page->state,
            'parent_id' => $page->parent_id,
            'menu_order' => $page->menu_order,
            'created_by' => $page->created_by,
            'created_at' => $page->created_at?->toISOString(),
            'updated_at' => $page->updated_at?->toISOString(),

            // Relations (chargées uniquement si incluses)
            'parent' => $this->whenLoaded('parent'),
            'children' => $this->whenLoaded('children'),
            'users' => $this->whenLoaded('users'),
            'sections' => $this->when($page->relationLoaded('sections') || $page->sections, function () use ($request, $page) {
                return $page->sections->map(function ($section) use ($request) {
                    return (new SectionResource($section))->toArray($request);
                });
            }),
            'campaigns' => $this->whenLoaded('campaigns'),
            'scenarios' => $this->whenLoaded('scenarios'),
            'createdBy' => $this->whenLoaded('createdBy'),

            // Droits d'accès pour l'utilisateur courant
            'can' => [
                'update' => $user ? $user->can('update', $page) : false,
                'delete' => $user ? $user->can('delete', $page) : false,
                'forceDelete' => $user ? $user->can('forceDelete', $page) : false,
                'restore' => $user ? $user->can('restore', $page) : false,
            ],
        ];
    }
}
