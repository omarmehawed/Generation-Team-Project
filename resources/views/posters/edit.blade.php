@extends('layouts.batu')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="posterForm()">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('posters.index') }}"
                class="text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold font-tech text-[var(--primary)] uppercase tracking-wider">Edit Block</h1>
        </div>

        <div class="ui-card p-6 md:p-8">
            <form action="{{ route('posters.update', $poster->id) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Template Type -->
                <div>
                    <label for="template_type" class="block text-sm font-bold text-[var(--text-main)] mb-2">Block Template
                        <span class="text-red-500">*</span></label>
                    <select name="template_type" id="template_type" x-model="templateType" required
                        class="w-full bg-[var(--bg-main)] border border-[var(--border)] text-[var(--text-main)] rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] block p-3">
                        <option value="standard">Standard Poster (Image + Text)</option>
                        <option value="slider">Image Slider (Carousel + Text)</option>
                        <option value="profile_card">Profile Card (Image + Text + Social Links)</option>
                    </select>
                    @error('template_type') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Single Image Upload -->
                <div x-show="templateType !== 'slider'" x-transition>
                    <label for="image" class="block text-sm font-bold text-[var(--text-main)] mb-2">Image</label>
                    <div class="mt-1 flex flex-col sm:flex-row gap-6 items-start">
                        @if($poster->image_path)
                            <div class="shrink-0 relative group">
                                <img src="{{ asset('storage/' . $poster->image_path) }}" id="image-preview"
                                    class="h-32 w-48 object-cover rounded-md border border-[var(--border)] shadow-sm"
                                    alt="Current poster image" />
                                <div
                                    class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-md">
                                    <span class="text-white text-xs font-bold">Current Image</span>
                                </div>
                            </div>
                        @else
                            <img id="image-preview"
                                class="h-32 w-48 object-cover rounded-md border border-[var(--border)] shadow-sm hidden" />
                        @endif

                        <div class="flex-grow w-full">
                            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-[var(--border)] border-dashed rounded-lg hover:border-[var(--primary)] transition-colors h-32 items-center"
                                id="drop-zone">
                                <div class="space-y-1 text-center">
                                    <div class="flex text-sm text-[var(--text-muted)] justify-center">
                                        <label for="image"
                                            class="relative cursor-pointer bg-[var(--bg-panel)] rounded-md font-medium text-[var(--primary)] hover:text-[var(--primary-hover)] focus-within:outline-none">
                                            <span>Upload a new file</span>
                                            <input id="image" name="image" type="file" class="sr-only" accept="image/*"
                                                onchange="previewImage(event)">
                                        </label>
                                        <p class="pl-1">to replace</p>
                                    </div>
                                    <p class="text-[10px] text-[var(--text-muted)] mt-2">Leave empty to keep current.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('image') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Multiple Images Upload (Slider) -->
                <div x-show="templateType === 'slider'" x-transition style="display: none;">
                    <label for="slider_images" class="block text-sm font-bold text-[var(--text-main)] mb-2">Slider Images
                        (Multiple)</label>

                    @if(!empty($poster->images))
                        <div class="mb-4 flex flex-wrap gap-2 p-3 bg-[var(--bg-panel)] rounded border border-[var(--border)]">
                            <span class="w-full text-xs text-[var(--text-muted)] mb-2">Current Images (Uploading new will
                                replace all):</span>
                            @foreach($poster->images as $img)
                                <img src="{{ asset('storage/' . $img) }}"
                                    class="h-16 w-16 object-cover rounded border border-gray-300">
                            @endforeach
                        </div>
                    @endif

                    <div
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-[var(--border)] border-dashed rounded-lg hover:border-[var(--primary)] transition-colors">
                        <div class="space-y-1 text-center w-full">
                            <i class="fas fa-images text-3xl text-[var(--text-muted)] mb-3"></i>
                            <div class="flex text-sm text-[var(--text-muted)] justify-center mb-3">
                                <label for="slider_images"
                                    class="relative cursor-pointer bg-[var(--bg-panel)] rounded-md font-medium text-[var(--primary)] hover:text-[var(--primary-hover)] focus-within:outline-none">
                                    <span>Select multiple files to REPLACE current ones</span>
                                    <input id="slider_images" name="slider_images[]" type="file" class="sr-only"
                                        accept="image/*" multiple onchange="previewMultipleImages(event)">
                                </label>
                            </div>
                            <div id="multiple-preview" class="flex flex-wrap gap-2 justify-center mt-4"></div>
                        </div>
                    </div>
                    @error('slider_images') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    @error('slider_images.*') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-bold text-[var(--text-main)] mb-2">Title <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $poster->title) }}" required
                        class="w-full bg-[var(--bg-main)] border border-[var(--border)] text-[var(--text-main)] rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] block p-2.5">
                    @error('title') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-bold text-[var(--text-main)] mb-2">Description <span
                            class="text-gray-400 font-normal">(Optional)</span></label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full bg-[var(--bg-main)] border border-[var(--border)] text-[var(--text-main)] rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] block p-2.5">{{ old('description', $poster->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Links Repeater (Profile Card) -->
                <div x-show="templateType === 'profile_card'" x-transition style="display: none;"
                    class="p-4 bg-[var(--bg-main)] border border-[var(--border)] rounded-lg">
                    <label class="block text-sm font-bold text-[var(--text-main)] mb-4">Social / Action Links</label>
                    <input type="hidden" name="links" :value="JSON.stringify(links)">

                    <template x-for="(link, index) in links" :key="index">
                        <div
                            class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-3 bg-[var(--bg-panel)] p-3 rounded border border-[var(--border)]">
                            <input type="text" x-model="link.platform" placeholder="Platform (e.g. LinkedIn)"
                                class="w-full sm:flex-1 bg-[var(--bg-main)] border border-[var(--border)] text-[var(--text-main)] rounded p-2 text-sm">
                            <input type="text" x-model="link.icon" placeholder="Icon (e.g. fab fa-linkedin)"
                                class="w-full sm:flex-1 bg-[var(--bg-main)] border border-[var(--border)] text-[var(--text-main)] rounded p-2 text-sm">
                            <input type="text" x-model="link.url" placeholder="URL (https://...)"
                                class="w-full sm:flex-[2] bg-[var(--bg-main)] border border-[var(--border)] text-[var(--text-main)] rounded p-2 text-sm">
                            <button type="button" @click="removeLink(index)"
                                class="text-red-500 hover:text-red-700 p-2 w-full sm:w-auto text-left sm:text-center">
                                <i class="fas fa-trash"></i> <span class="sm:hidden ml-2 text-sm">Remove Link</span>
                            </button>
                        </div>
                    </template>

                    <button type="button" @click="addLink()"
                        class="text-sm bg-[var(--primary)]/10 text-[var(--primary)] hover:bg-[var(--primary)] hover:text-white px-4 py-2 rounded transition-colors font-bold mt-2">
                        + Add Link
                    </button>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Image Size -->
                    <div>
                        <label for="image_size" class="block text-sm font-bold text-[var(--text-main)] mb-2">Image Setup
                            Size
                            <span class="text-red-500">*</span></label>
                        <select name="image_size" id="image_size" required
                            class="w-full bg-[var(--bg-main)] border border-[var(--border)] text-[var(--text-main)] rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] block p-2.5">
                            <option value="small" {{ old('image_size', $poster->image_size) == 'small' ? 'selected' : '' }}>
                                Small (1/3 Width)</option>
                            <option value="medium" {{ old('image_size', $poster->image_size) == 'medium' ? 'selected' : '' }}>
                                Medium (1/2 Width)</option>
                            <option value="large" {{ old('image_size', $poster->image_size) == 'large' ? 'selected' : '' }}>
                                Large (3/4 Width)</option>
                            <option value="full" {{ old('image_size', $poster->image_size) == 'full' ? 'selected' : '' }}>Full
                                Width within Card</option>
                        </select>
                        <p class="text-xs text-[var(--text-muted)] mt-1">Controls how much space the image takes inside its
                            block.</p>
                        @error('image_size') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <!-- Text Position -->
                    <div>
                        <label for="text_position" class="block text-sm font-bold text-[var(--text-main)] mb-2">Text
                            Position <span class="text-red-500">*</span></label>
                        <select name="text_position" id="text_position" required
                            class="w-full bg-[var(--bg-main)] border border-[var(--border)] text-[var(--text-main)] rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] block p-2.5">
                            <option value="overlay" {{ old('text_position', $poster->text_position) == 'overlay' ? 'selected' : '' }}>Overlay on Image</option>
                            <option value="above" {{ old('text_position', $poster->text_position) == 'above' ? 'selected' : '' }}>Above Image</option>
                            <option value="below" {{ old('text_position', $poster->text_position) == 'below' ? 'selected' : '' }}>Below Image</option>
                        </select>
                        @error('text_position') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Text Color -->
                    <div>
                        <label for="text_color" class="block text-sm font-bold text-[var(--text-main)] mb-2">Text
                            Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="text_color" id="text_color"
                                value="{{ old('text_color', $poster->text_color) }}"
                                class="h-10 w-20 bg-[var(--bg-main)] border border-[var(--border)] rounded cursor-pointer">
                            <span class="text-sm text-[var(--text-muted)]">Choose a color that contrasts nicely.</span>
                        </div>
                        @error('text_color') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-4 border-t border-[var(--border)] pt-6">
                    <a href="{{ route('posters.index') }}"
                        class="px-6 py-2.5 rounded-lg font-bold text-[var(--text-muted)] border border-[var(--border)] hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-8 py-2.5 rounded-lg font-bold shadow-md transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posterForm', () => ({
                templateType: '{{ old('template_type', $poster->template_type) }}',
                links: {!! old('links') ? (is_string(old('links')) ? old('links') : json_encode(old('links'))) : ($poster->links ? json_encode($poster->links) : '[]') !!},
                addLink() {
                    this.links.push({ platform: '', icon: 'fab fa-facebook', url: 'https://' });
                },
                removeLink(index) {
                    this.links.splice(index, 1);
                }
            }))
        });

        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('image-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewMultipleImages(event) {
            const container = document.getElementById('multiple-preview');
            container.innerHTML = '';
            const files = event.target.files;
            if (files) {
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'h-16 w-16 object-cover rounded shadow border border-gray-200';
                        container.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }
    </script>
@endsection