{{-- Port of ProjectMeta.tsx: role and tech on the left, the link always far right. --}}
@props(['project'])
<dl class="m-0 flex flex-wrap items-baseline gap-x-[32px] gap-y-[20px] border-y border-hair py-[18px]">
    <div class="flex w-[200px] flex-col gap-[6px]">
        <dt class="lbl text-ink-dimmer">{{ __('projects.role') }}</dt>
        <dd class="m-0 text-[16px]">{{ $project->role(app()->getLocale()) }}</dd>
    </div>
    {{-- flex-1 pushes the link all the way to the right. --}}
    <div class="flex min-w-[220px] flex-1 flex-col gap-[6px]">
        <dt class="lbl text-ink-dimmer">{{ __('projects.tech') }}</dt>
        <dd class="m-0 text-[16px]">{{ $project->tech() }}</dd>
    </div>
    <div class="ml-auto flex flex-col gap-[6px] text-left md:text-right">
        <dt class="lbl text-ink-dimmer">{{ __('projects.site') }}</dt>
        <dd class="m-0 text-[16px]">
            {{-- A rebuilt site no longer shows his work, so it is not linked. --}}
            @if ($project->rebuilt)
                <span class="text-ink-dimmer">{{ __('projects.rebuilt') }}</span>
            @else
                <a href="{{ $project->url }}" target="_blank" rel="noreferrer" class="wave-link inline-flex items-center gap-[7px] pb-[6px] text-accent">
                    {{ $project->host() }} <span aria-hidden="true">↗</span>
                    <x-wave-underline />
                </a>
            @endif
        </dd>
    </div>
</dl>
