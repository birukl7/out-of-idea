<x-guest-layout>
    <div class="p-5 flex flex-col justify-center dark:text-white max-width px-6 py-4 bg-white dark:bg-black shadow-md overflow-hidden sm:rounded-lg max-w-[600px] py-10">
        <h1 class="md:text-4xl sm:text-3xl text-2xl text-center">Don't have any <span class="">idea</span> to
            do something?</h1>
        <a wire:navigate href="{{ route('no-idea')}}"
            class="dark:bg-white dark:text-black bg-black text-white self-center p-2 px-3 mt-5 rounded-lg hover:bg-slate-700 dark:hover:bg-slate-200">Yes</a>
    </div>
</x-guest-layout>