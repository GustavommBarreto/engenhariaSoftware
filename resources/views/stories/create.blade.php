<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nova História de Usuário – {{ $project->nome }}
        </h2>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('projects.stories.index', $project) }}" class="text-sm text-gray-600 hover:underline">
                ← Voltar ao product backlog
            </a>
        </div>

        <div class="bg-white shadow rounded-xl p-6 space-y-6">
            @if ($errors->any())
                <div class="rounded bg-red-50 text-red-800 px-4 py-2 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('projects.stories.store', $project) }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Título da história
                    </label>
                    <input type="text" name="titulo" value="{{ old('titulo') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <p class="mt-1 text-xs text-gray-500">
                        Ex.: "Como [tipo de usuário] eu quero [algo] para [benefício]".
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Descrição
                    </label>
                    <textarea name="descricao" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('descricao') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Critérios de aceite
                    </label>
                    <textarea name="criterios_aceite" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('criterios_aceite') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Ordem no backlog
                        </label>
                        <input type="number" name="ordem" value="{{ old('ordem', 0) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Story points
                        </label>
                        <input type="number" name="story_points" value="{{ old('story_points') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Valor de negócio
                        </label>
                        <input type="number" name="valor_negocio" value="{{ old('valor_negocio') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <a href="{{ route('projects.stories.index', $project) }}"
                       class="px-4 py-2 rounded-lg border text-sm text-gray-700">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm">
                        Salvar história
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
