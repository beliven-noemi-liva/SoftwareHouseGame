<x-layout :game="$game" active="azienda">
    <div class=" space-y-10">
        <div class="mb-8">
            <a href="/games" class="text-blue-200 hover:underline">&laquo; Torna alla lista partite</a>
        </div>
        <h2 class="text-2xl font-bold mb-6">Partita: {{ $game->name }}</h2>
        <div class="mb-6 flex space-x-4">
            <div>
                <span class="font-semibold">Patrimonio:</span>
                <span class="text-blue-200 font-bold">€ {{ number_format($game->patrimonio, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="font-semibold">Stato:</span>
                @if($game->state === 'paused')
                    <span class="text-yellow-600">In pausa</span>
                @elseif($game->state === 'finish')
                    <span class="text-red-600">Terminata</span>
                @elseif($game->state === 'in_progress')
                    <span class="text-green-200">In corso</span>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-xl font-semibold mb-4 text-blue-400">Developer</h3>
                @foreach($devs as $dev)
                    @include('games.cardCandidati', [
                        'type' => 'dev',
                        'name' => $dev->name,
                        'exp' => $dev->exp,
                        'stipendio' => $dev->stipendio,
                        'id' => $dev->id,
                        'game' => $game,
                        'project_id' => $dev->project_id,
                    ])
                @endforeach
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-4 text-blue-200">Sales</h3>
                @foreach($sales as $sale)
                    @include('games.cardCandidati', [
                        'type' => 'sale',
                        'name' => $sale->name,
                        'exp' => $sale->exp,
                        'stipendio' => $sale->stipendio,
                        'id' => $sale->id,
                        'game' => $game,
                        'project_id' => null,
                    ])
                @endforeach
            </div>
        </div>
    </div>
</x-layout>

