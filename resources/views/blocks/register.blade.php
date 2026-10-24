{{-- Port of the register sections in page.tsx and projekt/page.tsx. --}}
@php
    $projects = \App\Models\Project::chosen(array_column($data['projects'] ?? [], 'project_id'));
@endphp
{{-- The air above depends on the page: the home page has more (see data-home in pages/show). --}}
<section class="gut pt-[clamp(56px,8vw,90px)] [[data-home]_&]:pt-[clamp(72px,10vw,116px)]">
    <x-section-head :number="$data['number'] ?? ''" :label="$data['label'] ?? ''" :aside="\App\Models\Project::withTotal($data['aside'] ?? null)" />
    <div class="mt-[44px]">
        @foreach ($projects as $project)
            <x-register-row :project="$project" :number="$loop->iteration" />
        @endforeach
        @if (filled($data['all_label'] ?? null))
            <div class="flex justify-end border-t border-hair pt-[24px]">
                <a href="{{ route('page.show', ['slug' => 'projekt']) }}" wire:navigate class="wave-link lbl flex items-center gap-[10px] pb-[8px] hover:text-accent">
                    {{ \App\Models\Project::withTotal($data['all_label']) }} <span aria-hidden="true">→</span>
                    <x-wave-underline />
                </a>
            </div>
        @else
            <div class="border-t border-hair"></div>
        @endif
    </div>
</section>
