<?php

namespace App\Http\Controllers;

use App\Enums\ProjectRole;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;

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

    /** (Opcional) excluir */
    public function destroy(Project $project, Request $request)
    {
        // simples: só o dono pode apagar
        abort_unless($project->owner_id === $request->user()->id, 403);
        $project->delete();

        return back()->with('success', 'Projeto removido.');
    }

    public function show(Project $project)
    {
        // opcional: autorize se o usuário é membro
        $this->authorizeView($project);

        $project->load(['owner','members']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorizeUpdate($project);

        return view('projects.edit', compact('project'));
    }

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

    // Adiciona membro por e-mail e papel
    public function membersStore(Request $request, Project $project)
    {
        $this->authorizeUpdate($project);

        $email = mb_strtolower(trim($request->input('email', '')));

        $data = $request->validate([
            'email' => ['required','email','exists:users,email'],
            'role'  => ['required', \Illuminate\Validation\Rule::in(['PRODUCT_OWNER','SCRUM_MASTER','DEVELOPER'])],
        ], [
            'email.exists' => 'Este e-mail não está cadastrado no sistema.',
        ]);

        $user = \App\Models\User::whereRaw('LOWER(email) = ?', [$email])->firstOrFail();

        if ($user->id === $project->owner_id) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'O dono já está no projeto como OWNER.');
        }

        if ($project->members()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Usuário já é membro deste projeto.');
        }

        $project->members()->attach($user->id, ['role' => $data['role']]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Membro adicionado.');
    }

    public function membersUpdate(Request $request, Project $project, User $user)
    {
        $this->authorizeUpdate($project);

        $data = $request->validate([
            'role' => ['required', \Illuminate\Validation\Rule::in(['PRODUCT_OWNER','SCRUM_MASTER','DEVELOPER'])],
        ]);

        if ($user->id === $project->owner_id) {
            return redirect()->route('projects.show', $project)
                ->with('error','O dono já é OWNER e não pode ter papel trocado.');
        }

        if (! $project->members()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.show', $project)
                ->with('error','Usuário não é membro deste projeto.');
        }

        $project->members()->updateExistingPivot($user->id, ['role' => $data['role']]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Papel atualizado.');
    }

    public function membersDestroy(Project $project, User $user)
    {
        $this->authorizeUpdate($project);

        if ($user->id === $project->owner_id) {
            return redirect()->route('projects.show', $project)
                ->with('error','Não é possível remover o dono do projeto.');
        }

        $project->members()->detach($user->id);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Membro removido.');
    }

    /* ======== Helpers simples de autorização ======== */
    private function authorizeView(Project $project): void
    {
        $isMember = $project->members()->where('user_id', auth()->id())->exists();
        abort_unless($isMember, 403);
    }

    private function authorizeUpdate(Project $project): void
    {
        // apenas o dono pode editar/gerenciar membros (simples)
        abort_unless($project->owner_id === auth()->id(), 403);
    }
}
