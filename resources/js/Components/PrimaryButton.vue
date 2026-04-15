<template>
    <button
        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-transparent bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-500/20 transition duration-200 ease-out hover:translate-y-[-1px] hover:shadow-xl hover:shadow-rose-500/25 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2 focus:ring-offset-white active:translate-y-0 active:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
    >
        <slot />
    </button>
</template>
