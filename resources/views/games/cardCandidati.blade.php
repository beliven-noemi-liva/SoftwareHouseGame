@php
    $border = $type === 'sale' ? 'border-blue-200' : 'border-blue-400';
    $bgcolor = $type === 'sale' ? 'bg-blue-200' : 'bg-blue-400';
    $text   = $type === 'sale' ? 'text-blue-200'   : 'text-blue-400';
@endphp

<div class="p-4 mb-4 rounded bg-transparent border-2 {{ $border }} flex justify-between items-center">
    <div>
        <div class="font-medium text-lg {{ $text }}">{{ $name }}</div>
        <div>Esperienza: <span class="font-semibold">{{ $exp }}</span></div>
        <div>Stipendio: €{{ $stipendio }}</div>
        @if($project_id)
            <div class="text-xs text-gray-400">Su progetto #{{ $project_id }}</div>
        @endif
    </div>
    @if(!$game->devs()->where('id', $id)->exists() && !$game->sales()->where('id', $id)->exists())
        <form method="POST" action="/candidati/{{ $id }}/assumi">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="id" value="{{ $id }}">
            <input type="hidden" name="game_id" value="{{ $game->id }}">
            <button type="submit" class="{{ $bgcolor }} hover:bg-blue-100 text-black font-bold py-2 px-4 rounded text-sm ml-4">Assumi</button>
        </form>
    @endif
</div>
