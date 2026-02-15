@props(['icon', 'message'])

<div class="text-center py-8 text-gray-500">
    <i class="{{ $icon }} text-3xl mb-3 opacity-30"></i>
    <p class="text-sm font-medium opacity-60">{{ $message }}</p>
</div>