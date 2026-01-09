<?php
require_once __DIR__ . '/../../vendor/autoload.php';
session_start();

require_once("../../includes/admin_middleware.php");
checkAdmin();

require_once("../../Classes/Admin.php");
$admin = new Admin();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message'])) {
        $admin->createAnnouncement($_POST['message'], $_POST['type']);
    } elseif (isset($_POST['delete_id'])) {
        $admin->deleteAnnouncement($_POST['delete_id']);
    } elseif (isset($_POST['toggle_maintenance'])) {
        $admin->toggleMaintenanceMode($_POST['maintenance_status'] == '1');
    }
    
    header("Location: announcements.php");
    exit;
}

$announcements = $admin->getAllAnnouncements();
$isMaintenance = $admin->isMaintenanceMode();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements & Settings - Admin</title>
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
                 <h2 class="text-xl font-bold">Announcements & Settings</h2>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 relative z-10">
            
            <!-- Maintenance Mode -->
            <div class="glass-panel p-6 rounded-xl mb-8 flex items-center justify-between border-l-4 <?= $isMaintenance ? 'border-l-brand' : 'border-l-transparent' ?>">
                <div>
                    <h3 class="text-lg font-bold mb-1">Maintenance Mode</h3>
                    <p class="text-gray-400 text-sm">Lock the site for non-admin users.</p>
                </div>
                <form method="POST">
                    <input type="hidden" name="toggle_maintenance" value="1">
                    <input type="hidden" name="maintenance_status" value="<?= $isMaintenance ? '0' : '1' ?>">
                    <button type="submit" class="px-6 py-2 rounded-full font-bold text-sm transition-all <?= $isMaintenance ? 'bg-brand text-black hover:bg-white' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                        <?= $isMaintenance ? 'DISABLE MAINTENANCE' : 'ENABLE MAINTENANCE' ?>
                    </button>
                </form>
            </div>

            <!-- New Announcement Form -->
            <div class="glass-panel p-6 rounded-xl mb-8">
                <h3 class="text-lg font-bold mb-4">Post New Announcement</h3>
                <form method="POST" class="flex flex-col gap-4">
                    <textarea name="message" rows="3" placeholder="Enter message here..." class="w-full bg-black/50 border border-white/10 rounded-xl p-4 text-sm focus:outline-none focus:border-brand/50 transition-colors" required></textarea>
                    <div class="flex items-center justify-between">
                         <select name="type" class="bg-black/50 border border-white/10 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-brand/50">
                            <option value="info">Info (Blue)</option>
                            <option value="warning">Warning (Yellow)</option>
                            <option value="danger">Danger (Red)</option>
                            <option value="success">Success (Green)</option>
                        </select>
                        <button type="submit" class="bg-white text-black px-6 py-2 rounded-lg font-bold hover:bg-gray-200 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i> Post
                        </button>
                    </div>
                </form>
            </div>

            <!-- Active Announcements -->
            <h3 class="text-lg font-bold mb-4">History</h3>
            <div class="flex flex-col gap-4">
                <?php foreach ($announcements as $a): ?>
                    <div class="glass-panel p-4 rounded-xl flex items-start gap-4 hover:border-white/20 transition-colors">
                        <div class="mt-1">
                            <?php if($a['type'] == 'warning'): ?><i class="fas fa-exclamation-triangle text-yellow-500"></i>
                            <?php elseif($a['type'] == 'danger'): ?><i class="fas fa-ban text-red-500"></i>
                            <?php elseif($a['type'] == 'success'): ?><i class="fas fa-check-circle text-green-500"></i>
                            <?php else: ?><i class="fas fa-info-circle text-blue-500"></i>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <p class="text-white text-sm"><?= htmlspecialchars($a['message']) ?></p>
                            <div class="text-[10px] font-mono text-gray-500 mt-2"><?= $a['created_at'] ?></div>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="delete_id" value="<?= $a['id'] ?>">
                            <button type="submit" class="text-gray-500 hover:text-red-500 transition-colors"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($announcements)): ?>
                    <div class="text-gray-500 text-center py-8">No announcements posted.</div>
                <?php endif; ?>
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
