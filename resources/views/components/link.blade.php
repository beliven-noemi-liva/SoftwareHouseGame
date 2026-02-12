@props(['href' => '#', 'active' => false])

<a  
    href="{{ $href }}"  
    class="
        text-white bg-transparent hover:text-blue-300 active:text-blue-300 transition-colors duration-150 {{ $active ? 'font-bold underline' : '' }}"
>
    {{ $slot }}
</a>