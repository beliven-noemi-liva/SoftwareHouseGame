<x-layout :game="$game" active="candidati">
    <div class="mb-8">
        <a href="/games" class="text-blue-200 hover:underline">&laquo; Torna alla lista partite</a>
    </div>
    <div class="mb-6">
        <span class="font-semibold">Patrimonio:</span>
        <span id="game-patrimonio" class="text-blue-200 font-bold">€ {{ number_format($game->patrimonio, 0, ',', '.') }}</span>
    </div>
    <h1 class="text-2xl font-bold mb-6">Candidati disponibili</h1>
    <form method="POST" action="/candidati/genera" class="mb-8">
        @csrf
         <button type="submit" class="bg-blue-200 hover:bg-blue-100 text-black font-bold py-2 px-4 rounded text-sm">Genera nuovi candidati</button>
        <span class="text-xs text-gray-300 ml-2">Max 50 totali</span>
    </form>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-xl font-semibold mb-4 text-blue-400">Developer</h3>
            @php
                $devs = $devs->sortByDesc('exp');
            @endphp

            @forelse($devs as $dev)
                @include('games.cardCandidati', [
                    'type' => 'dev',
                    'name' => $dev->name,
                    'exp' => $dev->exp,
                    'stipendio' => $dev->stipendio,
                    'id' => $dev->id,
                    'game' => $game,
                    'project_id' => $dev->project_id,
                ])
            @empty
                <div class="col-span-2 text-gray-400">Nessun candidato Dev disponibile.</div>
            @endforelse
        </div>
        <div>
            <h3 class="text-xl font-semibold mb-4 text-blue-200">Sales</h3>
             @php
                $sales = $sales->sortByDesc('exp');
            @endphp
            @forelse($sales as $sale)
                @include('games.cardCandidati', [
                    'type' => 'sale',
                    'name' => $sale->name,
                    'exp' => $sale->exp,
                    'stipendio' => $sale->stipendio,
                    'id' => $sale->id,
                    'game' => $game,
                    'project_id' => null,
                ])
            @empty
                <div class="col-span-2 text-gray-400">Nessun candidato Sales disponibile.</div>
            @endforelse
        </div>
    </div>
</x-layout>
