@props(['type', 'id', 'name', 'sale_name', 'complex', 'value', 'game_state', 'game_id'])
@php
    $text = $type === 'ready' ? 'text-white' : 'text-blue-900';
    $bgcolor = $type === 'ready' ? 'bg-blue-800' : 'bg-blue-300';
    $title   = $type === 'ready' ? 'text-blue-200'   : 'text-blue-700';
@endphp

<div class="{{ $bgcolor }} rounded-lg p-4 {{ $text }} shadow">
    <div class="flex justify-between items-start">
        <div>
            <div class="font-bold mb-2 {{ $title }}"> #{{ $id }} {{ $name }}</div>
            <div class="text-sm mb-2">
                <span class="font-semibold {{ $title }}">Sales: </span>{{ $sale_name ?? '-' }}
            </div>
            <div class="text-sm mb-2 mt-2">
                <span class="{{ $title }}">Complessità:</span> {{ $complex }}
            </div>
            <div class="text-sm">
                <span class="{{ $title }}">Valore:</span> {{ $value }}
            </div>
        </div>
        @if($game_state == 'in_progress' && $type == 'ready')
        <div>
            <form method="POST" action="/games/{{ $game_id }}/projects/{{ $id }}/assign-devs" style="display: inline">
                @csrf
                <button type="submit" class="bg-blue-200 hover:bg-blue-100 text-black font-bold py-2 px-4 rounded text-sm ml-4">
                    Assegna Dev
                </button>
            </form>
        </div>
        @endif
    </div>
</div>