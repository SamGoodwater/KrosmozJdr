<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Section.
 *
 * Structure et expose les champs principaux, relations et droits d'accès pour le frontend/API.
 * Permet d'inclure dynamiquement les relations si chargées.
 */
class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        
        // IMPORTANT : S'assurer que la page est chargée pour que canBeEditedBy() puisse vérifier les droits
        // La méthode canBeEditedBy() de Section vérifie les droits sur la section ET sur la page
        if (!$this->relationLoaded('page') && $this->page_id) {
            try {
                $this->load('page');
            } catch (\Exception $e) {
                // Si la page ne peut pas être chargée, continuer quand même
            }
        }
        
        return [
            'id' => $this->id,
            'page_id' => $this->page_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'order' => $this->order,
            'template' => $this->template instanceof \App\Enums\SectionType ? $this->template->value : $this->template,
            'settings' => $this->settings,
            'data' => $this->data,
            'is_visible' => $this->is_visible instanceof \App\Enums\Visibility ? $this->is_visible->value : $this->is_visible,
            'can_edit_role' => $this->can_edit_role instanceof \App\Enums\Visibility ? $this->can_edit_role->value : $this->can_edit_role,
            'state' => $this->state,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relations (chargées uniquement si incluses)
            'page' => $this->whenLoaded('page'),
            'users' => $this->whenLoaded('users'),
            'files' => $this->whenLoaded('files'),
            'createdBy' => $this->whenLoaded('createdBy'),

            // Droits d'accès pour l'utilisateur courant
            // Utilise SectionPolicy::update() qui appelle Section::canBeEditedBy()
            // qui vérifie maintenant les droits sur la section ET sur la page
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'forceDelete' => $user ? $user->can('forceDelete', $this->resource) : false,
                'restore' => $user ? $user->can('restore', $this->resource) : false,
            ],
        ];
    }
}
