{{-- Deprecated: This file/component is not used anymore.
Kept for historical/reference purposes. May be removed in the future. --}}
@props(['game'])
<div class="play-button">
    @if($game->state === 'paused')
    <form method="POST" action="/games/{{ $game->id }}/resume" style="display: inline">
        @csrf
        <button type="submit" class="bg-blue-200 text-black rounded py-2 px-6 font-bold hover:bg-blue-100">
            Riprendi
        </button>
    </form>
    @else
        @if($game->state === 'finish')
            @csrf
            <span class="bg-transparent py-2 px-6 font-bold">
                Game Over
            </span>
        @else
        <form method="POST" action="/games/{{ $game->id }}/pause" style="display: inline">
            @csrf
            <button type="submit" class="bg-blue-400 text-black rounded py-2 px-6 font-bold hover:bg-blue-100">
                Pausa
            </button>
        </form>
        @endif
    @endif
</div>