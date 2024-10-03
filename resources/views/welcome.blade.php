<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
    class="h-full"
    x-data="{ darkMode: localStorage.getItem('dark') === 'true'}"
    x-init="$watch('darkMode', val => localStorage.setItem('dark', val))"
    x-bind:class="{ 'dark': darkMode }"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="antialiased font-sans h-full relative">
        <header class="absolute right-0">
            <button 
                class=""
                @click= "darkMode = !darkMode"
            >
                <!-- Light Mode Icon -->
                <svg x-show="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                </svg>
                <!-- Dark Mode Icon -->
                <svg x-show="darkMode" class=" text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                </svg>
            </button>
        </header>
        <div class="dark:bg-black bg-white text-black dark:text-white h-screen flex justify-center items-center">
            <div class="p-5 flex flex-col justify-center">
                <h1 class="md:text-5xl sm:text-5xl text-4xl text-center">Don't have any <span class="">idea</span> to do something?</h1> 
                <button class="dark:bg-white dark:text-black bg-black text-white self-center p-2 px-3 mt-5 rounded-lg hover:bg-slate-700 dark:hover:bg-slate-200">Yes</button>
            </div>
        </div>
    </body>
</html>
