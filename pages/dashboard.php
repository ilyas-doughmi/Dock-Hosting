<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}
require_once("../php/connect.php");
require_once("../Classes/Project.php");

$projects = new Project();
$user_Projects = $projects->getProjects($_SESSION["id"]);

$user_projects_count = $projects->getContainersCount($_SESSION["id"]);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCK-HOSTING :: CONSOLE</title>
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
        body {
            background-color: #000;
            color: #fff;
        }

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

        /* Status Dot Animation */
        .status-dot {
            box-shadow: 0 0 10px currentColor;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #000;
        }

        ::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #2dd4bf;
        }
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
            font-mono text-sm shadow-xl z-50 backdrop-blur-md flex flex-col gap-2 animate-slide-up">
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-3"><i class="fas <?= $icon ?> <?= $icon_color ?>"></i> <?= $message ?></span>
                <button onclick="this.closest('.animate-slide-up').remove()" class="<?= $icon_color ?> hover:text-white transition-colors"><i class="fas fa-times"></i></button>
            </div>
            <?php if(isset($_GET['url'])): ?>
                <a href="<?= htmlspecialchars($_GET['url']) ?>" target="_blank" class="text-xs underline hover:text-white mt-1 ml-7">
                    Visit: <?= htmlspecialchars($_GET['domain'] ?? $_GET['url']) ?> <i class="fas fa-external-link-alt ml-1"></i>
                </a>
            <?php endif; ?>
        </div>
        <script>
            setTimeout(() => {
                const notification = document.querySelector('.animate-slide-up');
                if (notification) notification.remove();
            }, 5000);
        </script>
    <?php } ?>

    <!-- SIDEBAR OVERLAY (Mobile) -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/80 z-10 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

    <!-- SIDEBAR -->
    <?php include '../components/sidebar.php'; ?>


    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col relative overflow-hidden bg-bg w-full">

        <!-- Background Grid -->
        <div class="absolute inset-0 z-0 opacity-10 pointer-events-none"
            style="background-image: linear-gradient(#1f1f1f 1px, transparent 1px), linear-gradient(90deg, #1f1f1f 1px, transparent 1px); background-size: 40px 40px;">
        </div>

        <!-- Top Header -->
        <header class="h-20 border-b border-border flex items-center justify-between px-6 md:px-8 bg-black/50 backdrop-blur z-10">
            <div class="flex items-center gap-4 text-sm font-mono">
                <!-- Mobile Toggle -->
                <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-white mr-2">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <div class="flex items-center gap-2 text-brand">
                    <span class="w-2 h-2 rounded-full bg-brand status-dot animate-pulse"></span>
                    <span class="hidden sm:inline">System Online</span>
                </div>
            </div>
            <a href="create-project.php">
                <button class="bg-brand hover:bg-[#14b8a6] text-black font-bold py-2 px-4 rounded transition-all shadow-[0_0_20px_rgba(45,212,191,0.2)] flex items-center gap-2 text-sm">
                    <i class="fas fa-plus"></i> <span class="hidden sm:inline">New Project</span>
                </button>

            </a>
        </header>

        <!-- Content Scroll Area -->
        <div class="flex-1 overflow-y-auto p-8 relative z-10">

            <!-- Welcome Message -->
            <div class="mb-12 text-center max-w-2xl mx-auto">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-brand/10 text-brand mb-6 border border-brand/20 shadow-[0_0_30px_rgba(45,212,191,0.1)]">
                    <i class="fas fa-cubes text-3xl"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
                    Welcome back, <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand to-white"><?= $_SESSION["username"] ?></span>
                </h1>
                <p class="text-gray-400 text-lg">Manage and deploy your containerized PHP applications.</p>
            </div>

            <!-- Projects Grid -->
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-bold flex items-center gap-3">
                        <i class="fas fa-layer-group text-brand"></i> My Deployments
                    </h2>
                    <a href="create-project.php" class="px-6 py-2.5 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-sm font-mono transition-colors flex items-center gap-2">
                         <i class="fas fa-plus text-brand"></i> New Project
                    </a>
                </div>

                <?php if ($user_Projects): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($user_Projects as $project): ?>
                            <!-- Project Card -->
                            <div class="glass-panel group rounded-2xl p-6 relative overflow-hidden transition-all hover:-translate-y-1 hover:shadow-2xl hover:shadow-brand/5 hover:border-brand/30">
                                <div class="absolute inset-0 bg-gradient-to-br from-brand/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                
                                <div class="relative z-10">
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="w-12 h-12 rounded-xl bg-black/40 border border-white/10 flex items-center justify-center text-xl">
                                            <i class="fab fa-php text-blue-400"></i>
                                        </div>
                                        <?php if(strtolower($project['status']) === 'running'): ?>
                                            <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-green-500/10 border border-green-500/20 text-green-400 text-xs font-bold font-mono uppercase">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                                Running
                                            </div>
                                        <?php else: ?>
                                            <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-bold font-mono uppercase">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                Stopped
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <h3 class="text-xl font-bold mb-1 truncate"><?= htmlspecialchars($project['project_name']) ?></h3>
                                    <a href="http://<?= $project['project_name'] ?>.dockhosting.dev" target="_blank" class="text-xs font-mono text-gray-500 hover:text-brand transition-colors flex items-center gap-2 mb-6">
                                        <i class="fas fa-link"></i> <?= $project['project_name'] ?>.dockhosting.dev
                                    </a>

                                    <div class="grid grid-cols-2 gap-2 mb-6">
                                        <div class="bg-white/5 rounded-lg p-3 text-center">
                                            <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">CPU</div>
                                            <div class="font-mono text-sm font-bold">4%</div>
                                        </div>
                                        <div class="bg-white/5 rounded-lg p-3 text-center">
                                            <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">RAM</div>
                                            <div class="font-mono text-sm font-bold">128MB</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 pt-4 border-t border-white/5">
                                        <a href="./file-manager.php?container=<?= $project["container_name"] ?>" class="flex-1 py-2.5 rounded-lg bg-brand/10 hover:bg-brand text-brand hover:text-black font-bold text-xs text-center transition-all">
                                            CODE EDITOR
                                        </a>
                                        <button onclick="deleteContainer('<?= $project["container_name"] ?>')" class="w-10 h-10 rounded-lg border border-red-500/20 hover:bg-red-500/10 text-red-400 flex items-center justify-center transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Add New Card -->
                        <a href="create-project.php" class="border border-dashed border-white/10 rounded-2xl flex flex-col items-center justify-center p-6 text-gray-500 hover:text-brand hover:border-brand/30 hover:bg-white/5 transition-all group cursor-pointer h-full min-h-[300px]">
                            <div class="w-16 h-16 rounded-full bg-white/5 group-hover:bg-brand/10 flex items-center justify-center mb-4 transition-colors">
                                <i class="fas fa-plus text-2xl group-hover:scale-110 transition-transform"></i>
                            </div>
                            <span class="font-bold">Deploy New Container</span>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-20">
                        <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-box-open text-3xl text-gray-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-300 mb-2">No Projects Found</h3>
                        <p class="text-gray-500 mb-8">Get started by deploying your first PHP container.</p>
                        <a href="create-project.php" class="inline-flex items-center gap-2 px-8 py-3 rounded-full bg-brand hover:bg-brand-hover text-black font-bold transition-transform hover:scale-105">
                            <i class="fas fa-plus"></i> Create Project
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <!-- delete modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        
        <!-- Backdrop (Click to close) -->
        <div onclick="closeDeleteModal()" class="absolute inset-0 backdrop transition-opacity duration-300"></div>

        <!-- Modal Content -->
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md p-4">
            <div class="glass-panel rounded-xl shadow-2xl animate-modal-in overflow-hidden">

                <!-- Red Warning Strip -->
                <div class="h-1 w-full bg-danger"></div>

                <div class="p-6">
                    <!-- Icon -->
                    <div class="w-12 h-12 bg-red-500/10 rounded-full flex items-center justify-center text-danger mb-4 mx-auto">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>

                    <!-- Text -->
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold mb-2">Delete Project?</h3>
                        <p class="text-gray-400 text-sm">
                            Are you sure you want to delete this project?
                            <br>
                            This action cannot be undone.
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        <button onclick="closeDeleteModal()" class="flex-1 py-3 rounded-lg border border-[#333] hover:bg-[#1a1a1a] text-gray-300 font-medium transition-colors">
                            Cancel
                        </button>
                        <button id="deletebtn" class="flex-1 py-3 rounded-lg bg-danger hover:bg-dangerHover text-white font-bold shadow-[0_0_15px_rgba(239,68,68,0.4)] transition-transform active:scale-95">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Simple Open Function
        function openDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        // Simple Close Function
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }


        const deletebtn = document.getElementById("deletebtn");
        const deleteModal = document.getElementById("deleteModal");
        
        function deleteContainer(container){
            deleteModal.classList.remove("hidden");
            deletebtn.addEventListener("click",function(){
                    data = new FormData();
                    data.append("container_name",container);
                  fetch("../includes/actions/delete.php",{
                        method : "POST",
                        body:data
                })
                location.reload();
            })
          
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