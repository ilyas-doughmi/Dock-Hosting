<?php
require_once __DIR__ . '/../../vendor/autoload.php';
session_start();

require_once("../../includes/admin_middleware.php");
checkAdmin();

require_once("../../Classes/Project.php");
require_once("../../Classes/Admin.php");

$projectModel = new Project();
$adminModel = new Admin();

$projects = $projectModel->getAllProjects();
$dockerStats = $adminModel->getDockerStats();


$statsMap = [];
foreach ($dockerStats as $stat) {
    
    $name = $stat['Name'] ?? $stat['name'] ?? '';
    $statsMap[$name] = $stat;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management - Admin</title>
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
                 <h2 class="text-xl font-bold">All Projects</h2>
            </div>
            <div class="text-xs font-mono text-gray-500">Total: <?= count($projects) ?></div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 relative z-10">
            <div class="glass-panel rounded-xl overflow-hidden">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-white/5 text-xs uppercase font-mono">
                        <tr>
                            <th class="px-6 py-4">Project</th>
                            <th class="px-6 py-4">Container</th>
                            <th class="px-6 py-4">Stats</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($projects as $project): 
                            $cName = $project['container_name'];
                            $stat = $statsMap[$cName] ?? null;
                            $cpu = $stat['CPUPerc'] ?? $stat['cpu_perc'] ?? '0%';
                            $mem = $stat['MemUsage'] ?? $stat['mem_usage'] ?? '0MB';
                        ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-white"><?= htmlspecialchars($project['project_name']) ?></div>
                                <div class="text-xs text-gray-500">by <?= htmlspecialchars($project['username'] ?? 'Unknown') ?></div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">
                                <div><?= htmlspecialchars($cName) ?></div>
                                <div class="text-brand"><?= htmlspecialchars($project['port']) ?></div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">
                                <?php if($project['status'] === 'running' && $stat): ?>
                                    <div class="flex gap-4">
                                        <span class="text-blue-400"><i class="fas fa-microchip"></i> <?= $cpu ?></span>
                                        <span class="text-purple-400"><i class="fas fa-memory"></i> <?= $mem ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-600">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if(strtolower($project['status']) === 'running'): ?>
                                    <span class="flex items-center gap-2 text-green-400 text-xs font-bold uppercase"><span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span> Running</span>
                                <?php else: ?>
                                    <span class="flex items-center gap-2 text-red-400 text-xs font-bold uppercase"><span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Stopped</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <form action="../../includes/admin_actions.php" method="POST" class="inline">
                                    <input type="hidden" name="action" value="toggle_container">
                                    <input type="hidden" name="container_name" value="<?= htmlspecialchars($project['container_name']) ?>">
                                    <input type="hidden" name="current_status" value="<?= htmlspecialchars($project['status']) ?>">
                                    <button type="submit" class="w-8 h-8 rounded flex items-center justify-center border border-white/10 hover:bg-white/10 transition-colors" title="Toggle Status">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>
                                <form action="../../includes/admin_actions.php" method="POST" class="inline" onsubmit="return confirm('Delete this project?');">
                                    <input type="hidden" name="action" value="delete_project">
                                    <input type="hidden" name="container_name" value="<?= htmlspecialchars($project['container_name']) ?>">
                                    <input type="hidden" name="user_id" value="<?= $project['user_id'] ?>">
                                    <button type="submit" class="w-8 h-8 rounded flex items-center justify-center border border-white/10 hover:bg-red-500/20 hover:text-red-500 transition-colors text-red-400" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
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
