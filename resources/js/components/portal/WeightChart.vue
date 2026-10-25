<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { fmtDate, fmtShortDate } from '@/lib/format';

// Single-series trend line (weight over time). Drawn at the container's real
// pixel width so labels never scale; hover/touch shows a crosshair + tooltip.
const props = defineProps({
    points: { type: Array, required: true }, // [{ date: 'YYYY-MM-DD', value: Number }]
    unit: { type: String, default: 'kg' },
    height: { type: Number, default: 240 },
});

const root = ref(null);
const width = ref(640);
const hover = ref(null);
let observer = null;

const pad = { top: 16, right: 16, bottom: 28, left: 44 };

const data = computed(() =>
    props.points.map(p => ({ ...p, t: new Date(`${p.date}T00:00:00`).getTime() })).sort((a, b) => a.t - b.t)
);

const scale = computed(() => {
    const pts = data.value;
    const values = pts.map(p => p.value);
    let min = Math.min(...values);
    let max = Math.max(...values);
    const spread = Math.max(max - min, 1);
    const step = [0.5, 1, 2, 5, 10, 20].find(s => spread / s <= 4) ?? 50;
    min = Math.floor((min - spread * 0.1) / step) * step;
    max = Math.ceil((max + spread * 0.1) / step) * step;

    const ticks = [];
    for (let v = min; v <= max + 1e-9; v += step) ticks.push(Number(v.toFixed(1)));

    const t0 = pts[0].t;
    const t1 = pts[pts.length - 1].t;
    const innerW = Math.max(width.value - pad.left - pad.right, 10);
    const innerH = props.height - pad.top - pad.bottom;

    return {
        ticks,
        x: t => pad.left + (t1 === t0 ? innerW / 2 : ((t - t0) / (t1 - t0)) * innerW),
        y: v => pad.top + (1 - (v - min) / (max - min)) * innerH,
        baseline: pad.top + innerH,
    };
});

const coords = computed(() => data.value.map(p => ({ ...p, x: scale.value.x(p.t), y: scale.value.y(p.value) })));
const linePath = computed(() => coords.value.map((c, i) => `${i ? 'L' : 'M'}${c.x.toFixed(1)},${c.y.toFixed(1)}`).join(' '));
const areaPath = computed(() => {
    const c = coords.value;
    return `${linePath.value} L${c[c.length - 1].x.toFixed(1)},${scale.value.baseline} L${c[0].x.toFixed(1)},${scale.value.baseline} Z`;
});
const last = computed(() => coords.value[coords.value.length - 1]);
const active = computed(() => (hover.value === null ? null : coords.value[hover.value]));

const summary = computed(() => {
    const pts = data.value;
    if (pts.length < 2) return 'Weight trend';
    return `Weight trend from ${fmtDate(pts[0].date)} (${pts[0].value} ${props.unit}) to ${fmtDate(pts[pts.length - 1].date)} (${pts[pts.length - 1].value} ${props.unit})`;
});

function onMove(event) {
    const rect = root.value.getBoundingClientRect();
    const x = event.clientX - rect.left;
    let best = 0;
    coords.value.forEach((c, i) => {
        if (Math.abs(c.x - x) < Math.abs(coords.value[best].x - x)) best = i;
    });
    hover.value = best;
}

onMounted(() => {
    if (typeof ResizeObserver === 'undefined') return;
    observer = new ResizeObserver(entries => {
        width.value = entries[0].contentRect.width;
    });
    observer.observe(root.value);
});

onBeforeUnmount(() => observer?.disconnect());
</script>

<template>
    <div ref="root" class="relative w-full select-none" :style="{ height: `${height}px` }">
        <p v-if="points.length < 2" class="absolute inset-0 flex items-center justify-center text-center text-sm text-warm-grey px-6">
            Log at least two check-ins to see your trend here.
        </p>
        <template v-else>
            <svg :width="width" :height="height" role="img" :aria-label="summary" class="block touch-pan-y" @pointermove="onMove" @pointerdown="onMove" @pointerleave="hover = null">
                <defs>
                    <linearGradient id="weight-area" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#f2b90c" stop-opacity="0.22" />
                        <stop offset="100%" stop-color="#f2b90c" stop-opacity="0" />
                    </linearGradient>
                </defs>

                <g v-for="tick in scale.ticks" :key="tick">
                    <line :x1="pad.left" :x2="width - pad.right" :y1="scale.y(tick)" :y2="scale.y(tick)" stroke="#ffffff" stroke-opacity="0.06" />
                    <text :x="pad.left - 10" :y="scale.y(tick) + 4" text-anchor="end" font-size="11" fill="#8a8a8a">{{ tick }}</text>
                </g>

                <text :x="pad.left" :y="height - 6" font-size="11" fill="#8a8a8a">{{ fmtShortDate(data[0].date) }}</text>
                <text :x="width - pad.right" :y="height - 6" text-anchor="end" font-size="11" fill="#8a8a8a">{{ fmtShortDate(data[data.length - 1].date) }}</text>

                <path :d="areaPath" fill="url(#weight-area)" />
                <path :d="linePath" fill="none" stroke="#f2b90c" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />

                <template v-if="active">
                    <line :x1="active.x" :x2="active.x" :y1="pad.top" :y2="scale.baseline" stroke="#ffffff" stroke-opacity="0.25" />
                    <circle :cx="active.x" :cy="active.y" r="5" fill="#f2b90c" stroke="#171717" stroke-width="2" />
                </template>
                <circle v-else :cx="last.x" :cy="last.y" r="4.5" fill="#f2b90c" stroke="#171717" stroke-width="2" />
            </svg>

            <div
                v-if="active"
                class="pointer-events-none absolute z-10 px-3 py-2 rounded-lg bg-dark border border-white/10 shadow-xl text-xs whitespace-nowrap -translate-y-full"
                :class="active.x > width / 2 ? '-translate-x-full' : ''"
                :style="{ left: `${active.x + (active.x > width / 2 ? -10 : 10)}px`, top: `${Math.max(active.y - 8, 44)}px` }"
            >
                <p class="text-off-white font-semibold text-sm">{{ active.value }} {{ unit }}</p>
                <p class="text-warm-grey">{{ fmtDate(active.date) }}</p>
            </div>
        </template>
    </div>
</template>
