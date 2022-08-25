<x-dropdown>
    <x-slot name="trigger">
        <button
         class="py-2 pl-3 pr-9 text-sm font-semibold w-full lg:w-32 text-left flex lg:inline-flex">
         {{isset($currentType) ? ucwords($currentType->name) : 'Types'}}
    <x-icon name="down-arrow" class="absolute pointer-events-none" style="right:12px;" />
    </button>
    </x-slot>
    <x-dropdown-item href="/" :active="request()->routeIs('home')">All</x-dropdown-item>
    @foreach($types as $type)
    <x-dropdown-item 
        href="?type={{$type->slug}} & {{ http_build_query(request()->except('type', 'page')) }}"
        :active="request()->is('types/' . $type->slug)"
        >{{ucwords($type->name) }}</x-dropdown-item>
    @endforeach
</x-dropdown> 