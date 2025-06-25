<div class="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b dark:border-gray-700">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center flex-1">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ $title ?? $heading }}
            </h1>
        </div>

        @if ($actions ?? false)
            <div class="flex items-center gap-4">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
