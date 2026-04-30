<div id="eid-container" class="fixed inset-0 pointer-events-none z-[9999] overflow-hidden" aria-hidden="true">
    
    <!-- Floating Festive Header banner or accent -->
    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-pink-500 via-amber-400 to-teal-400 drop-shadow-[0_2px_10px_rgba(0,0,0,0.05)]"></div>

    <!-- Hanging Festive Balloons SVG (Top Right) -->
    <div class="absolute top-0 right-10 p-4 md:p-8 animate-float drop-shadow-[0_0_15px_rgba(251,191,36,0.3)]">
        <svg width="120" height="120" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 md:w-32 md:h-32">
            <!-- Balloon 1 (Gold/Amber) -->
            <g class="animate-sway-slow origin-top">
                <!-- String -->
                <path d="M45 50 Q50 70 48 90" stroke="#d1d5db" stroke-width="1" fill="none" />
                <ellipse cx="45" cy="40" rx="15" ry="18" fill="url(#ball1)" />
                <path d="M43 57 L47 57 L45 59 Z" fill="#2596be" />
                <ellipse cx="40" cy="35" rx="3" ry="5" fill="#ffffff" opacity="0.4" />
            </g>

            <!-- Balloon 2 (Pink/Red) -->
            <g style="animation: sway 4s infinite ease-in-out; transform-origin: 55px 10px;">
                <!-- String -->
                <path d="M60 55 Q55 75 58 95" stroke="#d1d5db" stroke-width="1" fill="none" />
                <ellipse cx="60" cy="45" rx="13" ry="16" fill="url(#ball2)" />
                <path d="M58 60 L62 60 L60 62 Z" fill="#be185d" />
                <ellipse cx="56" cy="40" rx="2" ry="4" fill="#ffffff" opacity="0.4" />
            </g>

            <defs>
                <radialGradient id="ball1" cx="30%" cy="30%" r="70%">
                    <stop offset="0%" stop-color="#FDE047" />
                    <stop offset="50%" stop-color="#EAB308" />
                    <stop offset="100%" stop-color="#A16207" />
                </radialGradient>
                <radialGradient id="ball2" cx="30%" cy="30%" r="70%">
                    <stop offset="0%" stop-color="#F472B6" />
                    <stop offset="50%" stop-color="#DB2777" />
                    <stop offset="100%" stop-color="#831843" />
                </radialGradient>
            </defs>
        </svg>
    </div>

    <!-- Hanging Banner (Top Left) -->
    <div class="absolute top-0 left-10 p-4 md:p-8 animate-swing-slow origin-top drop-shadow-[0_0_10px_rgba(0,0,0,0.05)]">
        <div class="bg-gradient-to-br from-amber-400 to-yellow-500 text-white font-bold px-4 py-2 rounded-b-xl shadow-lg border-t-0 border border-yellow-300">
            <span class="text-xs tracking-widest font-tech text-slate-800">EID MUBARAK</span>
        </div>
    </div>

    <!-- Festive Particles Canvas -->
    <canvas id="eid-canvas" class="absolute inset-0 w-full h-full"></canvas>

</div>

<style>
    /* Eid Theme Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    @keyframes sway {
        0%, 100% { transform: rotate(-3deg); }
        50% { transform: rotate(3deg); }
    }

    @keyframes sway-slow {
        0%, 100% { transform: rotate(-1.5deg); }
        50% { transform: rotate(1.5deg); }
    }

    .animate-float {
        animation: float 4s infinite ease-in-out;
    }

    .animate-sway-slow {
        animation: sway-slow 5s infinite ease-in-out;
    }

    .animate-swing-slow {
        animation: sway-slow 6s infinite ease-in-out;
        transform-origin: top center;
    }

    /* Soft accents to lift website warmth without breaking variables */
    :root {
        --glow: 0 0 20px rgba(251, 191, 36, 0.25) !important;
    }

    /* Add subtle shadow to hover panels just to feel special */
    .glass-panel:hover, .sidebar-link:hover {
        box-shadow: 0 4px 25px -5px rgba(251, 191, 36, 0.15) !important;
    }

</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('eid-canvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];
        const particleCount = 40; // Dense and lively

        // Vibrant Festive Mix
        const colors = [
            '#FF6B6B', // Red
            '#FFD93D', // Yellow/Gold
            '#6BCB77', // Green
            '#4D96FF', // Blue
            '#F472B6', // Pink
            '#A78BFA'  // Purple
        ];

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }

        class Confetti {
            constructor() {
                this.reset(true);
            }

            reset(initial = false) {
                this.x = Math.random() * width;
                this.y = initial ? Math.random() * height : -20;
                this.size = 6 + Math.random() * 8;
                this.speedY = 1.5 + Math.random() * 2.5; // Weightless fall
                this.speedX = (Math.random() - 0.5) * 1.5;
                this.color = colors[Math.floor(Math.random() * colors.length)];
                this.opacity = 0.7 + Math.random() * 0.3;
                this.rotation = Math.random() * 360;
                this.rotationSpeed = (Math.random() - 0.5) * 5;
                this.shape = Math.random() > 0.5 ? 'rect' : 'circle';
                this.oscillation = Math.random() * 3;
            }

            update() {
                this.y += this.speedY;
                this.x += this.speedX + Math.sin(this.y * 0.03 + this.oscillation) * 0.6; // Soft sway
                this.rotation += this.rotationSpeed;

                if (this.y > height + 20) {
                    this.reset();
                }
            }

            draw() {
                ctx.save();
                ctx.translate(this.x, this.y);
                ctx.rotate(this.rotation * Math.PI / 180);
                ctx.globalAlpha = this.opacity;
                ctx.fillStyle = this.color;

                if (this.shape === 'rect') {
                    ctx.fillRect(-this.size / 2, -this.size / 2, this.size, this.size * 1.3);
                } else {
                    ctx.beginPath();
                    ctx.arc(0, 0, this.size / 2, 0, Math.PI * 2);
                    ctx.fill();
                }
                ctx.restore();
            }
        }

        function init() {
            resize();
            particles = [];
            for (let i = 0; i < particleCount; i++) {
                particles.push(new Confetti());
            }
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
        animate();
    });
</script>
