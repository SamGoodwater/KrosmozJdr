<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Type\ResourceType;
use App\Models\Page;
use App\Models\Section;
use App\Policies\PagePolicy;
use App\Policies\SectionPolicy;
use App\Policies\Type\ResourceTypePolicy;

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
        ResourceType::class => ResourceTypePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
