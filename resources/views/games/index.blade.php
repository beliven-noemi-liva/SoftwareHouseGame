<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-widht, user-scalable=no, initial-scale=1.0, maximum-scale=1.0,minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" contennt="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Software House Game </title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-black text-white pb-10">
    <div class="px-10">
        <nav class="flex justify-end items-center py-4 border-b border-white/10">
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
            <div class="container">
                <x-page-heading>Nuova partita</x-page-heading>
                <x-forms.form method="POST" action="/games">
                    @csrf
                    <x-forms.input label="Nuova partita" name="name" required/>
                    <x-forms.button>Crea</x-forms.button>
                </x-forms.form>
                @if($errors->has('name'))
                    <div style="color: red">{{ $errors->first('name') }}</div>
                @endif
                <x-forms.divider class="my-10"/>
                <x-page-heading>Le tue partite</x-page-heading>
                <div id="games-container">    
                    @if($games->count())
                            @php
                                $games = $games->sortBy(function($game) {
                                    switch ($game->state) {               
                                        case 'in_progress':
                                            return 1;
                                        case 'paused':
                                            return 2;
                                        case 'finish':
                                            return 3;
                                        default:
                                            return 4;
                                    } 
                                    });
                            @endphp     
                            @foreach($games as $game)
                                    @include('games.card', ['game' => $game])
                            @endforeach
                    @else
                        <p>Nessuna partita ancora!</p>
                    @endif
                </div>


            </div>
        </main>
    </div>
</body>
</html>

