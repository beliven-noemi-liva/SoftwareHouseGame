<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0,minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Software House Game</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-black text-white pb-10">
    <div class="px-10">
        <nav class="flex justify-between items-center py-4 border-b border-white/10">
            <div> 
            </div>
            @auth
            <div class="space-x-6 font-bold">
                <x-link href="/games/{{ $game?->id }}/candidati" active="{{$active === 'candidati'}}"  >Candidati</x-link>
                <x-link href="{{ $game?->id ? '/games/' . $game->id : '#' }}" active="{{$active === 'azienda'}}" >Azienda</x-link>
                <x-link href="/games/{{ $game?->id }}/projects" active="{{$active === 'progetti'}}" >Progetti</x-link>
            </div>
            @endauth
            @auth
                <div class="space-x-6 font-bold flex">
                    <form method="POST" action="/logout">
                        @csrf
                        @method('DELETE')
                        <button class="text-white bg-transparent hover:text-blue-300 active:text-blue-300 transition-colors duration-150">Log out</button>
                    </form>
                </div>
            @endauth
            @guest
                <div class="space-x-6 font-bold">
                    <x-link  href="/register">Sing Up</x-link>
                    <x-link href="/login">Log In</x-link>
                </div>
            @endguest

        </nav>
        <main class="mt-10 max-w-[986px] mx-auto">
            {{ $slot}}
        </main>
    </div>
</body>
</html>