@if ($isAdmin)
    <x-admin-layout>
        <x-edit-product :product="$product" :categories="$categories" :isAdmin="$isAdmin" />
    </x-admin-layout>
@else
    <x-layout>
        <x-edit-product :product="$product" :categories="$categories" :isAdmin="$isAdmin" />
    </x-layout>
@endif
