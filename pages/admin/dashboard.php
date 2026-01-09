<?php
require_once __DIR__ . '/../../vendor/autoload.php';
session_start();

require_once("../../includes/admin_middleware.php");
checkAdmin();

require_once("../../php/connect.php");

$db = new db();
$pdo = $db->connect();



$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmt->fetchColumn();


$stmt = $pdo->query("SELECT COUNT(*) FROM Project");
$totalProjects = $stmt->fetchColumn();


$stmt = $pdo->query("SELECT audit_logs.*, users.username FROM audit_logs LEFT JOIN users ON audit_logs.user_id = users.id ORDER BY created_at DESC LIMIT 5");
$recentLogs = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dock Hosting</title>
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
                            DEFAULT: '#ef4444', // Red-500 for Admin
                            dim: 'rgba(239, 68, 68, 0.1)',
                            glow: 'rgba(239, 68, 68, 0.5)'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #000; color: #fff; }
        .glass-panel { background: #0a0a0a; border: 1px solid #1f1f1f; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #000; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #ef4444; }
    </style>
</head>
<body class="h-screen w-full flex overflow-hidden font-sans selection:bg-brand selection:text-black">

    <!-- SIDEBAR OVERLAY (Mobile) -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/80 z-10 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

    <?php include '../../components/admin_sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col relative overflow-hidden bg-bg w-full">
         <header class="h-20 border-b border-border flex items-center justify-between px-6 md:px-8 bg-black/50 backdrop-blur z-10">
            <div class="flex items-center gap-4 text-sm font-mono">
                <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-white mr-2">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center gap-2 text-brand">
                    <span class="w-2 h-2 rounded-full bg-brand animate-pulse"></span>
                    <span class="hidden sm:inline">Admin Access</span>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 relative z-10">
            
            <div class="mb-12">
                <h1 class="text-4xl font-bold mb-2">Dashboard Overview</h1>
                <p class="text-gray-400">System statistics and recent activity.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="glass-panel p-6 rounded-xl relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-users text-6xl text-brand"></i>
                    </div>
                    <div class="text-gray-400 text-sm font-mono mb-2">TOTAL USERS</div>
                    <div class="text-4xl font-bold"><?= $totalUsers ?></div>
                </div>

                <div class="glass-panel p-6 rounded-xl relative overflow-hidden group">
                     <div class="absolute right-0 top-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-cubes text-6xl text-brand"></i>
                    </div>
                    <div class="text-gray-400 text-sm font-mono mb-2">TOTAL PROJECTS</div>
                    <div class="text-4xl font-bold"><?= $totalProjects ?></div>
                </div>

                <!-- Visitors Card -->
                <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM site_analytics WHERE DATE(created_at) = DATE(NOW())");
                    $visitorsToday = $stmt->fetchColumn();
                ?>
                <div class="glass-panel p-6 rounded-xl relative overflow-hidden group">
                     <div class="absolute right-0 top-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-chart-line text-6xl text-brand"></i>
                    </div>
                    <div class="text-gray-400 text-sm font-mono mb-2">VISITORS TODAY</div>
                    <div class="text-4xl font-bold"><?= $visitorsToday ?></div>
                </div>
            </div>

            <!-- Recent Activity -->
            <h2 class="text-xl font-bold mb-4">Recent Activity</h2>
            <div class="glass-panel rounded-xl overflow-hidden">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-white/5 text-xs uppercase font-mono">
                        <tr>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Action</th>
                            <th class="px-6 py-4">Details</th>
                            <th class="px-6 py-4">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($recentLogs as $log): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 text-white font-medium"><?= htmlspecialchars($log['username'] ?? 'Unknown') ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded bg-brand/10 text-brand text-xs font-bold"><?= htmlspecialchars($log['action']) ?></span>
                            </td>
                            <td class="px-6 py-4"><?= htmlspecialchars($log['details']) ?></td>
                            <td class="px-6 py-4 font-mono text-xs"><?= $log['created_at'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentLogs)): ?>
                        <tr><td colspan="4" class="px-6 py-8 text-center">No recent activity found.</td></tr>
                        <?php endif; ?>
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
            if (overlay.classList.contains('hidden')) {
                overlay.classList.remove('hidden');
            } else {
                overlay.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
