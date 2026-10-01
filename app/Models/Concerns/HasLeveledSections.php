<?php

namespace App\Models\Concerns;

use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Relation many-to-many sections avec pivot {@code level} (classes, spécialisations, …).
 */
trait HasLeveledSections
{
    /**
     * Nom de la table pivot (ex. {@code section_breed}, {@code section_specialization}).
     */
    abstract protected function sectionsPivotTable(): string;

    /**
     * Clé étrangère vers l'entité porteuse (ex. {@code breed_id}, {@code specialization_id}).
     */
    abstract protected function sectionsPivotForeignKey(): string;

    /**
     * Sections CMS liées à l'entité, ordonnables par niveau pivot.
     */
    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(
            Section::class,
            $this->sectionsPivotTable(),
            $this->sectionsPivotForeignKey(),
            'section_id',
        )
            ->withPivot('level')
            ->withTimestamps();
    }

    /**
     * Contrainte d'eager-load standard (niveau pivot puis titre).
     *
     * @return \Closure(BelongsToMany): void
     */
    public static function orderedSectionsEagerLoadConstraint(): \Closure
    {
        return static fn (BelongsToMany $query): BelongsToMany => $query
            ->orderByPivot('level')
            ->orderBy('title');
    }

    /**
     * Eager-load lecture : brouillons / read_level hors portée exclus (comme {@see Section::displayable()}).
     * Les admins voient toutes les liaisons (aligné {@see \App\Policies\SectionPolicy::view}).
     * L’édition continue d’utiliser {@see self::orderedSectionsEagerLoadConstraint()} sans filtre.
     *
     * @return \Closure(BelongsToMany): void
     *
     * @example Breed::query()->with(['sections' => Breed::visibleSectionsEagerLoadConstraint($viewer)])
     */
    public static function visibleSectionsEagerLoadConstraint(?User $user): \Closure
    {
        return static function (BelongsToMany $query) use ($user): BelongsToMany {
            $ordered = self::orderedSectionsEagerLoadConstraint();
            $ordered($query);

            if ($user?->isAdmin()) {
                return $query;
            }

            return $query->displayable($user);
        };
    }
}
