<?php
session_start();
require_once '../includes/traffic_middleware.php';

if (!isset($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

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
    <title>DOCK-HOSTING :: NEW DEPLOYMENT</title>
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
        body { background-color: #000; color: #fff; }
        
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

        .input-field {
            background: #050505;
            border: 1px solid #1f1f1f;
            color: white;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: #2dd4bf;
            outline: none;
            box-shadow: 0 0 0 1px #2dd4bf;
        }

        .framework-radio:checked + div {
            border-color: #2dd4bf;
            background-color: rgba(45, 212, 191, 0.05);
        }
        
        .repo-radio:checked + div {
            border-color: #2dd4bf;
            background-color: rgba(45, 212, 191, 0.05);
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #000; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
    </style>
</head>
<body class="h-screen w-full flex overflow-hidden font-sans selection:bg-brand selection:text-black">

    <?php if(isset($_GET["msg"])){
        $message = htmlspecialchars($_GET["msg"]);
        $type = $_GET["type"] ?? "success";
        
        $bg_color = $type === "error" ? 'bg-red-900/40 border-red-500/30 text-red-200' : 'bg-green-900/40 border-green-500/30 text-green-200';
        $icon_color = $type === "error" ? 'text-red-400' : 'text-green-400';
        $icon = $type === "error" ? 'fa-circle-exclamation' : 'fa-circle-check';
    ?>
        <div class="fixed top-5 right-5 w-full max-w-md <?= $bg_color ?> px-4 py-3 rounded-xl 
            font-mono text-sm shadow-xl z-50 backdrop-blur-md flex items-center justify-between animate-slide-up">
            <span class="flex items-center gap-3"><i class="fas <?= $icon ?> <?= $icon_color ?>"></i> <?= $message ?></span>
            <button onclick="this.parentElement.remove()" class="<?= $icon_color ?> hover:text-white transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <script>
            setTimeout(() => {
                const notification = document.querySelector('.animate-slide-up');
                if (notification) notification.remove();
            }, 5000);
        </script>
    <?php } ?>

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/80 z-10 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

    <?php include_once("../components/sidebar.php") ?>


    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col relative overflow-hidden bg-bg">
        
        <!-- Background Grid -->
        <div class="absolute inset-0 z-0 opacity-10 pointer-events-none" 
             style="background-image: linear-gradient(#1f1f1f 1px, transparent 1px), linear-gradient(90deg, #1f1f1f 1px, transparent 1px); background-size: 40px 40px;">
        </div>

        <!-- Top Header -->
        <header class="h-20 border-b border-border flex items-center justify-between px-6 md:px-8 bg-black/50 backdrop-blur z-10">
            <div class="flex items-center gap-4 text-sm text-gray-500">
                <!-- Mobile Toggle -->
                <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <a href="dashboard.php" class="hover:text-brand transition-colors">Console</a>
                <span>/</span>
                <span class="text-white">Deploy</span>
            </div>
        </header>

        <!-- Form Area -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 relative z-10 flex justify-center">
            
            <div class="w-full max-w-3xl">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold tracking-tight mb-2">Deploy New Instance</h1>
                    <p class="text-gray-500 text-sm font-mono">Create a new container from scratch or import from GitHub.</p>
                </div>

                <!-- Tabs -->
                <div class="flex items-center gap-4 mb-8 border-b border-white/10">
                    <button onclick="switchSource('empty')" id="tab-empty" class="pb-2 border-b-2 border-brand text-brand font-bold text-sm transition-colors">
                        <i class="fas fa-code mr-2"></i> Empty Project
                    </button>
                    <button onclick="switchSource('github')" id="tab-github" class="pb-2 border-b-2 border-transparent text-gray-500 hover:text-white font-bold text-sm transition-colors">
                        <i class="fab fa-github mr-2"></i> Import from GitHub
                    </button>
                </div>

                <!-- Form -->
                <form action="../includes/create-project.php" method="POST" class="glass-panel p-6 md:p-8 rounded-xl space-y-8" id="deploy-form">
                    
                    <input type="hidden" name="source_type" id="source_type" value="empty">

                    <div id="section-empty" class="space-y-8">
                        <div class="space-y-4">
                            <label class="text-sm font-mono text-gray-400 uppercase tracking-wide ml-1">Project Name</label>
                            <div class="relative">
                                <i class="fas fa-tag absolute left-4 top-3.5 text-gray-600"></i>
                                <input type="text" name="project_name" placeholder="e.g. My Portfolio" 
                                       class="input-field w-full py-3 pl-10 pr-4 rounded-lg text-sm placeholder-gray-700 font-mono focus:ring-1 focus:ring-brand">
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="text-sm font-mono text-gray-400 uppercase tracking-wide ml-1">Select Framework</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="framework" value="php" checked class="peer sr-only framework-radio">
                                    <div class="p-4 rounded-xl border border-[#333] bg-[#050505] hover:border-gray-500 transition-all duration-300 flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 flex items-center justify-center">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/27/PHP-logo.svg" alt="PHP" class="h-10 w-auto">
                                        </div>
                                        <div class="text-center">
                                            <div class="font-bold text-sm text-white group-hover:text-brand transition-colors">PHP Native</div>
                                            <div class="text-[10px] text-gray-500 mt-1">Version 8.2</div>
                                        </div>
                                        <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity text-brand">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                </label>
                                <!-- Node.js -->
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="framework" value="node" class="peer sr-only framework-radio">
                                    <div class="p-4 rounded-xl border border-[#333] bg-[#050505] hover:border-gray-500 transition-all duration-300 flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 flex items-center justify-center">
                                            <i class="fab fa-node text-green-500 text-3xl"></i>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-bold text-sm text-white group-hover:text-brand transition-colors">Node.js</div>
                                            <div class="text-[10px] text-gray-500 mt-1">Version 18-alpine</div>
                                        </div>
                                        <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity text-brand">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Python -->
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="framework" value="python" class="peer sr-only framework-radio">
                                    <div class="p-4 rounded-xl border border-[#333] bg-[#050505] hover:border-gray-500 transition-all duration-300 flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 flex items-center justify-center">
                                            <i class="fab fa-python text-yellow-500 text-3xl"></i>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-bold text-sm text-white group-hover:text-brand transition-colors">Python</div>
                                            <div class="text-[10px] text-gray-500 mt-1">Version 3.11-alpine</div>
                                        </div>
                                        <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity text-brand">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>


                    <div id="section-github" class="space-y-8 hidden">
                        <?php if(!$is_connected): ?>
                            <div class="text-center py-10 bg-white/5 rounded-xl border border-dashed border-white/10">
                                <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                                    <i class="fab fa-github text-3xl"></i>
                                </div>
                                <h3 class="font-bold text-lg mb-2">Connect to GitHub</h3>
                                <p class="text-gray-500 text-sm mb-6 max-w-sm mx-auto">Link your GitHub account to import repositories and enable auto-deployments.</p>
                                <a href="settings.php" class="px-6 py-3 bg-brand text-black font-bold rounded-lg hover:bg-teal-400 transition-colors inline-flex items-center gap-2">
                                    <span>Go to Settings</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-mono text-gray-400 uppercase tracking-wide ml-1">Select Repository</label>
                                    <div class="relative w-48">
                                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-600 text-xs"></i>
                                        <input type="text" id="repo-filter" placeholder="Filter..." 
                                               class="w-full bg-black/20 border border-white/10 rounded-md py-1.5 pl-8 pr-3 text-xs focus:border-brand focus:outline-none placeholder-gray-700">
                                    </div>
                                </div>

                                <div id="repo-loader" class="py-8 text-center text-gray-500">
                                    <i class="fas fa-circle-notch fa-spin mr-2"></i> Loading repositories...
                                </div>

                                <div id="repo-grid" class="grid grid-cols-1 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar hidden">
                                </div>
                                <input type="hidden" name="github_repo" id="selected_repo" required disabled>
                                <input type="hidden" name="github_branch" id="selected_branch" value="main">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Submit -->
                    <div class="pt-4 border-t border-border">
                        <button type="submit" class="w-full bg-brand hover:bg-[#14b8a6] text-black font-bold py-4 rounded-lg transition-all transform hover:scale-[1.01] shadow-[0_0_20px_rgba(45,212,191,0.2)] flex items-center justify-center gap-2">
                            <span>Initialize Container</span>
                            <i class="fas fa-rocket"></i>
                        </button>
                    </div>

                </form>
            </div>

        </div>

        <!-- Deployment Overlay -->
        <div id="deploy-overlay" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md hidden flex-col items-center justify-center">
            <div class="w-full max-w-md p-8 text-center">
                
                <div class="w-24 h-24 mx-auto mb-8 relative">
                    <div class="absolute inset-0 rounded-full border-4 border-white/10"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-brand border-t-transparent animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-rocket text-3xl text-brand animate-pulse"></i>
                    </div>
                </div>

                <h2 class="text-2xl font-bold mb-2">Deploying Container</h2>
                <p class="text-gray-500 mb-8">Please wait while we set up your environment...</p>

                <!-- Terminal Log -->
                <div class="bg-black border border-white/10 rounded-lg p-4 font-mono text-xs text-left h-32 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-transparent to-black/50 pointer-events-none"></div>
                    <div id="deploy-logs" class="space-y-1 text-green-400">
                        <div>> Initializing deployment sequence...</div>
                    </div>
                </div>
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
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        function switchSource(source) {
            const emptyTab = document.getElementById('tab-empty');
            const githubTab = document.getElementById('tab-github');
            const emptySec = document.getElementById('section-empty');
            const githubSec = document.getElementById('section-github');
            const hiddenType = document.getElementById('source_type');

            hiddenType.value = source;

            if(source === 'empty'){
                emptyTab.classList.add('border-brand', 'text-brand');
                emptyTab.classList.remove('border-transparent', 'text-gray-500');
                githubTab.classList.remove('border-brand', 'text-brand');
                githubTab.classList.add('border-transparent', 'text-gray-500');

                emptySec.classList.remove('hidden');
                githubSec.classList.add('hidden');
                
                document.querySelector('[name="project_name"]').required = true;
                document.getElementById('selected_repo').required = false;
                document.getElementById('selected_repo').disabled = true;

            } else {
                githubTab.classList.add('border-brand', 'text-brand');
                githubTab.classList.remove('border-transparent', 'text-gray-500');
                emptyTab.classList.remove('border-brand', 'text-brand');
                emptyTab.classList.add('border-transparent', 'text-gray-500');

                githubSec.classList.remove('hidden');
                emptySec.classList.add('hidden');

                document.querySelector('[name="project_name"]').required = false;
                document.getElementById('selected_repo').required = true;
                document.getElementById('selected_repo').disabled = false;

                <?php if($is_connected): ?>
                if(document.getElementById('repo-grid').children.length === 0){
                    loadRepos();
                }
                <?php endif; ?>
            }
        }

        <?php if($is_connected): ?>
        let allRepos = [];

        async function loadRepos() {
            try {
                const res = await fetch('../api/get_repos.php');
                allRepos = await res.json();
                renderRepos(allRepos);
                document.getElementById('repo-loader').classList.add('hidden');
                document.getElementById('repo-grid').classList.remove('hidden');
            } catch (e) {
                console.error(e);
                document.getElementById('repo-loader').innerHTML = '<span class="text-red-400">Failed to load repositories.</span>';
            }
        }

        function renderRepos(repos) {
            const grid = document.getElementById('repo-grid');
            if(repos.length === 0){
                grid.innerHTML = '<div class="col-span-1 text-center text-gray-500">No repositories found.</div>';
                return;
            }

            grid.innerHTML = repos.map(repo => `
                <label class="cursor-pointer relative group block">
                    <input type="radio" name="github_selection" value="${repo.id}" onclick="selectRepo('${repo.full_name}', '${repo.default_branch}')" class="peer sr-only repo-radio">
                    <div class="p-4 rounded-xl border border-[#333] bg-[#050505] hover:border-gray-500 transition-all duration-300 flex items-center justify-between">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-10 h-10 rounded bg-white/5 flex items-center justify-center flex-shrink-0 text-white">
                                <i class="fab fa-github"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-sm text-white group-hover:text-brand transition-colors truncate">${repo.name}</div>
                                <div class="text-[10px] text-gray-500 mt-0.5 flex items-center gap-2">
                                    <span class="bg-white/10 px-1.5 py-0.5 rounded text-[9px]">${repo.visibility}</span>
                                    <span>Updated ${new Date(repo.updated_at).toLocaleDateString()}</span>
                                </div>
                            </div>
                        </div>
                        <div class="opacity-0 peer-checked:opacity-100 text-brand text-xl transition-opacity">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </label>
            `).join('');
        }

        function selectRepo(name, branch) {
            document.getElementById('selected_repo').value = name;
            document.getElementById('selected_branch').value = branch;
        }

        document.getElementById('repo-filter').addEventListener('keyup', (e) => {
            const val = e.target.value.toLowerCase();
            const filtered = allRepos.filter(r => r.name.toLowerCase().includes(val));
            renderRepos(filtered);
        });
        <?php endif; ?>

        
        
        const deployForm = document.getElementById('deploy-form');
        const overlay = document.getElementById('deploy-overlay');
        const logs = document.getElementById('deploy-logs');

        function addLog(msg) {
            const div = document.createElement('div');
            div.textContent = `> ${msg}`;
            logs.appendChild(div);
            logs.scrollTop = logs.scrollHeight;
        }

        deployForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btn = deployForm.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
            
            // Show Overlay
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            
            // Simulating steps for UX (since PHP blocks)
            addLog('Validating configuration...');
            await new Promise(r => setTimeout(r, 800));
            addLog('Allocating resources...');
            await new Promise(r => setTimeout(r, 800));
            addLog('Pulling Docker image...');
            
            const formData = new FormData(deployForm);

            try {
                const res = await fetch('../includes/create-project.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await res.json();
                
                if (data.success) {
                    addLog('Container started successfully!');
                    addLog('Redirecting to dashboard...');
                    setTimeout(() => {
                        window.location.href = 'dashboard.php?msg=Project deployed successfully';
                    }, 1000);
                } else {
                    throw new Error(data.error || 'Unknown error');
                }

            } catch (err) {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
                
                alert('Deployment Failed: ' + err.message);
                btn.disabled = false;
                btn.innerHTML = '<span>Initialize Container</span><i class="fas fa-rocket"></i>';
            }
        });
    </script>
</body>
</html>