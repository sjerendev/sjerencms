{{-- Port of the 01 section in page.tsx, FeaturedCase.tsx and CaseImage.tsx. --}}
@php
    $ids = array_column($data['projects'] ?? [], 'project_id');
    // Unlike the register, no projects chosen means no block at all.
    $projects = $ids === [] ? collect() : \App\Models\Project::chosen($ids);
@endphp
@if ($projects->isNotEmpty())
<section class="gut pt-[clamp(72px,10vw,116px)]">
    <x-section-head :motion="($motion ?? [])['head'] ?? null" :number="$data['number'] ?? ''" :label="$data['label'] ?? ''" :aside="\App\Models\Project::withTotal($data['aside'] ?? null)" />
    <div class="mt-[8px] flex flex-col">
        @foreach ($projects as $project)
            @php
                $tall = $project->tall();
                $image = $tall ? null : $project->imageUrl();
                $index = str_pad($loop->iteration, 2, '0', STR_PAD_LEFT);
                $total = str_pad($loop->count, 2, '0', STR_PAD_LEFT);
            @endphp
            <article>
                {{-- With a full-page screenshot the whole case is pinned (case-pin.js), not just the frame,
                     and is the window's height: the frame takes what the headline and meta row leave over.
                     Without one there is no motion, just a cropped image in a frame of fixed height. --}}
                <div @if ($tall) data-motion="case-pin" @endif @class(['flex flex-col gap-[36px] py-[var(--gutter)]', 'h-svh' => $tall])>
                    <div data-case-frame @class(['relative overflow-hidden bg-plate', 'min-h-[200px] flex-1' => $tall, 'h-[clamp(280px,45vw,520px)]' => ! $tall])>
                        @if ($tall)
                            {{-- width/height reserve the height before the image loads, or ScrollTrigger measures an empty frame. --}}
                            <img data-case-image src="{{ $tall['src'] }}" width="{{ $tall['width'] }}" height="{{ $tall['height'] }}" alt="{{ $project->client }}"
                                class="absolute top-0 left-0 h-auto w-full max-w-none will-change-transform">
                        @elseif ($image)
                            <img src="{{ $image }}" alt="{{ $project->client }}" class="absolute inset-0 size-full object-cover object-top">
                        @endif

                        {{-- A screen so the counter reads against any image. --}}
                        <span aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_bottom,transparent_72%,rgba(0,0,0,0.45))]"></span>

                        <div class="absolute bottom-[20px] left-[20px] flex items-center gap-[10px]">
                            <span class="relative block h-[2px] w-[110px] bg-white/35">
                                <span class="absolute top-0 left-0 block h-[2px] bg-white" style="width: {{ $loop->iteration / $loop->count * 100 }}%"></span>
                            </span>
                            <span class="lbl text-white/80">{{ $index }} / {{ $total }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-[22px]">
                        <span class="lbl text-ink-dim">{{ $project->client }}</span>
                        <h3 class="m-0 max-w-[30ch] text-[clamp(24px,3vw,40px)] leading-[1.18] font-medium tracking-[-0.025em]">{{ $project->headline(app()->getLocale()) }}</h3>
                        <x-project-meta :project="$project" />
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endif
