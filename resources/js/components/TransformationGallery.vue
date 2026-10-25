<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

// Client before/after collages as an endless, slowly drifting row of cards. The row eases to a
// stop on hover, focus or pause, can be dragged/swiped (with momentum) or stepped with the
// buttons, and a tap opens the card full screen. Portrait collages are 9:16 cards; the landscape
// one is wider. Faces are blurred in the files themselves, so the originals never ship.
// Files: public/images/transformations/transformation-N-{480,960}.webp
const props = defineProps({
    photos: { type: Array, required: true }, // [{ src: '/images/…/transformation-1', width, height }]
});

const SPEED = 36;    // drift, px per second
const FRICTION = 4;  // how quickly a fling or step glides to rest, per second
const COPIES = 3;    // repeated sets so the loop never shows a gap, even on ultra-wide screens

const reduceMotion = typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const viewport = ref(null);
const track = ref(null);
const paused = ref(reduceMotion);
const near = ref(false); // the row is close to the viewport, so load the photos
const cards = computed(() => Array.from({ length: COPIES }, (_, copy) => props.photos.map((photo, i) => ({ photo, i, clone: copy > 0 }))).flat());

const alt = i => `Client transformation ${i + 1} of ${props.photos.length}, before and after`;
const sizes = photo => (photo.width > photo.height
    ? '(min-width: 1024px) 496px, (min-width: 640px) 425px, 354px'
    : '(min-width: 1024px) 252px, (min-width: 640px) 216px, 180px');

let offset = 0;   // px the row has travelled
let drift = 0;    // current auto speed, eased toward SPEED or 0
let glide = 0;    // extra velocity from a fling, a step or keyboard focus
let setWidth = 0; // width of one set of cards, trailing gap included
let hovering = false;
let focused = false;
let drag = null;
let suppressClick = false;
let frame = null;
let last = 0;
let resizeObserver = null;
let visibilityObserver = null;

function render() {
    if (!setWidth || !track.value) return;
    const x = ((offset % setWidth) + setWidth) % setWidth;
    track.value.style.transform = `translate3d(${-x}px, 0, 0)`;
}

function tick(now) {
    // A frame's timestamp can predate the performance.now() taken in start().
    const dt = Math.max(0, Math.min((now - last) / 1000, 0.05));
    last = now;
    const still = paused.value || hovering || focused || drag || open.value !== null;
    drift += ((still ? 0 : SPEED) - drift) * Math.min(1, dt * 3);
    if (!drag) {
        offset += (drift + glide) * dt;
        glide *= Math.exp(-FRICTION * dt);
        if (Math.abs(glide) < 1) glide = 0;
    }
    render();
    frame = requestAnimationFrame(tick);
}

function start() {
    if (frame !== null) return;
    last = performance.now();
    frame = requestAnimationFrame(tick);
}

function stop() {
    if (frame !== null) cancelAnimationFrame(frame);
    frame = null;
}

function measure() {
    const first = track.value?.children[0];
    const nextSet = track.value?.children[props.photos.length];
    setWidth = first && nextSet ? nextSet.offsetLeft - first.offsetLeft : 0;
    render();
}

// Move the row by `distance` px: a glide that comes to rest exactly there, or a jump with reduced motion.
function nudge(distance) {
    if (reduceMotion) {
        offset += distance;
        render();
    } else {
        glide += distance * FRICTION;
    }
}

const shift = dir => nudge(dir * (setWidth / props.photos.length));

function onPointerDown(event) {
    if (event.pointerType === 'mouse' && event.button !== 0) return;
    drag = { id: event.pointerId, x: event.clientX, offset, lastX: event.clientX, lastT: event.timeStamp, velocity: 0, moved: false };
    glide = 0;
    suppressClick = false;
}

function onPointerMove(event) {
    if (!drag || event.pointerId !== drag.id) return;
    const dx = event.clientX - drag.x;
    if (!drag.moved) {
        if (Math.abs(dx) < 6) return; // still a tap
        drag.moved = true;
        track.value.setPointerCapture(event.pointerId);
    }
    const dt = (event.timeStamp - drag.lastT) / 1000;
    if (dt > 0) drag.velocity = drag.velocity * 0.2 + ((drag.lastX - event.clientX) / dt) * 0.8;
    drag.lastX = event.clientX;
    drag.lastT = event.timeStamp;
    offset = drag.offset - dx;
    render();
}

function onPointerUp(event) {
    if (!drag || event.pointerId !== drag.id) return;
    // Fling only if the finger was still moving when it let go.
    if (drag.moved && !reduceMotion && event.timeStamp - drag.lastT < 100) {
        glide = Math.max(-2500, Math.min(2500, drag.velocity));
    }
    suppressClick = drag.moved;
    drag = null;
}

const onPointerCancel = () => { drag = null; };

// A drag that ends over a card must not also open it.
function onClickCapture(event) {
    if (!suppressClick) return;
    event.preventDefault();
    event.stopPropagation();
    suppressClick = false;
}

// Trackpad side-swipes move the row; vertical scrolling passes through to the page.
function onWheel(event) {
    if (Math.abs(event.deltaX) <= Math.abs(event.deltaY)) return;
    event.preventDefault();
    offset += event.deltaX;
    glide = 0;
}

const onPointerEnter = event => { hovering = event.pointerType === 'mouse'; };
const onPointerLeave = () => { hovering = false; };

// Keyboard focus holds the row still and glides the focused card clear of the faded edges.
function onFocusIn(event) {
    focused = event.target.matches(':focus-visible');
    if (!focused) return;
    const view = viewport.value.getBoundingClientRect();
    const card = event.target.getBoundingClientRect();
    const pad = view.width * 0.08;
    if (card.left < view.left + pad) nudge(card.left - view.left - pad);
    else if (card.right > view.right - pad) nudge(card.right - view.right + pad);
}

const onFocusOut = () => { focused = false; };

// Positioning is all transform; undo any scroll the browser does to reveal a focused card.
const onScroll = () => { viewport.value.scrollLeft = 0; };

onMounted(() => {
    measure();
    resizeObserver = new ResizeObserver(measure);
    resizeObserver.observe(track.value);

    if (typeof IntersectionObserver === 'undefined') {
        near.value = true;
        start();
        return;
    }
    // Only animate (and load the photos) while the row is on or near the screen.
    visibilityObserver = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting) {
            near.value = true;
            start();
        } else {
            stop();
        }
    }, { rootMargin: '300px 0px' });
    visibilityObserver.observe(viewport.value);
});

// Full-screen viewer.
const open = ref(null); // index of the photo shown full screen
const closeButton = ref(null);
let opener = null;
let touchX = null;

const current = computed(() => (open.value === null ? null : props.photos[open.value]));

function show(i, event) {
    opener = event?.currentTarget ?? null;
    open.value = i;
}

function close() {
    open.value = null;
    opener?.focus({ preventScroll: true });
}

const step = by => { open.value = (open.value + by + props.photos.length) % props.photos.length; };

function onKey(event) {
    if (event.key === 'Escape') close();
    else if (event.key === 'ArrowRight') step(1);
    else if (event.key === 'ArrowLeft') step(-1);
}

const onTouchStart = event => { touchX = event.touches[0].clientX; };
function onTouchEnd(event) {
    if (touchX === null) return;
    const dx = event.changedTouches[0].clientX - touchX;
    if (Math.abs(dx) > 50) step(dx < 0 ? 1 : -1);
    touchX = null;
}

// Freeze the page behind the viewer while it's open.
watch(() => open.value !== null, isOpen => {
    document.documentElement.style.overflow = isOpen ? 'hidden' : '';
    if (isOpen) {
        window.addEventListener('keydown', onKey);
        nextTick(() => closeButton.value?.focus());
    } else {
        window.removeEventListener('keydown', onKey);
    }
});

onBeforeUnmount(() => {
    stop();
    resizeObserver?.disconnect();
    visibilityObserver?.disconnect();
    window.removeEventListener('keydown', onKey);
    document.documentElement.style.overflow = '';
});
</script>

<template>
    <div
        ref="viewport"
        class="overflow-hidden select-none py-1 [mask-image:linear-gradient(to_right,transparent,black_7%,black_93%,transparent)]"
        role="region"
        aria-roledescription="carousel"
        aria-label="Client transformations"
        @scroll="onScroll"
        @pointerenter="onPointerEnter"
        @pointerleave="onPointerLeave"
        @focusin="onFocusIn"
        @focusout="onFocusOut"
        @wheel="onWheel"
    >
        <div
            ref="track"
            class="flex w-max h-80 sm:h-96 lg:h-[28rem] gap-3 sm:gap-4 cursor-grab active:cursor-grabbing touch-pan-y will-change-transform"
            @pointerdown="onPointerDown"
            @pointermove="onPointerMove"
            @pointerup="onPointerUp"
            @pointercancel="onPointerCancel"
            @click.capture="onClickCapture"
        >
            <button
                v-for="(card, k) in cards"
                :key="k"
                type="button"
                class="group relative h-full shrink-0 overflow-hidden rounded-2xl border border-emerald-500/10 bg-dark-grey hover:border-emerald-500/40 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-emerald-400"
                :style="{ aspectRatio: `${card.photo.width} / ${card.photo.height}` }"
                :tabindex="card.clone ? -1 : undefined"
                :aria-hidden="card.clone ? 'true' : undefined"
                :aria-label="`View ${alt(card.i).toLowerCase()} full screen`"
                @click="show(card.i, $event)"
            >
                <img
                    :src="`${card.photo.src}-480.webp`"
                    :srcset="`${card.photo.src}-480.webp 480w, ${card.photo.src}-960.webp 960w`"
                    :sizes="sizes(card.photo)"
                    :width="card.photo.width"
                    :height="card.photo.height"
                    :alt="card.clone ? '' : alt(card.i)"
                    :loading="near ? 'eager' : 'lazy'"
                    decoding="async"
                    draggable="false"
                    class="pointer-events-none absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.03] motion-reduce:transition-none motion-reduce:group-hover:scale-100"
                >
                <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-dark/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></span>
                <span class="pointer-events-none absolute bottom-3 right-3 w-9 h-9 rounded-full bg-emerald-500 text-black flex items-center justify-center opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" /></svg>
                </span>
            </button>
        </div>
    </div>

    <div class="mt-8 flex items-center justify-center gap-3">
        <button type="button" class="w-11 h-11 rounded-full border border-emerald-500/20 text-off-white flex items-center justify-center hover:border-emerald-400 hover:text-emerald-400 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400" aria-label="Previous photos" @click="shift(-1)">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
        </button>
        <button type="button" class="w-11 h-11 rounded-full border border-emerald-500/20 text-off-white flex items-center justify-center hover:border-emerald-400 hover:text-emerald-400 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400" :aria-label="paused ? 'Play slideshow' : 'Pause slideshow'" @click="paused = !paused">
            <svg v-if="paused" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7 4.5v15a1 1 0 0 0 1.52.85l12-7.5a1 1 0 0 0 0-1.7l-12-7.5A1 1 0 0 0 7 4.5z" /></svg>
            <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="4.5" width="4" height="15" rx="1" /><rect x="14" y="4.5" width="4" height="15" rx="1" /></svg>
        </button>
        <button type="button" class="w-11 h-11 rounded-full border border-emerald-500/20 text-off-white flex items-center justify-center hover:border-emerald-400 hover:text-emerald-400 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400" aria-label="Next photos" @click="shift(1)">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
        </button>
    </div>

    <Teleport to="body">
        <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" enter-active-class="transition-opacity duration-200 motion-reduce:transition-none" leave-active-class="transition-opacity duration-150 motion-reduce:transition-none">
            <div
                v-if="current"
                class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-sm flex items-center justify-center p-4 sm:p-10"
                role="dialog"
                aria-modal="true"
                :aria-label="alt(open)"
                data-lenis-prevent
                @click.self="close"
                @touchstart.passive="onTouchStart"
                @touchend="onTouchEnd"
            >
                <img :src="`${current.src}-960.webp`" :alt="alt(open)" :width="current.width" :height="current.height" class="max-h-full max-w-full w-auto h-auto object-contain rounded-xl select-none" draggable="false">

                <p class="absolute top-5 left-5 text-sm text-warm-grey tabular-nums">{{ open + 1 }} / {{ photos.length }}</p>
                <button ref="closeButton" type="button" class="absolute top-3 right-3 w-11 h-11 rounded-full bg-white/10 text-off-white flex items-center justify-center hover:bg-white/20 transition-colors" aria-label="Close" @click="close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <button type="button" class="absolute left-2 sm:left-5 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 text-off-white flex items-center justify-center hover:bg-white/20 transition-colors" aria-label="Previous photo" @click="step(-1)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                </button>
                <button type="button" class="absolute right-2 sm:right-5 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 text-off-white flex items-center justify-center hover:bg-white/20 transition-colors" aria-label="Next photo" @click="step(1)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                </button>
            </div>
        </Transition>
    </Teleport>
</template>
