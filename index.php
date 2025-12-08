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
    <title>DOCK-HOSTING :: LOGIN</title>
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
                        bg: '#000000',      // Solid Black
                        panel: '#0a0a0a',   // Slightly lighter for cards
                        border: '#1f1f1f',  // Subtle borders
                        brand: {
                            DEFAULT: '#2dd4bf', // Teal-400 (Vibrant Teal)
                            hover: '#14b8a6',   // Teal-500
                            glow: 'rgba(45, 212, 191, 0.5)'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                        'slide-up': 'slideUp 0.5s ease-out forwards',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #000; color: #fff; }
        
        /* Glass effect specifically for black background */
        .glass-panel {
            background: rgba(10, 10, 10, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid #1f1f1f;
            box-shadow: 0 0 40px -10px rgba(45, 212, 191, 0.15); /* Teal shadow glow */
        }

        .input-field {
            background: #050505;
            border: 1px solid #1f1f1f;
            color: white;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: #2dd4bf;
            background: #0a0a0a;
            outline: none;
            box-shadow: 0 0 0 1px #2dd4bf;
        }

        /* Glow Utilities */
        .box-glow { box-shadow: 0 0 30px rgba(45, 212, 191, 0.2); }
    </style>
</head>

 <?php if(isset($_GET["error"])){
            $error_message = $_GET["error"];?>
            <div class="absolute top-5 left-1/2 -translate-x-1/2 w-[90%] max-w-xl 
    bg-red-600/80 border border-red-400 text-white px-4 py-3 rounded-xl 
    font-mono text-sm shadow-lg animate-slide-up z-50 backdrop-blur-md">
    <div class="flex justify-between items-center">
        <span><i class="fas fa-circle-exclamation mr-2"></i><?= $error_message ?></span>
        <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
       <?php } ?>
<body class="h-screen w-full flex items-center justify-center relative overflow-hidden font-sans selection:bg-brand selection:text-black">

    <!-- Background Elements -->
    <!-- Grid -->
    <div class="absolute inset-0 z-0 opacity-20" 
         style="background-image: linear-gradient(#1f1f1f 1px, transparent 1px), linear-gradient(90deg, #1f1f1f 1px, transparent 1px); background-size: 40px 40px;">
    </div>

    <!-- Teal Ambient Glow (Top Right & Bottom Left) -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-brand/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-brand/5 rounded-full blur-[100px] pointer-events-none"></div>
       
    <!-- Main Content Area -->
    <div class="w-full max-w-md z-10 px-6 animate-slide-up">
        
        
        <!-- Logo Header -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-brand/10 border border-brand/20 text-brand mb-4 box-glow">
                <!-- Using a Box icon to represent Containers/Docker -->
                <i class="fas fa-cubes text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold tracking-tight">DOCK<span class="text-brand">-HOSTING</span></h1>
            <p class="text-gray-500 text-sm mt-2 font-mono">Containerized Student Cloud</p>
        </div>

        <!-- Auth Container -->
        <div class="glass-panel rounded-2xl p-8 relative overflow-hidden">
            
            <!-- Decorative Top Border -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-brand to-transparent opacity-70"></div>

            <!-- LOGIN FORM -->
            <div id="login-form" class="transition-opacity duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold">Sign In</h2>
                    <span class="text-xs font-mono text-gray-500">SECURE ACCESS</span>
                </div>
                
                <form action="includes/login.php" method="POST">
                    <div class="space-y-5">
                        <!-- Email -->
                        <div class="space-y-1">
                            <label class="text-xs font-mono text-gray-400 uppercase ml-1">Email Address</label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute left-4 top-3.5 text-gray-600"></i>
                                <input type="email" name="email" placeholder="student@youcode.ma" 
                                       class="input-field w-full py-3 pl-10 pr-4 rounded-lg text-sm placeholder-gray-700 font-mono" required>
                            </div>
                        </div>
                        
                        <!-- Password -->
                        <div class="space-y-1">
                            <label class="text-xs font-mono text-gray-400 uppercase ml-1">Password</label>
                            <div class="relative">
                                <i class="fas fa-key absolute left-4 top-3.5 text-gray-600"></i>
                                <input type="password" name="password" placeholder="••••••••" 
                                       class="input-field w-full py-3 pl-10 pr-4 rounded-lg text-sm placeholder-gray-700 font-mono" required>
                            </div>
                            <div class="text-right">
                                <a href="#" class="text-[11px] text-gray-500 hover:text-brand transition-colors">Forgot Password?</a>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-brand hover:bg-brand-hover text-black font-bold py-3.5 rounded-lg transition-all transform hover:scale-[1.02] shadow-lg shadow-brand/20 flex items-center justify-center gap-2">
                            <span>Access Terminal</span>
                            <i class="fas fa-terminal text-xs"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-border text-center">
                    <p class="text-sm text-gray-500 mb-2">No active containers?</p>
                    <button onclick="switchView('register')" class="text-brand font-bold text-sm hover:text-white transition-colors">
                        Deploy New Instance
                    </button>
                </div>
            </div>

            <!-- REGISTER FORM -->
            <div id="register-form" class="hidden transition-opacity duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold">New Account</h2>
                    <span class="text-xs font-mono text-brand">DOCKER READY</span>
                </div>
                
                <form action="includes/signup.php" method="POST">
                    <div class="space-y-4">
                        <!-- Name Fields -->
                        <div class="w-full">
                            <div class="space-y-1">
                                <label class="text-xs font-mono text-gray-400 uppercase ml-1">Username</label>
                                <input type="text" name="username" class="input-field w-full py-3 px-4 rounded-lg text-sm font-mono" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="space-y-1">
                            <label class="text-xs font-mono text-gray-400 uppercase ml-1">Academic Email</label>
                            <div class="relative">
                                <i class="fas fa-graduation-cap absolute left-4 top-3.5 text-gray-600"></i>
                                <input type="email" name="email" placeholder="@student.youcode.ma" 
                                       class="input-field w-full py-3 pl-10 pr-4 rounded-lg text-sm placeholder-gray-700 font-mono" required>
                            </div>
                        </div>
                        
                        <!-- Password -->
                        <div class="space-y-1">
                            <label class="text-xs font-mono text-gray-400 uppercase ml-1">Password</label>
                            <div class="relative">
                                <i class="fas fa-lock absolute left-4 top-3.5 text-gray-600"></i>
                                <input type="password" name="password" 
                                       class="input-field w-full py-3 pl-10 pr-4 rounded-lg text-sm placeholder-gray-700 font-mono" required>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-white text-black hover:bg-gray-200 font-bold py-3.5 rounded-lg transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2">
                            <span>Initialize Environment</span>
                            <i class="fas fa-bolt text-brand"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-border text-center">
                    <p class="text-sm text-gray-500 mb-2">Already deployed?</p>
                    <button onclick="switchView('login')" class="text-brand font-bold text-sm hover:text-white transition-colors">
                        Return to Console
                    </button>
                </div>
            </div>

        </div>

        <!-- Footer Info -->
        <div class="mt-8 flex justify-center gap-6 text-xs text-gray-600 font-mono">
            <span>&copy; 2025 Dock-Hosting</span>
            <span>•</span>
            <span class="flex items-center gap-1"><span class="w-2 h-2 bg-brand rounded-full animate-pulse"></span> Daemon Running</span>
        </div>

    </div>

    <!-- JavaScript for Toggle -->
    <script>
        function switchView(view) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            
            if (view === 'register') {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                registerForm.classList.add('animate-fade-in');
            } else {
                registerForm.classList.add('hidden');
                loginForm.classList.remove('hidden');
                loginForm.classList.add('animate-fade-in');
            }
        }
    </script>
</body>
</html>