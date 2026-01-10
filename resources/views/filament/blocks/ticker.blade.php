@php
    $backgroundColor = $getBlockData('background_color');
    $textColor = $getBlockData('text_color');
    $angle = $getBlockData('angle');
    $direction = $getBlockData('direction');
    $words = json_encode($getBlockData('words'));
    $sectionClass = $getBlockData('section_class');
@endphp

<div id="ticker-block-{{ $getBlockLoopIteration() }}" class="{{ $sectionClass }}"></div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const { createApp } = Vue;
        const TickerBlock = {
            template: `
                <section
                    class="ticker-block"
                    :style="{
                        '--background-color': backgroundColor,
                        '--text-color': textColor,
                        '--angle': angle + 'deg'
                    }"
                >
                    <div class="ticker-wrap" :class="direction">
                        <div class="ticker-track" ref="track">
                            <div class="ticker-item" v-for="(word, index) in words" :key="index">{{ word }}</div>
                        </div>
                    </div>
                </section>
            `,
            props: {
                backgroundColor: { type: String, default: '#ffffff' },
                textColor: { type: String, default: '#000000' },
                angle: { type: Number, default: 0 },
                direction: { type: String, default: 'left-to-right' },
                words: { type: Array, default: () => [] },
            },
            mounted() {
                const track = this.$refs.track;
                const items = Array.from(track.querySelectorAll('.ticker-item'));
                const dir = this.direction === 'right-to-left' ? -1 : 1;

                items.forEach(item => {
                    track.appendChild(item.cloneNode(true));
                });

                const trackWidth = track.offsetWidth;
                const halfTrackWidth = trackWidth / 2;

                gsap.to(track, {
                    x: dir * -halfTrackWidth,
                    ease: 'none',
                    duration: 30,
                    repeat: -1,
                });
            }
        };

        createApp(TickerBlock, {
            backgroundColor: '{{ $backgroundColor }}',
            textColor: '{{ $textColor }}',
            angle: {{ $angle }},
            direction: '{{ $direction }}',
            words: {{ $words }}
        }).mount('#ticker-block-{{ $getBlockLoopIteration() }}');
    });
</script>