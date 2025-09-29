<template>
    <button
        class="inline-flex items-center rounded-md border border-transparent 
               bg-blue-600 text-white 
               px-4 py-2 text-xs font-semibold uppercase tracking-widest
               transition duration-150 ease-in-out
               hover:bg-blue-500 focus:bg-blue-700 active:bg-blue-800
               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2

               dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:active:bg-gray-800
               dark:text-gray-100"
    >
        <slot />
    </button>
</template>
