<?php

namespace App\Http\Resources;

use App\Enums\SectionType;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Section.
 *
 * Sur `pages.show`, l’attribut de requête `page_show_eager_section_ids` limite
 * le HTML (`data.content`) aux premières sections ; le reste charge via
 * `api.cms.sections.content`.
 */
class SectionResource extends JsonResource
{
    /** @mixin Section */
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        /** @var Section $section */
        $section = $this->resource;

        // canBeEditedBy() a besoin de la page ; éviter N+1 si déjà setRelation depuis PageController.
        if (! $section->relationLoaded('page') && $section->page_id) {
            try {
                $section->load('page');
            } catch (\Exception $e) {
                // continuer sans page
            }
        }

        $canUpdate = $user ? $user->can('update', $section) : false;

        $eagerIds = $request->attributes->get('page_show_eager_section_ids');
        $includeBody = ! is_array($eagerIds)
            || in_array((int) $section->id, array_map('intval', $eagerIds), true);

        $data = $section->data;
        if (! $includeBody && is_array($data)) {
            $data = array_merge($data, [
                'content' => null,
                'content_deferred' => true,
            ]);
        }

        return [
            'id' => $section->id,
            'page_id' => $section->page_id,
            'title' => $section->title,
            'slug' => $section->slug,
            'order' => $section->order,
            'pivot_level' => ($pivotLevel = data_get($section, 'pivot.level')) !== null ? (int) $pivotLevel : null,
            'template' => $section->template instanceof SectionType ? $section->template->value : $section->template,
            'type' => $section->type instanceof SectionType ? $section->type->value : $section->type,
            'settings' => $section->settings,
            'data' => $data,
            'state' => $section->state,
            'read_level' => (int) ($section->read_level ?? 0),
            'write_level' => (int) ($section->write_level ?? 0),
            'created_by' => $section->created_by,
            'created_at' => $section->created_at?->toISOString(),
            'updated_at' => $section->updated_at?->toISOString(),
            'content_deferred' => ! $includeBody,

            'page' => $this->when($includeBody && $section->relationLoaded('page'), function () use ($section) {
                return [
                    'id' => $section->page?->id,
                    'slug' => $section->page?->slug,
                    'title' => $section->page?->title,
                ];
            }),
            'users' => $this->whenLoaded('users'),
            'files' => $this->when($includeBody && $section->relationLoaded('media'), fn () => $section->getMedia('files')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'file' => $media->getUrl(),
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : null,
                    'title' => $media->getCustomProperty('title'),
                    'comment' => $media->getCustomProperty('comment'),
                    'description' => $media->getCustomProperty('description'),
                ];
            })->values()->all()),
            'createdBy' => $this->whenLoaded('createdBy'),

            'can' => [
                'update' => $canUpdate,
                'delete' => $canUpdate,
                'forceDelete' => $canUpdate && $user && $user->can('forceDelete', $section),
                'restore' => $canUpdate && $user && $user->can('restore', $section),
            ],
        ];
    }
}
