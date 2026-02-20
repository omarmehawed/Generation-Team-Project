@props(['assets', 'color'])

@php
    // Map colors to categories
    $categories = [
        'tasks' => [
            'label' => 'Tasks',
            'icon' => 'fas fa-tasks',
            'bg' => 'bg-indigo-500/10',
            'text' => 'text-indigo-400',
            'count' => count($assets['tasks'])
        ],
        'reports' => [
            'label' => 'Reports',
            'icon' => 'fas fa-file-contract',
            'bg' => 'bg-emerald-500/10',
            'text' => 'text-emerald-400',
            'count' => count($assets['reports'])
        ],
        'meetings' => [
            'label' => 'Meetings',
            'icon' => 'fas fa-calendar-check',
            'bg' => 'bg-amber-500/10',
            'text' => 'text-amber-400',
            'count' => count($assets['meetings'])
        ],
        'gallery' => [
            'label' => 'Gallery',
            'icon' => 'fas fa-images',
            'bg' => 'bg-rose-500/10',
            'text' => 'text-rose-400',
            'count' => count($assets['gallery'])
        ],
        'docs' => [
            'label' => 'Docs',
            'icon' => 'fas fa-folder-open',
            'bg' => 'bg-cyan-500/10',
            'text' => 'text-cyan-400',
            'count' => count($assets['docs'])
        ],
        'timeline' => [
            'label' => 'Timeline',
            'icon' => 'fas fa-history',
            'bg' => 'bg-fuchsia-500/10',
            'text' => 'text-fuchsia-400',
            'count' => count($assets['activities'])
        ]
    ];
@endphp

<div x-data="{ activeTab: 'timeline' }" class="p-6">
    
    <!-- 1. Categories Grid -->
    <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-6">
        @foreach($categories as $key => $cat)
            @if($cat['count'] > 0 || $key == 'tasks') {{-- Always show tasks even if empty, hide others if empty? Or show all? Let's show all for consistency but dim empty ones --}}
            <button @click="activeTab = '{{ $key }}'"
                class="flex flex-col items-center justify-center p-4 rounded-xl transition-all duration-300 border"
                :class="activeTab === '{{ $key }}' 
                    ? 'bg-gray-800 border-{{$color}}-500/50 shadow-[0_0_15px_rgba(0,0,0,0.3)] scale-105' 
                    : 'bg-gray-800/50 border-transparent hover:bg-gray-800 hover:border-gray-700 opacity-70 hover:opacity-100'">
                
                <div class="w-10 h-10 rounded-full {{ $cat['bg'] }} flex items-center justify-center mb-2">
                    <i class="{{ $cat['icon'] }} {{ $cat['text'] }} text-lg"></i>
                </div>
                <span class="text-xs font-bold text-gray-300 uppercase tracking-wide">{{ $cat['label'] }}</span>
                <span class="text-[10px] text-gray-500 mt-1">{{ $cat['count'] }} Items</span>
            </button>
            @endif
        @endforeach
    </div>

    <!-- 2. Content Area -->
    <div class="min-h-[200px]">
        
        <!-- Tasks Tab -->
        <div x-show="activeTab === 'tasks'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @if(count($assets['tasks']) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($assets['tasks'] as $task)
                        @include('profile.partials.task-card', ['task' => $task, 'color' => $color])
                    @endforeach
                </div>
            @else
                <x-empty-state icon="fas fa-tasks" message="No tasks assigned yet." />
            @endif
        </div>

        <!-- Reports Tab -->
        <div x-show="activeTab === 'reports'" style="display: none;" x-transition>
            @if(count($assets['reports']) > 0)
                <div class="space-y-3">
                    @foreach($assets['reports'] as $report)
                        <div class="bg-gray-800 p-4 rounded-xl border border-gray-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                                    <span class="font-bold text-sm">W{{ $report->week_number }}</span>
                                </div>
                                <div>
                                    <h4 class="text-white font-bold text-sm">Week {{ $report->week_number }} Report</h4>
                                    <p class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}</p>
                                </div>
                            </div>
                            @if($report->file_path)
                                <a href="{{ Str::startsWith($report->file_path, ['http://', 'https://']) ? $report->file_path : asset('storage/' . $report->file_path) }}" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                    <i class="fas fa-download"></i>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                 <x-empty-state icon="fas fa-file-contract" message="No reports submitted." />
            @endif
        </div>

        <!-- Meetings Tab -->
        <div x-show="activeTab === 'meetings'" style="display: none;" x-transition>
            @if(count($assets['meetings']) > 0)
                <div class="space-y-3">
                    @foreach($assets['meetings'] as $meeting)
                         <div class="bg-gray-800 p-4 rounded-xl border border-gray-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-400">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div>
                                    <h4 class="text-white font-bold text-sm">{{ $meeting->topic }}</h4>
                                    <p class="text-gray-500 text-xs">
                                        {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y h:i A') }} • {{ ucfirst($meeting->mode) }}
                                    </p>
                                </div>
                            </div>
                            <span class="px-2 py-1 rounded text-[10px] font-bold {{ $meeting->status == 'completed' ? 'bg-green-500/10 text-green-400' : 'bg-yellow-500/10 text-yellow-400' }}">
                                {{ strtoupper($meeting->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                 <x-empty-state icon="fas fa-calendar-times" message="No meetings recorded." />
            @endif
        </div>

        <!-- Gallery Tab -->
        <div x-show="activeTab === 'gallery'" style="display: none;" x-transition>
             @if(count($assets['gallery']) > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($assets['gallery'] as $item)
                        <div class="group relative rounded-xl overflow-hidden aspect-square bg-gray-800 border border-gray-700">
                            @if($item->type == 'image')
                                <img src="{{ Str::startsWith($item->file_path, ['http://', 'https://']) ? $item->file_path : asset('storage/' . $item->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-900">
                                    <i class="fas fa-video text-3xl text-gray-600"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-3">
                                <p class="text-white text-xs font-bold truncate">{{ $item->caption }}</p>
                                <p class="text-gray-400 text-[10px]">{{ $item->category }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                 <x-empty-state icon="fas fa-images" message="No media uploaded." />
            @endif
        </div>

        <!-- Docs Tab -->
        <div x-show="activeTab === 'docs'" style="display: none;" x-transition>
             @if(count($assets['docs']) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($assets['docs'] as $doc)
                        <a href="{{ Str::startsWith($doc['path'], ['http://', 'https://']) ? $doc['path'] : asset('storage/' . $doc['path']) }}" target="_blank" 
                           class="bg-gray-800 p-4 rounded-xl border border-gray-700 flex items-center gap-4 hover:bg-gray-750 transition-colors group">
                            <div class="w-12 h-12 rounded-lg bg-cyan-500/20 flex items-center justify-center text-cyan-400 text-xl group-hover:scale-110 transition-transform">
                                <i class="{{ $doc['icon'] }}"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-bold">{{ $doc['type'] }}</h4>
                                <p class="text-gray-500 text-xs truncate max-w-[150px]">{{ $doc['name'] }}</p>
                            </div>
                            <div class="ml-auto">
                                <i class="fas fa-download text-gray-500 group-hover:text-cyan-400 transition-colors"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                 <x-empty-state icon="fas fa-folder-open" message="No documents found." />
            @endif
        </div>

        <!-- Timeline Tab -->
        <div x-show="activeTab === 'timeline'" x-transition>
            @if(count($assets['activities']) > 0)
                <div class="space-y-0 relative">
                     {{-- Line --}}
                    <div class="absolute left-[1.65rem] top-4 bottom-4 w-0.5 bg-gray-800"></div>
                    
                    @foreach($assets['activities'] as $activity)
                        @include('profile.partials.activity-card', ['activity' => $activity])
                    @endforeach
                </div>
            @else
                 <x-empty-state icon="fas fa-history" message="No recent activity in this project." />
            @endif
        </div>

    </div>
</div>
