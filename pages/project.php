<?php
session_start();
if (!isset($_SESSION["id"])) { header("location: ../index.php"); exit; }

require_once("../php/connect.php");
require_once("../Classes/Project.php");
require_once("../Classes/DatabaseManager.php");

$project = new Project;
$dbManager = new DatabaseManager();

$container_name = $_GET["container"] ?? null;
if (!$container_name) { header("location: dashboard.php"); exit; }


$stats = $project->getContainerStats($container_name);
$logs = $project->getContainerLogs($container_name);

$pdo = (new db())->connect();
$stmt = $pdo->prepare("SELECT * FROM Project WHERE container_name = :c AND user_id = :u");
$stmt->execute([':c' => $container_name, ':u' => $_SESSION['id']]);
$projectInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$projectInfo) { header("location: dashboard.php"); exit; }

$type = $projectInfo['type'] ?? 'php';
$userDB = $dbManager->getDatabase($_SESSION["id"], $container_name);


$iconClass = 'fab fa-php';
$iconColor = 'text-blue-400';
if ($type === 'node') { $iconClass = 'fab fa-node'; $iconColor = 'text-green-500'; }
if ($type === 'python') { $iconClass = 'fab fa-python'; $iconColor = 'text-yellow-500'; }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($projectInfo['project_name']) ?> :: Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Space Grotesk', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                    colors: { 
                        bg: '#000000', 
                        panel: '#050505', 
                        surface: '#0a0a0a', 
                        border: '#1f1f1f', 
                        brand: { DEFAULT: '#2dd4bf', dim: 'rgba(45, 212, 191, 0.1)', hover: '#14b8a6' } 
                    },
                    animation: { 'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite' }
                }
            }
        }
    </script>
    <style>
        body { background-color: #000; color: #e5e5e5; }
        .glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-hover:hover { background: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.1); }
        .active-tab { background: rgba(45, 212, 191, 0.1); color: #2dd4bf; border-right: 2px solid #2dd4bf; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
        input:focus, textarea:focus { outline: none; border-color: #2dd4bf; }
    </style>
</head>
<body class="h-screen w-full flex overflow-hidden selection:bg-brand/30 selection:text-white">


    <aside class="w-20 lg:w-64 bg-panel border-r border-white/5 flex flex-col justify-between z-20 transition-all duration-300">
        <div>

            <div class="h-16 flex items-center justify-center lg:justify-start lg:px-6 border-b border-white/5">
                <a href="dashboard.php" class="group flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-gray-400 group-hover:text-white group-hover:bg-white/10 transition-all">
                        <i class="fas fa-arrow-left text-xs"></i>
                    </div>
                    <span class="font-bold text-sm tracking-wide hidden lg:block text-gray-400 group-hover:text-white transition-colors">BACK</span>
                </a>
            </div>


            <nav class="p-3 space-y-2 mt-4">
                <button onclick="switchTab('overview')" id="nav-overview" class="w-full flex items-center gap-4 px-3 py-3 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-all group active-tab">
                    <i class="fas fa-chart-pie text-lg w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-sm hidden lg:block">Overview</span>
                </button>
                <button onclick="switchTab('files')" id="nav-files" class="w-full flex items-center gap-4 px-3 py-3 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-all group">
                    <i class="fas fa-code text-lg w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-sm hidden lg:block">Code Editor</span>
                </button>
                <button onclick="switchTab('logs')" id="nav-logs" class="w-full flex items-center gap-4 px-3 py-3 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-all group">
                    <i class="fas fa-terminal text-lg w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-sm hidden lg:block">Console Logs</span>
                </button>
                <button onclick="switchTab('database')" id="nav-database" class="w-full flex items-center gap-4 px-3 py-3 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-all group">
                    <i class="fas fa-database text-lg w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-sm hidden lg:block">Database</span>
                </button>
                 <button onclick="switchTab('settings')" id="nav-settings" class="w-full flex items-center gap-4 px-3 py-3 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-all group">
                    <i class="fas fa-cog text-lg w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-sm hidden lg:block">Settings</span>
                </button>
            </nav>
        </div>


        <div class="p-4 border-t border-white/5">
            <div class="bg-surface rounded-xl p-3 flex items-center gap-3 border border-white/5">
                 <div class="w-8 h-8 rounded-full bg-<?= $type === 'node' ? 'green' : ($type === 'python' ? 'yellow' : 'blue') ?>-500/10 flex items-center justify-center">
                    <i class="<?= $iconClass ?> <?= $iconColor ?>"></i>
                </div>
                <div class="hidden lg:block overflow-hidden">
                    <div class="text-xs font-bold text-white truncate"><?= htmlspecialchars($projectInfo['project_name']) ?></div>
                    <div class="text-[10px] text-brand flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></span> Online
                    </div>
                </div>
            </div>
        </div>
    </aside>


    <main class="flex-1 flex flex-col relative bg-bg">
        

        <header class="h-16 border-b border-white/5 flex items-center justify-between px-6 lg:px-8 bg-black/50 backdrop-blur-sm z-10 sticky top-0">
            <div class="flex items-center gap-4">
                 <h2 id="page-title" class="text-lg font-bold tracking-tight text-white">Overview</h2>
                 <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-white/5 text-gray-400 border border-white/5">PORT: <?= $projectInfo['port'] ?></span>
            </div>

            <div class="flex items-center gap-3">
                <a href="http://<?= htmlspecialchars($projectInfo['project_name']) ?>.dockhosting.dev" target="_blank" class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-xs font-bold transition-all border border-white/10 group">
                    <span>Open Application</span>
                    <i class="fas fa-external-link-alt group-hover:text-brand transition-colors"></i>
                </a>
                
                <div class="h-6 w-[1px] bg-white/10 mx-1"></div>


                <form action="../includes/actions/start.php" method="POST" class="inline">
                    <input type="hidden" name="container_name" value="<?= htmlspecialchars($container_name) ?>">
                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center bg-white/5 hover:text-green-400 hover:bg-green-400/10 transition-all border border-white/5" title="Start">
                        <i class="fas fa-play text-xs"></i>
                    </button>
                </form>

                 <form action="../includes/actions/restart.php" method="POST" class="inline">
                    <input type="hidden" name="container_name" value="<?= htmlspecialchars($container_name) ?>">
                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center bg-white/5 hover:text-yellow-400 hover:bg-yellow-400/10 transition-all border border-white/5" title="Restart">
                        <i class="fas fa-redo-alt text-xs"></i>
                    </button>
                </form>
                 <form action="../includes/actions/stop.php" method="POST" class="inline">
                    <input type="hidden" name="container_name" value="<?= htmlspecialchars($container_name) ?>">
                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center bg-white/5 hover:text-red-400 hover:bg-red-400/10 transition-all border border-white/5" title="Stop">
                        <i class="fas fa-power-off text-xs"></i>
                    </button>
                </form>
            </div>
        </header>


        <div class="flex-1 overflow-hidden relative">
            

            <div id="view-overview" class="absolute inset-0 overflow-y-auto p-4 lg:p-8 space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    <div class="glass p-5 rounded-2xl relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-blue-500/10 blur-2xl rounded-full -mr-10 -mt-10 transition-all group-hover:bg-blue-500/20"></div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">CPU Usage</h3>
                        <div class="flex items-end justify-between relative z-10">
                            <span class="text-3xl font-mono font-bold text-white"><?= $stats['CPUPerc'] ?? '0%' ?></span>
                            <i class="fas fa-microchip text-blue-400/50 text-2xl"></i>
                        </div>
                         <div class="w-full bg-white/5 h-1 mt-4 rounded-full overflow-hidden">
                             <div class="h-full bg-blue-500 rounded-full" style="width: <?= floatval($stats['CPUPerc'] ?? 0) ?>%"></div>
                         </div>
                    </div>


                    <div class="glass p-5 rounded-2xl relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-purple-500/10 blur-2xl rounded-full -mr-10 -mt-10 transition-all group-hover:bg-purple-500/20"></div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Memory</h3>
                        <div class="flex items-end justify-between relative z-10">
                            <span class="text-3xl font-mono font-bold text-white"><?= $stats['MemUsage'] ?? '0MB' ?></span>
                            <i class="fas fa-memory text-purple-400/50 text-2xl"></i>
                        </div>
                        <div class="text-[10px] text-gray-500 mt-2 font-mono">Limit: <?= $stats['MemLimit'] ?? 'Unknown' ?></div>
                    </div>


                    <div class="glass p-5 rounded-2xl relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-green-500/10 blur-2xl rounded-full -mr-10 -mt-10 transition-all group-hover:bg-green-500/20"></div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Network I/O</h3>
                        <div class="flex items-end justify-between relative z-10">
                            <span class="text-xl font-mono font-bold text-white truncate"><?= $stats['NetIO'] ?? '0B / 0B' ?></span>
                        </div>
                         <div class="flex gap-2 mt-4 text-[10px] text-gray-400 font-mono">
                             <span class="flex items-center gap-1"><i class="fas fa-arrow-down text-green-400"></i> In</span>
                             <span class="flex items-center gap-1"><i class="fas fa-arrow-up text-blue-400"></i> Out</span>
                         </div>
                    </div>
                </div>


                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <div class="lg:col-span-2 glass rounded-2xl flex flex-col h-[400px]">
                        <div class="p-4 border-b border-white/5 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <i class="fas fa-terminal text-brand"></i> Live Logs Preview
                            </h3>
                            <button onclick="switchTab('logs')" class="text-xs text-brand hover:underline">View Full Logs</button>
                        </div>
                        <div class="flex-1 p-4 bg-black/40 overflow-hidden relative font-mono text-xs text-gray-400">
                             <div class="absolute inset-0 p-4 overflow-hidden opacity-70">
                                 <?= nl2br(htmlspecialchars(substr($logs, 0, 1500))) ?>...
                             </div>
                             <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-black to-transparent"></div>
                        </div>
                    </div>


                    <div class="glass rounded-2xl p-6 space-y-6">
                        <h3 class="text-sm font-bold text-white">Project Details</h3>
                        
                        <div class="space-y-4">
                            <div class="bg-white/5 rounded-lg p-3 border border-white/5">
                                <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Framework</label>
                                <div class="flex items-center gap-2 text-sm text-white font-mono">
                                    <i class="<?= $iconClass ?> <?= $iconColor ?>"></i> <?= strtoupper($type) ?>
                                </div>
                            </div>
                            <div class="bg-white/5 rounded-lg p-3 border border-white/5">
                                <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Created At</label>
                                <div class="text-sm text-white font-mono"><?= date('M d, Y', strtotime($projectInfo['created_at'])) ?></div>
                            </div>
                            <div class="bg-white/5 rounded-lg p-3 border border-white/5">
                                <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Domain</label>
                                <a href="http://<?= $projectInfo['project_name'] ?>.dockhosting.dev" target="_blank" class="text-xs text-brand hover:underline font-mono truncate block">
                                    <?= $projectInfo['project_name'] ?>.dockhosting.dev
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div id="view-files" class="absolute inset-0 bg-surface hidden flex flex-col">
                <iframe src="file-manager_embed.php?container=<?= htmlspecialchars($container_name) ?>" class="w-full h-full border-none"></iframe>
            </div>


            <div id="view-logs" class="absolute inset-0 bg-[#0c0c0c] hidden flex flex-col">
                <div class="p-3 border-b border-white/5 flex justify-between items-center bg-surface">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <span class="ml-2 text-xs font-mono text-gray-400">root@<?= htmlspecialchars($container_name) ?>:~# docker logs -f</span>
                    </div>
                </div>
                <pre class="flex-1 p-6 font-mono text-xs md:text-sm text-gray-300 overflow-auto whitespace-pre-wrap leading-relaxed selection:bg-brand/20"><?= htmlspecialchars($logs ?: "No recent logs available.") ?></pre>
            </div>


             <div id="view-database" class="absolute inset-0 p-8 hidden overflow-y-auto">
                <div class="max-w-4xl mx-auto glass p-8 rounded-2xl">
                    <h3 class="text-xl font-bold mb-6 flex items-center gap-2"><i class="fas fa-database text-blue-500"></i> Database</h3>
                     
                     <?php if($userDB): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                             <div class="space-y-2">
                                <label class="text-xs text-gray-500 uppercase font-bold">Connection String</label>
                                <div class="bg-black/40 p-3 rounded-lg border border-white/10 font-mono text-xs text-gray-300 flex items-center justify-between group">
                                    <span>mysql -h dock-hosting-db -u <?= htmlspecialchars($userDB['db_user']) ?> -p</span>
                                    <button class="text-gray-600 hover:text-white" title="Copy"><i class="far fa-copy"></i></button>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-white/5">
                                    <span class="text-gray-500 text-sm">Database Name</span>
                                    <span class="font-mono text-white text-sm"><?= htmlspecialchars($userDB['db_name']) ?></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-white/5">
                                    <span class="text-gray-500 text-sm">Username</span>
                                    <span class="font-mono text-green-400 text-sm"><?= htmlspecialchars($userDB['db_user']) ?></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-white/5">
                                    <span class="text-gray-500 text-sm">Password</span>
                                    <span class="font-mono text-red-400 text-sm blur-sm hover:blur-none transition-all cursor-pointer"><?= htmlspecialchars($userDB['db_password']) ?></span>
                                </div>
                            </div>
                        </div>
                        <a href="http://pma.dockhosting.dev" target="_blank" class="w-full py-4 rounded-xl bg-blue-600/10 text-blue-500 border border-blue-600/20 hover:bg-blue-600 hover:text-white transition-all font-bold text-center block">
                            Launch phpMyAdmin
                        </a>
                     <?php else: ?>
                        <div class="text-center py-12">
                            <div class="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-database text-2xl text-gray-500"></i>
                            </div>
                            <h3 class="text-lg font-bold">No Database Attached</h3>
                            <p class="text-gray-500 text-sm mb-6 max-w-sm mx-auto">Create a MySQL database to store your project's data.</p>
                             <form action="../includes/actions/create_database.php" method="POST">
                                <input type="hidden" name="container" value="<?= htmlspecialchars($container_name) ?>">
                                <button type="submit" class="px-8 py-3 rounded-xl bg-brand text-black font-bold hover:bg-brand-hover transition-colors shadow-lg shadow-brand/10">
                                    Provision Database
                                </button>
                            </form>
                        </div>
                     <?php endif; ?>
                </div>
            </div>


            <div id="view-settings" class="absolute inset-0 p-8 hidden overflow-y-auto">
                 <div class="max-w-2xl mx-auto space-y-8">
                     <div class="glass p-8 rounded-2xl border border-red-500/20">
                         <h3 class="text-lg font-bold text-red-500 mb-2">Danger Zone</h3>
                         <p class="text-gray-500 text-sm mb-6">Irreversible actions for your project.</p>
                         
                         <form action="../includes/admin_actions.php" method="POST" onsubmit="return confirm('CRITICAL WARNING: This will permanently delete your project and all associated data. Are you sure?');">
                            <input type="hidden" name="action" value="delete_project">
                            <input type="hidden" name="container_name" value="<?= htmlspecialchars($container_name) ?>">
                            <input type="hidden" name="user_id" value="<?= $_SESSION['id'] ?>">
                            <div class="flex items-center justify-between bg-red-500/5 p-4 rounded-xl border border-red-500/10">
                                <div>
                                    <div class="font-bold text-red-500 text-sm">Delete Project</div>
                                    <div class="text-[10px] text-gray-500">Tear down container and file system</div>
                                </div>
                                <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors">
                                    Delete Forever
                                </button>
                            </div>
                         </form>
                     </div>
                 </div>
            </div>

        </div>
    </main>

    <script>
        function switchTab(tab) {
            // Hide all views
            ['overview', 'files', 'logs', 'database', 'settings'].forEach(t => {
                document.getElementById('view-' + t).classList.add('hidden');
                const btn = document.getElementById('nav-' + t);
                btn.classList.remove('active-tab', 'text-white', 'bg-white/5');
                btn.classList.add('text-gray-400');
            });

            // Show active
            document.getElementById('view-' + tab).classList.remove('hidden');
            const activeBtn = document.getElementById('nav-' + tab);
            activeBtn.classList.remove('text-gray-400');
            activeBtn.classList.add('active-tab', 'text-white', 'bg-white/5');

            // Update Title
            const titles = {
                'overview': 'Overview',
                'files': 'Code Editor',
                'logs': 'Console Logs',
                'database': 'Database Manager',
                'settings': 'Project Settings'
            };
            document.getElementById('page-title').innerText = titles[tab];
        }
    </script>
</body>
</html>
