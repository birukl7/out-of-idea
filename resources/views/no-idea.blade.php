<x-guest-layout>
    <a wire:navigate href="{{url()->previous()}}"
  class="dark:bg-white dark:text-black bg-black text-white self-center p-2 px-3 mt-5 rounded-lg hover:bg-slate-700 dark:hover:bg-slate-200 ">Back</a>
  
  <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8 dark:text-white max-width px-6 dark:outline dark:outline-1 bg-white dark:bg-black shadow-md overflow-hidden sm:rounded-lg mt-2   py-10">
    <livewire:no-idea.index>
  </div>


</x-guest-layout>