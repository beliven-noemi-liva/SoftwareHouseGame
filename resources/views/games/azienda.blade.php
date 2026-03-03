<x-layout :game="$game" active="azienda">
    <div class=" space-y-10">
        <div class="mb-8">
            <a href="/games" class="text-blue-200 hover:underline">&laquo; Torna alla lista partite</a>
        </div>
        {{-- <x-statistics :game="$game"/> --}} 
        <div id="statistics-wrapper"
            data-game-id="{{ $game->id }}"
            data-game='@json($game)'>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-xl font-semibold mb-4 text-blue-400">Developer</h3>
                @foreach($devs as $dev)
                    <x-cards.candidati
                        type="dev"
                        :name="$dev->name"
                        :exp="$dev->exp"
                        :stipendio="$dev->stipendio"
                        :id="$dev->id"
                        :game="$game"
                        :project_id="$dev->project_id"
                    />
                @endforeach
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-4 text-blue-200">Sales</h3>
                @foreach($sales as $sale)
                    <x-cards.candidati
                        type="sale"
                        :name="$sale->name"
                        :exp="$sale->exp"
                        :stipendio="$sale->stipendio"
                        :id="$sale->id"
                        :game="$game"
                        :project_id="null"
                    />
                @endforeach
            </div>
        </div>
    </div>
</x-layout>

