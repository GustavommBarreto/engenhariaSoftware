<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Product Backlog – {{ $project->nome }}
        </h2>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-2">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex justify-between items-center">
            <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-600 hover:underline">
                ← Voltar para o projeto
            </a>

            <a href="{{ route('projects.stories.create', $project) }}"
               class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm">
                Nova história
            </a>
        </div>

        <div class="bg-white shadow rounded-xl overflow-hidden">
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
                        <td class="px-4 py-2 text-right space-x-2">
                            <a href="{{ route('projects.stories.edit', [$project, $story]) }}"
                               class="text-blue-600 hover:underline">Editar</a>

                            <form action="{{ route('projects.stories.destroy', [$project, $story]) }}"
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline"
                                        onclick="return confirm('Remover história?')">
                                    Remover
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-4 text-center text-gray-500" colspan="5">
                            Nenhuma história cadastrada.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
