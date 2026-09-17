<?php

declare(strict_types=1);

use App\Http\Controllers\Api\GenerativeAi\IaStatusController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:admin', 'password.confirm', 'throttle:30,1'])
    ->get('ia/status', IaStatusController::class)
    ->name('api.ia.status');
