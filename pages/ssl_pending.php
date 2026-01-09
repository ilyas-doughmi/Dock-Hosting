<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Securing Connection...</title>
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
                        brand: {
                            DEFAULT: '#2dd4bf', 
                            dim: 'rgba(45, 212, 191, 0.1)',
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #000; color: #fff; }
        .loader-ring {
            border: 4px solid rgba(45, 212, 191, 0.1);
            border-left-color: #2dd4bf;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="h-screen w-full flex flex-col items-center justify-center p-4 text-center">

    <div class="relative mb-8">
        <div class="absolute inset-0 bg-brand/20 blur-xl rounded-full animate-pulse-slow"></div>
        <div class="loader-ring relative z-10"></div>
    </div>

    <h1 class="text-3xl font-bold mb-4 tracking-tight">Securing Your Connection</h1>
    
    <div class="max-w-md space-y-4 text-gray-400">
        <p>We are provisioning a secure SSL certificate for <span class="text-white font-mono bg-white/10 px-2 py-0.5 rounded text-sm"><?= htmlspecialchars($_GET['domain'] ?? 'your project') ?></span>.</p>
        
        <p class="text-sm">This process typically takes 30-60 seconds for the first launch. Please wait while we establish a secure tunnel.</p>
    </div>

    <div class="mt-8 flex flex-col items-center gap-2">
        <div class="h-1 w-64 bg-white/10 rounded-full overflow-hidden">
            <div id="progress" class="h-full bg-brand w-0 transition-all duration-100 ease-linear"></div>
        </div>
        <div class="text-xs font-mono text-brand blink" id="status-text">Generatings Keys...</div>
    </div>

    <script>
        const targetUrl = "<?= htmlspecialchars($_GET['url'] ?? '#') ?>";
        const progressBar = document.getElementById('progress');
        const statusText = document.getElementById('status-text');
        
        let progress = 0;
        const duration = 20000; 
        const intervalTime = 100;
        const steps = duration / intervalTime;
        const increment = 100 / steps;

        const statuses = [
            "Requesting Certificate...",
            "Validating Domain...",
            "Generating Keys...",
            "Configuring Nginx Proxy...",
            "Finalizing Secure Handshake..."
        ];

        let interval = setInterval(() => {
            progress += increment;
            progressBar.style.width = Math.min(progress, 100) + "%";

            // Update status text based on progress
            const statusIndex = Math.floor((progress / 100) * statuses.length);
            if(statuses[statusIndex]) statusText.innerText = statuses[statusIndex];

            if (progress >= 100) {
                clearInterval(interval);
                statusText.innerText = "Redirecting...";
                statusText.classList.add("text-white");
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 500);
            }
        }, intervalTime);

    </script>
</body>
</html>
