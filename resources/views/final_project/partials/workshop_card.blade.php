@props(['workshop', 'myRole'])

<div class="relative group transition-all duration-300">
    <div class="p-6 rounded-3xl border border-gray-100 bg-white shadow-sm hover:shadow-xl transition-all duration-500 relative overflow-hidden flex flex-col h-full">
        
        {{-- Decorative Top Bar --}}
        <div class="absolute top-0 left-0 right-0 h-1.5 {{ $workshop->type == 'online' ? 'bg-indigo-500' : 'bg-amber-500' }}"></div>

        <div class="flex justify-between items-start mb-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-[10px] uppercase tracking-widest font-black px-2.5 py-1 rounded-lg {{ $workshop->domain == 'software' ? 'bg-blue-50 text-blue-600' : ($workshop->domain == 'hardware' ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-600') }}">
                        {{ $workshop->domain }} Team
                    </span>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg {{ $workshop->type == 'online' ? 'bg-indigo-50 text-indigo-600' : 'bg-amber-50 text-amber-600' }}">
                        <i class="fas fa-{{ $workshop->type == 'online' ? 'globe' : 'map-marker-alt' }} mr-1"></i> {{ ucfirst($workshop->type) }}
                    </span>
                </div>
                <h4 class="text-lg font-black text-gray-800 leading-tight group-hover:text-indigo-600 transition-colors">{{ $workshop->title }}</h4>
            </div>

            @if(in_array($myRole, ['leader', 'vice_leader']))
                <form action="{{ route('workshops.destroy', $workshop->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this workshop?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all border border-transparent hover:border-red-100">
                        <i class="fas fa-trash-alt text-sm"></i>
                    </button>
                </form>
            @endif
        </div>

        <div class="space-y-3 mb-6">
            <div class="flex items-center gap-3 text-gray-500">
                <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-indigo-500 border border-gray-100">
                    <i class="far fa-calendar-check text-sm"></i>
                </div>
                <span class="text-xs font-bold">{{ \Carbon\Carbon::parse($workshop->workshop_date)->format('l, d F Y') }}</span>
            </div>
            
            <div class="flex items-center gap-3 text-gray-500">
                <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-indigo-500 border border-gray-100">
                    <i class="far fa-clock text-sm"></i>
                </div>
                <span class="text-xs font-bold">{{ \Carbon\Carbon::parse($workshop->workshop_time)->format('h:i A') }}</span>
            </div>

            @if($workshop->location_or_link)
                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 mt-2">
                    <div class="flex items-center gap-2 text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">
                        <i class="fas fa-info-circle text-indigo-400"></i> {{ $workshop->type == 'online' ? 'Access Link' : 'Location Details' }}
                    </div>
                    @if(filter_var($workshop->location_or_link, FILTER_VALIDATE_URL))
                        <a href="{{ $workshop->location_or_link }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-2 truncate">
                            {{ $workshop->location_or_link }} <i class="fas fa-external-link-alt text-[10px]"></i>
                        </a>
                    @else
                        <p class="text-xs font-bold text-gray-700 truncate">{{ $workshop->location_or_link }}</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="mt-auto pt-4 border-t border-gray-50">
            @if(in_array($myRole, ['leader', 'vice_leader']) || (isset($myMemberRecord) && $myMemberRecord->is_sub_leader))
                <button onclick="openWorkshopAttendanceModal({{ $workshop->id }}, '{{ addslashes($workshop->title) }}')" 
                    class="w-full bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white border border-indigo-100 px-4 py-3 rounded-2xl text-xs font-black transition-all flex items-center justify-center gap-2 shadow-sm group/btn">
                    <i class="fas fa-user-check group-hover/btn:scale-110 transition-transform"></i>
                    MANAGE ATTENDANCE & SCORES
                </button>
            @else
                <div class="text-center py-2 px-4 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Awaiting Evaluation</span>
                </div>
            @endif
        </div>
    </div>
</div>
