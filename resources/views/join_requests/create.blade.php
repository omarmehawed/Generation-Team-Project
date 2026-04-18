<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join Generation Team</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Rajdhani:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind & Alpine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif'],
                        tech: ['Rajdhani', 'sans-serif'],
                    },
                    colors: {
                        primary: '#3b82f6',
                        dark: '#0f172a',
                        light: '#f8fafc',
                    }
                }
            }
        }
    </script>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-input:focus {
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
            border-color: #3b82f6;
        }
    </style>
</head>

<body class="font-cairo text-gray-100 bg-dark antialiased transition-colors duration-300 min-h-screen relative" x-data="{ 
          darkMode: localStorage.getItem('theme') !== 'light',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              if(this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }" :class="{ 'bg-gray-50 text-gray-900': !darkMode, 'bg-dark text-white': darkMode }" }"
    :class="{ 'bg-gray-50 text-gray-900': !darkMode, 'bg-dark text-white': darkMode }" x-init="$watch('darkMode', val => val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')); 
            if(!darkMode) document.documentElement.classList.remove('dark');
            
            if(firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.previousElementSibling?.focus();
            }">
    <style>
        body {
            overflow-x: hidden;
        }
    </style>

    <!-- Navbar (Same as Landing) -->
    <nav class="fixed w-full z-50 transition-all duration-300 glass border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-4">
                    <div class="relative w-10 h-10 rounded-xl overflow-hidden shadow-lg border border-blue-500/30">
                        <img src="{{ asset('assets/gt_logo.jpg') }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h1 class="text-xl font-bold font-tech tracking-wider"
                            :class="{ 'text-gray-900': !darkMode, 'text-white': darkMode }">
                            GEN<span class="hidden xs:inline">ERATION</span> <span class="text-blue-500">TEAM</span>
                        </h1>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button @click="toggleTheme()" class="p-2 rounded-full hover:bg-gray-200/20 transition-colors">
                        <i class="fas" :class="darkMode ? 'fa-sun text-yellow-400' : 'fa-moon text-gray-600'"></i>
                    </button>
                    <a href="/" class="text-sm font-bold hover:text-blue-500 transition-colors"
                        :class="{ 'text-gray-600': !darkMode, 'text-gray-300': darkMode }">
                        <i class="fas fa-arrow-right ml-2"></i> <span class="hidden sm:inline">Back to Home</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-32 pb-20 px-4 flex justify-center items-center min-h-screen">

        <div class="max-w-4xl w-full relative z-10 animate-fade-in-up">

            <!-- Success Message (Optional) -->
            @if(session('success'))
                <div
                    class="bg-green-500/20 border border-green-500 text-green-500 px-6 py-4 rounded-xl mb-8 flex items-center gap-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                    <div>
                        <h4 class="font-bold">Application Submitted!</h4>
                        <p class="text-sm">We have received your request and will review it perfectly.</p>
                    </div>
                </div>
            @endif

            <!-- Error Message (Duplicate Registration) -->
            @if(session('error'))
                <div
                    class="bg-red-500/20 border border-red-500 text-red-500 px-6 py-4 rounded-xl mb-8 flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                    <div>
                        <h4 class="font-bold text-lg">{{ session('error_title', 'عفواً! لقد قمت بالتسجيل مسبقاً.') }}</h4>
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-gray-700"
                :class="{ 'bg-white/90 border-gray-200 shadow-xl': !darkMode, 'bg-gray-800/80 border-gray-700': darkMode }">

                <!-- Header -->
                <div class="px-8 py-8 border-b" :class="{ 'border-gray-200': !darkMode, 'border-gray-700': darkMode }">
                    <h2 class="text-3xl font-bold text-center mb-2"
                        :class="{ 'text-gray-900': !darkMode, 'text-white': darkMode }">Join Our Team</h2>
                    <p class="text-center text-sm" :class="{ 'text-gray-500': !darkMode, 'text-gray-400': darkMode }">
                        Complete the form below to become a student of Generation Team.</p>
                </div>

                <form action="{{ route('join.store') }}" method="POST" enctype="multipart/form-data"
                    class="px-8 py-8 space-y-8">
                    @csrf

                    <!-- Section 1: Basic Information -->
                    <div>
                        <h3 class="text-xl font-bold mb-6 flex items-center gap-2 text-blue-500">
                            <i class="fas fa-user-circle"></i> Basic Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Photo Upload -->
                            <div class="col-span-2" x-data="{ preview: null }">
                                <label class="block text-sm font-bold mb-2"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">Formal Photo</label>
                                <div class="flex items-center gap-6">
                                    <div class="w-24 h-24 rounded-full border-2 border-dashed flex items-center justify-center overflow-hidden relative"
                                        :class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-300 bg-gray-100'">
                                        <template x-if="!preview">
                                            <i class="fas fa-camera text-2xl text-gray-400"></i>
                                        </template>
                                        <img :src="preview" class="w-full h-full object-cover">
                                        </template>
                                        @error('photo')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex-1">
                                        <label
                                            class="cursor-pointer inline-block px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                                            <span>Upload Photo</span>
                                            <input type="file" name="photo" class="hidden" accept="image/*"
                                                @change="preview = URL.createObjectURL($event.target.files[0])">
                                        </label>
                                        <p class="mt-2 text-xs text-gray-500">JPG, PNG or GIF. Max size 100MB.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Full Name -->
                    <div class="col-span-2">
                        <label class="block text-sm font-bold mb-2"
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">(الأسم بالعربي)Full Name</label>
                        <input type="text" name="full_name" required value="{{ old('full_name') }}"
                            class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors @error('full_name') border-red-500 @enderror"
                            :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">
                        @error('full_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- National ID -->
                    <div x-data="{ 
                            nationalId: '{{ old('national_id') }}', 
                            isChecking: false, 
                            errorMsg: '', 
                            checkDuplicate() {
                                if(this.nationalId.length < 5) { this.errorMsg = ''; return; }
                                this.isChecking = true;
                                fetch('/join/check-duplicate?nid=' + this.nationalId)
                                    .then(res => res.json())
                                    .then(data => {
                                        this.isChecking = false;
                                        if(data.exists) {
                                            this.errorMsg = 'هذا الرقم القومي مستخدم من قبل.';
                                        } else {
                                            this.errorMsg = '';
                                        }
                                    });
                            }
                        }" x-init="if(nationalId) checkDuplicate()">
                        <label class="block text-sm font-bold mb-2"
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">(الرقم القومي)National ID</label>
                        <input type="text" name="national_id" required x-model="nationalId"
                            @input.debounce.500ms="checkDuplicate"
                            class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors @error('national_id') border-red-500 @enderror"
                            :class="[
                                darkMode ? 'text-white focus:border-blue-500' : 'text-gray-900 focus:border-blue-500 bg-gray-50',
                                errorMsg ? 'border-red-500' : (darkMode ? 'border-gray-600' : 'border-gray-300')
                            ]">
                        <template x-if="isChecking">
                            <p class="text-blue-500 text-xs mt-1 animate-pulse"><i
                                    class="fas fa-spinner fa-spin mr-1"></i>جاري التحقق...</p>
                        </template>
                        <template x-if="errorMsg">
                            <p class="text-red-500 text-xs mt-1 font-bold" x-text="errorMsg"></p>
                        </template>
                        @error('national_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Academic ID -->
                    <div>
                        <label class="block text-sm font-bold mb-2"
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">(الرقم الأكاديمي)Academic ID</label>
                        <input type="text" name="academic_id" required value="{{ old('academic_id') }}"
                            class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors @error('academic_id') border-red-500 @enderror"
                            :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">
                        @error('academic_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-sm font-bold mb-2"
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">(تاريخ الميلاد)Date of Birth</label>
                        <input type="date" name="date_of_birth" required value="{{ old('date_of_birth') }}"
                            class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors @error('date_of_birth') border-red-500 @enderror"
                            :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">
                        @error('date_of_birth')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Group -->
                    <div>
                        <label class="block text-sm font-bold mb-2"
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">Group</label>
                        <select name="group" required
                            class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors appearance-none @error('group') border-red-500 @enderror"
                            :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500 bg-gray-800' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">
                            <option value="" disabled selected>Select Group</option>
                            <option value="G1" {{ old('group') == 'G1' ? 'selected' : '' }}>G1</option>
                            <option value="G2" {{ old('group') == 'G2' ? 'selected' : '' }}>G2</option>
                            <option value="G3" {{ old('group') == 'G3' ? 'selected' : '' }}>G3</option>
                            <option value="G4" {{ old('group') == 'G4' ? 'selected' : '' }}>G4</option>
                        </select>
                        @error('group')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-bold mb-2"
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">Phone Number</label>
                        <input type="text" name="phone_number" required value="{{ old('phone_number') }}"
                            class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors @error('phone_number') border-red-500 @enderror"
                            :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">
                        @error('phone_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label class="block text-sm font-bold mb-2"
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" required value="{{ old('whatsapp_number') }}"
                            class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors @error('whatsapp_number') border-red-500 @enderror"
                            :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">
                        @error('whatsapp_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-bold mb-2"
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">Address (العنوان)</label>
                        <input type="text" name="address" required placeholder="City, Street, Building No."
                            value="{{ old('address') }}"
                            class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors @error('address') border-red-500 @enderror"
                            :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dorm Status -->
                    <div>
                        <label class="block text-sm font-bold mb-3"
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">Are you staying in a
                            dorm/hostel? <span class="text-xs text-gray-500">(هل تسكن بسكن
                                جامعي؟)</span></label>
                        <div class="flex gap-6 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="is_dorm" value="1" class="w-5 h-5 text-blue-600">
                                <span :class="darkMode ? 'text-gray-300' : 'text-gray-700'">Yes</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="is_dorm" value="0" checked class="w-5 h-5 text-blue-600">
                                <span :class="darkMode ? 'text-gray-300' : 'text-gray-700'">No</span>
                            </label>
                        </div>
                        @error('is_dorm')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t border-gray-700 my-8"></div>

                    <!-- Section 2: Technical & Custom Questions -->
                    <div x-data="{ 
                        answers: {},
                        shouldShow(q) {
                            if (!q.conditional_logic || !q.conditional_logic.show_if_question_id) return true;
                            const targetId = q.conditional_logic.show_if_question_id;
                            const targetValue = q.conditional_logic.show_if_value;
                            return this.answers[targetId] == targetValue;
                        }
                    }">
                        <h3 class="text-xl font-bold mb-6 flex items-center gap-2 text-blue-500">
                            <i class="fas fa-cogs"></i> Technical & Team Questions
                        </h3>
                        @foreach ($questions as $q)
                            <div x-show="shouldShow({{ $q->toJson() }})" class="mb-8" x-transition>
                                <label class="block text-sm font-bold mb-3" :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    {{ $q->question_text }}
                                    @if($q->is_required) <span class="text-red-500">*</span> @endif
                                </label>

                                @if($q->question_type === 'text')
                                    <input type="text" name="answers[{{ $q->id }}]" 
                                        @input="answers[{{ $q->id }}] = $event.target.value"
                                        class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors"
                                        :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'"
                                        {{ $q->is_required ? 'required' : '' }}>

                                @elseif($q->question_type === 'textarea')
                                    <textarea name="answers[{{ $q->id }}]" rows="3"
                                        @input="answers[{{ $q->id }}] = $event.target.value"
                                        class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors"
                                        :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'"
                                        {{ $q->is_required ? 'required' : '' }}></textarea>

                                @elseif($q->question_type === 'radio')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($q->options as $opt)
                                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all hover:bg-blue-600/10"
                                                :class="darkMode ? 'border-gray-700 bg-gray-800/40 text-gray-300' : 'border-gray-200 bg-white text-gray-700'">
                                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}"
                                                    @change="answers[{{ $q->id }}] = '{{ $opt }}'"
                                                    class="w-5 h-5 text-blue-600" {{ $q->is_required ? 'required' : '' }}>
                                                <span class="text-sm font-medium">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                @elseif($q->question_type === 'select')
                                    <select name="answers[{{ $q->id }}]"
                                        @change="answers[{{ $q->id }}] = $event.target.value"
                                        class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors"
                                        :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'"
                                        {{ $q->is_required ? 'required' : '' }}>
                                        <option value="">Select an option</option>
                                        @foreach($q->options as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>

                                @elseif($q->question_type === 'checkbox')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-data="{ selected: [] }">
                                        @foreach($q->options as $opt)
                                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all hover:bg-blue-600/10"
                                                :class="darkMode ? 'border-gray-700 bg-gray-800/40 text-gray-300' : 'border-gray-200 bg-white text-gray-700'">
                                                <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $opt }}"
                                                    @change="if($event.target.checked) selected.push('{{ $opt }}'); else selected = selected.filter(v => v !== '{{ $opt }}'); answers[{{ $q->id }}] = selected"
                                                    class="w-5 h-5 text-blue-600 rounded">
                                                <span class="text-sm font-medium">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                @elseif($q->question_type === 'date')
                                    <input type="date" name="answers[{{ $q->id }}]"
                                        @input="answers[{{ $q->id }}] = $event.target.value"
                                        class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors"
                                        :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'"
                                        {{ $q->is_required ? 'required' : '' }}>

                                @elseif($q->question_type === 'scale')
                                    <div class="flex items-center justify-between bg-slate-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                                        <span class="text-xs text-gray-500">Poor</span>
                                        <div class="flex gap-4">
                                            @for($i=1; $i<=5; $i++)
                                                <label class="flex flex-col items-center gap-1 cursor-pointer">
                                                    <input type="radio" name="answers[{{ $q->id }}]" value="{{ $i }}"
                                                        @change="answers[{{ $q->id }}] = '{{ $i }}'"
                                                        class="w-6 h-6 text-blue-600" {{ $q->is_required ? 'required' : '' }}>
                                                    <span class="text-xs font-bold" :class="darkMode ? 'text-gray-400' : 'text-gray-600'">{{ $i }}</span>
                                                </label>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-gray-500">Excellent</span>
                                    </div>

                                @elseif($q->question_type === 'matrix')
                                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                                        <table class="w-full text-sm">
                                            <thead :class="darkMode ? 'bg-gray-800' : 'bg-gray-50'">
                                                <tr>
                                                    <th class="p-3 text-left">Item</th>
                                                    @foreach($q->options['cols'] as $col)
                                                        <th class="p-3 text-center">{{ $col }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                                @foreach($q->options['rows'] as $row)
                                                    <tr>
                                                        <td class="p-3 font-bold" :class="darkMode ? 'text-gray-300' : 'text-gray-700'">{{ $row }}</td>
                                                        @foreach($q->options['cols'] as $col)
                                                            <td class="p-3 text-center">
                                                                <input type="radio" name="answers[{{ $q->id }}][{{ $row }}]" value="{{ $col }}" 
                                                                    {{ $q->is_required ? 'required' : '' }}
                                                                    class="w-5 h-5 text-blue-600">
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                @error('answers.'.$q->id)
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-bold py-4 rounded-xl shadow-lg transform transition hover:-translate-y-1 hover:shadow-2xl flex justify-center items-center gap-2">
                            <span>Submit Join Request</span>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>

                </form>
            </div>
            @if ($errors->any())
                <div
                    class="bg-red-500/20 border border-red-500 text-red-500 px-6 py-4 rounded-xl mt-4 max-w-4xl mx-auto backdrop-blur-md">
                    <p class="font-bold mb-2">Please correct the following errors:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

</body>

</html>