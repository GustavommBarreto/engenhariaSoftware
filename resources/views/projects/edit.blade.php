<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar projeto</h2>
    </x-slot>

    <div class="py-10 max-w-4xl mx-auto sm:px-6 lg:px-8">

        {{-- FORMULÁRIO PRINCIPAL DO PROJETO --}}
        <form class="bg-white p-6 rounded-xl shadow space-y-4"
              method="POST" action="{{ route('projects.update', $project) }}">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="rounded bg-red-50 text-red-800 px-4 py-2">
                    <ul class="list-disc ms-6">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="text-sm">Nome</label>
                <input name="nome" class="w-full mt-1 rounded border-gray-300"
                       value="{{ old('nome',$project->nome) }}">
            </div>

            <div>
                <label class="text-sm">Descrição</label>
                <textarea name="descricao" rows="4"
                          class="w-full mt-1 rounded border-gray-300">{{ old('descricao',$project->descricao) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm">Status</label>
                    <select name="status" class="w-full mt-1 rounded border-gray-300">
                        @foreach(['Ativo','Pausado','Concluído'] as $st)
                            <option value="{{ $st }}" {{ $project->status===$st ? 'selected':'' }}>
                                {{ $st }}
                            </option>
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
                <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 rounded border">
                    Cancelar
                </a>
                <button class="px-4 py-2 rounded bg-blue-600 text-white">
                    Salvar
                </button>
            </div>
        </form>

        @php
            // garante que temos uma coleção de membros
            $members = $project->members ?? collect();
        @endphp

        {{-- ALERTAS DE SUCESSO/ERRO PARA A PARTE DE MEMBROS --}}
        @if (session('success'))
            <div class="mt-6 rounded bg-green-50 text-green-800 px-4 py-2">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mt-6 rounded bg-red-50 text-red-800 px-4 py-2">
                {{ session('error') }}
            </div>
        @endif

        {{-- BLOCO DE MEMBROS DO PROJETO --}}
        <div class="mt-8 bg-white p-6 rounded-xl shadow space-y-6">
            <h3 class="text-lg font-semibold">
                Membros do projeto
            </h3>

            {{-- Tabela de membros atuais --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Nome</th>
                            <th class="px-4 py-2 text-left">E-mail</th>
                            <th class="px-4 py-2 text-left">Papel</th>
                            <th class="px-4 py-2 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($members as $member)
                        @php
                            $isOwner = $member->id === $project->owner_id;
                        @endphp
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $member->name }}</td>
                            <td class="px-4 py-2">{{ $member->email }}</td>
                            <td class="px-4 py-2">
                                {{ $isOwner ? 'OWNER' : ($member->pivot->role ?? '-') }}
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                @if (! $isOwner)
                                    {{-- atualizar papel --}}
                                    <form method="POST"
                                          action="{{ route('projects.members.update', [$project, $member]) }}"
                                          class="inline">
                                        @csrf
                                        @method('PUT')

                                        <select name="role"
                                                class="rounded-md border-gray-300 text-xs">
                                            @foreach (['PRODUCT_OWNER','SCRUM_MASTER','DEVELOPER'] as $role)
                                                <option value="{{ $role }}"
                                                    @selected(($member->pivot->role ?? '') === $role)>
                                                    {{ $role }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <button type="submit"
                                                class="ml-2 text-xs text-blue-600 hover:underline">
                                            Atualizar
                                        </button>
                                    </form>

                                    {{-- remover membro --}}
                                    <form method="POST"
                                          action="{{ route('projects.members.destroy', [$project, $member]) }}"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Remover este membro do projeto?')"
                                                class="text-xs text-red-600 hover:underline">
                                            Remover
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-500">
                                        Papel fixo do dono do projeto (OWNER)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-4 text-center text-gray-500" colspan="4">
                                Nenhum membro além do dono do projeto.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Formulário para adicionar novo membro --}}
            <div class="pt-4 border-t">
                <h4 class="font-medium mb-2 text-sm">Adicionar novo membro</h4>

                <form method="POST"
                      action="{{ route('projects.members.store', $project) }}"
                      class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            E-mail do usuário
                        </label>
                        <input type="email" name="email"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                               placeholder="usuario@exemplo.com" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Papel no projeto
                        </label>
                        <select name="role"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="PRODUCT_OWNER">PRODUCT_OWNER</option>
                            <option value="SCRUM_MASTER">SCRUM_MASTER</option>
                            <option value="DEVELOPER">DEVELOPER</option>
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm">
                            Adicionar membro
                        </button>
                    </div>
                </form>

                <p class="mt-2 text-xs text-gray-500">
                    O usuário precisa já ter se registrado no sistema com este e-mail.
                </p>
            </div>
        </div>

    </div>
</x-app-layout>
export PATH="/usr/bin:$PATH"
hash -r
