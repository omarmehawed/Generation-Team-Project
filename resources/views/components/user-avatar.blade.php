@props(['user', 'size' => 'w-10 h-10', 'classes' => ''])

<div class="relative inline-block">
    <div class="{{ $size }} rounded-full overflow-hidden border border-gray-700 shadow-sm {{ $classes }}">
        @if($user && $user->profile_photo_path)
            <img src="{{ $user->profile_photo_path }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
        @else
            <img src="https://ui-avatars.com/api/?name={{ urlencode(optional($user)->name ?? 'User') }}&background=000&color=00f3ff&bold=true&size=128"
                alt="{{ optional($user)->name ?? 'User' }}" class="w-full h-full object-cover">
        @endif
    </div>

    {{-- Online Status or other indicators could go here if needed --}}
</div>