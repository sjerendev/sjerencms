{{-- Port of Services.tsx with its section from page.tsx. Four columns, three dividers: none before the first, none after the last. --}}
@php
    $columns = array_values($data['columns'] ?? []);
@endphp
<section class="gut pt-[clamp(72px,10vw,116px)]">
    <x-section-head :motion="($motion ?? [])['head'] ?? null" :number="$data['number'] ?? ''" :label="$data['label'] ?? ''" />
    <div class="mt-[56px] grid grid-cols-2 gap-y-[48px] lg:grid-cols-4">
        @foreach ($columns as $column)
            {{-- 2x2 on mobile: only column 1 keeps its divider on the right. --}}
            <div class="flex flex-col px-[20px] first:pl-0 last:pr-0 lg:px-[40px] {{ ! $loop->last ? 'border-r border-hair-mid' : '' }} {{ $loop->index % 2 === 1 ? 'max-lg:border-r-0 max-lg:pr-0' : 'max-lg:pl-0' }}">
                <h3 class="m-0 text-[21px] font-bold tracking-[-0.01em] uppercase lg:text-[25px]"@textMotion('columns_title')>{{ $column['title'] ?? '' }}</h3>
                <div class="mt-[40px] flex flex-col gap-[13px] text-[15px] text-ink-dim lg:mt-[76px] lg:text-[16px]"@textMotion('columns_items')>
                    @foreach (array_filter(array_map('trim', preg_split('/\R/u', $column['items'] ?? ''))) as $item)
                        <span{{ \App\Support\TextMotion::piece(($motion ?? [])['columns_items'] ?? null) }}>{{ $item }}</span>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
