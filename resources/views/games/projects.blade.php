<x-layout :game="$game" active="progetti">
    <div class="container mx-auto" data-game-state="{{ $game->state }}" data-game-id="{{ $game->id }}" id="game-container">
        @if(session('success'))
            <div class="bg-blue-100 border border-blue-600 text-black px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-blue-900 border border-blue-100 text-white px-4 py-3 rounded mb-6">
                {{ session('warning') }}
            </div>
        @endif
        <div class="mb-8">
            <a href="/games" class="text-blue-200 hover:underline">&laquo; Torna alla lista partite</a>
        </div>
        <h1 class="text-2xl font-bold mb-2">Progetti di {{ $game->name }}</h1>
        <div class="mb-6">
            <span class="font-semibold">Patrimonio:</span>
            <span id="game-patrimonio" class="text-blue-200 font-bold">€ {{ number_format($game->patrimonio, 0, ',', '.') }}</span>
        </div>
        <h2 class="text-xl font-semibold mt-8 mb-3">Progetti attuali</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($projects->where('status', 'in_progress') as $project)
                <div class="bg-blue-200 text-black rounded-lg p-4 shadow project-card" data-project-id="{{ $project->id }}" data-game-id="{{ $game->id }}" data-initial-complexity="{{ $project->initial_complex ?? $project->complex }}">
                    <div class="font-bold text-blue-800 mb-2">#{{ $project->id }}  {{ $project->name }}</div>
                    <div class="flex justify-between items-start">
                        <div class="w-full">
                            <div class="text-sm mb-2">
                                <span class="text-blue-900">Dev assegnati:</span> {{ $project->devs->count() }}
                            </div>
                            <div class="text-sm mb-3">
                                <span class="text-blue-900">Valore:</span> € {{ number_format($project->value, 0, ',', '.') }}
                            </div>
                            <div class="text-sm mb-2">
                                <span class="text-blue-900">Complessità:</span> <span class="initial-complexity">{{ $project->initial_complex}}</span>
                            </div>
                            
                            <div class="mb-2">
                                <div class="flex justify-between mb-1">
                                    <span class="text-blue-900">Progresso: </span>
                                </div>
                                <div class="w-full bg-blue-300 border border-blue-900 rounded-full h-4">
                                    <div class="bg-blue-800 h-4 rounded-full transition-all duration-300 progress-bar" style="width: {{ number_format(($project->initial_complex - $project->complex) / $project->initial_complex * 100)}}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-gray-400">Nessun progetto in corso.</div>
            @endforelse
        </div>

        <h2 class="text-xl font-semibold mt-10 mb-3">Nuovi progetti assegnabili</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($projects->where('status', 'ready') as $project)            
                @include('games.cardproject', [
                    'type' => 'ready',
                    'id' => $project->id,
                    'name' => $project->name,
                    'Salename' => $project->sale->name ?? null,
                    'Complex' => $project->complex,
                    'Value' => $project->value,
                    'gameState' => $game->state,
                    'gameid' => $game->id,
                ])
            @empty
                <div class="col-span-2 text-gray-400">Nessun nuovo progetto disponibile.</div>
            @endforelse
        </div>
        <h2 class="text-xl font-semibold mt-10 mb-3">Progetti completati</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($projects->where('status', 'complete') as $project)            
                @include('games.cardproject', [
                    'type' => 'complete',
                    'id' => $project->id,
                    'name' => $project->name,
                    'Salename' => $project->sale->name ?? null,
                    'Complex' => $project->initial_complex,
                    'Value' => $project->value,
                    'gameState' => $game->state,
                    'gameid' => $game->id,
                ])
            @empty
                <div class="col-span-2 text-gray-400">Nessun progetto completato.</div>
            @endforelse

            @foreach($projects->where('status', 'done') as $project)            
                @include('games.cardproject', [
                    'type' => 'done',
                    'id' => $project->id,
                    'name' => $project->name,
                    'Salename' => $project->sale->name ?? null,
                    'Complex' => $project->initial_complex,
                    'Value' => $project->value,
                    'gameState' => $game->state,
                    'gameid' => $game->id,
                ])
            @endforeach
        </div>
    </div>
</x-layout>