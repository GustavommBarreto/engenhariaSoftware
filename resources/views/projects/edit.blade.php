<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar projeto</h2>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <form class="bg-white p-6 rounded-xl shadow space-y-4"
              method="POST" action="{{ route('projects.update', $project) }}">
            @csrf @method('PUT')

            @if($errors->any())
                <div class="rounded bg-red-50 text-red-800 px-4 py-2">
                    <ul class="list-disc ms-6">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="text-sm">Nome</label>
                <input name="nome" class="w-full mt-1 rounded border-gray-300" value="{{ old('nome',$project->nome) }}">
            </div>

            <div>
                <label class="text-sm">Descrição</label>
                <textarea name="descricao" rows="4" class="w-full mt-1 rounded border-gray-300">{{ old('descricao',$project->descricao) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm">Status</label>
                    <select name="status" class="w-full mt-1 rounded border-gray-300">
                        @foreach(['Ativo','Pausado','Concluído'] as $st)
                            <option value="{{ $st }}" {{ $project->status===$st ? 'selected':'' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm">Início</label>
                    <input type="date" name="inicio" class="w-full mt-1 rounded border-gray-300"
                           value="{{ old('inicio', $project->inicio->toDateString()) }}">
                </div>
                <div>
                    <label class="text-sm">Fim</label>
                    <input type="date" name="fim" class="w-full mt-1 rounded border-gray-300"
                           value="{{ old('fim', $project->fim?->toDateString()) }}">
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 rounded border">Cancelar</a>
                <button class="px-4 py-2 rounded bg-blue-600 text-white">Salvar</button>
            </div>
        </form>
    </div>
</x-app-layout>
