<?php
use App\Http\Controllers\FasilitasController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FasilitasController::class, 'index']);
Route::get('/api/fasilitas', [FasilitasController::class, 'geojson']);
Route::post('/api/fasilitas', [FasilitasController::class, 'store']);
