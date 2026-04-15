<script setup lang="ts">
</script>

<template>
    <div class="relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top,_rgba(244,114,182,0.16),_transparent_30%),linear-gradient(180deg,_#fff8fb_0%,_#fffdfd_45%,_#fdf2f8_100%)] text-slate-800">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-24 top-10 h-72 w-72 rounded-full bg-rose-300/20 blur-3xl"></div>
            <div class="absolute right-0 top-20 h-80 w-80 rounded-full bg-fuchsia-300/20 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-pink-200/20 blur-3xl"></div>
        </div>

        <div class="relative z-10">
        <slot />
        </div>
    </div>
</template>
