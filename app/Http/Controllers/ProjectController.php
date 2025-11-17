<?php

namespace App\Http\Controllers;

use App\Enums\ProjectRole;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /** Lista projetos do usuário logado (para o Dashboard) */
    public function index(Request $request)
    {
        $user = $request->user();

        // projetos em que ele é membro (inclui os que ele criou)
        $projects = $user->projects()
            ->with('owner')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard', compact('projects'));
    }

    /** Cria projeto a partir do modal */
    public function store(StoreProjectRequest $request)
    {
        $user = $request->user();

        $project = Project::create([
            'owner_id'  => $user->id,
            'nome'      => $request->nome,
            'descricao' => $request->descricao,
            'status'    => $request->status,
            'inicio'    => $request->inicio,
            'fim'       => $request->fim,
        ]);

        // Dono entra automaticamente na equipe como OWNER
        $project->members()->attach($user->id, ['role' => ProjectRole::OWNER->value]);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Projeto criado com sucesso!');
    }

    /** Excluir projeto */
    public function destroy(Project $project, Request $request)
    {
        // só o dono pode apagar
        abort_unless($project->owner_id === $request->user()->id, 403);
        $project->delete();

        return back()->with('success', 'Projeto removido.');
    }

    /** Detalhes do projeto (dashboard do projeto) */
    public function show(Project $project)
    {
        $this->authorizeView($project);

        $project->load([
            'owner',
            'members',
            'tasks' => function($q) {
                $q->orderBy('priority', 'desc')
                  ->orderBy('due_date', 'asc')
                  ->with('assignee');
            }
        ]);

        $total      = $project->tasks->count();
        $done       = $project->tasks->where('status','DONE')->count();
        $open       = $project->tasks->where('status','OPEN')->count();
        $inprogress = $project->tasks->where('status','IN_PROGRESS')->count();
        $blocked    = $project->tasks->where('status','BLOCKED')->count();

        $today = now()->startOfDay();
        $overdue = $project->tasks
            ->filter(fn($t) => $t->due_date && $t->due_date->lt($today) && $t->status !== 'DONE')
            ->count();

        $nextDue = $project->tasks
            ->filter(fn($t) => $t->due_date && $t->due_date->gte($today) && $t->status !== 'DONE')
            ->sortBy('due_date')
            ->first();

        $progress = $total > 0 ? round(($done / $total) * 100) : 0;

        // Quadro Kanban
        $tasksByStatus = $project->tasks->groupBy('status');
        $allStatuses = collect(['OPEN', 'IN_PROGRESS', 'BLOCKED', 'DONE']);
        $tasksByStatus = $allStatuses
            ->mapWithKeys(fn ($status) => [$status => $tasksByStatus->get($status) ?? collect()])
            ->all();

        $userRole = auth()->user()->id === $project->owner_id
            ? 'OWNER'
            : ($project->members->firstWhere('id', auth()->id())?->pivot?->role ?? '—');

        return view('projects.show', compact(
            'project',
            'total','done','open','inprogress','blocked','overdue','nextDue','progress',
            'userRole',
            'tasksByStatus'
        ));
    }

    /** Tela de edição / configuração do projeto */
    public function edit(Project $project)
    {
        $this->authorizeUpdate($project);

        $project->load('members');

        return view('projects.edit', [
            'project'  => $project,
            'members'  => $project->members,
        ]);
    }

    /** Salvar alterações básicas do projeto */
    public function update(Request $request, Project $project)
    {
        $this->authorizeUpdate($project);

        $data = $request->validate([
            'nome'      => ['required','string','max:150'],
            'descricao' => ['nullable','string','max:5000'],
            'status'    => ['required', Rule::in(['Ativo','Pausado','Concluído'])],
            'inicio'    => ['required','date'],
            'fim'       => ['nullable','date','after_or_equal:inicio'],
        ]);

        $project->update($data);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projeto atualizado.');
    }

    /* ======== MEMBERS ======== */

    /** Adicionar membro por e-mail e papel */
    public function membersStore(Request $request, Project $project)
    {
        $this->authorizeUpdate($project);

        $email = mb_strtolower(trim($request->input('email', '')));

        $data = $request->validate([
            'email' => ['required','email','exists:users,email'],
            'role'  => ['required', Rule::in(['PRODUCT_OWNER','SCRUM_MASTER','DEVELOPER'])],
        ], [
            'email.exists' => 'Este e-mail não está cadastrado no sistema.',
        ]);

        $user = User::whereRaw('LOWER(email) = ?', [$email])->firstOrFail();

        if ($user->id === $project->owner_id) {
            return redirect()->route('projects.edit', $project)
                ->with('error', 'O dono já está no projeto como OWNER.');
        }

        if ($project->members()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.edit', $project)
                ->with('error', 'Usuário já é membro deste projeto.');
        }

        $project->members()->attach($user->id, ['role' => $data['role']]);

        return redirect()->route('projects.edit', $project)
            ->with('success', 'Membro adicionado.');
    }

    /** Atualizar papel de um membro */
    public function membersUpdate(Request $request, Project $project, User $user)
    {
        $this->authorizeUpdate($project);

        $data = $request->validate([
            'role' => ['required', Rule::in(['PRODUCT_OWNER','SCRUM_MASTER','DEVELOPER'])],
        ]);

        if ($user->id === $project->owner_id) {
            return redirect()->route('projects.edit', $project)
                ->with('error','O dono já é OWNER e não pode ter papel trocado.');
        }

        if (! $project->members()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.edit', $project)
                ->with('error','Usuário não é membro deste projeto.');
        }

        $project->members()->updateExistingPivot($user->id, ['role' => $data['role']]);

        return redirect()->route('projects.edit', $project)
            ->with('success', 'Papel atualizado.');
    }

    /** Remover membro do projeto */
    public function membersDestroy(Project $project, User $user)
    {
        $this->authorizeUpdate($project);

        if ($user->id === $project->owner_id) {
            return redirect()->route('projects.edit', $project)
                ->with('error','Não é possível remover o dono do projeto.');
        }

        $project->members()->detach($user->id);

        return redirect()->route('projects.edit', $project)
            ->with('success', 'Membro removido.');
    }

    /* ======== Helpers de autorização ======== */

    private function authorizeView(Project $project): void
    {
        $isMember = $project->members()->where('user_id', auth()->id())->exists();
        abort_unless($isMember, 403);
    }

    private function authorizeUpdate(Project $project): void
    {
        // apenas o dono pode editar/gerenciar membros
        abort_unless($project->owner_id === auth()->id(), 403);
    }
}
