@extends('layouts.batu')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="posterForm()">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('posters.index') }}"
                class="text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold font-tech text-[var(--primary)] uppercase tracking-wider">Add New Block</h1>
        </div>

        <div class="ui-card p-6 md:p-8">
            <form action="{{ route('posters.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

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

                <!-- Single Image Upload (Standard & Profile Card) -->
                <div x-show="templateType !== 'slider'" x-transition>
                    <label for="image" class="block text-sm font-bold text-[var(--text-main)] mb-2">Image <span
                            class="text-red-500" x-show="templateType !== 'slider'">*</span></label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-[var(--border)] border-dashed rounded-lg hover:border-[var(--primary)] transition-colors"
                        id="drop-zone-single">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-cloud-upload-alt text-4xl text-[var(--text-muted)] mb-3"
                                id="upload-icon-single"></i>
                            <img id="image-preview" class="mx-auto h-48 w-auto object-cover rounded-md hidden mb-3" />
                            <div class="flex text-sm text-[var(--text-muted)] justify-center">
                                <label for="image"
                                    class="relative cursor-pointer bg-[var(--bg-panel)] rounded-md font-medium text-[var(--primary)] hover:text-[var(--primary-hover)] focus-within:outline-none">
                                    <span>Upload a file</span>
                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*"
                                        :required="templateType !== 'slider'" onchange="previewSingleImage(event)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-[var(--text-muted)]">PNG, JPG, GIF up to 5MB</p>
                        </div>
                    </div>
                    @error('image') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Multiple Images Upload (Slider) -->
                <div x-show="templateType === 'slider'" x-transition style="display: none;">
                    <label for="slider_images" class="block text-sm font-bold text-[var(--text-main)] mb-2">Slider Images
                        (Multiple) <span class="text-red-500" x-show="templateType === 'slider'">*</span></label>
                    <div
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-[var(--border)] border-dashed rounded-lg hover:border-[var(--primary)] transition-colors">
                        <div class="space-y-1 text-center w-full">
                            <i class="fas fa-images text-4xl text-[var(--text-muted)] mb-3"></i>
                            <div class="flex text-sm text-[var(--text-muted)] justify-center mb-3">
                                <label for="slider_images"
                                    class="relative cursor-pointer bg-[var(--bg-panel)] rounded-md font-medium text-[var(--primary)] hover:text-[var(--primary-hover)] focus-within:outline-none">
                                    <span>Select multiple files</span>
                                    <input id="slider_images" name="slider_images[]" type="file" class="sr-only"
                                        accept="image/*" multiple :required="templateType === 'slider'"
                                        onchange="previewMultipleImages(event)">
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
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full bg-[var(--bg-main)] border border-[var(--border)] text-[var(--text-main)] rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] block p-2.5"
                        placeholder="E.g., IT Club Collaboration">
                    @error('title') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-bold text-[var(--text-main)] mb-2">Description <span
                            class="text-gray-400 font-normal">(Optional)</span></label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full bg-[var(--bg-main)] border border-[var(--border)] text-[var(--text-main)] rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] block p-2.5"
                        placeholder="Write a short description...">{{ old('description') }}</textarea>
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
                            <option value="small" {{ old('image_size') == 'small' ? 'selected' : '' }}>Small (1/3 Width)
                            </option>
                            <option value="medium" {{ old('image_size') == 'medium' ? 'selected' : '' }}>Medium (1/2 Width)
                            </option>
                            <option value="large" {{ old('image_size') == 'large' ? 'selected' : '' }}>Large (3/4 Width)
                            </option>
                            <option value="full" {{ old('image_size', 'full') == 'full' ? 'selected' : '' }}>Full Width within
                                Card
                            </option>
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
                            <option value="overlay" {{ old('text_position') == 'overlay' ? 'selected' : '' }}>Overlay on Image
                            </option>
                            <option value="above" {{ old('text_position') == 'above' ? 'selected' : '' }}>Above Image</option>
                            <option value="below" {{ old('text_position') == 'below' ? 'selected' : '' }}>Below Image</option>
                        </select>
                        @error('text_position') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Text Color -->
                    <div>
                        <label for="text_color" class="block text-sm font-bold text-[var(--text-main)] mb-2">Text
                            Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="text_color" id="text_color" value="{{ old('text_color', '#ffffff') }}"
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
                        <i class="fas fa-save"></i> Save Block
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posterForm', () => ({
                templateType: '{{ old('template_type', 'standard') }}',
                links: {!! old('links') ?: '[]' !!},
                addLink() {
                    this.links.push({ platform: '', icon: 'fab fa-facebook', url: 'https://' });
                },
                removeLink(index) {
                    this.links.splice(index, 1);
                }
            }))
        });

        function previewSingleImage(event) {
            const input = event.target;
            const preview = document.getElementById('image-preview');
            const icon = document.getElementById('upload-icon-single');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    icon.classList.add('hidden');
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
                        img.className = 'h-24 w-24 object-cover rounded shadow border border-gray-200';
                        container.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }
    </script>
@endsection