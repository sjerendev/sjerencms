@props([
    'actions' => [],
    'heading',
    'subheading' => null,
])

<header {{ $attributes->class(['fi-header flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between p-4 sm:p-6 filament-sticky']) }}>
    <div>
        <h1 class="fi-header-heading text-2xl font-bold tracking-tight">
            {{ $heading }}
        </h1>

        @if ($subheading)
            <p class="fi-header-subheading mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ $subheading }}
            </p>
        @endif
    </div>

    @if (count($actions))
        <div class="fi-header-actions flex shrink-0 gap-3">
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
</header>

<style>
.filament-sticky {
    position: sticky;
    top: 0;
    z-index: 50;
    background-color: rgb(255, 255, 255);
    border-bottom: 1px solid rgb(229, 231, 235);
    margin: 0;
    width: 100%;
    padding: 1rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

[data-theme='dark'] .filament-sticky {
    background-color: rgb(17, 24, 39);
    border-color: rgb(55, 65, 81);
}
</style>
