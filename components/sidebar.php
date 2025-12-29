<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- SIDEBAR -->
<aside id="sidebar" class="w-64 sidebar flex flex-col justify-between h-full z-20 flex-shrink-0 border-r border-white/5 bg-black/80 backdrop-blur-xl absolute md:static transition-transform duration-300 -translate-x-full md:translate-x-0">
    <div>
        <!-- Logo -->
        <div class="h-24 flex items-center px-8 border-b border-white/5">
            <a href="dashboard.php" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-brand text-black flex items-center justify-center text-lg shadow-[0_0_15px_rgba(45,212,191,0.2)] group-hover:shadow-[0_0_25px_rgba(45,212,191,0.4)] transition-all">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold tracking-tight text-xl text-white group-hover:text-brand transition-colors">DOCK</span>
                    <span class="text-[9px] text-gray-500 font-mono tracking-widest uppercase">Hosting</span>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-2 mt-4">
            <div class="px-4 py-2 text-[10px] font-mono text-gray-600 uppercase tracking-widest">Platform</div>

            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all group <?= $current_page == 'dashboard.php' ? 'bg-brand/10 text-brand border border-brand/10' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-terminal w-5 <?= $current_page == 'dashboard.php' ? '' : 'text-gray-600 group-hover:text-white' ?> transition-colors text-center"></i>
                <span class="font-medium text-sm">Console</span>
                <?php if($current_page == 'dashboard.php'): ?>
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></span>
                <?php endif; ?>
            </a>

            <div class="px-4 py-2 mt-8 text-[10px] font-mono text-gray-600 uppercase tracking-widest">Personal</div>

            <a href="settings.php" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all group <?= $current_page == 'settings.php' ? 'bg-brand/10 text-brand border border-brand/10' : 'text-gray-400 hover:text-white hover:bg-white/5 border border-transparent' ?>">
                <i class="fas fa-cog w-5 <?= $current_page == 'settings.php' ? '' : 'text-gray-600 group-hover:text-white' ?> transition-colors text-center"></i>
                <span class="font-medium text-sm">Settings</span>
            </a>
        </nav>
    </div>

    <!-- Bottom Section -->
    <div>
        <!-- User Profile -->
        <div class="p-4 mx-4 mb-4 rounded-2xl bg-white/5 border border-white/5 hover:border-brand/20 transition-colors group relative">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-brand to-teal-600 flex items-center justify-center font-bold text-black shadow-lg">
                    <?= substr($_SESSION["username"] ?? 'G', 0, 1) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm text-white truncate"><?= $_SESSION["username"] ?? 'Guest' ?></div>
                    <div class="text-[10px] text-brand font-mono truncate">Online</div>
                </div>
                
                <form action="../includes/user_actions/logout.php" method="POST">
                    <button type="submit" title="Logout" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:bg-red-500/10 hover:text-red-400 transition-all">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>