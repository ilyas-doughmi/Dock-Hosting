<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- SIDEBAR -->
<aside class="w-64 sidebar flex flex-col justify-between h-full z-20 flex-shrink-0">
    <div>
        <!-- Logo -->
        <div class="h-20 flex items-center px-6 border-b border-border">
            <a href="dashboard.php" class="flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-brand/10 border border-brand/20 flex items-center justify-center text-brand">
                    <i class="fas fa-cubes text-sm"></i>
                </div>
                <span class="font-bold tracking-tight text-lg">DOCK<span class="text-brand">-HOSTING</span></span>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-2">
            <div class="px-4 py-2 text-xs font-mono text-gray-500 uppercase tracking-wider">Main</div>

            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group <?= $current_page == 'dashboard.php' ? 'bg-brand/10 text-brand border border-brand/20' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-terminal w-5 <?= $current_page == 'dashboard.php' ? '' : 'group-hover:text-brand' ?> transition-colors"></i>
                <span class="font-medium text-sm">Console</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group <?= $current_page == 'projects.php' ? 'bg-brand/10 text-brand border border-brand/20' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-folder w-5 <?= $current_page == 'projects.php' ? '' : 'group-hover:text-brand' ?> transition-colors"></i>
                <span class="font-medium text-sm">Projects</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group <?= $current_page == 'databases.php' ? 'bg-brand/10 text-brand border border-brand/20' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-database w-5 <?= $current_page == 'databases.php' ? '' : 'group-hover:text-brand' ?> transition-colors"></i>
                <span class="font-medium text-sm">Databases</span>
            </a>

            <div class="px-4 py-2 mt-6 text-xs font-mono text-gray-500 uppercase tracking-wider">Account</div>

            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group <?= $current_page == 'settings.php' ? 'bg-brand/10 text-brand border border-brand/20' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-cog w-5 <?= $current_page == 'settings.php' ? '' : 'group-hover:text-brand' ?> transition-colors"></i>
                <span class="font-medium text-sm">Settings</span>
            </a>
        </nav>
    </div>

    <!-- Bottom Section -->
    <div>
        <!-- User Profile -->
        <div class="p-4 border-t border-border">
            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 cursor-pointer transition-colors group relative">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-gray-700 to-gray-900 border border-border flex items-center justify-center font-bold text-gray-300">
                    <?= substr($_SESSION["username"] ?? 'Guest', 0, 2) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm truncate"><?= $_SESSION["username"] ?? 'Guest' ?></div>
                    <div class="text-xs text-gray-500 font-mono truncate"><?= $_SESSION["email"] ?? 'guest@youcode.ma' ?></div>
                </div>
                
                <form action="../includes/user_actions/logout.php" method="POST" class="absolute right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button type="submit" title="Logout">
                        <i class="fas fa-sign-out-alt text-gray-500 hover:text-red-400 transition-colors"></i>
                    </button>
                </form>
            </div>
        </div>

    
    </div>
</aside>