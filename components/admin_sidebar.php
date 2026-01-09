<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- ADMIN SIDEBAR -->
<aside id="sidebar" class="w-64 sidebar flex flex-col justify-between h-full z-20 flex-shrink-0 border-r border-white/5 bg-black/80 backdrop-blur-xl absolute md:static transition-transform duration-300 -translate-x-full md:translate-x-0">
    <div>
        <!-- Logo -->
        <div class="h-24 flex items-center px-8 border-b border-white/5">
            <a href="dashboard.php" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-red-500 text-black flex items-center justify-center text-lg shadow-[0_0_15px_rgba(239,68,68,0.2)] group-hover:shadow-[0_0_25px_rgba(239,68,68,0.4)] transition-all">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold tracking-tight text-xl text-white group-hover:text-red-500 transition-colors">ADMIN</span>
                    <span class="text-[9px] text-gray-500 font-mono tracking-widest uppercase">Panel</span>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-2 mt-4">
            <div class="px-4 py-2 text-[10px] font-mono text-gray-600 uppercase tracking-widest">Management</div>

            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all group <?= $current_page == 'dashboard.php' ? 'bg-red-500/10 text-red-500 border border-red-500/10' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-chart-line w-5 <?= $current_page == 'dashboard.php' ? '' : 'text-gray-600 group-hover:text-white' ?> transition-colors text-center"></i>
                <span class="font-medium text-sm">Overview</span>
            </a>

            <a href="users.php" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all group <?= $current_page == 'users.php' ? 'bg-red-500/10 text-red-500 border border-red-500/10' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-users w-5 <?= $current_page == 'users.php' ? '' : 'text-gray-600 group-hover:text-white' ?> transition-colors text-center"></i>
                <span class="font-medium text-sm">Users</span>
            </a>

            <a href="projects.php" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all group <?= $current_page == 'projects.php' ? 'bg-red-500/10 text-red-500 border border-red-500/10' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-cubes w-5 <?= $current_page == 'projects.php' ? '' : 'text-gray-600 group-hover:text-white' ?> transition-colors text-center"></i>
                <span class="font-medium text-sm">Projects</span>
            </a>
            
            <a href="logs.php" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all group <?= $current_page == 'logs.php' ? 'bg-red-500/10 text-red-500 border border-red-500/10' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-list-alt w-5 <?= $current_page == 'logs.php' ? '' : 'text-gray-600 group-hover:text-white' ?> transition-colors text-center"></i>
                <span class="font-medium text-sm">Audit Logs</span>
            </a>

            <a href="system.php" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all group <?= $current_page == 'system.php' ? 'bg-red-500/10 text-red-500 border border-red-500/10' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-server w-5 <?= $current_page == 'system.php' ? '' : 'text-gray-600 group-hover:text-white' ?> transition-colors text-center"></i>
                <span class="font-medium text-sm">System Health</span>
            </a>

            <a href="analytics.php" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all group <?= $current_page == 'analytics.php' ? 'bg-red-500/10 text-red-500 border border-red-500/10' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-chart-bar w-5 <?= $current_page == 'analytics.php' ? '' : 'text-gray-600 group-hover:text-white' ?> transition-colors text-center"></i>
                <span class="font-medium text-sm">Traffic Analytics</span>
            </a>

            <a href="announcements.php" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all group <?= $current_page == 'announcements.php' ? 'bg-red-500/10 text-red-500 border border-red-500/10' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-bullhorn w-5 <?= $current_page == 'announcements.php' ? '' : 'text-gray-600 group-hover:text-white' ?> transition-colors text-center"></i>
                <span class="font-medium text-sm">Announcements</span>
            </a>

            <div class="px-4 py-2 mt-8 text-[10px] font-mono text-gray-600 uppercase tracking-widest">System</div>

            <a href="../dashboard.php" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all group text-gray-400 hover:text-white hover:bg-white/5 border border-transparent">
                <i class="fas fa-arrow-left w-5 text-gray-600 group-hover:text-white transition-colors text-center"></i>
                <span class="font-medium text-sm">Back to User Panel</span>
            </a>
        </nav>
    </div>

    <!-- Bottom Section -->
    <div>
        <!-- User Profile -->
        <div class="p-4 mx-4 mb-4 rounded-2xl bg-white/5 border border-white/5 hover:border-red-500/20 transition-colors group relative">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-red-500 to-orange-600 flex items-center justify-center font-bold text-black shadow-lg">
                    <?= htmlspecialchars(substr($_SESSION["username"] ?? 'A', 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm text-white truncate"><?= htmlspecialchars($_SESSION["username"] ?? 'Admin') ?></div>
                    <div class="text-[10px] text-red-500 font-mono truncate">Administrator</div>
                </div>
                
                <form action="../../includes/user_actions/logout.php" method="POST">
                    <button type="submit" title="Logout" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:bg-red-500/10 hover:text-red-400 transition-all">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
