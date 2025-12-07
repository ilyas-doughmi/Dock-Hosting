<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}
require_once("../php/connect.php");
require_once("../Classes/Project.php");

$projects = new Project();
$user_Projects = $projects->getProjects($_SESSION["id"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCK-HOSTING :: CONSOLE</title>
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
        body {
            background-color: #000;
            color: #fff;
        }

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

        /* Status Dot Animation */
        .status-dot {
            box-shadow: 0 0 10px currentColor;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #000;
        }

        ::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #2dd4bf;
        }
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

                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-brand/10 text-brand rounded-lg border border-brand/20 transition-all">
                    <i class="fas fa-terminal w-5"></i>
                    <span class="font-medium text-sm">Console</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all group">
                    <i class="fas fa-folder w-5 group-hover:text-brand transition-colors"></i>
                    <span class="font-medium text-sm">Projects</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all group">
                    <i class="fas fa-database w-5 group-hover:text-brand transition-colors"></i>
                    <span class="font-medium text-sm">Databases</span>
                </a>

                <div class="px-4 py-2 mt-6 text-xs font-mono text-gray-500 uppercase tracking-wider">Account</div>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all group">
                    <i class="fas fa-cog w-5 group-hover:text-brand transition-colors"></i>
                    <span class="font-medium text-sm">Settings</span>
                </a>
            </nav>
        </div>

        <!-- User Profile -->
        <div class="p-4 border-t border-border">
            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 cursor-pointer transition-colors">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-gray-700 to-gray-900 border border-border flex items-center justify-center font-bold text-gray-300">
                    <?= substr($_SESSION["username"], 0, 2)   ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm truncate"><?= $_SESSION["username"] ?></div>
                    <div class="text-xs text-gray-500 font-mono truncate"><?= $_SESSION["email"] ?></div>
                </div>
                <i class="fas fa-sign-out-alt text-gray-500 hover:text-red-400 transition-colors"></i>
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
            <div class="flex items-center gap-4 text-sm font-mono">
                <div class="flex items-center gap-2 text-brand">
                    <span class="w-2 h-2 rounded-full bg-brand status-dot animate-pulse"></span>
                    <span>System Online</span>
                </div>
                <span class="text-gray-700">|</span>
                <span class="text-gray-500">v2.4.0</span>
            </div>
            <a href="create-project.php">
                <button class="bg-brand hover:bg-[#14b8a6] text-black font-bold py-2 px-4 rounded transition-all shadow-[0_0_20px_rgba(45,212,191,0.2)] flex items-center gap-2 text-sm">
                    <i class="fas fa-plus"></i> New Project
                </button>

            </a>
        </header>

        <!-- Content Scroll Area -->
        <div class="flex-1 overflow-y-auto p-8 relative z-10">

            <!-- Welcome Message -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight mb-2">Welcome back, <span class="text-brand"><?= $_SESSION["username"] ?></span> 👋</h1>
                <p class="text-gray-500 text-sm font-mono">Here is an overview of your active containers and system resources.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Card 1 -->
                <div class="glass-panel p-6 rounded-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-server text-4xl"></i>
                    </div>
                    <div class="text-gray-500 text-xs font-mono uppercase mb-2">Active Containers</div>
                    <div class="text-3xl font-bold">2<span class="text-lg text-gray-600">/5</span></div>
                </div>

                <!-- Card 2 -->
                <div class="glass-panel p-6 rounded-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-microchip text-4xl"></i>
                    </div>
                    <div class="text-gray-500 text-xs font-mono uppercase mb-2">CPU Usage</div>
                    <div class="text-3xl font-bold font-mono">12<span class="text-lg text-gray-600">%</span></div>
                    <div class="w-full h-1 bg-gray-800 mt-4 rounded-full overflow-hidden">
                        <div class="h-full bg-brand w-[12%]"></div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="glass-panel p-6 rounded-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-memory text-4xl"></i>
                    </div>
                    <div class="text-gray-500 text-xs font-mono uppercase mb-2">Memory (RAM)</div>
                    <div class="text-3xl font-bold font-mono">256<span class="text-lg text-gray-600">MB</span></div>
                    <div class="w-full h-1 bg-gray-800 mt-4 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-500 w-[15%]"></div>
                    </div>
                </div>
            </div>

            <!-- Projects Table -->
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <i class="fas fa-layer-group text-gray-600"></i> Deployments
            </h2>

            <div class="glass-panel rounded-xl overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-gray-500 bg-white/5 font-mono uppercase border-b border-border">
                        <tr>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Project Name</th>
                            <th class="px-6 py-4">Port</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <?php if ($user_Projects): ?>
                            <?php foreach ($user_Projects as $project): ?>
                                <tr class="group hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-brand text-xs font-mono">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand status-dot animate-pulse"></span>
                                            <?= $project['status'] ?? 'Unknown' ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold"><?= $project['project_name'] ?? 'Unnamed' ?></div>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-gray-400 group-hover:text-white transition-colors">
                                        <a href="http://localhost:<?= $project['port'] ?>" target="_blank" class="hover:underline flex items-center gap-2">
                                            localhost:<?= $project['port'] ?>
                                            <i class="fas fa-external-link-alt text-xs"></i>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        
                                            <button type="submit" class="text-gray-500 hover:text-white p-2 transition-colors"><i class="fas fa-terminal"></i></button>
                                        
                                        <form action="../includes/stop.php" method="POST">
                                            <input type="hidden" name="container_name" value="<?= $project["container_name"] ?>">
                                        <button class="text-gray-500 hover:text-red-400 p-2 transition-colors"><i class="fas fa-stop-circle"></i></button>
                                        </form>
                                        <button class="text-gray-500 hover:text-red-400 p-2 transition-colors"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="p-12 text-center text-gray-500">
                                    <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-box-open text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-300">No Containers Found</h3>
                                    <p class="text-gray-500 text-sm mb-6">You haven't deployed any projects yet.</p>
                                    <button class="text-brand text-sm font-bold hover:underline">Deploy your first project</button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>