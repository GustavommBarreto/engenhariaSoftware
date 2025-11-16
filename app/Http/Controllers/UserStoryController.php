<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\UserStory;
use Illuminate\Http\Request;

class UserStoryController extends Controller
{
    private function authorizeProject(Project $project): void
    {
        $isMember = $project->members()
            ->where('user_id', auth()->id())
            ->exists();

        abort_unless($isMember, 403);
    }

    public function index(Project $project)
    {
        $this->authorizeProject($project);

        // Product backlog = todas as histórias do projeto, ordenadas
        $stories = $project->stories()
            ->orderBy('ordem')
            ->orderBy('id')
            ->get();

        return view('stories.index', compact('project', 'stories'));
    }

    public function create(Project $project)
    {
        $this->authorizeProject($project);

        return view('stories.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $data = $request->validate([
            'titulo'           => ['required', 'string', 'max:255'],
            'descricao'        => ['nullable', 'string'],
            'criterios_aceite' => ['nullable', 'string'],
            'ordem'            => ['nullable', 'integer', 'min:0'],
            'story_points'     => ['nullable', 'integer', 'min:0'],
            'valor_negocio'    => ['nullable', 'integer', 'min:0'],
        ]);

        $data['project_id'] = $project->id;
        $data['status'] = 'BACKLOG';

        UserStory::create($data);

        return redirect()
            ->route('projects.stories.index', $project)
            ->with('success', 'História criada com sucesso.');
    }

    public function edit(Project $project, UserStory $story)
    {
        $this->authorizeProject($project);
        abort_unless($story->project_id === $project->id, 404);

        return view('stories.edit', compact('project', 'story'));
    }

    public function update(Request $request, Project $project, UserStory $story)
    {
        $this->authorizeProject($project);
        abort_unless($story->project_id === $project->id, 404);

        $data = $request->validate([
            'titulo'           => ['required', 'string', 'max:255'],
            'descricao'        => ['nullable', 'string'],
            'criterios_aceite' => ['nullable', 'string'],
            'ordem'            => ['nullable', 'integer', 'min:0'],
            'story_points'     => ['nullable', 'integer', 'min:0'],
            'valor_negocio'    => ['nullable', 'integer', 'min:0'],
            'status'           => ['required', 'in:BACKLOG,EM_SPRINT,CONCLUIDA,CANCELADA'],
        ]);

        $story->update($data);

        return redirect()
            ->route('projects.stories.index', $project)
            ->with('success', 'História atualizada.');
    }

    public function destroy(Project $project, UserStory $story)
    {
        $this->authorizeProject($project);
        abort_unless($story->project_id === $project->id, 404);

        $story->delete();

        return redirect()
            ->route('projects.stories.index', $project)
            ->with('success', 'História removida.');
    }
}
