<x-filament::layouts.base :livewire="$livewire">
    <div class="filament-app-layout flex h-full w-full overflow-x-clip">
        <div
            class="filament-main flex-1 flex flex-col w-full mx-auto filament-main-content"
        >
            <header @class([
                'filament-main-topbar sticky top-0 z-10 flex h-16 w-full shrink-0 items-center border-b bg-white',
                'dark:bg-gray-800 dark:border-gray-700' => config('filament.dark_mode'),
            ])>
                {{ $slot }}
            </header>

            <main class="filament-main-content flex-1 w-full px-4 mx-auto md:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-filament::layouts.base>
