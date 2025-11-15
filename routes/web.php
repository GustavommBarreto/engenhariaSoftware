<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Quando o usuário acessar "/", redireciona para a tela de login
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Rotas protegidas por autenticação
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [ProjectController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------
    | Perfil (rotas padrão Breeze)
    |--------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------
    | Projetos - CRUD completo
    |--------------------------------------------------
    */
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])
        ->name('projects.tasks.store');

    Route::get('/tasks/{task}', [TaskController::class, 'show'])
        ->name('tasks.show');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.updateStatus');

    /*
    |--------------------------------------------------
    | Membros de projeto (adicionar / atualizar / remover)
    |--------------------------------------------------
    */
    Route::post('/projects/{project}/members', [ProjectController::class, 'membersStore'])->name('projects.members.store');
    Route::put('/projects/{project}/members/{user}', [ProjectController::class, 'membersUpdate'])->name('projects.members.update');
    Route::delete('/projects/{project}/members/{user}', [ProjectController::class, 'membersDestroy'])->name('projects.members.destroy');
});

/*
|--------------------------------------------------------------------------
| Auth (rotas geradas pelo Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
