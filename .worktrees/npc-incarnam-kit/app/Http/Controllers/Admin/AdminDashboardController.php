<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Tableau de bord central de l’administration (liens vers sync, backup, données, effets, etc.).
 */
class AdminDashboardController extends Controller
{
    public function __invoke(): InertiaResponse
    {
        return Inertia::render('Admin/Dashboard/Index', [
            'appEnv' => config('app.env'),
        ]);
    }
}
