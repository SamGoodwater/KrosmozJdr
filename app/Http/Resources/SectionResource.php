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
    /** @mixin \App\Models\Section */
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        /** @var \App\Models\Section $section */
        $section = $this->resource;
        
        // IMPORTANT : S'assurer que la page est chargée pour que canBeEditedBy() puisse vérifier les droits
        // La méthode canBeEditedBy() de Section vérifie les droits sur la section ET sur la page
        if (!$section->relationLoaded('page') && $section->page_id) {
            try {
                $section->load('page');
            } catch (\Exception $e) {
                // Si la page ne peut pas être chargée, continuer quand même
            }
        }
        
        return [
            'id' => $section->id,
            'page_id' => $section->page_id,
            'title' => $section->title,
            'slug' => $section->slug,
            'order' => $section->order,
            'template' => $section->template instanceof \App\Enums\SectionType ? $section->template->value : $section->template,
            'settings' => $section->settings,
            'data' => $section->data,
            'state' => $section->state,
            'read_level' => (int) ($section->read_level ?? 0),
            'write_level' => (int) ($section->write_level ?? 0),
            'created_by' => $section->created_by,
            'created_at' => $section->created_at?->toISOString(),
            'updated_at' => $section->updated_at?->toISOString(),

            // Relations (chargées uniquement si incluses)
            'page' => $this->whenLoaded('page'),
            'users' => $this->whenLoaded('users'),
            'files' => $this->whenLoaded('files'),
            'createdBy' => $this->whenLoaded('createdBy'),

            // Droits d'accès pour l'utilisateur courant
            // Utilise SectionPolicy::update() qui appelle Section::canBeEditedBy()
            // qui vérifie maintenant les droits sur la section ET sur la page
            'can' => [
                'update' => $user ? $user->can('update', $section) : false,
                'delete' => $user ? $user->can('delete', $section) : false,
                'forceDelete' => $user ? $user->can('forceDelete', $section) : false,
                'restore' => $user ? $user->can('restore', $section) : false,
            ],
        ];
    }
}
