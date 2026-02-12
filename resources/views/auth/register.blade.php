<x-layout>
    <x-page-heading>Register</x-page-heading>
    <x-forms.form method="POST" action="/register">
        <x-forms.input label="Username" name="username"/>
        <x-forms.input label="Password" name="password" type="password"/>
        <x-forms.button>Invia</x-forms.button>
    </x-forms.form>
</x-layout>