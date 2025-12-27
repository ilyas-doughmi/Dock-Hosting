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

    <?php if(isset($_GET["msg"])){
        $message = htmlspecialchars($_GET["msg"]);
        $type = $_GET["type"] ?? "success";
        
        $bg_color = $type === "error" ? 'bg-red-900/40 border-red-500/30 text-red-200' : 'bg-green-900/40 border-green-500/30 text-green-200';
        $icon_color = $type === "error" ? 'text-red-400' : 'text-green-400';
        $icon = $type === "error" ? 'fa-circle-exclamation' : 'fa-circle-check';
    ?>
        <div class="fixed top-5 right-5 w-full max-w-md <?= $bg_color ?> px-4 py-3 rounded-xl 
            font-mono text-sm shadow-xl z-50 backdrop-blur-md flex items-center justify-between animate-slide-up">
            <span class="flex items-center gap-3"><i class="fas <?= $icon ?> <?= $icon_color ?>"></i> <?= $message ?></span>
            <button onclick="this.parentElement.remove()" class="<?= $icon_color ?> hover:text-white transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <script>
            setTimeout(() => {
                const notification = document.querySelector('.animate-slide-up');
                if (notification) notification.remove();
            }, 5000);
        </script>
    <?php } ?>

    <?php include_once("../components/sidebar.php") ?>


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

                <form action="../includes/create-project.php" method="POST" class="glass-panel p-8 rounded-xl space-y-8">
                    
                    <!-- 1. Project Name -->
                    <div class="space-y-4">
                        <label class="text-sm font-mono text-gray-400 uppercase tracking-wide ml-1">Project Name</label>
                        <div class="relative">
                            <i class="fas fa-tag absolute left-4 top-3.5 text-gray-600"></i>
                            <input required type="text" name="project_name" placeholder="e.g. My Portfolio" 
                                   class="input-field w-full py-3 pl-10 pr-4 rounded-lg text-sm placeholder-gray-700 font-mono focus:ring-1 focus:ring-brand" required>
                        </div>
                        <p class="text-[10px] text-gray-600 ml-1">This will be used to identify your container.</p>
                    </div>

                    <!-- 2. Framework Selection -->
                    <div class="space-y-4">
                        <label class="text-sm font-mono text-gray-400 uppercase tracking-wide ml-1">Select Framework</label>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <!-- PHP Option (Active) -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="framework" value="php" checked class="peer sr-only">
                                <div class="p-4 rounded-xl border border-[#333] bg-[#050505] peer-checked:border-brand peer-checked:bg-brand/5 hover:border-gray-500 transition-all duration-300 flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 flex items-center justify-center">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/27/PHP-logo.svg" alt="PHP" class="h-10 w-auto">
                                    </div>
                                    <div class="text-center">
                                        <div class="font-bold text-sm text-white group-hover:text-brand transition-colors">PHP Native</div>
                                        <div class="text-[10px] text-gray-500 mt-1">Version 8.2</div>
                                    </div>
                                    <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity text-brand">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </label>

                            <!-- Node.js Option (Disabled/Coming Soon) -->
                            <div class="opacity-50 cursor-not-allowed relative">
                                <div class="p-4 rounded-xl border border-[#1f1f1f] bg-[#0a0a0a] flex flex-col items-center gap-3 grayscale">
                                    <div class="w-12 h-12 flex items-center justify-center">
                                        <i class="fab fa-node text-3xl text-gray-600"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="font-bold text-sm text-gray-500">Node.js</div>
                                        <div class="text-[10px] text-gray-600 mt-1">Coming Soon</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-600 ml-1">Your container will be initialized with a default index.php file.</p>
                    </div>

                    <!-- Submit -->
                    <div class="pt-4 border-t border-border">
                        <button type="submit" class="w-full bg-brand hover:bg-[#14b8a6] text-black font-bold py-4 rounded-lg transition-all transform hover:scale-[1.01] shadow-[0_0_20px_rgba(45,212,191,0.2)] flex items-center justify-center gap-2">
                            <span>Initialize Container</span>
                            <i class="fas fa-rocket"></i>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </main>
    <!-- Removed JS script for file upload -->
    <script>
        // No custom JS needed for this simple form interactions are handled by CSS/Tailwind
    </script>
</body>
</html>