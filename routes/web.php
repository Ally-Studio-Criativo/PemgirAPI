<?php

use Illuminate\Support\Facades\Route;

// Rota raiz - página padrão do Laravel
Route::get('/', function () {
    return view('welcome');
});
