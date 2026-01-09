<?php
require_once __DIR__ . '/../../vendor/autoload.php';
session_start();

require_once("../../includes/admin_middleware.php");
checkAdmin();

require_once("../../Classes/Admin.php");
$admin = new Admin();
$system = $admin->getSystemHealth();
$images = $admin->getDockerImages();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Health - Admin</title>
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
                            DEFAULT: '#ef4444',
                            dim: 'rgba(239, 68, 68, 0.1)'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #000; color: #fff; }
        .glass-panel { background: #0a0a0a; border: 1px solid #1f1f1f; }
    </style>
</head>
<body class="h-screen w-full flex overflow-hidden font-sans">
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/80 z-10 hidden md:hidden backdrop-blur-sm transition-opacity"></div>
    <?php include '../../components/admin_sidebar.php'; ?>

    <main class="flex-1 flex flex-col relative overflow-hidden bg-bg w-full">
        <header class="h-20 border-b border-border flex items-center justify-between px-6 md:px-8 bg-black/50 backdrop-blur z-10">
            <div class="flex items-center gap-4">
                 <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-white mr-2"><i class="fas fa-bars text-xl"></i></button>
                 <h2 class="text-xl font-bold">System Status</h2>
            </div>
            
        </header>

        <div class="flex-1 overflow-y-auto p-8 relative z-10">
            
            <!-- Host Stats -->
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2"><i class="fas fa-server text-brand"></i> Host Resources</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- CPU -->
                <div class="glass-panel p-6 rounded-xl">
                    <div class="flex justify-between items-end mb-2">
                        <div class="text-gray-400 text-xs font-mono uppercase">CPU Load</div>
                        <div class="text-2xl font-bold"><?= $system['cpu_usage'] ?>%</div>
                    </div>
                    <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-brand transition-all duration-500" style="width: <?= $system['cpu_usage'] ?>%"></div>
                    </div>
                </div>

                <!-- RAM -->
                <div class="glass-panel p-6 rounded-xl">
                     <div class="flex justify-between items-end mb-2">
                        <div class="text-gray-400 text-xs font-mono uppercase">Memory Usage</div>
                        <div class="text-lg font-bold"><?= $system['ram_used'] ?> / <?= $system['ram_total'] ?> MB</div>
                    </div>
                    <?php $ramPercent = ($system['ram_total'] > 0) ? ($system['ram_used'] / $system['ram_total']) * 100 : 0; ?>
                    <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 transition-all duration-500" style="width: <?= $ramPercent ?>%"></div>
                    </div>
                </div>

                 <!-- Disk -->
                <div class="glass-panel p-6 rounded-xl">
                     <div class="flex justify-between items-end mb-2">
                        <div class="text-gray-400 text-xs font-mono uppercase">Disk Space (C:)</div>
                         <div class="text-lg font-bold"><?= $system['disk_free'] ?> GB Free</div>
                    </div>
                     <?php $diskPercent = ($system['disk_total'] > 0) ? (1 - ($system['disk_free'] / $system['disk_total'])) * 100 : 0; ?>
                     <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-500 transition-all duration-500" style="width: <?= $diskPercent ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Docker Images -->
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2"><i class="fab fa-docker text-blue-400"></i> Docker Images</h3>
            <div class="glass-panel rounded-xl overflow-hidden mb-8">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-white/5 text-xs uppercase font-mono">
                         <tr>
                            <th class="px-6 py-4">Repository</th>
                            <th class="px-6 py-4">Tag</th>
                            <th class="px-6 py-4">Image ID</th>
                            <th class="px-6 py-4">Created</th>
                            <th class="px-6 py-4 text-right">Size</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($images as $img): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-bold text-white"><?= htmlspecialchars($img['Repository']) ?></td>
                            <td class="px-6 py-4 font-mono text-xs"><?= htmlspecialchars($img['Tag']) ?></td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-500"><?= htmlspecialchars(substr($img['ID'], 0, 12)) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($img['CreatedSince']) ?></td>
                            <td class="px-6 py-4 text-right font-mono text-brand"><?= htmlspecialchars($img['Size']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        if (overlay.classList.contains('hidden')) overlay.classList.remove('hidden'); else overlay.classList.add('hidden');
    }
    </script>
</body>
</html>
