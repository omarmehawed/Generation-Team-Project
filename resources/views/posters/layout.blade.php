@extends('layouts.batu')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('posters.index') }}"
                class="text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold font-tech text-[var(--primary)] uppercase tracking-wider">Edit Poster Layout:
                {{ $poster->title }}
            </h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Control Panel -->
            <div class="lg:col-span-1 space-y-6">
                <div class="ui-card p-6 sticky top-24">
                    <h3 class="font-bold text-xl mb-4 text-[var(--text-main)] border-b pb-2 border-[var(--border)]">Layout
                        Tools</h3>

                    <form id="layout-form" class="space-y-6">
                        <!-- Block Controls -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-[var(--primary)]"><i class="fas fa-columns"></i> Block Settings
                            </h4>
                            <div>
                                <label class="block text-sm text-[var(--text-main)] mb-1">Card Grid Width: <span
                                        id="card-width-val">12</span> / 12</label>
                                <input type="range" id="card_width" min="4" max="12" step="1" value="12"
                                    class="w-full accent-[var(--primary)]">
                                <p class="text-xs text-[var(--text-muted)] mt-1">12 = Full, 6 = Half, 4 = Third Width.</p>
                            </div>
                        </div>

                        <hr class="border-[var(--border)]">

                        <!-- Image Controls -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-[var(--primary)]"><i class="fas fa-image"></i> Image</h4>

                            <div>
                                <label class="block text-sm text-[var(--text-main)] mb-1">Scale: <span
                                        id="img-scale-val">100</span>%</label>
                                <input type="range" id="img_scale" min="50" max="150" value="100"
                                    class="w-full accent-[var(--primary)]">
                            </div>

                            <div>
                                <label class="block text-sm text-[var(--text-main)] mb-1">Position X: <span
                                        id="img-x-val">0</span>px</label>
                                <input type="range" id="img_x" min="-200" max="200" value="0"
                                    class="w-full accent-[var(--primary)]">
                            </div>

                            <div>
                                <label class="block text-sm text-[var(--text-main)] mb-1">Position Y: <span
                                        id="img-y-val">0</span>px</label>
                                <input type="range" id="img_y" min="-200" max="200" value="0"
                                    class="w-full accent-[var(--primary)]">
                            </div>

                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="has_frame"
                                    class="w-4 h-4 text-[var(--primary)] border-[var(--border)] rounded focus:ring-[var(--primary)]">
                                <label for="has_frame" class="text-sm font-bold text-[var(--text-main)]">Add Gold
                                    Frame</label>
                            </div>
                        </div>

                        <hr class="border-[var(--border)]">

                        <!-- Text Controls -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-[var(--primary)]"><i class="fas fa-font"></i> Text Content</h4>

                            <div>
                                <label class="block text-sm text-[var(--text-main)] mb-1">Scale: <span
                                        id="txt-scale-val">100</span>%</label>
                                <input type="range" id="text_scale" min="50" max="200" value="100"
                                    class="w-full accent-[var(--primary)]">
                            </div>

                            <div>
                                <label class="block text-sm text-[var(--text-main)] mb-1">Position X: <span
                                        id="txt-x-val">0</span>px</label>
                                <input type="range" id="text_x" min="-200" max="200" value="0"
                                    class="w-full accent-[var(--primary)]">
                            </div>

                            <div>
                                <label class="block text-sm text-[var(--text-main)] mb-1">Position Y: <span
                                        id="txt-y-val">0</span>px</label>
                                <input type="range" id="text_y" min="-200" max="200" value="0"
                                    class="w-full accent-[var(--primary)]">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-[var(--border)] flex gap-2">
                            <button type="button" id="reset-btn"
                                class="flex-1 px-4 py-2 border border-[var(--border)] text-[var(--text-muted)] rounded hover:bg-[var(--bg-panel)] transition-colors">
                                Reset
                            </button>
                            <button type="button" id="save-btn"
                                class="flex-1 bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-4 py-2 rounded shadow transition-all font-bold">
                                Save Layout
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Live Preview -->
            <div class="lg:col-span-3">
                <div
                    class="ui-card p-4 overflow-hidden bg-white dark:bg-dark border-4 border-dashed border-[var(--border)] min-h-[800px] relative">
                    <div class="absolute top-2 right-2 bg-black/50 text-white px-3 py-1 rounded text-xs z-50">Live Preview
                    </div>

                    <!-- Preview Container mimicking welcome.blade.php poster look -->
                    <div class="max-w-7xl mx-auto w-full grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                        <div class="md:col-span-12 col-span-1 transition-all duration-300 relative w-full rounded-3xl overflow-hidden border border-[var(--border)] shadow-xl group my-8"
                            id="poster-container">

                            @php
                                $sizeClass = match ($poster->image_size) {
                                    'small' => 'w-full md:w-1/3 mx-auto',
                                    'medium' => 'w-full md:w-1/2 mx-auto',
                                    'large' => 'w-full md:w-3/4 mx-auto',
                                    default => 'w-full',
                                };
                                $settings = $poster->layout_settings ?? [];
                            @endphp

                            <!-- Above Position -->
                            @if($poster->text_position === 'above')
                                <div class="p-8 text-center" style="background-color: var(--bg-panel);" id="text-block">
                                    <div id="text-wrapper" class="transition-transform duration-75">
                                        <h2 class="text-3xl md:text-5xl font-bold font-amiri mb-4"
                                            style="color: {{ $poster->text_color }}">{{ $poster->title }}</h2>
                                        @if($poster->description)
                                            <p class="text-lg opacity-90 font-amiri max-w-4xl mx-auto"
                                                style="color: {{ $poster->text_color }}">{{ $poster->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="overflow-hidden p-4">
                                    <img src="{{ asset('storage/' . $poster->image_path) }}" id="image-block"
                                        class="{{ $sizeClass }} h-auto max-h-[70vh] object-cover block transition-transform duration-75"
                                        alt="{{ $poster->title }}">
                                </div>

                                <!-- Overlay Position -->
                            @elseif($poster->text_position === 'overlay')
                                <div class="relative {{ $sizeClass }} h-[60vh] md:h-[80vh] mx-auto overflow-hidden rounded-3xl">
                                    <img src="{{ asset('storage/' . $poster->image_path) }}" id="image-block"
                                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-75"
                                        alt="{{ $poster->title }}">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent flex flex-col justify-end p-8 md:p-16 text-center pointer-events-none">
                                        <div id="text-wrapper" class="transition-transform duration-75">
                                            <h2 class="text-4xl md:text-6xl font-black font-amiri mb-4 drop-shadow-lg"
                                                style="color: {{ $poster->text_color }}">{{ $poster->title }}</h2>
                                            @if($poster->description)
                                                <p class="text-xl md:text-2xl font-amiri max-w-4xl mx-auto drop-shadow-md"
                                                    style="color: {{ $poster->text_color }}">{{ $poster->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Below Position -->
                            @elseif($poster->text_position === 'below')
                                <div class="overflow-hidden p-4">
                                    <img src="{{ asset('storage/' . $poster->image_path) }}" id="image-block"
                                        class="{{ $sizeClass }} h-auto max-h-[70vh] object-cover block transition-transform duration-75"
                                        alt="{{ $poster->title }}">
                                </div>
                                <div class="p-8 text-center" style="background-color: var(--bg-panel);" id="text-block">
                                    <div id="text-wrapper" class="transition-transform duration-75">
                                        <h2 class="text-3xl md:text-5xl font-bold font-amiri mb-4"
                                            style="color: {{ $poster->text_color }}">{{ $poster->title }}</h2>
                                        @if($poster->description)
                                            <p class="text-lg opacity-90 font-amiri max-w-4xl mx-auto"
                                                style="color: {{ $poster->text_color }}">{{ $poster->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const settings = @json($settings);
            const defaults = {
                card_width: 12,
                image_scale: 100,
                image_x: 0,
                image_y: 0,
                has_frame: false,
                text_scale: 100,
                text_x: 0,
                text_y: 0
            };

            const currentSettings = { ...defaults, ...settings };

            // Elements
            const controls = {
                cardWidth: document.getElementById('card_width'),
                imgScale: document.getElementById('img_scale'),
                imgX: document.getElementById('img_x'),
                imgY: document.getElementById('img_y'),
                hasFrame: document.getElementById('has_frame'),
                txtScale: document.getElementById('text_scale'),
                txtX: document.getElementById('text_x'),
                txtY: document.getElementById('text_y')
            };

            const elements = {
                container: document.getElementById('poster-container'),
                image: document.getElementById('image-block'),
                text: document.getElementById('text-wrapper')
            };

            // Initialize Values
            function initControls() {
                controls.cardWidth.value = currentSettings.card_width || 12;
                controls.imgScale.value = currentSettings.image_scale;
                controls.imgX.value = currentSettings.image_x;
                controls.imgY.value = currentSettings.image_y;
                controls.hasFrame.checked = currentSettings.has_frame;
                controls.txtScale.value = currentSettings.text_scale;
                controls.txtX.value = currentSettings.text_x;
                controls.txtY.value = currentSettings.text_y;
                updatePreview();
            }

            // Apply visual updates
            function updatePreview() {
                // Update labels
                document.getElementById('card-width-val').innerText = controls.cardWidth.value;
                document.getElementById('img-scale-val').innerText = controls.imgScale.value;
                document.getElementById('img-x-val').innerText = controls.imgX.value;
                document.getElementById('img-y-val').innerText = controls.imgY.value;
                document.getElementById('txt-scale-val').innerText = controls.txtScale.value;
                document.getElementById('txt-x-val').innerText = controls.txtX.value;
                document.getElementById('txt-y-val').innerText = controls.txtY.value;

                // Apply to Container (Card Width)
                if (elements.container) {
                    elements.container.className = `md:col-span-${controls.cardWidth.value} col-span-1 transition-all duration-300 relative w-full rounded-3xl overflow-hidden border border-[var(--border)] shadow-xl group my-8`;
                }

                // Apply to Image
                if (elements.image) {
                    const imgScale = controls.imgScale.value / 100;
                    elements.image.style.transform = `translate(${controls.imgX.value}px, ${controls.imgY.value}px) scale(${imgScale})`;

                    if (controls.hasFrame.checked) {
                        elements.image.classList.add('border-8', 'border-amber-500', 'shadow-[0_0_50px_rgba(251,191,36,0.3)]', 'rounded-xl');
                    } else {
                        elements.image.classList.remove('border-8', 'border-amber-500', 'shadow-[0_0_50px_rgba(251,191,36,0.3)]', 'rounded-xl');
                    }
                }

                // Apply to Text
                if (elements.text) {
                    const txtScale = controls.txtScale.value / 100;
                    elements.text.style.transform = `translate(${controls.txtX.value}px, ${controls.txtY.value}px) scale(${txtScale})`;
                }
            }

            // Attach Event Listeners to all controls
            Object.values(controls).forEach(input => {
                input.addEventListener('input', updatePreview);
                // Handle checkbox separately
                if (input.type === 'checkbox') input.addEventListener('change', updatePreview);
            });

            // Reset Button
            document.getElementById('reset-btn').addEventListener('click', () => {
                Object.assign(currentSettings, defaults);
                initControls();
            });

            // Save Button
            document.getElementById('save-btn').addEventListener('click', () => {
                const btn = document.getElementById('save-btn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                btn.disabled = true;

                const payload = {
                    card_width: parseInt(controls.cardWidth.value) || 12,
                    image_scale: parseInt(controls.imgScale.value),
                    image_x: parseInt(controls.imgX.value),
                    image_y: parseInt(controls.imgY.value),
                    has_frame: controls.hasFrame.checked,
                    text_scale: parseInt(controls.txtScale.value),
                    text_x: parseInt(controls.txtX.value),
                    text_y: parseInt(controls.txtY.value)
                };

                fetch('{{ route('posters.update_layout', $poster->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ layout_settings: payload })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Layout Saved',
                                text: 'Your visual adjustments have been saved successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to save layout.'
                        });
                    })
                    .finally(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
            });

            initControls();
        });
    </script>
@endsection