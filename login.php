<?php
    session_start();
    if(isset($_SESSION["id"])){
        header("location: pages/dashboard.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCK-HOSTING :: Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js" type="module"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Space Grotesk', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            DEFAULT: '#2dd4bf', 
                            hover: '#14b8a6',
                        }
                    },
                    backgroundImage: {
                        'grid-pattern': "linear-gradient(#1f1f1f 1px, transparent 1px), linear-gradient(90deg, #1f1f1f 1px, transparent 1px)",
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #000; color: #fff; }
        .glass-panel {
            background: rgba(10, 10, 10, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .form-input {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .form-input:focus {
            background: rgba(45, 212, 191, 0.05);
            border-color: #2dd4bf;
            outline: none;
            box-shadow: 0 0 20px rgba(45, 212, 191, 0.1);
        }
    </style>
</head>
<body class="h-screen flex items-center justify-center p-4 relative overflow-hidden bg-black selection:bg-brand selection:text-black">

    <!-- Background Grid -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 opacity-20 bg-grid-pattern bg-[length:40px_40px] animate-[pulse_8s_ease-in-out_infinite]"></div>
        <div class="absolute top-1/2 left-1/4 w-[500px] h-[500px] bg-brand/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[120px] pointer-events-none"></div>
    </div>

    <!-- Notification -->
    <?php if(isset($_GET["msg"]) || isset($_GET["error"])): 
        $msg = $_GET["msg"] ?? $_GET["error"];
        $type = isset($_GET["error"]) ? "error" : "success";
        $color = $type == "error" ? "red" : "brand";
    ?>
    <div class="fixed top-5 right-5 z-50 animate-[slide-in_0.5s_ease-out]">
        <div class="glass-panel px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 text-sm font-mono border-l-4 border-<?= $color ?>-500">
            <i class="fas fa-<?= $type == 'error' ? 'exclamation-triangle' : 'check-circle' ?> text-<?= $color ?>-400"></i>
            <span><?= htmlspecialchars($msg) ?></span>
            <button onclick="this.parentElement.parentElement.remove()" class="hover:text-white text-gray-500 ml-4"><i class="fas fa-times"></i></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Card -->
    <div class="relative z-10 w-full max-w-5xl h-[600px] glass-panel rounded-3xl overflow-hidden shadow-2xl flex">
        
        <!-- Left Side: Animation -->
        <div class="hidden lg:flex w-1/2 bg-black/40 relative items-center justify-center flex-col p-12 border-r border-white/5">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-brand/5 via-transparent to-transparent"></div>
            
            <dotlottie-wc src="https://lottie.host/c47e3d43-9351-4bf6-9e77-e84a4f5d5e1b/jgQCDmysWv.lottie" autoplay loop style="width: 100%; height: 100%; max-width: 400px;"></dotlottie-wc>
            
            <div class="text-center mt-[-40px] relative z-10">
                <h2 class="text-2xl font-bold mb-2">Deploy in Seconds</h2>
                <p class="text-gray-500 text-sm font-mono">Your Code. Containerized. Live.</p>
            </div>
        </div>

        <!-- Right Side: Forms -->
        <div class="w-full lg:w-1/2 p-8 md:p-12 relative flex flex-col justify-center">
            
            <!-- Logo -->
            <a href="index.php" class="absolute top-8 right-8 flex items-center gap-2 group opacity-50 hover:opacity-100 transition-opacity">
                <i class="fas fa-cubes text-brand"></i>
                <span class="font-bold tracking-tight text-sm">DOCK-HOSTING</span>
            </a>

            <!-- Toggle Buttons -->
            <div class="flex gap-6 mb-10 border-b border-white/10 pb-1 w-fit">
                <button onclick="switchTab('login')" id="tab-login" class="text-lg font-bold pb-2 border-b-2 border-brand text-white transition-all">Sign In</button>
                <button onclick="switchTab('register')" id="tab-register" class="text-lg font-bold pb-2 border-b-2 border-transparent text-gray-500 hover:text-white transition-all">Create Account</button>
            </div>

            <!-- Login Form -->
            <form id="form-login" action="includes/login.php" method="POST" class="space-y-5 animate-[fade-in_0.3s_ease-out]">
                <div>
                    <label class="block text-xs font-mono text-gray-500 uppercase mb-2">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="email" name="email" required placeholder="name@company.com" 
                            class="w-full pl-11 pr-4 py-3 rounded-xl form-input text-sm text-white placeholder-gray-600 focus:placeholder-gray-500">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-mono text-gray-500 uppercase">Password</label>
                        <a href="#" class="text-[10px] text-brand hover:underline">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="password" name="password" required placeholder="••••••••" 
                            class="w-full pl-11 pr-4 py-3 rounded-xl form-input text-sm text-white placeholder-gray-600">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 rounded-xl bg-brand hover:bg-brand-hover text-black font-bold tracking-wide shadow-[0_0_20px_rgba(45,212,191,0.2)] hover:shadow-[0_0_30px_rgba(45,212,191,0.4)] transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2 mt-4">
                    <span>ACCESS CONSOLE</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <!-- Register Form (Hidden by default) -->
            <form id="form-register" action="includes/signup.php" method="POST" class="space-y-4 hidden animate-[fade-in_0.3s_ease-out]">
                <div>
                    <label class="block text-xs font-mono text-gray-500 uppercase mb-2">Username</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="text" name="username" required placeholder="dev_master" 
                            class="w-full pl-11 pr-4 py-3 rounded-xl form-input text-sm text-white placeholder-gray-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-mono text-gray-500 uppercase mb-2">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="email" name="email" required placeholder="name@company.com" 
                            class="w-full pl-11 pr-4 py-3 rounded-xl form-input text-sm text-white placeholder-gray-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-mono text-gray-500 uppercase mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="password" name="password" required placeholder="Create a strong password" 
                            class="w-full pl-11 pr-4 py-3 rounded-xl form-input text-sm text-white placeholder-gray-600">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 rounded-xl bg-white hover:bg-gray-200 text-black font-bold tracking-wide transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2 mt-6">
                    <i class="fas fa-rocket text-brand"></i>
                    <span>CREATE ACCOUNT</span>
                </button>
            </form>
            
            <div class="text-center mt-8 text-xs text-gray-600">
                By continuing, you agree to our <a href="#" class="underline hover:text-white">Terms of Service</a>.
            </div>

        </div>
    </div>

    <script>
        function switchTab(tab) {
            const loginForm = document.getElementById('form-login');
            const registerForm = document.getElementById('form-register');
            const loginTab = document.getElementById('tab-login');
            const registerTab = document.getElementById('tab-register');

            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                
                loginTab.classList.add('border-brand', 'text-white');
                loginTab.classList.remove('border-transparent', 'text-gray-500');
                
                registerTab.classList.remove('border-brand', 'text-white');
                registerTab.classList.add('border-transparent', 'text-gray-500');
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                
                registerTab.classList.add('border-brand', 'text-white');
                registerTab.classList.remove('border-transparent', 'text-gray-500');
                
                loginTab.classList.remove('border-brand', 'text-white');
                loginTab.classList.add('border-transparent', 'text-gray-500');
            }
        }

        // Check URL for view parameter
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get('view') === 'register'){
            switchTab('register');
        }
    </script>
</body>
</html>
