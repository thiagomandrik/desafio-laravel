<?php

use App\Http\Controllers\PlaceController;
use Illuminate\Support\Facades\Route;

Route::apiResource('places', PlaceController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy'])
    ->whereNumber('place');
