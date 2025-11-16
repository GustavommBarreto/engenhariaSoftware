<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\UserStory;
use Illuminate\Http\Request;

class SprintController extends Controller
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

        $sprints = $project->sprints()
            ->orderByDesc('inicio')
            ->get();

        return view('sprints.index', compact('project', 'sprints'));
    }

    public function create(Project $project)
    {
        $this->authorizeProject($project);

        return view('sprints.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $data = $request->validate([
            'nome'       => ['required', 'string', 'max:255'],
            'objetivo'   => ['required', 'string'],
            'trabalho'   => ['nullable', 'string'],
            'equipe'     => ['nullable', 'string'],
            'incremento' => ['nullable', 'string'],
            'inicio'     => ['required', 'date'],
            'fim'        => ['required', 'date', 'after_or_equal:inicio'],
            'status'     => ['required', 'in:PLANEJADA,EM_ANDAMENTO,CONCLUIDA,CANCELADA'],
        ]);

        $data['project_id'] = $project->id;

        Sprint::create($data);

        return redirect()
            ->route('projects.sprints.index', $project)
            ->with('success', 'Sprint criada.');
    }

    public function show(Project $project, Sprint $sprint)
    {
        $this->authorizeProject($project);
        abort_unless($sprint->project_id === $project->id, 404);

        $stories = $sprint->stories()->orderBy('ordem')->get();

        // Também vamos querer ver as histórias que ainda estão só no product backlog
        $backlogStories = $project->stories()
            ->whereNull('sprint_id')
            ->orderBy('ordem')
            ->get();

        return view('sprints.show', compact('project', 'sprint', 'stories', 'backlogStories'));
    }

    public function edit(Project $project, Sprint $sprint)
    {
        $this->authorizeProject($project);
        abort_unless($sprint->project_id === $project->id, 404);

        return view('sprints.edit', compact('project', 'sprint'));
    }

    public function update(Request $request, Project $project, Sprint $sprint)
    {
        $this->authorizeProject($project);
        abort_unless($sprint->project_id === $project->id, 404);

        $data = $request->validate([
            'nome'       => ['required', 'string', 'max:255'],
            'objetivo'   => ['required', 'string'],
            'trabalho'   => ['nullable', 'string'],
            'equipe'     => ['nullable', 'string'],
            'incremento' => ['nullable', 'string'],
            'inicio'     => ['required', 'date'],
            'fim'        => ['required', 'date', 'after_or_equal:inicio'],
            'status'     => ['required', 'in:PLANEJADA,EM_ANDAMENTO,CONCLUIDA,CANCELADA'],
        ]);

        $sprint->update($data);

        return redirect()
            ->route('projects.sprints.show', [$project, $sprint])
            ->with('success', 'Sprint atualizada.');
    }

    public function destroy(Project $project, Sprint $sprint)
    {
        $this->authorizeProject($project);
        abort_unless($sprint->project_id === $project->id, 404);

        $sprint->delete();

        return redirect()
            ->route('projects.sprints.index', $project)
            ->with('success', 'Sprint removida.');
    }

    /** Adiciona história ao sprint backlog */
    public function addStory(Project $project, Sprint $sprint, UserStory $story)
    {
        $this->authorizeProject($project);
        abort_unless($sprint->project_id === $project->id, 404);
        abort_unless($story->project_id === $project->id, 404);

        $story->update([
            'sprint_id' => $sprint->id,
            'status'    => 'EM_SPRINT',
        ]);

        return back()->with('success', 'História adicionada ao sprint backlog.');
    }

    /** Remove história do sprint backlog (volta para product backlog) */
    public function removeStory(Project $project, Sprint $sprint, UserStory $story)
    {
        $this->authorizeProject($project);
        abort_unless($sprint->project_id === $project->id, 404);
        abort_unless($story->project_id === $project->id, 404);

        $story->update([
            'sprint_id' => null,
            'status'    => 'BACKLOG',
        ]);

        return back()->with('success', 'História removida do sprint.');
    }
}
