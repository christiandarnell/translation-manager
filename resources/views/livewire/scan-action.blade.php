<div class="ml-auto flex items-center gap-3">
    @if (session()->has('scan.completed'))
        <p class="text-slate-800 dark:text-slate-200 text-sm italic">{{ session('scan.completed') }}</p>
    @endif

    <button
        class="px-4 py-2 uppercase text-teal-50 dark:text-teal-900 inline-flex gap-3 items-center bg-teal-500 shadow-md rounded-full font-semibold text-sm tracking-wider transition duration-300 hover:bg-teal-600 group"
        wire:click.prevent="scan"
    >
        <i class="fa-solid fa-rotate transition duration-300 text-teal-700 group-hover:text-teal-800"></i>
        Scan Files
    </button>
</div>
