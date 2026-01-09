<?php
require_once __DIR__ . '/../../vendor/autoload.php';
session_start();

require_once("../../includes/admin_middleware.php");
checkAdmin();

require_once("../../Classes/Admin.php");
$admin = new Admin();
$logs = $admin->getAllLogs();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - Admin</title>
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
                 <h2 class="text-xl font-bold">Audit Logs</h2>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 relative z-10">
            <div class="glass-panel rounded-xl overflow-hidden">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-white/5 text-xs uppercase font-mono">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Action</th>
                            <th class="px-6 py-4">Details</th>
                            <th class="px-6 py-4">IP</th>
                            <th class="px-6 py-4">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-mono">#<?= $log['id'] ?></td>
                            <td class="px-6 py-4 text-white font-medium"><?= htmlspecialchars($log['username'] ?? 'System') ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded bg-red-500/10 text-red-500 text-xs font-bold"><?= htmlspecialchars($log['action']) ?></span>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate" title="<?= htmlspecialchars($log['details']) ?>"><?= htmlspecialchars($log['details']) ?></td>
                            <td class="px-6 py-4 font-mono text-xs"><?= htmlspecialchars($log['ip_address']) ?></td>
                            <td class="px-6 py-4 font-mono text-xs"><?= $log['created_at'] ?></td>
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
