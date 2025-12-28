<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCK-HOSTING :: Professional Cloud</title>
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
                        panel: '#0a0a0a',
                        border: '#1f1f1f',
                        brand: {
                            DEFAULT: '#2dd4bf', 
                            hover: '#14b8a6',
                            dim: '#115e59',
                        }
                    },
                    backgroundImage: {
                        'grid-pattern': "linear-gradient(#1f1f1f 1px, transparent 1px), linear-gradient(90deg, #1f1f1f 1px, transparent 1px)",
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #000; color: #fff; }
        
        .glass-nav {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid #1f1f1f;
        }

        .hero-glow {
            box-shadow: 0 0 150px 60px rgba(45, 212, 191, 0.15);
        }

        .feature-card {
            background: rgba(10, 10, 10, 0.5);
            border: 1px solid #1f1f1f;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            border-color: #2dd4bf;
            transform: translateY(-5px);
            box-shadow: 0 10px 40px -10px rgba(45, 212, 191, 0.2);
        }
    </style>
</head>
<body class="font-sans antialiased selection:bg-brand selection:text-black">

    <!-- Navbar -->
    <!-- Navbar -->
    <!-- Announcement Banner -->
    <div class="fixed top-0 left-0 right-0 z-[60] bg-brand text-black text-xs font-mono font-bold py-2 text-center transform transition-transform duration-300 hover:py-3 group cursor-pointer" onclick="window.location.href='pages/changelog.php'">
        <span class="animate-pulse mr-2">●</span>
        NEW UPDATE v1.1.0: User Databases & Security Hardening Available Now
        <span class="ml-2 opacity-60 group-hover:opacity-100 transition-opacity underline">Read Changelog &rarr;</span>
    </div>

    <!-- Navbar -->
    <nav class="fixed top-12 left-0 right-0 z-50 flex justify-center px-4">
        <div class="w-full max-w-5xl bg-black/40 backdrop-blur-xl border border-white/10 rounded-full px-6 py-4 flex items-center justify-between shadow-[0_8px_32px_rgba(0,0,0,0.5)] transition-all duration-300 hover:border-brand/20 hover:bg-black/50">
            <!-- Logo -->
            <a href="index.php" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-full bg-brand text-black flex items-center justify-center text-lg shadow-[0_0_15px_rgba(45,212,191,0.2)] group-hover:shadow-[0_0_25px_rgba(45,212,191,0.4)] group-hover:scale-110 transition-all duration-300">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-lg tracking-tight text-white group-hover:text-brand transition-colors">DOCK-HOSTING</span>
                </div>
            </a>

            <!-- Actions -->
            <div class="flex items-center gap-6">
                
                <div class="hidden md:flex items-center gap-6 border-r border-white/10 pr-6 mr-2">
                     <a href="pages/about.php" class="text-xs font-mono font-medium text-gray-400 hover:text-white transition-colors uppercase tracking-widest relative group">
                        About
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-brand transition-all group-hover:w-full"></span>
                     </a>
                     
                     <a href="pages/changelog.php" class="flex items-center gap-2 text-[10px] font-bold font-mono text-gray-500 uppercase tracking-widest hover:text-brand transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></span>
                        Updates
                    </a>
                </div>

                <?php if(isset($_SESSION["id"])): ?>
                    <a href="pages/dashboard.php" class="text-xs font-mono font-medium text-white hover:text-brand transition-colors uppercase tracking-widest">Dashboard</a>
                    <a href="includes/user_actions/logout.php" class="w-8 h-8 rounded-full border border-white/10 bg-white/5 flex items-center justify-center text-gray-400 hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/30 transition-all">
                        <i class="fas fa-power-off text-xs"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="hidden sm:block text-xs font-mono font-medium text-gray-400 hover:text-white transition-colors uppercase tracking-widest">Sign In</a>
                    <a href="login.php?view=register" class="px-6 py-2.5 rounded-full bg-brand hover:bg-[#14b8a6] text-black text-xs font-bold font-mono transition-all hover:scale-105 shadow-[0_0_20px_rgba(45,212,191,0.2)] hover:shadow-[0_0_30px_rgba(255,255,255,0.3)]">
                        GET STARTED
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Hero Content -->
    <main class="relative h-screen flex flex-col items-center justify-center overflow-hidden pt-20">
        
        <!-- Animated Background -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 opacity-20 bg-grid-pattern bg-[length:60px_60px] animate-[pulse_4s_ease-in-out_infinite]"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-brand/10 rounded-full blur-[150px] pointer-events-none"></div>
            <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-purple-500/5 rounded-full blur-[150px] pointer-events-none"></div>
        </div>

        <div class="relative z-10 max-w-5xl mx-auto px-6 text-center">
            
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full border border-white/10 bg-white/5 backdrop-blur-md text-gray-300 text-xs font-mono mb-10 animate-[fade-in_1s_ease-out] hover:border-brand/30 transition-colors cursor-default">
                <span class="w-2 h-2 rounded-full bg-brand animate-pulse"></span>
                <span>Beta Access Now Open</span>
            </div>

            <h1 class="text-6xl md:text-8xl font-bold tracking-tighter mb-8 leading-[1.1] animate-[slide-up_0.8s_ease-out] drop-shadow-2xl">
                Deploy <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-brand">PHP Projects</span> <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand via-teal-200 to-white">From a Single ZIP.</span>
            </h1>

            <p class="text-xl md:text-2xl text-gray-400 max-w-2xl mx-auto mb-12 leading-relaxed font-light animate-[slide-up_1s_ease-out]">
                Upload your archive and get a live URL in seconds. <br class="hidden md:block">
                Powered by Docker containers for full isolation.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 animate-[slide-up_1.2s_ease-out]">
                <a href="login.php" class="relative w-full sm:w-auto px-10 py-5 rounded-full bg-brand hover:bg-white text-black font-bold text-lg transition-all transform hover:scale-105 shadow-[0_0_40px_rgba(45,212,191,0.4)] hover:shadow-[0_0_60px_rgba(45,212,191,0.6)] flex items-center justify-center gap-3 group overflow-hidden">
                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    <span class="relative">DEPLOY PHP APP</span>
                    <i class="fas fa-cloud-upload-alt relative group-hover:-translate-y-1 transition-transform duration-300"></i>
                </a>
            </div>

            <!-- Tech Badges -->
            <div class="mt-24 flex justify-center gap-10 opacity-30 hover:opacity-100 transition-all duration-700 grayscale hover:grayscale-0">
                <i class="fab fa-docker text-5xl hover:scale-110 transition-transform duration-300"></i>
                <i class="fab fa-php text-5xl hover:scale-110 transition-transform duration-300"></i>
                <i class="fab fa-linux text-5xl hover:scale-110 transition-transform duration-300"></i>
                <i class="fas fa-database text-5xl hover:scale-110 transition-transform duration-300"></i>
            </div>

        </div>

        <!-- Footer Strip -->
        <div class="absolute bottom-0 w-full border-t border-white/5 bg-black/40 backdrop-blur-sm py-6">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-mono text-gray-500">
                <span>&copy; 2025 DOCK-HOSTING</span>
                <div class="flex items-center gap-2">
                    <span class="uppercase tracking-widest">Created at</span>
                    <a href="https://youcode.ma/" target="_blank" class="hover:opacity-100 opacity-60 transition-opacity">
                        <img src="https://youcode.ma/images/logos/youcode.png" alt="YouCode" class="h-6 filter brightness-0 invert">
                    </a>
                </div>
            </div>
        </div>
    </main>


</body>
</html>