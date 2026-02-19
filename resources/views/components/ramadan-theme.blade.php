@if(config('app.ramadan_theme', false))
    <!-- Ramadan Theme Container -->
    <div id="ramadan-container" class="fixed inset-0 pointer-events-none z-[9999] overflow-hidden" aria-hidden="true">

        <!-- Golden Curtain Animation -->
        <div
            class="ramadan-curtain absolute inset-0 w-full h-full bg-gradient-to-b from-[#D4AF37] via-[#F4C430] to-transparent opacity-0 transform -translate-y-full">
        </div>

        <!-- Decorative Crescent Moon (Top Right) -->
        <div
            class="absolute top-0 right-0 p-4 md:p-8 opacity-90 drop-shadow-[0_0_15px_rgba(212,175,55,0.6)] animate-pulse-slow">
            <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg"
                class="w-16 h-16 md:w-24 md:h-24">
                <path
                    d="M85 50C85 69.33 69.33 85 50 85C35.86 85 23.68 76.61 18 64.5C21.6 66.73 25.84 68 30.5 68C49.83 68 65.5 52.33 65.5 33C65.5 24.1 62.17 15.98 56.68 9.85C72.8 13.92 85 28.53 85 50Z"
                    fill="url(#moon-gradient)" stroke="#F4C430" stroke-width="1" />
                <defs>
                    <linearGradient id="moon-gradient" x1="20" y1="20" x2="80" y2="80" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#F4C430" />
                        <stop offset="100%" stop-color="#D4AF37" />
                    </linearGradient>
                </defs>
                <!-- Hanging Lantern (Fanous) attached to Moon -->
                <line x1="85" y1="50" x2="85" y2="70" stroke="#D4AF37" stroke-width="1" />
                <rect x="80" y="70" width="10" height="15" rx="2" fill="#F4C430" class="animate-swing origin-top" />
            </svg>
        </div>

        <!-- Falling Particles Canvas -->
        <canvas id="ramadan-canvas" class="absolute inset-0 w-full h-full"></canvas>

    </div>

    <style>
        /* Animation Keyframes */
        @keyframes curtainSweep {
            0% {
                transform: translateY(-100%);
                opacity: 0.8;
            }

            50% {
                opacity: 0.4;
            }

            100% {
                transform: translateY(100%);
                opacity: 0;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes pulse-slow {

            0%,
            100% {
                text-shadow: 0 0 10px #D4AF37, 0 0 20px #D4AF37;
                filter: drop-shadow(0 0 5px rgba(212, 175, 55, 0.5));
                transform: scale(1);
            }

            50% {
                text-shadow: 0 0 20px #D4AF37, 0 0 30px #FFD700;
                filter: drop-shadow(0 0 15px rgba(212, 175, 55, 0.8));
                transform: scale(1.05);
            }
        }

        @keyframes swing {
            0% {
                transform: rotate(-5deg);
            }

            50% {
                transform: rotate(5deg);
            }

            100% {
                transform: rotate(-5deg);
            }
        }

        .ramadan-curtain {
            animation: curtainSweep 2.5s cubic-bezier(0.4, 0.0, 0.2, 1) forwards;
            background: linear-gradient(180deg, rgba(212, 175, 55, 0.4) 0%, rgba(244, 196, 48, 0.1) 50%, transparent 100%);
            backdrop-filter: blur(2px);
        }

        .animate-pulse-slow {
            animation: pulse-slow 4s infinite ease-in-out;
        }

        .animate-swing {
            animation: swing 3s infinite ease-in-out;
            transform-origin: top center;
        }

        /* 
               Global Theme Overrides for Ramadan 
               Should work in both Dark and Light modes
            */
        :root {
            /* Override Accents with Gold */
            --primary: #D4AF37 !important;
            /* Metallic Gold */
            --primary-hover: #B4941F !important;
            /* Darker Gold */
            --accent: #10B981 !important;
            /* Emerald Green (Classic Ramadan pairing) */
            --glow: 0 0 15px rgba(212, 175, 55, 0.4) !important;
        }

        /* Specific Dark Mode Adjustments */
        [data-theme="dark"] {
            --bg-main: #0c1220 !important;
            /* Deep Night Blue */
            --bg-panel: #131c2e !important;
            --border: #D4AF37 !important;
            /* Gold Borders */
            --text-muted: #94a3b8 !important;
        }

        [data-theme="dark"] .sidebar-link.active {
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.15) 0%, transparent 100%) !important;
            border-left-color: #D4AF37 !important;
        }

        /* Button & Link Gold Accents */
        a:hover,
        button:hover {
            color: #D4AF37;
            /* Force Gold on Hover */
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('ramadan-canvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            let width, height;
            let particles = [];

            // Configuration
            const particleCount = 20; // Lightweight
            const symbols = ['★', '☾', '✦'];

            function resize() {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }

            class Particle {
                constructor() {
                    this.reset(true);
                }

                reset(initial = false) {
                    this.x = Math.random() * width;
                    this.y = initial ? Math.random() * height : -20;
                    this.speed = 0.5 + Math.random() * 1.5; // Slow fall
                    this.size = 10 + Math.random() * 15;
                    this.symbol = symbols[Math.floor(Math.random() * symbols.length)];
                    this.opacity = 0.3 + Math.random() * 0.5;
                    this.rotation = Math.random() * 360;
                    this.rotationSpeed = (Math.random() - 0.5) * 0.5;
                    this.oscillation = Math.random() * 2; // Horizontal sway
                }

                update() {
                    this.y += this.speed;
                    this.rotation += this.rotationSpeed;
                    this.x += Math.sin(this.y * 0.01) * 0.5; // Gentle sway

                    if (this.y > height + 20) {
                        this.reset();
                    }
                }

                draw() {
                    ctx.save();
                    ctx.translate(this.x, this.y);
                    ctx.rotate(this.rotation * Math.PI / 180);
                    ctx.globalAlpha = this.opacity;
                    ctx.fillStyle = '#D4AF37'; // Gold
                    ctx.font = `${this.size}px serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(this.symbol, 0, 0);
                    ctx.restore();
                }
            }

            function init() {
                resize();
                for (let i = 0; i < particleCount; i++) {
                    particles.push(new Particle());
                }
                animate();
            }

            function animate() {
                ctx.clearRect(0, 0, width, height);
                particles.forEach(p => {
                    p.update();
                    p.draw();
                });
                requestAnimationFrame(animate);
            }

            window.addEventListener('resize', resize);
            init();
        });
    </script>
@endif