<?php

use App\Http\Controllers\Api\CoordinatorController;
use Illuminate\Support\Facades\Route;

// The LoRaWAN network server (TTN / ChirpStack) webhook points here.
// Protect this in production (a secret query token or IP allowlist is enough
// for a student project — see the README for the quick way to do it).
Route::post('/coordinator/attendance', [CoordinatorController::class, 'receive']);
