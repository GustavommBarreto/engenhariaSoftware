<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nova Sprint – {{ $project->nome }}
        </h2>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('projects.sprints.index', $project) }}" class="text-sm text-gray-600 hover:underline">
                ← Voltar para lista de sprints
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

            <form method="POST" action="{{ route('projects.sprints.store', $project) }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Nome da sprint
                    </label>
                    <input type="text" name="nome" value="{{ old('nome') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Início
                        </label>
                        <input type="date" name="inicio" value="{{ old('inicio') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Fim
                        </label>
                        <input type="date" name="fim" value="{{ old('fim') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Objetivo (What)
                    </label>
                    <textarea name="objetivo" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('objetivo') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Trabalho (How)
                    </label>
                    <textarea name="trabalho" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('trabalho') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Equipe (Who)
                    </label>
                    <textarea name="equipe" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('equipe') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Incremento / Entrega esperada
                    </label>
                    <textarea name="incremento" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('incremento') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Status
                    </label>
                    <select name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach (['PLANEJADA', 'EM_ANDAMENTO', 'CONCLUIDA', 'CANCELADA'] as $status)
                            <option value="{{ $status }}" @selected(old('status', 'PLANEJADA') === $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <a href="{{ route('projects.sprints.index', $project) }}"
                       class="px-4 py-2 rounded-lg border text-sm text-gray-700">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm">
                        Salvar sprint
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
