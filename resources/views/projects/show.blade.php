<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Projeto: {{ $project->nome }}
            </h2>

            @if(auth()->id() === $project->owner_id)
                <a href="{{ route('projects.edit', $project) }}"
                   class="px-3 py-2 rounded-lg bg-blue-600 text-white">Editar</a>
            @endif
        </div>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
        @if(session('success'))
            <div class="rounded-lg bg-green-50 text-green-800 px-4 py-2">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-lg bg-red-50 text-red-800 px-4 py-2">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg bg-red-50 text-red-800 px-4 py-2 mb-4">
                <ul class="list-disc ms-6">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="bg-white p-6 rounded-xl shadow">
            <h3 class="font-semibold text-lg">Resumo</h3>
            <p class="text-gray-700 mt-2">{{ $project->descricao }}</p>
            <dl class="mt-4 grid grid-cols-2 gap-4 text-sm text-gray-600">
                <div><dt class="font-medium">Status</dt><dd>{{ $project->status }}</dd></div>
                <div><dt class="font-medium">Dono</dt><dd>{{ $project->owner->name }}</dd></div>
                <div><dt class="font-medium">Início</dt><dd>{{ $project->inicio->format('d/m/Y') }}</dd></div>
                <div><dt class="font-medium">Fim</dt><dd>{{ $project->fim?->format('d/m/Y') ?? '—' }}</dd></div>
            </dl>
        </section>

        <section class="bg-white p-6 rounded-xl shadow">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-lg">Membros</h3>
            </div>

            <table class="mt-4 w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Nome</th>
                        <th class="px-3 py-2 text-left">E-mail</th>
                        <th class="px-3 py-2 text-left">Papel</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                <tr class="border-b">
                    <td class="px-3 py-2">{{ $project->owner->name }}</td>
                    <td class="px-3 py-2">{{ $project->owner->email }}</td>
                    <td class="px-3 py-2">OWNER</td>
                    <td class="px-3 py-2"></td>
                </tr>
                @foreach($project->members as $m)
                    @continue($m->id === $project->owner_id)
                    <tr class="border-b">
                        <td class="px-3 py-2">{{ $m->name }}</td>
                        <td class="px-3 py-2">{{ $m->email }}</td>
                        <td class="px-3 py-2">
                            @if(auth()->id() === $project->owner_id)
                                <form method="POST" action="{{ route('projects.members.update', [$project, $m]) }}">
                                    @csrf @method('PUT')
                                    <select name="role" class="rounded border-gray-300"
                                            onchange="this.form.submit()">
                                        @foreach(['PRODUCT_OWNER','SCRUM_MASTER','DEVELOPER'] as $role)
                                            <option value="{{ $role }}" {{ $m->pivot->role === $role ? 'selected':'' }}>
                                                {{ $role }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                {{ $m->pivot->role }}
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right">
                            @if(auth()->id() === $project->owner_id)
                                <form method="POST" action="{{ route('projects.members.destroy', [$project, $m]) }}">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Remover</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if(auth()->id() === $project->owner_id)
                <form class="mt-6 flex gap-3 items-end" method="POST" action="{{ route('projects.members.store', $project) }}">
                    @csrf
                    <div class="flex-1">
                        <label class="text-sm text-gray-700">E-mail do usuário</label>
                        <input name="email" type="email" required class="w-full mt-1 rounded border-gray-300" placeholder="usuario@dominio.com">
                    </div>
                    <div>
                        <label class="text-sm text-gray-700">Papel</label>
                        <select name="role" class="mt-1 rounded border-gray-300">
                            <option value="PRODUCT_OWNER">PRODUCT_OWNER</option>
                            <option value="SCRUM_MASTER">SCRUM_MASTER</option>
                            <option value="DEVELOPER">DEVELOPER</option>
                        </select>
                    </div>
                    <button class="px-4 py-2 rounded bg-blue-600 text-white">Adicionar</button>
                </form>
            @endif
        </section>
    </div>
</x-app-layout>
