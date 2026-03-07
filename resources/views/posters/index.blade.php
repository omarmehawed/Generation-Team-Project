@extends('layouts.batu')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-3xl font-bold font-tech text-[var(--primary)] uppercase tracking-wider">Manage Posters</h1>
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" target="_blank"
                    class="bg-[var(--bg-panel)] border border-[var(--border)] hover:bg-gray-100 dark:hover:bg-gray-800 text-[var(--text-main)] px-4 py-2 rounded-lg font-bold shadow-sm transition-all flex items-center gap-2">
                    <i class="fas fa-external-link-alt text-blue-500"></i> View Landing Page
                </a>
                <a href="{{ route('posters.create') }}"
                    class="bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-6 py-2 rounded-lg font-bold shadow-md transition-all flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Poster
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p class="font-bold">Success</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="ui-card flex flex-col p-6">
            <div class="mb-4 text-sm text-[var(--text-muted)] flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                Drag and drop rows to reorder how posters appear on the homepage.
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="border-b border-[var(--border)] text-[var(--text-muted)] text-sm uppercase tracking-wider">
                            <th class="py-3 px-4 w-12 text-center"><i class="fas fa-sort"></i></th>
                            <th class="py-3 px-4">Image</th>
                            <th class="py-3 px-4">Title</th>
                            <th class="py-3 px-4">Description</th>
                            <th class="py-3 px-4 text-center">Settings</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-posters" class="divide-y divide-[var(--border)]">
                        @forelse($posters as $poster)
                            <tr data-id="{{ $poster->id }}"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition duration-150 cursor-move">
                                <td
                                    class="py-4 px-4 text-center text-gray-400 drag-handle cursor-move hover:text-gray-600 transition-colors">
                                    <i class="fas fa-grip-lines text-xl"></i>
                                </td>
                                <td class="py-4 px-4 w-32">
                                    <img src="{{ asset('storage/' . $poster->image_path) }}"
                                        class="w-24 h-16 object-cover rounded-md shadow-sm border border-[var(--border)]"
                                        alt="Poster image">
                                </td>
                                <td class="py-4 px-4 font-bold text-[var(--text-main)]">
                                    {{ $poster->title }}
                                </td>
                                <td class="py-4 px-4 text-[var(--text-muted)] text-sm max-w-xs truncate">
                                    {{ $poster->description ?: 'No description' }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex flex-col items-center gap-1 text-xs">
                                        <span
                                            class="bg-gray-100 dark:bg-gray-700 text-[var(--text-main)] px-2 py-1 rounded">Position:
                                            {{ ucfirst($poster->text_position) }}</span>
                                        <div class="flex items-center gap-1 mt-1">
                                            <span>Color:</span>
                                            <span class="w-4 h-4 rounded-full border border-gray-300 shadow-sm inline-block"
                                                style="background-color: {{ $poster->text_color }}"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('posters.edit_layout', $poster->id) }}"
                                            class="text-indigo-500 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 p-2 rounded transition-colors"
                                            title="Edit Layout">
                                            <i class="fas fa-layer-group"></i>
                                        </a>
                                        <a href="{{ route('posters.edit', $poster->id) }}"
                                            class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 p-2 rounded transition-colors"
                                            title="Edit Details">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('posters.destroy', $poster->id) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this poster?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/50 p-2 rounded transition-colors"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-[var(--text-muted)]">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-image text-4xl mb-3 opacity-50"></i>
                                        <p class="text-lg">No posters added yet.</p>
                                        <p class="text-sm mt-1">Click "Add Poster" to create your first one.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('sortable-posters');
            if (el && el.children.length > 0) {
                Sortable.create(el, {
                    animation: 150,
                    ghostClass: 'bg-yellow-50',
                    dragClass: 'shadow-lg',
                    handle: '.drag-handle',
                    delay: 200,
                    delayOnTouchOnly: true,
                    onEnd: function () {
                        let order = [];
                        document.querySelectorAll('#sortable-posters tr').forEach(function (row) {
                            order.push(row.getAttribute('data-id'));
                        });

                        fetch('{{ route('posters.update_order') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ order: order })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Toast.fire({
                                        icon: 'success',
                                        title: 'Order updated successfully'
                                    });
                                }
                            })
                            .catch(error => console.error('Error updating order:', error));
                    }
                });
            }
        });
    </script>
@endsection