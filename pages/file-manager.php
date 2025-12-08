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
if (isset($_GET["container"])) {
    $container_name = $_GET["container"];
    $files = $project->getProjectFiles($container_name);
} else {
    header("location: dashboard.php");
}

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
    <title>DOCK-HOSTING :: EDITOR</title>
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
                        brand: { DEFAULT: '#2dd4bf', hover: '#14b8a6' }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #000; color: #fff; }
        .glass-panel { background: #0a0a0a; border: 1px solid #1f1f1f; }
        /* Make the textarea look like code */
        textarea {
            font-family: 'JetBrains Mono', monospace;
            background-color: #050505;
            color: #d4d4d4;
            line-height: 1.5;
        }
    </style>
</head>
<body class="h-screen w-full flex overflow-hidden">

    <?php include("../components/sidebar.php"); ?>

    <main class="flex-1 flex flex-col h-full relative">
        
        <header class="h-14 border-b border-border flex items-center justify-between px-6 bg-panel">
            <div class="flex items-center gap-2 text-sm text-gray-400">
                <i class="fas fa-box text-brand"></i>
                <span><?= htmlspecialchars($container_name) ?></span>
                <span>/</span>
                <span class="text-white"><?= htmlspecialchars($file_requested ?? 'Select a file') ?></span>
            </div>
            
            <?php if(isset($file_requested)): ?>
                <button onclick="document.getElementById('save-form').submit()" class="bg-brand hover:bg-brand-hover text-black text-xs font-bold py-2 px-4 rounded flex items-center gap-2 transition-colors">
                    <i class="fas fa-save"></i> SAVE
                </button>
            <?php endif; ?>
        </header>

        <div class="flex-1 flex overflow-hidden">
            
            <div class="w-64 border-r border-border bg-black/50 flex flex-col">
                <div class="p-3 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-border">Files</div>
                <div class="flex-1 overflow-y-auto p-2 space-y-1">
                    <?php foreach($files as $fl): ?>
                        <a href="file-manager.php?container=<?= $container_name ?>&file=<?= $fl ?>" 
                           class="block px-3 py-2 rounded text-sm font-mono hover:bg-white/5 <?= ($file_requested ?? '') === $fl ? 'text-brand bg-brand/10' : 'text-gray-400' ?> transition-colors truncate">
                            <i class="fas fa-file-code w-5 text-center opacity-50"></i>
                            <?= $fl ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex-1 relative bg-[#050505]">
                <?php if(isset($file_requested)): ?>
                    <form id="save-form" action="file-manager.php?container=<?= $container_name ?>&file=<?= $file_requested ?>" method="POST" class="h-full w-full">
                        <textarea name="newcontent" class="w-full h-full p-6 resize-none outline-none border-none text-sm" spellcheck="false"><?= htmlspecialchars($content) ?></textarea>
                    </form>
                <?php else: ?>
                    <div class="h-full w-full flex flex-col items-center justify-center text-gray-600">
                        <i class="fas fa-code text-4xl mb-4 opacity-20"></i>
                        <p>Select a file to start editing</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>

</body>
</html>