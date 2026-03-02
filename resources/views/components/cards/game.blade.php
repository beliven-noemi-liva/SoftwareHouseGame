<div style="display: flex; align-items: center; justify-content: space-between; border: 1px solid #aaa; border-radius: 12px; margin-bottom: 20px; padding: 16px; min-width: 400px;" data-game-id="{{ $game->id }}" data-game-state="{{ $game->state }}">
<div style="flex:1; font-size: 1.25rem; font-weight: bold;">
    <a href="/games/{{$game->id}}" class="text-white hover:text-blue-300 transition-colors duration-150">
        {{ $game->name }}
    </a>
</div>
    <div style="flex:1; text-align: center;">  
        <span class="game-patrimonio text-2xl font-bold {{ $game->patrimonio > 0 ? 'text-green-500' : 'text-red-500' }}">
            € {{ number_format($game->patrimonio, 0, ',', '.') }}
        </span>
    </div>
    <div style="flex:1; text-align: right;">
        <x-playButton :game="$game"/>
    </div>
</div>