<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generation Team - Robot Project</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Rajdhani:wght@400;500;600;700&family=Amiri:wght@400;700&display=swap"
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
                        amiri: ['Amiri', 'serif'],
                    },
                    colors: {
                        primary: '#fbbf24', // Gold
                        dark: '#0f172a',
                        light: '#f8fafc',
                        ramadan: {
                            night: '#1e1b4b',
                            gold: '#fbbf24',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-in': 'fadeIn 1s ease-out forwards',
                        'slide-up': 'slideUp 0.8s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #1e293b;
        }

        ::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }

        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-nav {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Text Gradients */
        .text-gradient {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Hover Effects */
        .hover-card {
            transition: all 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px -5px rgba(59, 130, 246, 0.3);
            border-color: #3b82f6;
        }

        /* Video Background */
        .video-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            /* Dark overlay */
            z-index: 0;
        }
    </style>
</head>

<body class="font-cairo text-gray-100 bg-dark antialiased transition-colors duration-300" x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark',
          mobileMenuOpen: false,
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
    <x-ramadan-theme />

    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300 glass-nav"
        :class="{'bg-white/90 text-gray-900 border-gray-200': !darkMode}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">

                <!-- Logo & Brand -->
                <div class="flex items-center gap-4">
                    <div class="relative w-12 h-12 rounded-xl overflow-hidden shadow-lg border border-amber-500/30">
                        <img src="{{ asset('assets/gt_logo.jpg') }}" class="w-full h-full object-cover"
                            alt="Generation Team">
                    </div>
                    <div class="hidden md:block">
                        <h1 class="text-2xl font-bold font-tech tracking-wider"
                            :class="{'text-gray-900': !darkMode, 'text-white': darkMode}">
                            GENERATION <span class="text-amber-500">TEAM</span>
                            <span class="text-xs text-amber-500 block font-amiri -mt-1">رمضان كريم 🌙</span>
                        </h1>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8 font-amiri">
                    <a href="#home" class="text-lg font-bold hover:text-amber-500 transition-colors"
                        :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">الرئيسية</a>
                    <a href="#team" class="text-lg font-bold hover:text-amber-500 transition-colors"
                        :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">عن الفريق</a>
                    <a href="#itclub" class="text-lg font-bold hover:text-amber-500 transition-colors"
                        :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">التعاون مع IT</a>
                    <a href="#leadership" class="text-lg font-bold hover:text-amber-500 transition-colors"
                        :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">القيادة والإشراف</a>
                    <a href="#project" class="text-lg font-bold hover:text-amber-500 transition-colors"
                        :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">عن المشروع</a>
                    <a href="{{ route('join.create') }}"
                        class="text-lg font-bold hover:text-amber-500 transition-colors"
                        :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">انضم إلينا</a>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4">
                    <!-- Theme Toggle -->
                    <button @click="toggleTheme()" class="p-2 rounded-full hover:bg-gray-700/20 transition-colors">
                        <i class="fas fa-sun text-yellow-500" x-show="!darkMode"></i>
                        <i class="fas fa-moon text-blue-400" x-show="darkMode"></i>
                    </button>

                    <a href="{{ route('login') }}"
                        class="px-6 py-2 rounded-full font-bold text-sm transition-all duration-300 border border-amber-500 hover:shadow-[0_0_20px_rgba(251,191,36,0.5)] font-amiri"
                        :class="{'text-amber-600 hover:bg-amber-50': !darkMode, 'text-amber-400 hover:bg-amber-500/10': darkMode}">
                        تسجيل الدخول
                    </a>

                    <!-- Mobile Menu Toggle -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg transition-colors"
                        :class="{'hover:bg-gray-100 text-gray-900': !darkMode, 'hover:bg-gray-800 text-white': darkMode}">
                        <i class="fas fa-bars text-xl" x-show="!mobileMenuOpen"></i>
                        <i class="fas fa-times text-xl" x-show="mobileMenuOpen" style="display: none;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenuOpen" style="display: none;"
            class="md:hidden absolute top-20 left-0 w-full border-t shadow-2xl backdrop-blur-md transition-all duration-300"
            :class="{'bg-white/95 border-gray-200': !darkMode, 'bg-dark/95 border-gray-800': darkMode}"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4">
            <div class="flex flex-col px-6 py-8 space-y-6 font-amiri border-b-4 border-amber-500">
                <a href="#home" @click="mobileMenuOpen = false"
                    class="text-xl font-bold flex items-center justify-between hover:text-amber-500 transition-colors"
                    :class="{'text-gray-800': !darkMode, 'text-gray-200': darkMode}">
                    <span>الرئيسية</span> <i class="fas fa-home text-sm opacity-50"></i>
                </a>
                <a href="#team" @click="mobileMenuOpen = false"
                    class="text-xl font-bold flex items-center justify-between hover:text-amber-500 transition-colors"
                    :class="{'text-gray-800': !darkMode, 'text-gray-200': darkMode}">
                    <span>عن الفريق</span> <i class="fas fa-users text-sm opacity-50"></i>
                </a>
                <a href="#itclub" @click="mobileMenuOpen = false"
                    class="text-xl font-bold flex items-center justify-between hover:text-amber-500 transition-colors"
                    :class="{'text-gray-800': !darkMode, 'text-gray-200': darkMode}">
                    <span>التعاون مع IT</span> <i class="fas fa-handshake text-sm opacity-50"></i>
                </a>
                <a href="#leadership" @click="mobileMenuOpen = false"
                    class="text-xl font-bold flex items-center justify-between hover:text-amber-500 transition-colors"
                    :class="{'text-gray-800': !darkMode, 'text-gray-200': darkMode}">
                    <span>القيادة والإشراف</span> <i class="fas fa-user-tie text-sm opacity-50"></i>
                </a>
                <a href="#project" @click="mobileMenuOpen = false"
                    class="text-xl font-bold flex items-center justify-between hover:text-amber-500 transition-colors"
                    :class="{'text-gray-800': !darkMode, 'text-gray-200': darkMode}">
                    <span>عن المشروع</span> <i class="fas fa-robot text-sm opacity-50"></i>
                </a>
                <hr class="border-gray-200 dark:border-gray-800 mb-2">
                <a href="{{ route('join.create') }}"
                    class="text-xl font-bold flex items-center justify-between text-amber-500 hover:text-amber-400 transition-colors">
                    <span>انضم إلينا</span> <i class="fas fa-arrow-left text-sm"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Video Background -->
        <video autoplay loop muted playsinline class="video-bg">
            <source src="{{ asset('assets/videos/login_bg.mp4') }}" type="video/mp4">
        </video>
        <div class="overlay" style="background: rgba(30, 27, 75, 0.7);"></div>

        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
            <div class="animate-fade-in space-y-8">
                <!-- Branding Badge -->
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass border border-amber-500/30 text-amber-400 text-sm font-bold mb-4 animate-slide-up">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    GENERATION TEAM IS HIRING
                </div>

                <!-- Main Title -->
                <h1 class="text-5xl md:text-7xl font-black leading-tight mb-6 animate-slide-up"
                    style="animation-delay: 0.2s">
                    مشروع روبوت <br>
                    <span class="text-gradient">بفكرة مختلفة تماماً</span>
                </h1>

                <!-- Quote -->
                <div class="max-w-3xl mx-auto bg-black/30 backdrop-blur-sm p-6 rounded-2xl border-r-4 border-amber-500 mb-8 animate-slide-up font-amiri"
                    style="animation-delay: 0.4s"
                    :class="{'bg-white/60 text-gray-900 shadow-lg': !darkMode, 'bg-black/40 text-gray-100': darkMode}">
                    <p class="text-xl md:text-3xl font-bold leading-relaxed">
                        "لو نفسك تشتغل على روبوت شبه بني آدم… بس مش أي روبوت 👀 <br>
                        روبوت بفكرة مختلفة تمامًا… الفكرة دي هتفهمها لما تنضم معانا في التيم 🤫🤖"
                    </p>
                </div>

                <!-- CTAs -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6 animate-slide-up"
                    style="animation-delay: 0.6s">
                    <a href="{{ route('join.create') }}"
                        class="group relative px-8 py-4 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white rounded-full font-bold text-lg transition-all hover:scale-105 shadow-[0_0_30px_rgba(245,158,11,0.5)] font-amiri">
                        <span class="relative z-10 flex items-center gap-3">
                            قدم طلب انضمام الآن
                            <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                        </span>
                    </a>
                    <a href="#project"
                        class="px-8 py-4 rounded-full font-bold text-lg border transition-all hover:scale-105 glass"
                        :class="{'border-gray-800 text-gray-800 hover:bg-gray-100': !darkMode, 'border-white/30 text-white hover:bg-white/10': darkMode}">
                        اكتشف المزيد
                    </a>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
            <a href="#team" class="text-amber-500 text-2xl">
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    </section>

    <!-- About Team Section -->
    <section id="team" class="py-24 relative overflow-hidden" :class="{'bg-white': !darkMode, 'bg-dark': darkMode}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-16 font-amiri">
                <h2 class="text-4xl font-bold mb-4 text-ramadan-night dark:text-gray-100">عن <span
                        class="text-amber-500">الفريق</span></h2>
                <div class="w-24 h-1 bg-amber-500 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div class="space-y-6 text-right order-2 lg:order-1 font-amiri">
                    <h3 class="text-2xl font-bold text-amber-500">Generation Team</h3>
                    <p class="text-lg leading-loose opacity-80"
                        :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">
                        "نحن فريق Generation Team، مجموعة من الطلاب الشغوفين بالتكنولوجيا والابتكار.
                        هدفنا ليس مجرد بناء روبوت، بل خلق تجربة هندسية متكاملة تجمع بين التصميم الميكانيكي،
                        الأنظمة المدمجة، والذكاء الاصطناعي."
                    </p>
                    <p class="text-lg leading-loose opacity-80"
                        :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">
                        نؤمن بأن العمل الجماعي هو سر النجاح، ونبحث دائمًا عن العقول المبدعة
                        لتكون جزءًا من رحلتنا نحو المستقبل.
                    </p>

                    <div class="grid grid-cols-2 gap-6 mt-8">
                        <div class="p-4 rounded-xl border hover-card glass-panel"
                            :class="{'bg-white border-amber-200': !darkMode, 'bg-ramadan-night border-amber-500/30': darkMode}">
                            <i class="fas fa-users text-3xl text-amber-500 mb-3"></i>
                            <h4 class="font-bold text-xl text-ramadan-night dark:text-gray-200">عمل جماعي</h4>
                            <p class="text-sm opacity-70">بيئة تعاونية محفزة</p>
                        </div>
                        <div class="p-4 rounded-xl border hover-card glass-panel"
                            :class="{'bg-white border-amber-200': !darkMode, 'bg-ramadan-night border-amber-500/30': darkMode}">
                            <i class="fas fa-lightbulb text-3xl text-amber-500 mb-3"></i>
                            <h4 class="font-bold text-xl text-ramadan-night dark:text-gray-200">ابتكار</h4>
                            <p class="text-sm opacity-70">أفكار خارج الصندوق</p>
                        </div>
                    </div>
                </div>

                <!-- Visual -->
                <div class="relative order-1 lg:order-2 flex justify-center">
                    <div
                        class="relative w-80 h-80 md:w-96 md:h-96 rounded-full overflow-hidden border-4 border-amber-500 shadow-[0_0_50px_rgba(251,191,36,0.3)] animate-float">
                        <div class="absolute inset-0 bg-blue-900/20 z-10"></div>
                        <img src="{{ asset('assets/gt_logo.jpg') }}"
                            class="w-full h-full object-cover transform scale-110" alt="Team Logo">
                    </div>
                    <!-- Decorative Elements -->
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- IT Club Collaboration Section (Slider) -->
    <section id="itclub" class="py-24 relative overflow-hidden select-none"
        :class="{'bg-gray-50': !darkMode, 'bg-gray-900': darkMode}" x-data="{ 
            activeSlide: 1, 
            maxSlides: 2, 
            autoInterval: null, 
            inactivityTimeout: null,
            startX: 0,
            currentX: 0,
            isDragging: false,
            startSlideShow() { 
                clearInterval(this.autoInterval);
                this.autoInterval = setInterval(() => { this.activeSlide = this.activeSlide === this.maxSlides ? 1 : this.activeSlide + 1; }, 40000); 
            }, 
            interact() { 
                clearInterval(this.autoInterval);
                clearTimeout(this.inactivityTimeout);
                this.inactivityTimeout = setTimeout(() => { this.startSlideShow(); }, 20000);
            }, 
            next() { 
                this.activeSlide = this.activeSlide === this.maxSlides ? 1 : this.activeSlide + 1; 
                this.interact();
            }, 
            prev() { 
                this.activeSlide = this.activeSlide === 1 ? this.maxSlides : this.activeSlide - 1; 
                this.interact();
            }, 
            goTo(slide) { 
                this.activeSlide = slide; 
                this.interact();
            },
            dragStart(e) {
                this.isDragging = true;
                this.startX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
                this.currentX = this.startX;
                this.interact();
            },
            dragMove(e) {
                if (!this.isDragging) return;
                this.currentX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
            },
            dragEnd() {
                if (!this.isDragging) return;
                this.isDragging = false;
                const diffX = this.startX - this.currentX;
                if (Math.abs(diffX) > 50) {
                    if (diffX > 0) this.next();
                    else this.prev();
                }
            }
        }" x-init="startSlideShow()" @mousedown="dragStart" @mousemove="dragMove" @mouseup="dragEnd"
        @mouseleave="dragEnd" @touchstart="dragStart" @touchmove="dragMove" @touchend="dragEnd">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16 font-amiri">
                <h2 class="text-4xl font-bold mb-4 text-ramadan-night dark:text-gray-100">التعاون مع <span
                        class="text-amber-500">IT Club</span></h2>
                <div class="w-24 h-1 bg-amber-500 mx-auto rounded-full"></div>
            </div>

            <!-- Slider Container -->
            <div class="relative min-h-[700px] lg:min-h-[500px]">

                <!-- Slide 1: Collaboration with IT Club -->
                <div x-show="activeSlide === 1" x-transition.opacity.duration.700ms class="absolute inset-0 w-full"
                    style="display: none;" :style="activeSlide === 1 ? 'display: block;' : 'display: none;'">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div class="relative order-1 lg:order-2 flex justify-center">
                            <div
                                class="relative w-full h-auto rounded-3xl overflow-hidden border-4 border-amber-500 shadow-[0_0_50px_rgba(251,191,36,0.2)] bg-[#1e293b]">
                                <img src="{{ asset('assets/itclub/generation-team_x_itclub.png') }}"
                                    class="w-full h-auto object-cover transform hover:scale-105 transition-transform duration-500"
                                    alt="Generation Team x IT Club Collaboration">
                            </div>
                            <div class="absolute -top-10 -left-10 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl">
                            </div>
                            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl">
                            </div>
                        </div>

                        <div class="space-y-6 order-2 lg:order-1 text-left font-tech" dir="ltr">
                            <div>
                                <h3 class="text-3xl font-bold text-amber-500 mb-2">Collaboration with IT Club</h3>
                            </div>

                            <div class="space-y-4 text-lg leading-relaxed opacity-80"
                                :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">
                                <p>Our collaboration with the IT Club at Borg El Arab Technological University has
                                    played a vital role in the success and development of the Generation Team project.
                                    The club provided continuous technical support, professional mentorship, and a
                                    productive environment that helped transform the project from an idea into a real,
                                    working system.</p>
                                <p>Through this collaboration, the team benefited from expert guidance in software
                                    development, system architecture, and project planning. The IT Club also helped
                                    organize the workflow, divide the project into clear phases, and improve the overall
                                    quality and professionalism of the final product.</p>
                                <p>This partnership represents a strong example of student collaboration, innovation,
                                    and real-world project development.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: About IT Club -->
                <div x-show="activeSlide === 2" x-transition.opacity.duration.700ms class="absolute inset-0 w-full"
                    style="display: none;" :style="activeSlide === 2 ? 'display: block;' : 'display: none;'">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div class="relative order-1 lg:order-2 flex justify-center">
                            <div
                                class="relative w-80 h-auto md:w-[400px] bg-white rounded-3xl overflow-hidden border-4 border-blue-500 shadow-[0_0_50px_rgba(59,130,246,0.2)] p-6 flex flex-col items-center justify-center">
                                <img src="{{ asset('assets/itclub/it_club.png') }}"
                                    class="w-full h-auto object-contain transform hover:scale-105 transition-transform duration-500"
                                    alt="IT Club Logo">
                            </div>
                            <div class="absolute -top-10 -left-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
                            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-purple-500/10 rounded-full blur-3xl">
                            </div>
                        </div>

                        <div class="space-y-6 order-2 lg:order-1 text-left font-tech" dir="ltr">
                            <div>
                                <h3 class="text-3xl font-bold text-blue-500 mb-2">About IT Club</h3>
                                <p class="text-xl font-semibold opacity-90"
                                    :class="{'text-gray-800': !darkMode, 'text-gray-200': darkMode}">Borg El Arab
                                    Technological University (BATU)</p>
                            </div>
                            <div class="space-y-4 text-lg leading-relaxed opacity-80"
                                :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">
                                <p>The IT Club is a dynamic, student-led organization at Borg El Arab Technological
                                    University (BATU). It focuses on developing students’ technical skills, encouraging
                                    innovation, and building a strong technology community.</p>
                                <p>The club organizes workshops, hackathons, and real-world projects to help students
                                    gain practical experience and connect with industry standards. It operates under the
                                    supervision of Prof. Dr. Osama Al-Nahhas, Head of the IT Department and Program
                                    Coordinator.</p>
                                <p>The IT Club plays an important role in supporting student projects and preparing
                                    future technology leaders.</p>
                            </div>

                            <!-- Social Links -->
                            <div class="flex flex-wrap items-center gap-4 mt-8">
                                <a href="https://www.facebook.com/profile.php?id=100092738074559" target="_blank"
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-600/10 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white transition-all transform hover:scale-110 shadow-lg border border-blue-600/20"
                                    title="Facebook">
                                    <i class="fab fa-facebook-f text-2xl"></i>
                                </a>
                                <a href="https://www.instagram.com/information.technology_club" target="_blank"
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-pink-500/10 text-pink-500 hover:bg-pink-500 hover:text-white transition-all transform hover:scale-110 shadow-lg border border-pink-500/20"
                                    title="Instagram">
                                    <i class="fab fa-instagram text-2xl"></i>
                                </a>
                                <a href="https://www.linkedin.com/company/itclub-1/posts/?feedView=all" target="_blank"
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-700/10 text-blue-700 dark:text-blue-400 hover:bg-blue-700 hover:text-white transition-all transform hover:scale-110 shadow-lg border border-blue-700/20"
                                    title="LinkedIn">
                                    <i class="fab fa-linkedin-in text-2xl"></i>
                                </a>
                                <a href="https://www.tiktok.com/@information.techn92" target="_blank"
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-900/10 text-gray-900 dark:text-gray-200 hover:bg-gray-900 dark:hover:bg-gray-600 hover:text-white transition-all transform hover:scale-110 shadow-lg border border-gray-900/20 dark:border-gray-600/50"
                                    title="TikTok">
                                    <i class="fab fa-tiktok text-2xl"></i>
                                </a>
                                <a href="https://itclub.sherifhamdy.tech/" target="_blank"
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all transform hover:scale-110 shadow-lg border border-emerald-500/20"
                                    title="Website">
                                    <i class="fas fa-globe text-2xl"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Navigation Controls -->
            <div class="flex items-center justify-center gap-6 mt-16 relative z-20" dir="ltr">
                <button aria-label="Next slide" @click="next()"
                    class="w-10 h-10 rounded-full flex items-center justify-center border transition-all glass hover:bg-amber-500 hover:text-white hover:border-amber-500"
                    :class="{'border-gray-300 text-gray-600': !darkMode, 'border-gray-700 text-gray-400': darkMode}">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="flex items-center gap-3">
                    <template x-for="i in maxSlides" :key="i">
                        <button :aria-label="'Go to slide ' + i" @click="goTo(i)"
                            class="h-2 rounded-full transition-all duration-300"
                            :class="activeSlide === i ? 'w-8 bg-amber-500' : (darkMode ? 'w-2 bg-gray-700 hover:bg-gray-500' : 'w-2 bg-gray-300 hover:bg-gray-400')">
                        </button>
                    </template>
                </div>
                <button aria-label="Previous slide" @click="prev()"
                    class="w-10 h-10 rounded-full flex items-center justify-center border transition-all glass hover:bg-amber-500 hover:text-white hover:border-amber-500"
                    :class="{'border-gray-300 text-gray-600': !darkMode, 'border-gray-700 text-gray-400': darkMode}">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Leadership & Supervision Section (Slider) -->
    <section id="leadership" class="py-24 relative overflow-hidden select-none"
        :class="{'bg-gray-50': !darkMode, 'bg-gray-900': darkMode}" x-data="{ 
            activeSlide: 1, 
            maxSlides: 4, 
            autoInterval: null, 
            inactivityTimeout: null,
            startX: 0,
            currentX: 0,
            isDragging: false,
            startSlideShow() { 
                clearInterval(this.autoInterval);
                this.autoInterval = setInterval(() => { this.activeSlide = this.activeSlide === this.maxSlides ? 1 : this.activeSlide + 1; }, 40000); 
            }, 
            interact() { 
                clearInterval(this.autoInterval);
                clearTimeout(this.inactivityTimeout);
                this.inactivityTimeout = setTimeout(() => { this.startSlideShow(); }, 20000);
            }, 
            next() { 
                this.activeSlide = this.activeSlide === this.maxSlides ? 1 : this.activeSlide + 1; 
                this.interact();
            }, 
            prev() { 
                this.activeSlide = this.activeSlide === 1 ? this.maxSlides : this.activeSlide - 1; 
                this.interact();
            }, 
            goTo(slide) { 
                this.activeSlide = slide; 
                this.interact();
            },
            dragStart(e) {
                this.isDragging = true;
                this.startX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
                this.currentX = this.startX;
                this.interact();
            },
            dragMove(e) {
                if (!this.isDragging) return;
                this.currentX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
            },
            dragEnd() {
                if (!this.isDragging) return;
                this.isDragging = false;
                const diffX = this.startX - this.currentX;
                if (Math.abs(diffX) > 50) {
                    if (diffX > 0) this.next();
                    else this.prev();
                }
            }
        }" x-init="startSlideShow()" @mousedown="dragStart" @mousemove="dragMove" @mouseup="dragEnd"
        @mouseleave="dragEnd" @touchstart="dragStart" @touchmove="dragMove" @touchend="dragEnd">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16 font-amiri">
                <h2 class="text-4xl font-bold mb-4 text-ramadan-night dark:text-gray-100">القيادة <span
                        class="text-amber-500">والإشراف</span></h2>
                <div class="w-24 h-1 bg-amber-500 mx-auto rounded-full"></div>
            </div>

            <!-- Slider Container -->
            <div class="relative min-h-[700px] lg:min-h-[500px]">

                <!-- Slide 1: Project Leader -->
                <div x-show="activeSlide === 1" x-transition.opacity.duration.700ms class="absolute inset-0 w-full"
                    style="display: none;" :style="activeSlide === 1 ? 'display: block;' : 'display: none;'">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div class="relative order-1 lg:order-2 flex justify-center">
                            <div
                                class="relative w-80 h-auto md:w-[400px] rounded-3xl overflow-hidden border-4 border-amber-500 shadow-[0_0_50px_rgba(251,191,36,0.2)]">
                                <img src="{{ asset('assets/team_leaders_photos/leader.png') }}"
                                    class="w-full h-auto object-cover transform hover:scale-105 transition-transform duration-500"
                                    alt="Omar Mehawed - Project Leader">
                            </div>
                            <div class="absolute -top-10 -left-10 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl">
                            </div>
                            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl">
                            </div>
                        </div>

                        <div class="space-y-6 order-2 lg:order-1 text-left font-tech" dir="ltr">
                            <div>
                                <h3 class="text-3xl font-bold text-amber-500 mb-2">Omar Mehawed</h3>
                                <p class="text-xl font-semibold opacity-90"
                                    :class="{'text-gray-800': !darkMode, 'text-gray-200': darkMode}">Project Leader</p>
                            </div>

                            <div class="space-y-4 text-lg leading-relaxed opacity-80"
                                :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">
                                <p>Omar Mehawed is an Information Technology student and Project Leader with a strong
                                    passion for software development, cybersecurity, and emerging technologies.</p>
                                <p>He is an active Head of the IT Club in the PR department and has participated in
                                    multiple technology workshops, including Artificial Intelligence, Internet of Things
                                    (IoT), and 3D Printing.</p>
                                <p>Omar is also working on innovative technology projects, including AI-powered smart
                                    glasses and advanced digital platforms, and has experience in full-stack web
                                    development and managing real-world technical projects.</p>
                                <p>His goal is to build impactful technology solutions and lead teams to create
                                    professional and scalable systems.</p>
                            </div>

                            <div class="flex items-center gap-4 mt-8">
                                <a href="https://wa.me/01009489979" target="_blank"
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-green-500/10 text-green-500 hover:bg-green-500 hover:text-white transition-all transform hover:scale-110 shadow-lg border border-green-500/20"
                                    title="WhatsApp">
                                    <i class="fab fa-whatsapp text-2xl"></i>
                                </a>
                                <a href="https://www.linkedin.com/in/omar-mehawed-861098249/" target="_blank"
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-600/10 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white transition-all transform hover:scale-110 shadow-lg border border-blue-600/20"
                                    title="LinkedIn">
                                    <i class="fab fa-linkedin-in text-2xl"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: Vice Leader -->
                <div x-show="activeSlide === 2" x-transition.opacity.duration.700ms class="absolute inset-0 w-full"
                    style="display: none;" :style="activeSlide === 2 ? 'display: block;' : 'display: none;'">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div class="relative order-1 lg:order-2 flex justify-center">
                            <div
                                class="relative w-80 h-auto md:w-[400px] rounded-3xl overflow-hidden border-4 border-purple-500 shadow-[0_0_50px_rgba(168,85,247,0.2)]">
                                <img src="{{ asset('assets/team_leaders_photos/Vice_Leader.jpeg') }}"
                                    class="w-full h-auto object-cover transform hover:scale-105 transition-transform duration-500"
                                    alt="Jana Tarek - Vice Leader">
                            </div>
                            <div class="absolute -top-10 -left-10 w-40 h-40 bg-purple-500/10 rounded-full blur-3xl">
                            </div>
                            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-pink-500/10 rounded-full blur-3xl">
                            </div>
                        </div>
                        <div class="space-y-6 order-2 lg:order-1 text-left font-tech" dir="ltr">
                            <div>
                                <h3 class="text-3xl font-bold text-purple-500 mb-2">Jana Tarek</h3>
                                <p class="text-xl font-semibold opacity-90"
                                    :class="{'text-gray-800': !darkMode, 'text-gray-200': darkMode}">Vice Leader</p>
                            </div>
                            <div class="space-y-4 text-lg leading-relaxed opacity-80"
                                :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">
                                <p>Jana Tarek is the Vice Leader of the project, supporting team management,
                                    coordination, and internal operations. She plays a key role in organizing the
                                    workflow, ensuring smooth communication, and helping achieve the project goals
                                    efficiently.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: Project Supervisor -->
                <div x-show="activeSlide === 3" x-transition.opacity.duration.700ms class="absolute inset-0 w-full"
                    style="display: none;" :style="activeSlide === 3 ? 'display: block;' : 'display: none;'">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div class="relative order-1 lg:order-2 flex justify-center">
                            <div
                                class="relative w-80 h-auto md:w-[400px] rounded-3xl overflow-hidden border-4 border-blue-500 shadow-[0_0_50px_rgba(59,130,246,0.2)]">
                                <img src="{{ asset('assets/team_leaders_photos/Eng.Mo7amed_elfayoumi.jpg') }}"
                                    class="w-full h-auto object-cover object-top transform hover:scale-105 transition-transform duration-500"
                                    alt="Mohamed El-Fayoumi - Project Supervisor">
                            </div>
                            <div class="absolute -top-10 -left-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
                            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-cyan-500/10 rounded-full blur-3xl">
                            </div>
                        </div>
                        <div class="space-y-6 order-2 lg:order-1 text-left font-tech" dir="ltr">
                            <div>
                                <h3 class="text-3xl font-bold text-blue-500 mb-2">Mohamed El-Fayoumi</h3>
                                <p class="text-xl font-semibold opacity-90"
                                    :class="{'text-gray-800': !darkMode, 'text-gray-200': darkMode}">Project Supervisor
                                </p>
                            </div>
                            <div class="space-y-4 text-lg leading-relaxed opacity-80"
                                :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">
                                <p>Mohamed El-Fayoumi is a Mechatronics Engineering student with strong interests in
                                    autonomous vehicles and machine learning, aiming to become a Self-Driving Car
                                    Engineer. He has developed professional and leadership experience through his
                                    volunteer work at Bibliotheca Alexandrina, his role as Head of Finance at TEDx
                                    AASTMT, and his work as an events organizer and part-time mathematics teacher. He
                                    supervises and guides the technical direction of the project.</p>
                            </div>
                            <!-- Social Links -->
                            <div class="flex items-center gap-4 mt-8">
                                <a href="https://www.linkedin.com/in/mohamedelfayoumi/" target="_blank"
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-600/10 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white transition-all transform hover:scale-110 shadow-lg border border-blue-600/20"
                                    title="LinkedIn">
                                    <i class="fab fa-linkedin-in text-2xl"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 4: Academic Supervisor -->
                <div x-show="activeSlide === 4" x-transition.opacity.duration.700ms class="absolute inset-0 w-full"
                    style="display: none;" :style="activeSlide === 4 ? 'display: block;' : 'display: none;'">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div class="relative order-1 lg:order-2 flex justify-center">
                            <div
                                class="relative w-80 h-auto md:w-[400px] rounded-3xl overflow-hidden border-4 border-emerald-500 shadow-[0_0_50px_rgba(16,185,129,0.2)]">
                                <img src="{{ asset('assets/team_leaders_photos/Osama_Eln7as.png') }}"
                                    class="w-full h-auto object-cover object-top transform hover:scale-105 transition-transform duration-500"
                                    alt="Dr. Osama El-Nahhas - Academic Supervisor">
                            </div>
                            <div class="absolute -top-10 -left-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl">
                            </div>
                            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-green-500/10 rounded-full blur-3xl">
                            </div>
                        </div>
                        <div class="space-y-6 order-2 lg:order-1 text-left font-tech" dir="ltr">
                            <div>
                                <h3 class="text-3xl font-bold text-emerald-500 mb-2">Dr. Osama El-Nahhas</h3>
                                <p class="text-xl font-semibold opacity-90"
                                    :class="{'text-gray-800': !darkMode, 'text-gray-200': darkMode}">Dean of the Faculty
                                    of Information Technology</p>
                            </div>
                            <div class="space-y-4 text-lg leading-relaxed opacity-80"
                                :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">
                                <p>Dr. Osama El-Nahhas is the Dean of the Faculty of Information Technology and provides
                                    academic supervision and official support for the project. His leadership ensures
                                    the project aligns with academic standards and institutional excellence.</p>
                            </div>
                            <!-- Social Links -->
                            <div class="flex items-center gap-4 mt-8">
                                <a href="https://www.linkedin.com/in/osama-elnahas-37666b127/" target="_blank"
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-600/10 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white transition-all transform hover:scale-110 shadow-lg border border-blue-600/20"
                                    title="LinkedIn">
                                    <i class="fab fa-linkedin-in text-2xl"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Navigation Controls -->
            <div class="flex items-center justify-center gap-6 mt-16 relative z-20">
                <button aria-label="Next slide" @click="next()"
                    class="w-10 h-10 rounded-full flex items-center justify-center border transition-all glass hover:bg-amber-500 hover:text-white hover:border-amber-500"
                    :class="{'border-gray-300 text-gray-600': !darkMode, 'border-gray-700 text-gray-400': darkMode}">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <div class="flex items-center gap-3" dir="ltr">
                    <template x-for="i in maxSlides" :key="i">
                        <button :aria-label="'Go to slide ' + i" @click="goTo(i)"
                            class="h-2 rounded-full transition-all duration-300"
                            :class="activeSlide === i ? 'w-8 bg-amber-500' : (darkMode ? 'w-2 bg-gray-700 hover:bg-gray-500' : 'w-2 bg-gray-300 hover:bg-gray-400')">
                        </button>
                    </template>
                </div>

                <button aria-label="Previous slide" @click="prev()"
                    class="w-10 h-10 rounded-full flex items-center justify-center border transition-all glass hover:bg-amber-500 hover:text-white hover:border-amber-500"
                    :class="{'border-gray-300 text-gray-600': !darkMode, 'border-gray-700 text-gray-400': darkMode}">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>

        </div>
    </section>

    <!-- About Project Section -->
    <section id="project" class="py-24" :class="{'bg-gray-50': !darkMode, 'bg-gray-900': darkMode}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 font-amiri">
                <h2 class="text-4xl font-bold mb-4 text-ramadan-night dark:text-gray-100">عن <span
                        class="text-amber-500">المشروع</span></h2>
                <div class="w-24 h-1 bg-amber-500 mx-auto rounded-full"></div>
                <p class="mt-6 text-xl max-w-2xl mx-auto opacity-80"
                    :class="{'text-gray-700': !darkMode, 'text-gray-300': darkMode}">
                    "إحنا شغالين على مشروع ضخم، ومحتاجين ناس جاهزة تتحرك بعد الميد تِرم مباشرة"
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 text-center">

                <!-- Card 1 -->
                <div class="p-8 rounded-3xl border hover-card group ramadan-card">
                    <div
                        class="w-16 h-16 bg-amber-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-microchip text-3xl text-amber-500 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Embedded Systems</h3>
                    <p class="opacity-70 text-sm">برمجة المتحكمات الدقيقة والتعامل مع الأنظمة المدمجة لبناء عقل الروبوت.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-8 rounded-3xl border hover-card group ramadan-card">
                    <div
                        class="w-16 h-16 bg-indigo-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-brain text-3xl text-indigo-500 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">AI & Voice</h3>
                    <p class="opacity-70 text-sm">تطوير مساعد ذكي يتفاعل Real-time ويفهم ويعالج الأوامر الصوتية.</p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 rounded-3xl border hover-card group ramadan-card">
                    <div
                        class="w-16 h-16 bg-emerald-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-cogs text-3xl text-emerald-500 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Mechanical Design</h3>
                    <p class="opacity-70 text-sm">تصميم وهيكلة أجزاء الروبوت ميكانيكيًا باستخدام الطباعة ثلاثية الأبعاد.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="p-8 rounded-3xl border hover-card group ramadan-card">
                    <div
                        class="w-16 h-16 bg-red-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-red-500 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-battery-full text-3xl text-red-500 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Power Systems</h3>
                    <p class="opacity-70 text-sm">إدارة الطاقة واختيار البطاريات المناسبة لضمان أداء مستقر وآمن.</p>
                </div>

                <!-- Card 5 -->
                <div class="p-8 rounded-3xl border hover-card group ramadan-card">
                    <div
                        class="w-16 h-16 bg-orange-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-orange-500 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-flask text-3xl text-orange-500 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Materials Science</h3>
                    <p class="opacity-70 text-sm">اختيار المواد المناسبة التي تجمع بين الخفة والقوة ومقاومة الظروف.</p>
                </div>

                <!-- Card 6 -->
                <div
                    class="p-8 rounded-3xl border bg-gradient-to-br from-ramadan-light to-ramadan-night text-white hover-card transform scale-105 shadow-2xl">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-rocket text-3xl text-amber-400"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4 font-amiri">انضم للمستقبل</h3>
                    <p class="opacity-90 text-sm mb-6 font-amiri">هل أنت مستعد لتكون جزءاً من هذا التحدي؟ انضم إلينا
                        الآن.</p>
                    <a href="{{ route('join.create') }}"
                        class="inline-block px-6 py-2 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-bold rounded-full hover:bg-amber-600 transition-colors font-amiri">
                        قدم الآن
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 text-center text-sm border-t"
        :class="{'bg-white border-amber-100 text-gray-600': !darkMode, 'bg-dark border-gray-800 text-gray-400': darkMode}">
        <p class="font-amiri">Copyright © 2026 <span class="text-amber-500 font-bold">Generation Team</span>. All rights
            reserved.</p>
    </footer>

</body>

</html>