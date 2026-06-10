@php
    $isAdmin = auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super-Admin'));
@endphp

@if (!$isAdmin)
    <x-layout>
        <x-add-product />
    </x-layout>
@endif
