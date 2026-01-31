<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Models\Type\MonsterRace;
use App\Models\Type\ResourceType;
use App\Models\Type\SpellType;
use App\Models\Page;
use App\Models\Section;
use App\Policies\PagePolicy;
use App\Policies\SectionPolicy;
use App\Policies\Type\ConsumableTypePolicy;
use App\Policies\Type\ItemTypePolicy;
use App\Policies\Type\MonsterRacePolicy;
use App\Policies\Type\ResourceTypePolicy;
use App\Policies\Type\SpellTypePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Page::class => PagePolicy::class,
        Section::class => SectionPolicy::class,
        ItemType::class => ItemTypePolicy::class,
        ConsumableType::class => ConsumableTypePolicy::class,
        ResourceType::class => ResourceTypePolicy::class,
        MonsterRace::class => MonsterRacePolicy::class,
        SpellType::class => SpellTypePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
