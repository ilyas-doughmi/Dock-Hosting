<?php
require_once __DIR__ . '/../../vendor/autoload.php';
session_start();

require_once("../../includes/admin_middleware.php");
checkAdmin();

require_once("../../Classes/Admin.php");
$admin = new Admin();
$traffic = $admin->getTrafficStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                 <h2 class="text-xl font-bold">Visitor Analytics</h2>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 relative z-10">
            
            <!-- Key Metrics -->
             <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="glass-panel p-6 rounded-xl relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-6 opacity-10"><i class="fas fa-users text-6xl text-brand"></i></div>
                    <div class="text-gray-400 text-xs font-mono uppercase mb-2">Visitors Today</div>
                    <div class="text-4xl font-bold"><?= $traffic['today'] ?></div>
                </div>
            </div>

            <!-- Chart -->
            <div class="glass-panel p-6 rounded-xl mb-8">
                <h3 class="text-lg font-bold mb-6">Traffic Last 7 Days</h3>
                <div class="h-64">
                    <canvas id="trafficChart"></canvas>
                </div>
            </div>

            <!-- Top Pages -->
            <h3 class="text-lg font-bold mb-4">Top Pages</h3>
            <div class="glass-panel rounded-xl overflow-hidden">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-white/5 text-xs uppercase font-mono">
                        <tr>
                            <th class="px-6 py-4">Page URL</th>
                            <th class="px-6 py-4 text-right">Visits</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($traffic['pages'] as $page): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-mono text-white"><?= htmlspecialchars($page['page_url']) ?></td>
                            <td class="px-6 py-4 text-right font-bold text-brand"><?= $page['count'] ?></td>
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

    // Chart.js Setup
    const ctx = document.getElementById('trafficChart').getContext('2d');
    const data = <?= json_encode($traffic['daily']) ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.date),
            datasets: [{
                label: 'Visitors',
                data: data.map(d => d.count),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#888' } },
                x: { grid: { display: false }, ticks: { color: '#888' } }
            }
        }
    });
    </script>
</body>
</html>
