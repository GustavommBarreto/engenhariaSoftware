<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Plano de Sprint – {{ $sprint->nome }} ({{ $project->nome }})
        </h2>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
        @if(session('success'))
            <div class="rounded bg-green-50 text-green-800 px-4 py-2">
                {{ session('success') }}
            </div>
        @endif

        {{-- Plano de sprint: WHAT / HOW / WHO / INCREMENT --}}
        <div class="bg-white rounded-xl shadow p-6 space-y-4">
            <h3 class="font-semibold text-lg">Plano de Sprint</h3>

            <div>
                <h4 class="font-semibold">Objetivo (What)</h4>
                <p class="text-gray-700 whitespace-pre-line">{{ $sprint->objetivo }}</p>
            </div>

            <div>
                <h4 class="font-semibold">Trabalho (How)</h4>
                <p class="text-gray-700 whitespace-pre-line">{{ $sprint->trabalho }}</p>
            </div>

            <div>
                <h4 class="font-semibold">Equipe (Who)</h4>
                <p class="text-gray-700 whitespace-pre-line">{{ $sprint->equipe }}</p>
            </div>

            <div>
                <h4 class="font-semibold">Saída / Incremento</h4>
                <p class="text-gray-700 whitespace-pre-line">{{ $sprint->incremento }}</p>
            </div>
        </div>

        {{-- Sprint backlog: histórias ligadas à sprint --}}
        <div class="bg-white rounded-xl shadow p-6 space-y-4">
            <h3 class="font-semibold text-lg">Sprint Backlog</h3>

            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Ordem</th>
                        <th class="px-4 py-2 text-left">Título</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">SP</th>
                        <th class="px-4 py-2 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($stories as $story)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $story->ordem }}</td>
                        <td class="px-4 py-2">{{ $story->titulo }}</td>
                        <td class="px-4 py-2">{{ $story->status }}</td>
                        <td class="px-4 py-2">{{ $story->story_points }}</td>
                        <td class="px-4 py-2 text-right">
                            <form action="{{ route('projects.sprints.stories.remove', [$project, $sprint, $story]) }}"
                                  method="POST">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">
                                    Remover da sprint
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-4 text-center text-gray-500" colspan="5">
                            Nenhuma história na sprint.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Product backlog disponível para adicionar à sprint --}}
        <div class="bg-white rounded-xl shadow p-6 space-y-4">
            <h3 class="font-semibold text-lg">Histórias no Product Backlog</h3>

            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Ordem</th>
                        <th class="px-4 py-2 text-left">Título</th>
                        <th class="px-4 py-2 text-left">SP</th>
                        <th class="px-4 py-2 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($backlogStories as $story)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $story->ordem }}</td>
                        <td class="px-4 py-2">{{ $story->titulo }}</td>
                        <td class="px-4 py-2">{{ $story->story_points }}</td>
                        <td class="px-4 py-2 text-right">
                            <form action="{{ route('projects.sprints.stories.add', [$project, $sprint, $story]) }}"
                                  method="POST">
                                @csrf
                                <button class="text-blue-600 hover:underline">
                                    Adicionar à sprint
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-4 text-center text-gray-500" colspan="4">
                            Nenhuma história disponível no backlog.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
