<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserStoryController;
use App\Http\Controllers\SprintController;

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
    Route::get('/projects', [ProjectController::class, 'index'])
    ->name('projects.index');
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

    /*
    |--------------------------------------------------
    | Tasks (já existentes no projeto)
    |--------------------------------------------------
    */
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
    Route::post('/projects/{project}/members', [ProjectController::class, 'membersStore'])
        ->name('projects.members.store');

    Route::put('/projects/{project}/members/{user}', [ProjectController::class, 'membersUpdate'])
        ->name('projects.members.update');

    Route::delete('/projects/{project}/members/{user}', [ProjectController::class, 'membersDestroy'])
        ->name('projects.members.destroy');

    /*
    |--------------------------------------------------
    | Product Backlog (Histórias de Usuário) e Sprints
    |  - /projects/{project}/stories   -> backlog do produto
    |  - /projects/{project}/sprints   -> sprints + plano de sprint
    |--------------------------------------------------
    */
    Route::prefix('projects/{project}')->group(function () {

        // Histórias de usuário (product backlog)
        Route::resource('stories', UserStoryController::class)
            ->parameters(['stories' => 'story'])
            ->names('projects.stories');

        // Sprints + plano de sprint
        Route::resource('sprints', SprintController::class)
            ->names('projects.sprints');

        // Sprint backlog: adicionar / remover histórias da sprint
        Route::post('sprints/{sprint}/stories/{story}', [SprintController::class, 'addStory'])
            ->name('projects.sprints.stories.add');

        Route::delete('sprints/{sprint}/stories/{story}', [SprintController::class, 'removeStory'])
            ->name('projects.sprints.stories.remove');
    });
});

/*
|--------------------------------------------------------------------------
| Auth (rotas geradas pelo Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
