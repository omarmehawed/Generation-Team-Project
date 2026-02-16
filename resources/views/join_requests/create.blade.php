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
      }" :class="{ 'bg-gray-50 text-gray-900': !darkMode, 'bg-dark text-white': darkMode }"
    x-init="$watch('darkMode', val => val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')); if(!darkMode) document.documentElement.classList.remove('dark');">

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
                            GENERATION <span class="text-blue-500">TEAM</span>
                        </h1>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button @click="toggleTheme()" class="p-2 rounded-full hover:bg-gray-200/20 transition-colors">
                        <i class="fas" :class="darkMode ? 'fa-sun text-yellow-400' : 'fa-moon text-gray-600'"></i>
                    </button>
                    <a href="/" class="text-sm font-bold hover:text-blue-500 transition-colors"
                        :class="{ 'text-gray-600': !darkMode, 'text-gray-300': darkMode }">
                        <i class="fas fa-arrow-right ml-2"></i> Back to Home
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
                                        <p class="mt-2 text-xs text-gray-500">JPG, PNG or GIF. Max size 10MB.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Full Name -->
                    <div class="col-span-2">
                        <label class="block text-sm font-bold mb-2"
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">Full Name</label>
                        <input type="text" name="full_name" required value="{{ old('full_name') }}"
                            class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors @error('full_name') border-red-500 @enderror"
                            :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">
                        @error('full_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- National ID -->
                    <div>
                        <label class="block text-sm font-bold mb-2"
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">National ID</label>
                        <input type="text" name="national_id" required value="{{ old('national_id') }}"
                            class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors @error('national_id') border-red-500 @enderror"
                            :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">
                        @error('national_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Academic ID -->
                    <div>
                        <label class="block text-sm font-bold mb-2"
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">Academic ID</label>
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
                            :class="darkMode ? 'text-gray-300' : 'text-gray-700'">Date of Birth</label>
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

                    <!-- Section 2: Technical Information -->
                    <div>
                        <h3 class="text-xl font-bold mb-6 flex items-center gap-2 text-blue-500">
                            <i class="fas fa-cogs"></i> Technical & Team Questions
                        </h3>

                        <div class="space-y-8">

                            <!-- 1. Experience Field -->
                            <div>
                                <label class="block text-sm font-bold mb-3"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "عندك خبرة في اي"
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach(['Embedded Systems / Microcontrollers', 'Robotics & Actuators', 'Mechanical Design & 3D Printing', 'Power Systems & Battery Management', 'Chemistry / Materials Science'] as $option)
                                        <label
                                            class="flex items-center gap-3 p-3 rounded-lg border transition-all cursor-pointer"
                                            :class="darkMode ? 'border-gray-700 hover:bg-gray-700' : 'border-gray-200 hover:bg-gray-50'">
                                            <input type="radio" name="answers[experience_field]" value="{{ $option }}" {{ old('answers.experience_field') == $option ? 'checked' : '' }}
                                                class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                            <span class="text-sm font-medium"
                                                :class="darkMode ? 'text-gray-300' : 'text-gray-700'">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('answers.experience_field')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 2. Large Team Experience -->
                            <div>
                                <label class="block text-sm font-bold mb-3"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "هل لديك أي تجربة في العمل ضمن فريق كبير (أكثر من 10 أفراد)؟"
                                </label>
                                <div class="flex gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="answers[large_team_experience]" value="Yes" {{ old('answers.large_team_experience') == 'Yes' ? 'checked' : '' }}
                                            class="w-5 h-5 text-blue-600 bg-transparent border-gray-500">
                                        <span :class="darkMode ? 'text-gray-300' : 'text-gray-700'">Yes</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="answers[large_team_experience]" value="No" {{ old('answers.large_team_experience') == 'No' ? 'checked' : '' }}
                                            class="w-5 h-5 text-blue-600 bg-transparent border-gray-500">
                                        <span :class="darkMode ? 'text-gray-300' : 'text-gray-700'">No</span>
                                    </label>
                                </div>
                                @error('answers.large_team_experience')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 3. Start Date -->
                            <div>
                                <label class="block text-sm font-bold mb-2"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "في حال تم قبولك، متى يمكنك البدء بشكل فعلي للعمل على المشروع (بعد الميد مباشرة)؟"
                                </label>
                                <input type="date" name="answers[start_date]" value="{{ old('answers.start_date') }}"
                                    class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors @error('answers.start_date') border-red-500 @enderror"
                                    :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">
                            </div>
                            @error('answers.start_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                            <!-- 4. Weekly Hours -->
                            <div>
                                <label class="block text-sm font-bold mb-3"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "ما هي الفترة الزمنية التي يمكنك تخصيصها للعمل على هذا المشروع أسبوعيًا؟"
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach(['أقل من 5 ساعات', '5 - 10 ساعات', '10 - 20 ساعة', 'أكثر من 20 ساعة'] as $option)
                                        <label
                                            class="flex items-center gap-3 p-3 rounded-lg border transition-all cursor-pointer"
                                            :class="darkMode ? 'border-gray-700 hover:bg-gray-700' : 'border-gray-200 hover:bg-gray-50'">
                                            <input type="radio" name="answers[weekly_hours]" value="{{ $option }}" {{ old('answers.weekly_hours') == $option ? 'checked' : '' }}
                                                class="w-5 h-5 text-blue-600">
                                            <span class="text-sm font-medium"
                                                :class="darkMode ? 'text-gray-300' : 'text-gray-700'">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('answers.weekly_hours')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 5. Best Project -->
                            <div>
                                <label class="block text-sm font-bold mb-2"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "اذكر أبرز مشروع سابق عملته"
                                </label>
                                <textarea name="answers[best_project]" rows="4"
                                    class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors @error('answers.best_project') border-red-500 @enderror"
                                    :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">{{ old('answers.best_project') }}</textarea>
                                @error('answers.best_project')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 6. Confidence Scale -->
                            <div>
                                <label class="block text-sm font-bold mb-4"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "على مقياس من 1 إلى 5، ما مدى ثقتك بقدرتك على إحداث فرق كبير في المشروع؟"
                                </label>
                                <div class="flex justify-between items-center px-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <label class="flex flex-col items-center cursor-pointer gap-2">
                                            <span class="text-xs font-bold"
                                                :class="darkMode ? 'text-gray-400' : 'text-gray-600'">{{ $i }}</span>
                                            <input type="radio" name="answers[confidence_scale]" value="{{ $i }}" {{ old('answers.confidence_scale') == $i ? 'checked' : '' }}
                                                class="w-6 h-6 text-blue-600 bg-transparent border-gray-500 focus:ring-blue-500">
                                        </label>
                                    @endfor
                                </div>
                                @error('answers.confidence_scale')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 7. Team Skills Matrix -->
                            <div class="overflow-x-auto">
                                <label class="block text-sm font-bold mb-4"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "يرجى تحديد مدى إتقانك للعمل ضمن فريق في الجوانب التالية:"
                                </label>
                                <table class="w-full text-sm text-left min-w-[600px]">
                                    <thead class="text-xs uppercase"
                                        :class="darkMode ? 'text-gray-400 bg-gray-700' : 'text-gray-700 bg-gray-100'">
                                        <tr>
                                            <th class="px-4 py-3 rounded-l-lg">Skill</th>
                                            <th class="px-4 py-3 text-center">ضعيف</th>
                                            <th class="px-4 py-3 text-center">مقبول</th>
                                            <th class="px-4 py-3 text-center">جيد</th>
                                            <th class="px-4 py-3 text-center rounded-r-lg">ممتاز</th>
                                        </tr>
                                    </thead>
                                    <tbody :class="darkMode ? 'divide-gray-700' : 'divide-gray-200'">
                                        @foreach(['Communication', 'Team Problem Solving', 'Meeting Deadlines'] as $skill)
                                            <tr class="border-b" :class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                                                <td class="px-4 py-4 font-medium"
                                                    :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $skill }}</td>
                                                @foreach(['ضعيف', 'مقبول', 'جيد', 'ممتاز'] as $level)
                                                    <td class="px-4 py-4 text-center">
                                                        <input type="radio" name="answers[team_skills][{{ $skill }}]"
                                                            value="{{ $level }}" {{ old("answers.team_skills.$skill") == $level ? 'checked' : '' }}
                                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300">
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($errors->has('answers.team_skills'))
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('answers.team_skills') }}</p>
                            @endif
                            @foreach(['Communication', 'Team Problem Solving', 'Meeting Deadlines'] as $skill)
                                @error("answers.team_skills.$skill")
                                    <p class="text-red-500 text-xs mt-1">{{ $skill }}: {{ $message }}</p>
                                @enderror
                            @endforeach

                            <!-- 8. Programming Language -->
                            <div>
                                <label class="block text-sm font-bold mb-3"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "ما هي اللغة البرمجية التي تتقنها وتستخدمها بشكل أساسي في مجال خبرتك المذكورة؟"
                                </label>
                                <select name="answers[programming_language]"
                                    class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors"
                                    :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500 bg-gray-800' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">
                                    <option value="" disabled selected>Select Language</option>
                                    @foreach(['C/C++', 'Python', 'Java', 'MATLAB/Simulink', 'Other'] as $lang)
                                        <option value="{{ $lang }}" {{ old('answers.programming_language') == $lang ? 'selected' : '' }}>{{ $lang }}</option>
                                    @endforeach
                                </select>
                                @error('answers.programming_language')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 9. Prototyping Skills Matrix -->
                            <div class="overflow-x-auto">
                                <label class="block text-sm font-bold mb-4"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "يرجى تحديد مدى إتقانك للمهارات التالية المتعلقة بتصنيع النماذج الأولية
                                    (Prototyping):"
                                </label>
                                <table class="w-full text-sm text-left min-w-[600px]">
                                    <thead class="text-xs uppercase"
                                        :class="darkMode ? 'text-gray-400 bg-gray-700' : 'text-gray-700 bg-gray-100'">
                                        <tr>
                                            <th class="px-4 py-3 rounded-l-lg">Skill</th>
                                            <th class="px-4 py-3 text-center">ضعيف</th>
                                            <th class="px-4 py-3 text-center">متوسط</th>
                                            <th class="px-4 py-3 text-center">جيد</th>
                                            <th class="px-4 py-3 text-center">ممتاز</th>
                                            <th class="px-4 py-3 text-center rounded-r-lg">لا اعرف</th>
                                        </tr>
                                    </thead>
                                    <tbody :class="darkMode ? 'divide-gray-700' : 'divide-gray-200'">
                                        @foreach(['3D Printing', 'Soldering', 'PCB Design', 'Electrical Testing'] as $skill)
                                            <tr class="border-b" :class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                                                <td class="px-4 py-4 font-medium"
                                                    :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $skill }}</td>
                                                @foreach(['ضعيف', 'متوسط', 'جيد', 'ممتاز', 'لا اعرف'] as $level)
                                                    <td class="px-4 py-4 text-center">
                                                        <input type="radio" name="answers[prototyping_skills][{{ $skill }}]"
                                                            value="{{ $level }}" {{ old("answers.prototyping_skills.$skill") == $level ? 'checked' : '' }}
                                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300">
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($errors->has('answers.prototyping_skills'))
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('answers.prototyping_skills') }}</p>
                            @endif
                            @foreach(['3D Printing', 'Soldering', 'PCB Design', 'Electrical Testing'] as $skill)
                                @error("answers.prototyping_skills.$skill")
                                    <p class="text-red-500 text-xs mt-1">{{ $skill }}: {{ $message }}</p>
                                @enderror
                            @endforeach

                            <!-- 10. Funding Experience -->
                            <div>
                                <label class="block text-sm font-bold mb-3"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "هل لديك أي تجربة سابقة في تأمين تمويل أو التعامل مع مستثمرين لمشاريعك؟"
                                </label>
                                <div class="flex gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="answers[funding_experience]" value="Yes" {{ old('answers.funding_experience') == 'Yes' ? 'checked' : '' }}
                                            class="w-5 h-5 text-blue-600">
                                        <span :class="darkMode ? 'text-gray-300' : 'text-gray-700'">Yes</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="answers[funding_experience]" value="No" {{ old('answers.funding_experience') == 'No' ? 'checked' : '' }}
                                            class="w-5 h-5 text-blue-600">
                                        <span :class="darkMode ? 'text-gray-300' : 'text-gray-700'">No</span>
                                    </label>
                                </div>
                                @error('answers.funding_experience')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 11. Tools Matrix -->
                            <div class="overflow-x-auto">
                                <label class="block text-sm font-bold mb-4"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "أي من الأدوات أو البرامج التالية تستخدمها بشكل متكرر في مجال خبرتك؟"
                                </label>
                                <table class="w-full text-sm text-left min-w-[600px]">
                                    <thead class="text-xs uppercase"
                                        :class="darkMode ? 'text-gray-400 bg-gray-700' : 'text-gray-700 bg-gray-100'">
                                        <tr>
                                            <th class="px-4 py-3 rounded-l-lg">Tool</th>
                                            <th class="px-4 py-3 text-center">قليلًا</th>
                                            <th class="px-4 py-3 text-center">أحياناً</th>
                                            <th class="px-4 py-3 text-center rounded-r-lg">بشكل متكرر</th>
                                        </tr>
                                    </thead>
                                    <tbody :class="darkMode ? 'divide-gray-700' : 'divide-gray-200'">
                                        @foreach(['SolidWorks / Fusion 360', 'Arduino / Raspberry Pi IDE', 'TensorFlow / PyTorch', 'Altium Designer / Eagle'] as $tool)
                                            <tr class="border-b" :class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                                                <td class="px-4 py-4 font-medium"
                                                    :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $tool }}</td>
                                                @foreach(['قليلًا', 'أحياناً', 'بشكل متكرر'] as $level)
                                                    <td class="px-4 py-4 text-center">
                                                        <input type="radio" name="answers[tools_usage][{{ $tool }}]"
                                                            value="{{ $level }}" {{ old("answers.tools_usage.$tool") == $level ? 'checked' : '' }}
                                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300">
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($errors->has('answers.tools_usage'))
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('answers.tools_usage') }}</p>
                            @endif
                            @foreach(['SolidWorks / Fusion 360', 'Arduino / Raspberry Pi IDE', 'TensorFlow / PyTorch', 'Altium Designer / Eagle'] as $tool)
                                @error("answers.tools_usage.$tool")
                                    <p class="text-red-500 text-xs mt-1">{{ $tool }}: {{ $message }}</p>
                                @enderror
                            @endforeach

                            <!-- 12. Voice Assistants -->
                            <div>
                                <label class="block text-sm font-bold mb-3"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "هل لديك خبرة في تطوير مساعدات صوتية (Voice Assistants) تتفاعل بشكل لحظي
                                    (Real-time)؟"
                                </label>
                                <div class="flex gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="answers[voice_assistants_realtime]" value="Yes" {{ old('answers.voice_assistants_realtime') == 'Yes' ? 'checked' : '' }}
                                            class="w-5 h-5 text-blue-600">
                                        <span :class="darkMode ? 'text-gray-300' : 'text-gray-700'">Yes</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="answers[voice_assistants_realtime]" value="No" {{ old('answers.voice_assistants_realtime') == 'No' ? 'checked' : '' }}
                                            class="w-5 h-5 text-blue-600">
                                        <span :class="darkMode ? 'text-gray-300' : 'text-gray-700'">No</span>
                                    </label>
                                </div>
                                @error('answers.voice_assistants_realtime')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 13. Importance Scale -->
                            <div>
                                <label class="block text-sm font-bold mb-4"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "ما هي درجة أهمية المشروع بالنسبة لأهدافك المهنية والشخصية؟"
                                </label>
                                <div class="flex justify-between items-center px-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <label class="flex flex-col items-center cursor-pointer gap-2">
                                            <span class="text-xs font-bold"
                                                :class="darkMode ? 'text-gray-400' : 'text-gray-600'">{{ $i }}</span>
                                            <input type="radio" name="answers[project_importance]" value="{{ $i }}" {{ old('answers.project_importance') == $i ? 'checked' : '' }}
                                                class="w-6 h-6 text-blue-600 bg-transparent border-gray-500 focus:ring-blue-500">
                                        </label>
                                    @endfor
                                </div>
                                @error('answers.project_importance')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 14. Stress Handling -->
                            <div>
                                <label class="block text-sm font-bold mb-2"
                                    :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    "في حال وجود ضغط أو تحديات غير متوقعة في المشروع، كيف تتعامل معها؟"
                                </label>
                                <textarea name="answers[stress_handling]" rows="4"
                                    class="w-full px-4 py-3 rounded-lg bg-transparent border focus:outline-none transition-colors @error('answers.stress_handling') border-red-500 @enderror"
                                    :class="darkMode ? 'border-gray-600 text-white focus:border-blue-500' : 'border-gray-300 text-gray-900 focus:border-blue-500 bg-gray-50'">{{ old('answers.stress_handling') }}</textarea>
                                @error('answers.stress_handling')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
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