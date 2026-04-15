<template>
    <button
        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-transparent bg-gradient-to-r from-rose-600 to-red-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-600/20 transition duration-200 ease-out hover:-translate-y-px hover:shadow-xl hover:shadow-rose-600/25 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2 focus:ring-offset-white active:translate-y-0 disabled:cursor-not-allowed disabled:opacity-60"
    >
        <slot />
    </button>
</template>
