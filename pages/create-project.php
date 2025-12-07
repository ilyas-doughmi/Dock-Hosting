<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCK-HOSTING :: NEW DEPLOYMENT</title>
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
                            DEFAULT: '#2dd4bf', // Teal-400
                            dim: 'rgba(45, 212, 191, 0.1)',
                            glow: 'rgba(45, 212, 191, 0.5)'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #000; color: #fff; }
        
        /* Sidebar Glass */
        .sidebar {
            background: rgba(5, 5, 5, 0.9);
            border-right: 1px solid #1f1f1f;
        }

        /* Card Style */
        .glass-panel {
            background: #0a0a0a;
            border: 1px solid #1f1f1f;
        }

        .input-field {
            background: #050505;
            border: 1px solid #1f1f1f;
            color: white;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: #2dd4bf;
            outline: none;
            box-shadow: 0 0 0 1px #2dd4bf;
        }

        /* Upload Zone Animation */
        .upload-zone {
            background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='12' ry='12' stroke='%23333' stroke-width='2' stroke-dasharray='10%2c 10' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
            transition: all 0.3s ease;
        }
        .upload-zone:hover, .upload-zone.dragover {
            background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='12' ry='12' stroke='%232dd4bf' stroke-width='2' stroke-dasharray='10%2c 10' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
            background-color: rgba(45, 212, 191, 0.05);
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #000; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
    </style>
</head>
<body class="h-screen w-full flex overflow-hidden font-sans selection:bg-brand selection:text-black">

    <!-- SIDEBAR -->
    <aside class="w-64 sidebar flex flex-col justify-between h-full z-20">
        <div>
            <!-- Logo -->
            <div class="h-20 flex items-center px-6 border-b border-border">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-brand/10 border border-brand/20 flex items-center justify-center text-brand">
                        <i class="fas fa-cubes text-sm"></i>
                    </div>
                    <span class="font-bold tracking-tight text-lg">DOCK<span class="text-brand">.HOST</span></span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-2">
                <div class="px-4 py-2 text-xs font-mono text-gray-500 uppercase tracking-wider">Main</div>
                <a href="user_dashboard.html" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all group">
                    <i class="fas fa-terminal w-5 group-hover:text-brand transition-colors"></i>
                    <span class="font-medium text-sm">Console</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-brand/10 text-brand rounded-lg border border-brand/20 transition-all">
                    <i class="fas fa-plus-circle w-5"></i>
                    <span class="font-medium text-sm">New Project</span>
                </a>
            </nav>
        </div>

        <!-- User Profile -->
        <div class="p-4 border-t border-border">
            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 cursor-pointer transition-colors">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-gray-700 to-gray-900 border border-border flex items-center justify-center font-bold text-gray-300">JD</div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm truncate">John Doe</div>
                    <div class="text-xs text-gray-500 font-mono truncate">student@youcode.ma</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col relative overflow-hidden bg-bg">
        
        <!-- Background Grid -->
        <div class="absolute inset-0 z-0 opacity-10 pointer-events-none" 
             style="background-image: linear-gradient(#1f1f1f 1px, transparent 1px), linear-gradient(90deg, #1f1f1f 1px, transparent 1px); background-size: 40px 40px;">
        </div>

        <!-- Top Header -->
        <header class="h-20 border-b border-border flex items-center justify-between px-8 bg-black/50 backdrop-blur z-10">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="user_dashboard.html" class="hover:text-brand transition-colors">Console</a>
                <span>/</span>
                <span class="text-white">Deploy</span>
            </div>
        </header>

        <!-- Form Area -->
        <div class="flex-1 overflow-y-auto p-8 relative z-10 flex justify-center">
            
            <div class="w-full max-w-2xl">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold tracking-tight mb-2">Deploy New Instance</h1>
                    <p class="text-gray-500 text-sm font-mono">Upload your PHP code to initialize a new container.</p>
                </div>

                <form action="../includes/create-project.php" method="POST" enctype="multipart/form-data" class="glass-panel p-8 rounded-xl space-y-8">
                    
                    <!-- 1. Project Name -->
                    <div class="space-y-2">
                        <label class="text-sm font-mono text-gray-400 uppercase tracking-wide ml-1">Project Name</label>
                        <div class="relative">
                            <i class="fas fa-tag absolute left-4 top-3.5 text-gray-600"></i>
                            <input required type="text" name="project_name" placeholder="e.g. My Portfolio" 
                                   class="input-field w-full py-3 pl-10 pr-4 rounded-lg text-sm placeholder-gray-700 font-mono focus:ring-1 focus:ring-brand" required>
                        </div>
                        <p class="text-[10px] text-gray-600 ml-1">This will be used to identify your container.</p>
                    </div>

                    <!-- 2. File Upload -->
                    <div class="space-y-2">
                        <label class="text-sm font-mono text-gray-400 uppercase tracking-wide ml-1">Source Code (.zip)</label>
                        
                        <div class="upload-zone rounded-xl p-8 text-center cursor-pointer relative" id="drop-zone">
                            <input type="file" required name="files" id="file-input" accept=".zip" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                            
                            <div class="flex flex-col items-center justify-center space-y-4 pointer-events-none">
                                <div class="w-16 h-16 bg-brand/10 rounded-full flex items-center justify-center text-brand mb-2">
                                    <i class="fas fa-cloud-upload-alt text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-lg" id="file-label">Click or Drag ZIP file here</p>
                                    <p class="text-sm text-gray-500 mt-1 font-mono">Max size: 10MB</p>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-600 ml-1">Ensure your zip contains an index.php file in the root.</p>
                    </div>

                    <!-- Submit -->
                    <div class="pt-4 border-t border-border">
                        <button type="submit" class="w-full bg-brand hover:bg-[#14b8a6] text-black font-bold py-4 rounded-lg transition-all transform hover:scale-[1.01] shadow-[0_0_20px_rgba(45,212,191,0.2)] flex items-center justify-center gap-2">
                            <span>Launch Container</span>
                            <i class="fas fa-rocket"></i>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </main>

    <script>
        const fileInput = document.getElementById('file-input');
        const fileLabel = document.getElementById('file-label');
        const dropZone = document.getElementById('drop-zone');

        // Drag effects
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.remove('dragover');
            });
        });

        // File Selection Logic
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                fileLabel.innerHTML = `<span class="text-brand">${fileName}</span> selected`;
            }
        });
    </script>
</body>
</html>