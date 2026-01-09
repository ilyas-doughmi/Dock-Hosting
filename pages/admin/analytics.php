<?php
require_once __DIR__ . '/../../vendor/autoload.php';
session_start();

require_once("../../includes/admin_middleware.php");
checkAdmin();

require_once("../../Classes/Admin.php");
$admin = new Admin();
$stats = $admin->getTrafficStats();
$trafficData = json_encode($stats['daily']);
$funnel = $admin->getConversionFunnel();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Space Grotesk', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                    colors: { bg: '#000000', panel: '#0a0a0a', border: '#1f1f1f', brand: '#ef4444' }
                }
            }
        }
    </script>
    <style>body { background-color: #000; color: #fff; } .glass-panel { background: #0a0a0a; border: 1px solid #1f1f1f; }
    /* Custom Scrollbar for tables */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
    </style>
</head>
<body class="h-screen w-full flex overflow-hidden font-sans">
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/80 z-10 hidden md:hidden backdrop-blur-sm transition-opacity"></div>
    <?php include '../../components/admin_sidebar.php'; ?>

    <main class="flex-1 flex flex-col relative overflow-hidden bg-bg w-full">
        <header class="h-20 border-b border-border flex items-center justify-between px-6 md:px-8 bg-black/50 backdrop-blur z-10">
             <div class="flex items-center gap-4">
                 <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-white mr-2"><i class="fas fa-bars text-xl"></i></button>
                 <h2 class="text-xl font-bold">Traffic Intelligence</h2>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 relative z-10">
            
            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="glass-panel p-6 rounded-xl">
                    <div class="text-gray-400 text-sm font-mono uppercase">Visitors Today</div>
                    <div class="text-3xl font-bold mt-2 text-white"><?= number_format($stats['today']) ?></div>
                    <div class="text-xs text-green-400 mt-1"><i class="fas fa-arrow-up"></i> Live Tracking</div>
                </div>
                <div class="glass-panel p-6 rounded-xl">
                    <div class="text-gray-400 text-sm font-mono uppercase">Bounce Rate</div>
                    <div class="text-3xl font-bold mt-2 text-white"><?= $stats['bounce_rate'] ?>%</div>
                    <div class="text-xs text-gray-500 mt-1">Single page sessions</div>
                </div>
                <div class="glass-panel p-6 rounded-xl">
                    <div class="text-gray-400 text-sm font-mono uppercase">Conversion (Deploy)</div>
                    <div class="text-3xl font-bold mt-2 text-brand">
                        <?= ($funnel['home'] > 0) ? round(($funnel['deployed'] / $funnel['home']) * 100, 2) : 0 ?>%
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Home to Deployed</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Main Traffic Chart -->
                <div class="glass-panel p-6 rounded-xl">
                    <h3 class="text-lg font-bold mb-6">Traffic Overview (7 Days)</h3>
                    <div class="h-64">
                        <canvas id="trafficChart"></canvas>
                    </div>
                </div>

                <!-- Conversion Funnel -->
                <div class="glass-panel p-6 rounded-xl">
                    <h3 class="text-lg font-bold mb-6">User Journey Funnel</h3>
                    <div class="space-y-6">
                        <!-- Step 1 -->
                        <div class="relative">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-400">1. Landing Page</span>
                                <span class="font-mono"><?= $funnel['home'] ?></span>
                            </div>
                            <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500" style="width: 100%"></div>
                            </div>
                        </div>
                        <!-- Step 2 -->
                        <div class="relative pl-4">
                            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-white/10"></div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-400">2. Login Page</span>
                                <span class="font-mono"><?= $funnel['login'] ?></span>
                            </div>
                            <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-400" style="width: <?= ($funnel['home'] > 0) ? ($funnel['login'] / $funnel['home']) * 100 : 0 ?>%"></div>
                            </div>
                        </div>
                        <!-- Step 3 -->
                        <div class="relative pl-4">
                            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-white/10"></div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-400">3. Dashboard (Auth)</span>
                                <span class="font-mono"><?= $funnel['dashboard'] ?></span>
                            </div>
                            <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-500" style="width: <?= ($funnel['login'] > 0) ? ($funnel['dashboard'] / $funnel['login']) * 100 : 0 ?>%"></div>
                            </div>
                        </div>
                         <!-- Step 4 -->
                         <div class="relative pl-4">
                            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-white/10"></div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-400">4. Create Project</span>
                                <span class="font-mono"><?= $funnel['create'] ?></span>
                            </div>
                            <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-orange-500" style="width: <?= ($funnel['dashboard'] > 0) ? ($funnel['create'] / $funnel['dashboard']) * 100 : 0 ?>%"></div>
                            </div>
                        </div>
                        <!-- Step 5 -->
                        <div class="relative pl-4">
                            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-white/10"></div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-white font-bold">5. Deployed Project</span>
                                <span class="font-mono text-brand"><?= $funnel['deployed'] ?></span>
                            </div>
                            <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-brand" style="width: <?= ($funnel['create'] > 0) ? ($funnel['deployed'] / $funnel['create']) * 100 : 0 ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Top Pages -->
                <div class="glass-panel rounded-xl overflow-hidden">
                    <div class="p-6 border-b border-white/5"><h3 class="font-bold">Top Pages</h3></div>
                    <table class="w-full text-left text-sm text-gray-400">
                        <tbody class="divide-y divide-white/5">
                            <?php foreach ($stats['pages'] as $page): ?>
                            <tr class="hover:bg-white/5">
                                <td class="px-6 py-3 text-white"><?= htmlspecialchars($page['page_url']) ?></td>
                                <td class="px-6 py-3 text-right font-mono"><?= $page['count'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Top Referrers -->
                <div class="glass-panel rounded-xl overflow-hidden">
                    <div class="p-6 border-b border-white/5"><h3 class="font-bold">Top Sources</h3></div>
                    <table class="w-full text-left text-sm text-gray-400">
                        <tbody class="divide-y divide-white/5">
                            <?php foreach ($stats['referrers'] as $ref): ?>
                            <tr class="hover:bg-white/5">
                                <td class="px-6 py-3 text-white"><?= htmlspecialchars($ref['referrer']) ?></td>
                                <td class="px-6 py-3 text-right font-mono"><?= $ref['count'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($stats['referrers'])): ?>
                                <tr><td class="px-6 py-3 text-center" colspan="2">No external referrer data yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
    const rawData = <?= $trafficData ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: rawData.map(d => d.date),
            datasets: [{
                label: 'Unique Visitors',
                data: rawData.map(d => d.count),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: '#333' } },
                x: { grid: { display: false } }
            }
        }
    });
    </script>
</body>
</html>
