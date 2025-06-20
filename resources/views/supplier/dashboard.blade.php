<x-layouts.app>
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Supplier Dashboard</h1>
        <p>Welcome, {{ auth()->user()->name }}! You are logged in as a Supplier.</p><br><br>
        <!-- Add supplier-specific content here -->
         <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 flex items-center justify-center">
    <!--<div class="relative grid grid-cols-1 gap-4 max-w-md p-6 rounded-xl overflow-hidden bg-gradient-to-br from-green-600 via-green-300 to-yellow-100">-->
    <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
    <h1 class="relative m-0 whitespace-nowrap text-base text-white text-center">
        <!-- <img src="../assets/img/logo-ct.png" class="inline h-full max-w-full transition-all duration-200 ease-nav-brand max-h-8" alt="main_logo" /> -->
        <span class="ml-1 font-bold transition-all duration-200 ease-nav-brand">Today's stocks</span>
    </h1>
</div>
<!--second small grid-->
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <!--third small grid-->
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <!--the last bigger grid with graphs and more details-->
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
    </div>
</x-layouts.app>
