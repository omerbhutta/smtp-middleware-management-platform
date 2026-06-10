<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= BASE_URL ?>">
    <title><?= escape($title ?? 'Login') ?> - SMMP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Inter', sans-serif;
            background: #0a0e1a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        #particles-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(59,130,246,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59,130,246,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .bg-glow {
            position: fixed;
            z-index: 0;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.15;
        }
        .bg-glow-1 { width: 500px; height: 500px; background: #3b82f6; top: -200px; left: -100px; animation: float 20s ease-in-out infinite; }
        .bg-glow-2 { width: 400px; height: 400px; background: #06b6d4; bottom: -200px; right: -100px; animation: float 25s ease-in-out infinite reverse; }
        .bg-glow-3 { width: 300px; height: 300px; background: #8b5cf6; top: 50%; left: 50%; transform: translate(-50%, -50%); animation: float 30s ease-in-out infinite 5s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        .auth-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .auth-card {
            background: rgba(26, 31, 46, 0.8);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(59, 130, 246, 0.15);
            border-radius: 24px;
            padding: 40px 32px;
            box-shadow: 0 24px 80px rgba(0,0,0,0.6), 0 0 40px rgba(59,130,246,0.05);
            transition: all 0.3s ease;
        }

        .auth-card:hover {
            border-color: rgba(59,130,246,0.3);
            box-shadow: 0 24px 80px rgba(0,0,0,0.6), 0 0 60px rgba(59,130,246,0.08);
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .auth-brand-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 16px;
            box-shadow: 0 8px 24px rgba(59,130,246,0.3);
        }

        .auth-brand h2 {
            color: #e8eaed;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .auth-brand p {
            color: #6b7280;
            font-size: 0.82rem;
        }

        .form-label-smm {
            color: #9aa0b0;
            font-size: 0.82rem;
            font-weight: 500;
            margin-bottom: 6px;
            display: block;
        }

        .input-smm {
            width: 100%;
            padding: 12px 16px;
            background: rgba(15, 20, 30, 0.6);
            border: 1px solid #2a3040;
            border-radius: 12px;
            color: #e8eaed;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .input-smm:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1), inset 0 0 0 1px rgba(59,130,246,0.1);
        }

        .input-smm::placeholder { color: #4b5563; }

        .btn-auth {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(59,130,246,0.3);
        }

        .btn-auth:active { transform: translateY(0); }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #2a3040;
            color: #6b7280;
            font-size: 0.78rem;
        }

        .auth-footer a { color: #3b82f6; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }

        .alert-smm {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-smm-danger { background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.2); color: #ef4444; }
        .alert-smm-success { background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.2); color: #10b981; }

        .input-group-smm {
            position: relative;
        }

        .input-group-smm i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #4b5563;
            font-size: 0.9rem;
        }

        .input-group-smm .input-smm { padding-left: 42px; }

        @media (max-width: 480px) {
            .auth-card { padding: 28px 20px; }
        }
    </style>
</head>
<body>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>
    <div class="bg-glow bg-glow-3"></div>
    <div class="bg-grid"></div>
    <canvas id="particles-canvas"></canvas>

    <div class="auth-container">
        <div class="auth-card">
            <?= $content ?? '' ?>
        </div>
    </div>

    <script>
    // Particles
    (function() {
        var canvas = document.getElementById('particles-canvas');
        var ctx = canvas.getContext('2d');
        var particles = [];
        var count = 80;

        function resize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resize();
        window.addEventListener('resize', resize);

        for (var i = 0; i < count; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * 0.5,
                vy: (Math.random() - 0.5) * 0.5,
                r: Math.random() * 2 + 0.5,
                a: Math.random() * 0.4 + 0.1
            });
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(function(p) {
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < 0) p.x = canvas.width;
                if (p.x > canvas.width) p.x = 0;
                if (p.y < 0) p.y = canvas.height;
                if (p.y > canvas.height) p.y = 0;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(59, 130, 246, ' + p.a + ')';
                ctx.fill();
            });

            // Draw connections
            for (var i = 0; i < particles.length; i++) {
                for (var j = i + 1; j < particles.length; j++) {
                    var dx = particles[i].x - particles[j].x;
                    var dy = particles[i].y - particles[j].y;
                    var dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 150) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = 'rgba(59, 130, 246, ' + (0.08 * (1 - dist / 150)) + ')';
                        ctx.lineWidth = 0.5;
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }
        animate();
    })();
    </script>
</body>
</html>
