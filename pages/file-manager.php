<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}
require_once("../php/connect.php");
require_once("../Classes/Project.php");

$project = new Project;
$content = "";
$container_name = $_GET["container"] ?? null;

if (!$container_name) {
    header("location: dashboard.php");
    exit;
}

if (isset($_GET["path"])) {
    $new_path = $_GET["path"];
} else {
    $new_path = "";
}

$parent_path = "";
if ($new_path != "") {
    $parent_path = dirname($new_path);
    if ($parent_path == ".") {
        $parent_path = "";
    }
}

$files = $project->getProjectFiles($container_name, $new_path);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $file_requested = $_GET["file"];
    $newcontent = $_POST["newcontent"];
    $save = $project->saveFileChanges($container_name, $file_requested, $newcontent);
}
if (isset($_GET["file"])) {
    $file_requested = $_GET["file"];
    $content = $project->getFileContent($container_name, $file_requested);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDE :: <?= htmlspecialchars($container_name) ?></title>
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
                            hover: '#14b8a6'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #050505; color: #fff; }
        .glass-panel { background: #0a0a0a; border: 1px solid #1f1f1f; }
        textarea {
            font-family: 'JetBrains Mono', monospace;
            background-color: #050505;
            color: #d4d4d4;
            line-height: 1.6;
        }
        /* Scrollbar styles */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #444; }
    </style>
</head>

<body class="h-screen w-full flex flex-col overflow-hidden">

    <!-- Top Bar / Header -->
    <header class="h-16 border-b border-border bg-[#0a0a0a] flex items-center justify-between px-6 z-20 flex-shrink-0">
        
        <!-- Left: Project Info -->
        <div class="flex items-center gap-6">
            <a href="dashboard.php" class="text-gray-500 hover:text-white transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-brand/10 text-brand flex items-center justify-center">
                    <i class="fab fa-php"></i>
                </div>
                <div>
                    <h1 class="font-bold text-sm leading-none mb-1"><?= htmlspecialchars($container_name) ?></h1>
                    <div class="flex items-center gap-2 text-[10px] text-gray-500 font-mono">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        Running
                    </div>
                </div>
            </div>
        </div>

        <!-- Center: Editor Title/Tabs (Visual only) -->
        <div class="hidden md:flex items-center gap-1 bg-black/50 p-1 rounded-lg border border-white/5">
            <div class="px-3 py-1.5 rounded-md bg-white/5 text-xs font-mono text-gray-300 flex items-center gap-2 border border-white/5">
                <i class="fas fa-code text-blue-400"></i> Editor
            </div>
            <div class="px-3 py-1.5 rounded-md text-xs font-mono text-gray-500 flex items-center gap-2 hover:bg-white/5 transition-colors cursor-not-allowed">
                <i class="fas fa-terminal"></i> Terminal
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-4">
            <!-- Container Controls -->
            <div class="flex items-center gap-1 bg-white/5 p-1 rounded-lg border border-white/5">
                <form action="../includes/actions/start.php" method="POST">
                    <input type="hidden" name="container_name" value="<?= $container_name ?>">
                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded hover:bg-green-500/20 text-green-500 transition-colors" title="Start Container">
                        <i class="fas fa-play text-xs"></i>
                    </button>
                </form>
                
                <form action="../includes/actions/stop.php" method="POST">
                    <input type="hidden" name="container_name" value="<?= $container_name ?>">
                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded hover:bg-red-500/20 text-red-500 transition-colors" title="Stop Container">
                        <i class="fas fa-stop text-xs"></i>
                    </button>
                </form>

                <button class="w-8 h-8 flex items-center justify-center rounded hover:bg-yellow-500/20 text-yellow-500 transition-colors" title="Restart Container">
                    <i class="fas fa-redo text-xs"></i>
                </button>
            </div>

            <div class="w-[1px] h-8 bg-white/10"></div>

            <?php if (isset($file_requested)): ?>
                <button onclick="document.getElementById('save-form').submit()" class="bg-brand hover:bg-brand-hover text-black text-xs font-bold py-2 px-5 rounded-lg flex items-center gap-2 transition-all shadow-lg shadow-brand/10">
                    <i class="fas fa-save"></i> SAVE CHANGES
                </button>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Workspace -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- File Explorer Sidebar -->
        <aside class="w-72 bg-[#0a0a0a] border-r border-border flex flex-col flex-shrink-0">
            <!-- Explorer Header -->
            <div class="p-4 border-b border-border flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Explorer</span>
                <div class="flex gap-2 text-gray-500">
                    <button onclick="openNewFileModal()" class="hover:text-white transition-colors"><i class="fas fa-file-circle-plus"></i></button>
                    <button onclick="openNewFolderModal()" class="hover:text-white transition-colors"><i class="fas fa-folder-plus"></i></button>
                </div>
            </div>

            <!-- Path Breadcrumb -->
            <div class="px-4 py-2 text-[10px] font-mono text-gray-600 border-b border-border bg-black/20 truncate">
                root/<?= $new_path ?>
            </div>

            <!-- File List -->
            <div class="flex-1 overflow-y-auto p-2">
                <?php if ($new_path != ""): ?>
                    <a href="file-manager.php?container=<?= $container_name ?>&path=<?= $parent_path ?>"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-500 hover:text-white hover:bg-white/5 transition-colors font-mono mb-1">
                        <i class="fas fa-level-up-alt w-4"></i> ..
                    </a>
                <?php endif; ?>

                <?php foreach ($files as $fl): ?>
                    <?php
                    $is_folder = ($fl["type"] == "folder");
                    $param = $is_folder ? "path" : "file";
                    $target_path = $new_path == "" ? $fl["name"] : $new_path . "/" . $fl["name"];
                    $active = (isset($file_requested) && $file_requested === $target_path);
                    ?>

                    <a href="file-manager.php?container=<?= $container_name ?>&<?= $param ?>=<?= $target_path ?>&path=<?= $is_folder ? $target_path : $new_path ?>"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-mono transition-colors mb-1 truncate group <?= $active ? 'bg-brand/10 text-brand' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' ?>">
                        
                        <?php if ($is_folder): ?>
                            <i class="fas fa-folder w-4 text-center text-yellow-500/80 group-hover:text-yellow-400 transition-colors"></i>
                        <?php else: ?>
                            <i class="fas fa-file-code w-4 text-center text-blue-400/80 group-hover:text-blue-300 transition-colors"></i>
                        <?php endif; ?>
                        
                        <?= $fl["name"] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </aside>

        <!-- Editor Area -->
        <main class="flex-1 flex flex-col relative bg-[#050505]">
            <!-- Tab/Breadcrumb Strip -->
            <?php if (isset($file_requested)): ?>
                <div class="h-10 bg-[#0a0a0a] border-b border-border flex items-center px-4">
                    <div class="flex items-center gap-2 text-xs font-mono text-gray-400">
                        <span class="text-brand"><?= basename($file_requested) ?></span>
                        <span class="text-gray-600 text-[10px] ml-2 opacity-50">Edited</span>
                    </div>
                </div>
                
                <form id="save-form" action="file-manager.php?container=<?= $container_name ?>&file=<?= $file_requested ?>" method="POST" class="flex-1 relative">
                    <textarea name="newcontent" class="w-full h-full p-6 resize-none outline-none border-none text-sm font-mono focus:bg-white/[0.02] transition-colors" spellcheck="false"><?= htmlspecialchars($content) ?></textarea>
                </form>
            <?php else: ?>
                <div class="h-full w-full flex flex-col items-center justify-center text-gray-700">
                    <div class="w-20 h-20 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-center mb-6">
                        <i class="fas fa-code text-4xl opacity-50"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-400 mb-2">No File Selected</h3>
                    <p class="text-sm font-mono max-w-md text-center">Select a file from the explorer sidebar to view and edit its contents.</p>
                </div>
            <?php endif; ?>

            <!-- Terminal/Console Area (Mock for UI) -->
            <div class="h-48 border-t border-border bg-[#0a0a0a] flex flex-col">
                <div class="h-9 border-b border-border flex items-center px-4 justify-between bg-black/20">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-terminal"></i> Console Output
                    </span>
                    <div class="flex gap-2">
                         <button class="text-gray-600 hover:text-gray-400"><i class="fas fa-trash-alt text-[10px]"></i></button>
                         <button class="text-gray-600 hover:text-gray-400"><i class="fas fa-chevron-down text-[10px]"></i></button>
                    </div>
                </div>
                <div class="flex-1 p-4 font-mono text-xs text-gray-400 overflow-y-auto">
                    <div class="mb-1"><span class="text-green-500">➜</span> <span class="text-blue-400">~</span> Container started successfully [ID: <?= substr(md5($container_name), 0, 8) ?>]</div>
                    <div class="mb-1"><span class="text-green-500">➜</span> <span class="text-blue-400">~</span> Port binding: 0.0.0.0:80->80/tcp</div>
                    <div class="text-gray-600 mt-2">_ Ready for input...</div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals (Re-styled) -->
    <div id="newFileModal" class="fixed inset-0 z-50 hidden">
        <div onclick="closeNewFileModal()" class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md p-4">
            <div class="glass-panel rounded-xl shadow-2xl overflow-hidden border border-white/10">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-file-code text-brand"></i> New File
                    </h3>
                    <form action="../includes/actions/create_file.php" method="POST">
                        <input type="hidden" name="container" value="<?= $container_name ?>">
                        <input type="hidden" name="path" value="<?= $new_path ?>">
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-mono text-gray-500 uppercase">Filename</label>
                                <input type="text" name="name" placeholder="style.css" required class="w-full bg-[#050505] border border-border text-white text-sm rounded-lg p-3 mt-1 focus:border-brand focus:outline-none font-mono">
                            </div>
                            <div class="flex gap-3">
                                <button type="button" onclick="closeNewFileModal()" class="flex-1 py-3 rounded-lg border border-[#333] hover:bg-[#1a1a1a] text-gray-300 font-medium transition-colors">Cancel</button>
                                <button type="submit" class="flex-1 py-3 rounded-lg bg-brand hover:bg-brand-hover text-black font-bold transition-colors">Create</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="newFolderModal" class="fixed inset-0 z-50 hidden">
        <div onclick="closeNewFolderModal()" class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md p-4">
            <div class="glass-panel rounded-xl shadow-2xl overflow-hidden border border-white/10">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-folder text-yellow-500"></i> New Folder
                    </h3>
                    <form action="../includes/actions/create_folder.php" method="POST">
                        <input type="hidden" name="container" value="<?= $container_name ?>">
                        <input type="hidden" name="path" value="<?= $new_path ?>">
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-mono text-gray-500 uppercase">Folder Name</label>
                                <input type="text" name="name" placeholder="assets" required class="w-full bg-[#050505] border border-border text-white text-sm rounded-lg p-3 mt-1 focus:border-brand focus:outline-none font-mono">
                            </div>
                            <div class="flex gap-3">
                                <button type="button" onclick="closeNewFolderModal()" class="flex-1 py-3 rounded-lg border border-[#333] hover:bg-[#1a1a1a] text-gray-300 font-medium transition-colors">Cancel</button>
                                <button type="submit" class="flex-1 py-3 rounded-lg bg-brand hover:bg-brand-hover text-black font-bold transition-colors">Create</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openNewFileModal() { document.getElementById('newFileModal').classList.remove('hidden'); }
        function closeNewFileModal() { document.getElementById('newFileModal').classList.add('hidden'); }
        function openNewFolderModal() { document.getElementById('newFolderModal').classList.remove('hidden'); }
        function closeNewFolderModal() { document.getElementById('newFolderModal').classList.add('hidden'); }
    </script>
</body>
</html>