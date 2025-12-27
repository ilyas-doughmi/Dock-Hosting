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
    <title>DOCK-HOSTING :: AUTHENTICATION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@300;400;600;700&display=swap" rel="stylesheet">
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
                            hover: '#14b8a6', 
                            dim: '#115e59',
                        }
                    },
                    backgroundImage: {
                        'grid-pattern': "linear-gradient(#1f1f1f 1px, transparent 1px), linear-gradient(90deg, #1f1f1f 1px, transparent 1px)",
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                        'slide-up': 'slideUp 0.6s ease-out forwards',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
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
        
        .glass-panel {
            background: rgba(10, 10, 10, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .input-group {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .input-field {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            background: rgba(45, 212, 191, 0.05);
            border-color: #2dd4bf;
            outline: none;
            box-shadow: 0 0 0 1px rgba(45, 212, 191, 0.2);
        }

        .input-field:focus ~ i {
            color: #2dd4bf; 
        }

        /* Subtle glow behind the card */
        .card-glow {
            box-shadow: 0 0 100px 20px rgba(45, 212, 191, 0.1);
        }
    </style>
</head>

<?php if(isset($_GET["msg"])){
    $message = htmlspecialchars($_GET["msg"]);
    $is_error = (strpos(strtolower($message), 'invalid') !== false || 
                 strpos(strtolower($message), 'failed') !== false || 
                 strpos(strtolower($message), 'exists') !== false);
    
    $bg_color = $is_error ? 'bg-red-900/40 border-red-500/30 text-red-200' : 'bg-green-900/40 border-green-500/30 text-green-200';
    $icon_color = $is_error ? 'text-red-400' : 'text-green-400';
    $icon = $is_error ? 'fa-circle-exclamation' : 'fa-circle-check';
?>
    <div class="absolute top-5 left-1/2 -translate-x-1/2 w-[90%] max-w-xl 
        <?= $bg_color ?> px-4 py-3 rounded-xl 
        font-mono text-sm shadow-xl animate-slide-up z-50 backdrop-blur-md flex items-center justify-between">
        <span class="flex items-center gap-3"><i class="fas <?= $icon ?> <?= $icon_color ?>"></i> <?= $message ?></span>
        <button onclick="this.parentElement.remove()" class="<?= $icon_color ?> hover:text-white transition-colors"><i class="fas fa-times"></i></button>
    </div>
<?php } ?>

<body class="h-screen w-full flex items-center justify-center relative overflow-hidden font-sans selection:bg-brand selection:text-black">

    <!-- Dynamic Background -->
    <div class="absolute inset-0 z-0 bg-black">
        <div class="absolute inset-0 opacity-20 bg-grid-pattern bg-[length:50px_50px] animate-[pulse-slow_8s_ease-in-out_infinite]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-brand/5 rounded-full blur-[150px] pointer-events-none animate-pulse-slow"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-500/5 rounded-full blur-[150px] pointer-events-none"></div>
    </div>

    <!-- Main Card Container -->
    <div class="w-full max-w-md z-10 px-6 animate-slide-up relative">
        
        <!-- Decoration behind card -->
        <div class="absolute inset-0 bg-brand/20 blur-[100px] opacity-20 pointer-events-none"></div>

        <!-- content -->
        <div class="relative">
            <!-- Header -->
            <div class="text-center mb-8">
                <a href="index.php" class="inline-flex items-center gap-2 text-gray-400 hover:text-white transition-colors mb-8 group">
                    <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center group-hover:border-brand/30 group-hover:bg-brand/10 transition-all">
                        <i class="fas fa-arrow-left text-xs group-hover:text-brand"></i>
                    </div>
                    <span class="text-sm font-mono">Back to Home</span>
                </a>
                
       
                
                <h1 class="text-4xl font-bold tracking-tight mb-2 text-white">Welcome Back</h1>
                <p class="text-gray-400 text-sm font-mono">Enter your credentials to access the console</p>
            </div>

            <!-- Auth Card -->
            <div class="glass-panel rounded-3xl p-8 sm:p-10 relative overflow-hidden border-t border-white/10">
                
                <!-- Top sheen -->
                <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-brand/50 to-transparent opacity-50"></div>

                <!-- Login Form -->
                <div id="login-form" class="transition-all duration-500 ease-in-out">
                    <form action="includes/login.php" method="POST" class="space-y-6">
                        
                        <div class="space-y-2">
                            <label class="text-xs font-mono text-gray-400 uppercase tracking-widest ml-1">Email Address</label>
                            <div class="input-group">
                                <i class="fas fa-envelope absolute left-4 top-4 text-gray-500 transition-colors pointer-events-none"></i>
                                <input type="email" name="email" placeholder="student@youcode.ma" 
                                       class="input-field w-full py-3.5 pl-11 pr-4 rounded-xl text-sm font-mono placeholder-gray-700" required>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-mono text-gray-400 uppercase tracking-widest ml-1">Password</label>
                            <div class="input-group">
                                <i class="fas fa-lock absolute left-4 top-4 text-gray-500 transition-colors pointer-events-none"></i>
                                <input type="password" name="password" placeholder="••••••••" 
                                       class="input-field w-full py-3.5 pl-11 pr-4 rounded-xl text-sm font-mono placeholder-gray-700" required>
                            </div>
                            <div class="flex justify-end">
                                <a href="#" class="text-[11px] text-gray-500 hover:text-brand transition-colors">Forgot Password?</a>
                            </div>
                        </div>

                        <button type="submit" class="w-full group relative overflow-hidden bg-brand hover:bg-white text-black font-bold py-4 rounded-xl transition-all shadow-[0_0_30px_rgba(45,212,191,0.2)] hover:shadow-[0_0_50px_rgba(45,212,191,0.4)]">
                            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/50 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                            <span class="flex items-center justify-center gap-2 relative z-10">
                                SIGN IN <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </span>
                        </button>
                    </form>

                    <div class="mt-8 text-center pt-6 border-t border-white/5">
                        <p class="text-sm text-gray-500 mb-3">No account yet?</p>
                        <button onclick="switchView('register')" class="text-white font-bold hover:text-brand transition-colors text-sm flex items-center justify-center gap-2 mx-auto group">
                            Create Student Account 
                            <i class="fas fa-chevron-right text-xs text-gray-600 group-hover:text-brand transition-colors"></i>
                        </button>
                    </div>
                </div>

                <!-- Register Form -->
                <div id="register-form" class="hidden transition-all duration-500 ease-in-out opacity-0 translate-x-10">
                    <form action="includes/signup.php" method="POST" class="space-y-5">
                        
                        <div class="space-y-2">
                            <label class="text-xs font-mono text-gray-400 uppercase tracking-widest ml-1">Username</label>
                            <div class="input-group">
                                <i class="fas fa-user absolute left-4 top-4 text-gray-500 transition-colors pointer-events-none"></i>
                                <input type="text" name="username" 
                                       class="input-field w-full py-3.5 pl-11 pr-4 rounded-xl text-sm font-mono" required>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-mono text-gray-400 uppercase tracking-widest ml-1">Academic Email</label>
                            <div class="input-group">
                                <i class="fas fa-graduation-cap absolute left-4 top-4 text-gray-500 transition-colors pointer-events-none"></i>
                                <input type="email" name="email" placeholder="@student.youcode.ma"
                                       class="input-field w-full py-3.5 pl-11 pr-4 rounded-xl text-sm font-mono placeholder-gray-700" required>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-mono text-gray-400 uppercase tracking-widest ml-1">Set Password</label>
                            <div class="input-group">
                                <i class="fas fa-key absolute left-4 top-4 text-gray-500 transition-colors pointer-events-none"></i>
                                <input type="password" name="password" 
                                       class="input-field w-full py-3.5 pl-11 pr-4 rounded-xl text-sm font-mono" required>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-white hover:bg-gray-200 text-black font-bold py-4 rounded-xl transition-all flex items-center justify-center gap-2 mt-4">
                            <span>INITIALIZE ACCOUNT</span>
                            <i class="fas fa-bolt text-brand"></i>
                        </button>
                    </form>

                    <div class="mt-8 text-center pt-6 border-t border-white/5">
                        <p class="text-sm text-gray-500 mb-3">Already have credentials?</p>
                        <button onclick="switchView('login')" class="text-white font-bold hover:text-brand transition-colors text-sm flex items-center justify-center gap-2 mx-auto group">
                            Back to Login
                            <i class="fas fa-chevron-left text-xs text-gray-600 group-hover:text-brand transition-colors"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Branding -->
            <div class="mt-12 flex items-center justify-center gap-3 opacity-50 hover:opacity-100 transition-opacity duration-300">
                <span class="text-[10px] font-mono text-gray-500 tracking-widest uppercase">Created at</span>
                <a href="https://youcode.ma/" target="_blank" class="block transition-transform hover:scale-105">
                    <img src="https://youcode.ma/images/logos/youcode.png" alt="YouCode" class="h-6 filter grayscale hover:grayscale-0 transition-all">
                </a>
            </div>
        </div>

    </div>

    <script>
        function switchView(view) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            
            if (view === 'register') {
                // Hide Login
                loginForm.style.opacity = '0';
                loginForm.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    loginForm.classList.add('hidden');
                    registerForm.classList.remove('hidden');
                    
                    // Show Register
                    requestAnimationFrame(() => {
                        registerForm.style.opacity = '1';
                        registerForm.style.transform = 'translateX(0)';
                    });
                }, 300);
            } else {
                // Hide Register
                registerForm.style.opacity = '0';
                registerForm.style.transform = 'translateX(20px)';
                
                setTimeout(() => {
                    registerForm.classList.add('hidden');
                    loginForm.classList.remove('hidden');
                    
                    // Show Login
                    requestAnimationFrame(() => {
                        loginForm.style.opacity = '1';
                        loginForm.style.transform = 'translateX(0)';
                    });
                }, 300);
            }
        }
    </script>
</body>
</html>
