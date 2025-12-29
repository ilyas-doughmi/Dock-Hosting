<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCK-HOSTING :: CHANGELOG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
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
    </style>
</head>
<body class="font-sans antialiased selection:bg-brand selection:text-black">

    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 glass-nav border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 h-24 flex items-center justify-between relative">
            <a href="../index.php" class="flex items-center gap-4 group">
                <div class="w-12 h-12 rounded-xl bg-brand text-black flex items-center justify-center text-xl shadow-[0_0_20px_rgba(45,212,191,0.3)]">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-2xl leading-none tracking-tight text-white group-hover:text-brand transition-colors">DOCK-HOSTING</span>
                    <span class="text-[10px] text-gray-500 font-mono tracking-widest uppercase mt-1">Beta v1.0.0</span>
                </div>
            </a>
            
            <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden text-gray-300 hover:text-white focus:outline-none p-2">
                <i class="fas fa-bars text-2xl"></i>
            </button>
            <a href="../index.php" class="hidden md:flex text-sm font-mono text-gray-400 hover:text-white transition-colors items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Home
            </a>
            <div id="mobile-menu" class="hidden absolute top-full left-0 right-0 bg-[#0a0a0a] border-b border-white/10 p-4 shadow-2xl md:hidden flex flex-col gap-4">
                 <a href="../index.php" class="text-sm font-mono text-white hover:text-brand px-4 py-3 hover:bg-white/5 rounded-lg transition-colors flex items-center">
                    <i class="fas fa-arrow-left mr-3"></i> Back to Home
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-6 pt-40 pb-20">
        <div class="mb-12">
            <h1 class="text-4xl font-bold mb-4">Platform Updates</h1>
            <p class="text-gray-400 font-mono text-sm">Track the evolution of the Dock-Hosting platform.</p>
        </div>

        <div class="space-y-12 relative border-l border-white/10 ml-3 pl-10">
            
            <!-- v1.1.0 -->
            <div class="relative">
                <span class="absolute -left-[45px] top-1 w-4 h-4 rounded-full bg-brand border-2 border-black ring-4 ring-brand/20"></span>
                <div class="flex items-baseline justify-between mb-4">
                    <h2 class="text-2xl font-bold">Database & Security Update <span class="text-brand text-sm font-mono ml-2">v1.1.0</span></h2>
                    <span class="text-xs font-mono text-gray-500">2025-12-28</span>
                </div>
                
                <div class="bg-[#0a0a0a] border border-white/10 rounded-2xl p-6">
                    <div class="mb-6">
                        <span class="px-3 py-1 rounded-full bg-brand/10 text-brand text-xs font-bold font-mono border border-brand/20">NEW RELEASE</span>
                    </div>
                    <ul class="space-y-4 text-gray-300">
                        <li class="flex gap-3">
                            <i class="fas fa-check-circle text-gray-500 mt-1"></i>
                            <div>
                                <strong class="text-white block">User Databases (MySQL)</strong>
                                <span class="text-sm text-gray-400">Every project now comes with full MySQL support. Create, delete and manage databases directly from the sidebar.</span>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-check-circle text-gray-500 mt-1"></i>
                            <div>
                                <strong class="text-white block">Enhanced Security</strong>
                                <span class="text-sm text-gray-400">Critical security hardening for all containers. Custom execution policies and malware prevention systems deployed.</span>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-check-circle text-gray-500 mt-1"></i>
                            <div>
                                <strong class="text-white block">Custom PHP Environments</strong>
                                <span class="text-sm text-gray-400">Resolved driver issues with custom Docker images featuring pre-installed PDO/MySQLi extensions.</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- v1.0.0 -->
            <div class="relative">
                <span class="absolute -left-[45px] top-1 w-4 h-4 rounded-full bg-brand border-2 border-black ring-4 ring-brand/20"></span>
                <div class="flex items-baseline justify-between mb-4">
                    <h2 class="text-2xl font-bold">Beta Flight <span class="text-brand text-sm font-mono ml-2">v1.0.0</span></h2>
                    <span class="text-xs font-mono text-gray-500">2025-12-27</span>
                </div>
                
                <div class="bg-[#0a0a0a] border border-white/10 rounded-2xl p-6">
                    <div class="mb-6">
                        <span class="px-3 py-1 rounded-full bg-brand/10 text-brand text-xs font-bold font-mono border border-brand/20">NEW RELEASE</span>
                    </div>
                    <ul class="space-y-4 text-gray-300">
                        <li class="flex gap-3">
                            <i class="fas fa-check-circle text-gray-500 mt-1"></i>
                            <div>
                                <strong class="text-white block">PHP Application Hosting</strong>
                                <span class="text-sm text-gray-400">Deploy generic PHP applications instantly by uploading a ZIP file. Support for index.php entry point.</span>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-check-circle text-gray-500 mt-1"></i>
                            <div>
                                <strong class="text-white block">Docker Containerization</strong>
                                <span class="text-sm text-gray-400">Isolated containers for every project with dedicated resources and secure networking.</span>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-check-circle text-gray-500 mt-1"></i>
                            <div>
                                <strong class="text-white block">Web Terminal</strong>
                                <span class="text-sm text-gray-400">Direct console access to your containers via the browser.</span>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-check-circle text-gray-500 mt-1"></i>
                            <div>
                                <strong class="text-white block">File Manager</strong>
                                <span class="text-sm text-gray-400">Edit code on the fly with our integrated web-based editor.</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </main>

    <footer class="border-t border-white/10 bg-black pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-6 flex flex-col items-center">
            <div class="flex items-center gap-2 mb-4">
                <span class="uppercase tracking-widest text-[10px] text-gray-600 font-mono">Created at</span>
                <a href="https://youcode.ma/" target="_blank" class="hover:opacity-100 opacity-60 transition-opacity">
                    <img src="https://youcode.ma/images/logos/youcode.png" alt="YouCode" class="h-6 filter brightness-0 invert">
                </a>
            </div>
            <p class="text-xs text-gray-600 font-mono">&copy; 2025 Dock-Hosting Platform.</p>
        </div>
    </footer>

</body>
</html>
