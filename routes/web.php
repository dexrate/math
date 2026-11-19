<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'home'])->name('home');
Route::post('/gera-exercicios', [MainController::class, 'gerarExercicios'])->name('gera-exercicios');
Route::get('/imprimir-exercicios', [MainController::class, 'imprimeExercicios'])->name('imprimir-exercicios');
Route::get('/exportar-exercicios', [MainController::class, 'exportarExercicios'])->name('exportar-exercicios');
