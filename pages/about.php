<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCK-HOSTING :: About</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Space Grotesk', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        bg: '#000000',
                        brand: '#2dd4bf',
                    }
                }
            }
        }
    </script>
    <style>body { background-color: #000; color: #fff; }</style>
</head>
<body class="font-sans antialiased selection:bg-brand selection:text-black">
    
    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 p-6 flex justify-between items-center backdrop-blur-md border-b border-white/5 bg-black/50">
        <a href="../index.php" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-brand text-black flex items-center justify-center text-lg group-hover:shadow-[0_0_15px_rgba(45,212,191,0.4)] transition-all">
                <i class="fas fa-cubes"></i>
            </div>
            <span class="font-bold text-xl tracking-tight">DOCK-HOSTING</span>
        </a>
        <a href="../index.php" class="text-gray-400 hover:text-white transition-colors font-mono text-xs uppercase tracking-widest border border-white/10 px-4 py-2 rounded-full hover:bg-white/5">
            <i class="fas fa-arrow-left mr-2"></i> Back Home
        </a>
    </nav>

    <main class="min-h-screen flex items-center justify-center relative overflow-hidden px-6 py-24">
        
        <!-- Background Elements -->
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-brand/5 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-500/5 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="relative z-10 max-w-5xl w-full grid md:grid-cols-12 gap-12 items-center">
            
            <!-- Image Section (Left) -->
            <div class="md:col-span-5 relative group order-2 md:order-1">
                <div class="absolute inset-0 bg-brand/30 rounded-2xl blur-2xl group-hover:blur-3xl transition-all duration-700 opacity-50 group-hover:opacity-80"></div>
                <!-- Card Container -->
                <div class="relative rounded-2xl p-2 bg-gradient-to-br from-white/10 to-transparent border border-white/10 rotate-2 group-hover:rotate-0 transition-transform duration-500">
                    <img src="https://i.pinimg.com/1200x/a9/4e/64/a94e641d95373aa52dce0d8606610ae3.jpg" 
                         alt="Ilyas Doughmi" 
                         class="w-full aspect-[4/5] object-cover rounded-xl shadow-2xl filter brightness-90 contrast-110 group-hover:brightness-100 transition-all duration-500">
                </div>
            </div>

            <!-- Content Section (Right) -->
            <div class="md:col-span-7 space-y-8 order-1 md:order-2">
                
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-brand/20 bg-brand/5 text-brand text-[10px] font-mono uppercase tracking-widest mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></span>
                        About The Creator
                    </div>
                    
                    <h1 class="text-6xl md:text-7xl font-bold tracking-tighter leading-none mb-2">
                        ILYAS <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand via-teal-200 to-white">DOUGHMI</span>
                    </h1>
                    <p class="text-gray-500 font-mono text-sm tracking-widest uppercase">Full Stack Developer</p>
                </div>

                <div class="relative pl-6 border-l-2 border-brand/30">
                    <i class="fas fa-quote-left absolute -top-4 -left-3 text-2xl text-black bg-brand rounded-full p-2"></i>
                    <p class="text-2xl md:text-3xl font-light leading-relaxed text-gray-200 italic">
                        "I want to host my own website to share it, that's why I made it."
                    </p>
                </div>

                <div class="prose prose-invert text-gray-400 font-light">
                    <p>
                        Dock-Hosting isn't just a platform; it's a personal mission to simplify the complex world of containerized deployment. 
                        Built with a passion for clean code and seamless user experiences, it empowers developers to focus on what matters most: 
                        <b>their creations.</b>
                    </p>
                </div>

                <!-- Signature / Socials -->
                <div class="pt-8 flex flex-wrap gap-4">
                    <a href="https://github.com/ilyas-doughmi" target="_blank" class="px-6 py-3 rounded-xl border border-white/10 bg-white/5 hover:bg-white hover:text-black hover:scale-105 transition-all flex items-center gap-3 font-mono text-sm group">
                        <i class="fab fa-github text-xl"></i>
                        <span>GitHub</span>
                    </a>
                    <a href="https://linkedin.com/in/ilyas-doughmi" target="_blank" class="px-6 py-3 rounded-xl border border-white/10 bg-white/5 hover:bg-[#0077b5] hover:text-white hover:border-[#0077b5] hover:scale-105 transition-all flex items-center gap-3 font-mono text-sm">
                        <i class="fab fa-linkedin-in text-xl"></i>
                        <span>LinkedIn</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
