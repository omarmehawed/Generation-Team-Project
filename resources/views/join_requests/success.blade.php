<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generation Team - Application Submitted</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Rajdhani:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif'],
                        tech: ['Rajdhani', 'sans-serif'],
                    },
                    colors: {
                        dark: '#0f172a',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-dark text-white font-cairo min-h-screen flex items-center justify-center relative overflow-hidden">

    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none"
        style="background-image: radial-gradient(rgba(0, 243, 255, 0.1) 1px, transparent 1px); background-size: 30px 30px;">
    </div>

    {{-- Glow Effects --}}
    <div
        class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none">
    </div>
    <div
        class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none">
    </div>

    <div class="relative z-10 max-w-lg w-full px-4">
        <div
            class="bg-gray-800/50 backdrop-blur-xl border border-gray-700/50 rounded-3xl p-8 md:p-12 text-center shadow-2xl shadow-cyan-500/10 transform transition-all hover:scale-[1.01]">

            {{-- Success Icon --}}
            <div class="w-24 h-24 mx-auto mb-8 relative">
                <div class="absolute inset-0 bg-green-500/20 rounded-full blur-xl animate-pulse"></div>
                <div
                    class="w-full h-full bg-gray-900 border-2 border-green-500 rounded-full flex items-center justify-center relative z-10 shadow-[0_0_20px_rgba(34,197,94,0.3)]">
                    <i class="fas fa-check text-4xl text-green-500"></i>
                </div>
            </div>

            {{-- Title --}}
            <h1 class="text-3xl font-bold mb-4 font-tech tracking-wide">Application Submitted</h1>

            {{-- Message --}}
            <p class="text-gray-300 text-lg mb-2">
                Thank you! Your join request has been received successfully.
            </p>
            <p class="text-gray-400 text-sm mb-8" dir="rtl">
                شكراً لك! تم استلام طلب انضمامك بنجاح. سيقوم فريقنا بمراجعته والرد عليك في أقرب وقت ممكن.
            </p>

            {{-- Action Button --}}
            <a href="/"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold py-3.5 px-8 rounded-xl shadow-lg shadow-cyan-500/20 transition-all transform hover:-translate-y-1">
                <span>Return to Home</span>
                <i class="fas fa-arrow-right"></i>
            </a>

            {{-- Branding Footer --}}
            <div class="mt-8 pt-6 border-t border-gray-700/30">
                <p class="text-xs text-gray-500 font-tech tracking-widest uppercase">Generation Team © {{ date('Y') }}
                </p>
            </div>
        </div>
    </div>

</body>

</html>