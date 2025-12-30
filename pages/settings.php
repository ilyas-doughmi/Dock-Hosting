<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

if (!isset($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$clientid_github = $_ENV['GITHUB_CLIENT_ID'];
$redirecturl_github = $_ENV['GITHUB_CALLBACK_URL'];

require_once '../Classes/GitHubManager.php';
$gh = new GitHubManager();
$token = $gh->getAccessToken($_SESSION['id']);
$is_connected = !empty($token);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCK-HOSTING :: SETTINGS</title>
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
                            DEFAULT: '#2dd4bf', 
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
        .sidebar { background: rgba(5, 5, 5, 0.9); border-right: 1px solid #1f1f1f; }
        .glass-panel { background: #0a0a0a; border: 1px solid #1f1f1f; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #000; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
    </style>
</head>
<body class="h-screen w-full flex overflow-hidden font-sans selection:bg-brand selection:text-black">

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/80 z-10 hidden md:hidden backdrop-blur-sm transition-opacity"></div>
    <?php include_once("../components/sidebar.php") ?>

    <main class="flex-1 flex flex-col relative overflow-hidden bg-bg">
        <!-- Background Grid -->
        <div class="absolute inset-0 z-0 opacity-10 pointer-events-none" 
             style="background-image: linear-gradient(#1f1f1f 1px, transparent 1px), linear-gradient(90deg, #1f1f1f 1px, transparent 1px); background-size: 40px 40px;">
        </div>

        <!-- Header -->
        <header class="h-20 border-b border-border flex items-center justify-between px-6 md:px-8 bg-black/50 backdrop-blur z-10">
            <div class="flex items-center gap-4 text-sm text-gray-500">
                <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <span class="text-white text-lg font-bold tracking-tight">Settings</span>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-8 relative z-10">
            <div class="max-w-5xl mx-auto">
                
                <!-- Tabs -->
                <div class="flex items-center gap-2 mb-8 border-b border-white/10">
                    <button onclick="switchTab('general')" id="tab-general" class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-brand text-brand hover:text-brand transition-colors">
                        General
                    </button>
                    <button onclick="switchTab('integrations')" id="tab-integrations" class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-white transition-colors">
                        Integrations
                    </button>
                    <button onclick="switchTab('account')" id="tab-account" class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-white transition-colors">
                        Account
                    </button>
                </div>

                <!-- Content Area -->

                <!-- GENERAL TAB -->
                <div id="content-general" class="tab-content space-y-8 animate-fade-in">
                    
                    <div class="glass-panel p-8 rounded-xl">
                        <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                            <i class="fas fa-user-circle text-brand"></i> Profile Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name (Username) -->
                            <div class="space-y-2">
                                <label class="text-xs font-mono text-gray-500 uppercase">Username</label>
                                <div class="relative">
                                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                                    <input type="text" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" readonly 
                                           class="w-full bg-black/40 border border-white/10 rounded-lg py-3 pl-10 pr-4 text-sm text-gray-300 cursor-not-allowed focus:outline-none">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label class="text-xs font-mono text-gray-500 uppercase">Email Address</label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                                    <input type="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" readonly 
                                           class="w-full bg-black/40 border border-white/10 rounded-lg py-3 pl-10 pr-4 text-sm text-gray-300 cursor-not-allowed focus:outline-none">
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="space-y-2">
                                <label class="text-xs font-mono text-gray-500 uppercase">Password</label>
                                <div class="relative">
                                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                                    <input type="password" value="********" readonly 
                                           class="w-full bg-black/40 border border-white/10 rounded-lg py-3 pl-10 pr-4 text-sm text-gray-300 cursor-not-allowed focus:outline-none">
                                    <button class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-brand hover:underline">Change</button>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                
                <!-- INTEGRATIONS TAB -->
                <div id="content-integrations" class="tab-content hidden space-y-8 animate-fade-in">
                    
                    <!-- GitHub Card -->
                    <div class="glass-panel p-6 md:p-8 rounded-xl relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-3 opacity-10 text-9xl transform translate-x-10 -translate-y-10 rotate-12 pointer-events-none">
                            <i class="fab fa-github"></i>
                        </div>

                        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex items-start gap-5">
                                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-3xl shadow-inner border border-white/5">
                                    <i class="fab fa-github"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-1">GitHub Integration</h3>
                                    <p class="text-sm text-gray-400 max-w-md">Connect your GitHub account to access your repositories directly within Dock-Hosting. Enable auto-deployments and streamlined workflow.</p>
                                </div>
                            </div>
                            
                            <div>
                                <?php if($is_connected): ?>
                                    <div class="flex flex-col items-end gap-2">
                                        <div class="flex items-center gap-2 px-4 py-2 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400 font-mono text-xs font-bold uppercase">
                                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                            Connected
                                        </div>
                                        <form action="../includes/user_actions/disconnect_github.php" method="POST" onsubmit="return confirm('Are you sure you want to disconnect your GitHub account?');">
                                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 hover:underline">Disconnect Account</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <a href="https://github.com/login/oauth/authorize?client_id=<?= $clientid_github ?>&redirect_uri=<?= $redirecturl_github ?>&scope=repo" 
                                       class="px-6 py-3 bg-white text-black font-bold rounded-xl hover:bg-gray-200 transition-all transform hover:scale-105 shadow-lg flex items-center gap-3">
                                        <i class="fab fa-github text-lg"></i>
                                        <span>Connect GitHub</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </main>

    <script>
        // Tab Switching Logic
        function switchTab(tabName) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Show selected content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Reset tab styles
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-brand', 'text-brand');
                el.classList.add('border-transparent', 'text-gray-400');
            });

            // Highlight selected tab
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.remove('border-transparent', 'text-gray-400');
            activeTab.classList.add('border-brand', 'text-brand');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            if (overlay.classList.contains('hidden')) {
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }
    </script>
</body>
</html>
