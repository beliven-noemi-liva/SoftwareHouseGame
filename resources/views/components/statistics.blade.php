{{-- Deprecated: This file/component is not used anymore.
Kept for historical/reference purposes. May be removed in the future. --}}

@props(['game'])
<div class="border-3 border-blue-100 bg-blue-950 rounded px-5 py-4 mx-50" data-game-id="{{ $game->id }}" data-game-state="{{ $game->state }}" style="display: flex; align-items: center; justify-content: space-between;">
    <div class="font-semibold">
        <h2 class="text-2xl font-bold mb-6">Partita: {{ $game->name }}</h2>
        <div>
            Patrimonio:
            <span class="game-patrimonio font-bold {{ $game->patrimonio > 0 ? 'text-green-500' : 'text-red-500' }}">
                € {{ number_format($game->patrimonio, 0, ',', '.') }}
            </span>
        </div>
        <div class="mt-1">
            Stato:
            <span class="game-stato font-bold {{ $game->state === 'paused' ? 'text-yellow-600' : ($game->state === 'finish' ? 'text-red-800' : 'text-green-300') }}">
                @if($game->state === 'paused')
                    In pausa
                @elseif($game->state === 'finish')
                    Terminata
                @elseif($game->state === 'in_progress')
                    In corso
                @endif
            </span>
        </div>
    </div>
    <div style="flex:1; text-align: right;">
        <x-playButton :game="$game"/>
    </div>
</div>