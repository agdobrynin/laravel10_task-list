@props([
    'message'
])
<div id="flashMessage" class="relative mb-4 p-4 rounded-md border border-green-400 bg-green-100 text-green-700 text-lg">
    <p>{{ $message }}</p>
    <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="document.querySelector('#flashMessage').remove()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="h-6 w-6 cursor-pointer">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </span>
</div>
