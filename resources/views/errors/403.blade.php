<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied | Pathology Lab</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    
    <style>
        :root {
            --primary: #3b71ca;
            --primary-glow: rgba(59, 113, 202, 0.15);
            --accent: #f43f5e;
            --accent-glow: rgba(244, 63, 94, 0.15);
            --dark: #0f172a;
            --light-bg: #f8fafc;
        }

        body {
            background: radial-gradient(circle at 10% 20%, rgba(238, 244, 255, 1) 0%, rgba(243, 228, 255, 0.4) 100%);
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Mesh Background */
        .mesh-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--primary-glow) 0%, rgba(59, 113, 202, 0) 70%);
            border-radius: 50%;
            z-index: -1;
            filter: blur(80px);
            animation: float-slow 25s infinite alternate ease-in-out;
        }
        .mesh-glow-2 {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--accent-glow) 0%, rgba(244, 63, 94, 0) 70%);
            border-radius: 50%;
            z-index: -1;
            filter: blur(80px);
            bottom: -5%;
            right: -5%;
            animation: float-slow-reverse 20s infinite alternate ease-in-out;
        }

        @keyframes float-slow {
            0% { transform: translate(-50px, -50px) scale(1); }
            100% { transform: translate(50px, 50px) scale(1.15); }
        }
        @keyframes float-slow-reverse {
            0% { transform: translate(50px, 50px) scale(1); }
            100% { transform: translate(-50px, -50px) scale(1.1); }
        }

        .error-container {
            width: 100%;
            max-width: 580px;
            padding: 24px;
            z-index: 10;
        }

        .premium-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: 36px;
            box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.08), 
                        0 0 50px -10px rgba(59, 113, 202, 0.05);
            overflow: hidden;
            padding: 48px 40px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .premium-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--accent) 0%, var(--primary) 100%);
        }

        .premium-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 40px 80px -20px rgba(15, 23, 42, 0.12),
                        0 0 60px -5px rgba(59, 113, 202, 0.08);
        }

        .error-header {
            position: relative;
            margin-bottom: 24px;
        }

        .badge-capsule {
            display: inline-flex;
            align-items: center;
            padding: 6px 18px;
            background: rgba(244, 63, 94, 0.08);
            color: var(--accent);
            border: 1px solid rgba(244, 63, 94, 0.15);
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(244, 63, 94, 0.03);
        }

        .error-code-watermark {
            font-family: 'Outfit', sans-serif;
            font-size: 140px;
            font-weight: 900;
            color: rgba(15, 23, 42, 0.02);
            line-height: 1;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            user-select: none;
        }

        .icon-shield-wrapper {
            width: 100px;
            height: 100px;
            background: white;
            color: var(--accent);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            box-shadow: 0 20px 35px -10px rgba(244, 63, 94, 0.25);
            transform: rotate(-8deg);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(244, 63, 94, 0.1);
        }

        .premium-card:hover .icon-shield-wrapper {
            transform: rotate(0deg) scale(1.06);
            box-shadow: 0 25px 40px -8px rgba(244, 63, 94, 0.3);
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 34px;
            margin-top: 10px;
            margin-bottom: 12px;
            color: var(--dark);
            letter-spacing: -0.5px;
        }

        p.error-message {
            color: #64748b;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 24px;
            padding: 0 10px;
        }

        /* Diagnostic User Capsule */
        .user-capsule {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 8px 18px;
            background: rgba(15, 23, 42, 0.03);
            border: 1px solid rgba(15, 23, 42, 0.05);
            border-radius: 100px;
            margin-bottom: 30px;
            max-width: 100%;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary) 0%, #1d4ed8 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            box-shadow: 0 4px 10px rgba(59, 113, 202, 0.25);
        }

        .user-details {
            text-align: left;
        }

        .user-name {
            font-weight: 700;
            font-size: 12px;
            color: var(--dark);
            line-height: 1.2;
        }

        .user-role {
            font-size: 10px;
            color: #64748b;
            font-weight: 500;
            line-height: 1.2;
        }

        /* Auto-Redirect Progress Area */
        .redirect-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 16px 20px;
            margin-bottom: 32px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);
        }

        .btn-pause {
            border: none;
            background: rgba(59, 113, 202, 0.08);
            color: var(--primary);
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-pause:hover {
            background: rgba(59, 113, 202, 0.15);
            color: #1d4ed8;
        }

        .progress-bar-container {
            height: 6px;
            background: #f1f5f9;
            border-radius: 100px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary) 0%, #1d4ed8 100%);
            width: 100%;
            border-radius: 100px;
            transition: width 1s linear;
        }

        /* Action Buttons */
        .button-stack {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-premium-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #1a56be 100%);
            color: white !important;
            border: none;
            padding: 15px 30px;
            border-radius: 18px;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 20px -5px rgba(59, 113, 202, 0.35);
            text-decoration: none;
        }

        .btn-premium-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(59, 113, 202, 0.45);
            filter: brightness(1.05);
        }

        .btn-premium-secondary {
            background: #ffffff;
            color: var(--dark) !important;
            border: 1px solid #e2e8f0;
            padding: 15px 30px;
            border-radius: 18px;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .btn-premium-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        .footer-support {
            margin-top: 36px;
            font-size: 13px;
            color: #94a3b8;
        }

        .footer-support a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .footer-support a:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .premium-card {
                padding: 36px 20px;
                border-radius: 28px;
            }
            h1 { font-size: 28px; }
            p.error-message { font-size: 14px; }
        }
    </style>
</head>
<body>

    <div class="mesh-glow"></div>
    <div class="mesh-glow mesh-glow-2"></div>

    <div class="error-container">
        <div class="premium-card">
            <div class="error-header">
                <div class="badge-capsule">Security Alert</div>
                <div class="error-code-watermark">403</div>
                <div class="icon-shield-wrapper">
                    <i data-feather="shield-off" style="width: 44px; height: 44px;"></i>
                </div>
            </div>

            <h1>Access Restricted</h1>
            
            <p class="error-message">
                @if(auth()->check() && auth()->user()->collection_center_id)
                    Your Collection Center partner account is successfully authorized but does not have permission to view or manage this administrative resource.
                @else
                    {{ $exception->getMessage() ?: 'I\'m sorry, but you do not have the necessary permissions to access this specific area. Please contact your lab administrator to request access.' }}
                @endif
            </p>

            {{-- User Identity Capsule --}}
            @if(auth()->check())
                <div class="user-capsule shadow-sm">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">
                            Role: 
                            @if(auth()->user()->collection_center_id)
                                <span class="badge bg-primary text-white font-medium py-0.5 px-2 rounded">Collection Center</span>
                            @else
                                <span class="badge bg-secondary text-white font-medium py-0.5 px-2 rounded">{{ auth()->user()->roles->pluck('name')->first() ?? 'Partner' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Auto-Redirect Progress Card --}}
            <div class="redirect-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fs-12 text-muted fw-medium">Auto-redirecting back in <strong class="text-dark" id="countdown-sec">15</strong> seconds...</span>
                    <button id="btn-pause-redirect" class="btn-pause">
                        <i data-feather="pause" class="me-1" style="width: 11px; height: 11px; vertical-align: middle;"></i>Pause
                    </button>
                </div>
                <div class="progress-bar-container">
                    <div id="redirect-progress-bar" class="progress-bar-fill"></div>
                </div>
            </div>

            {{-- Action Stack --}}
            <div class="button-stack">
                <a href="{{ url()->previous() == url()->current() ? url('/') : url()->previous() }}" class="btn-premium-primary">
                    <i data-feather="arrow-left" style="width: 18px; height: 18px;"></i>
                    Go Back Previous Page
                </a>
                
                <a href="{{ url('/') }}" class="btn-premium-secondary">
                    <i data-feather="home" style="width: 18px; height: 18px;"></i>
                    Return to Dashboard
                </a>
            </div>

            <div class="footer-support">
                Need immediate assistance? Contact <a href="mailto:support@sws.com">support@sws.com</a>
            </div>
        </div>
    </div>

    <script>
        // Init feather icons
        feather.replace();

        // Redirect Timer script
        let seconds = 15;
        const total = 15;
        let isPaused = false;
        const countdownEl = document.getElementById('countdown-sec');
        const progressBar = document.getElementById('redirect-progress-bar');
        const pauseBtn = document.getElementById('btn-pause-redirect');
        
        const previousUrl = "{{ url()->previous() == url()->current() ? url('/') : url()->previous() }}";

        const interval = setInterval(() => {
            if (!isPaused) {
                seconds--;
                countdownEl.textContent = seconds;
                const percent = (seconds / total) * 100;
                progressBar.style.width = percent + '%';
                
                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.href = previousUrl;
                }
            }
        }, 1000);

        pauseBtn.addEventListener('click', () => {
            isPaused = !isPaused;
            if (isPaused) {
                pauseBtn.innerHTML = '<i data-feather="play" class="me-1" style="width: 11px; height: 11px; vertical-align: middle;"></i>Resume';
                progressBar.style.background = '#94a3b8';
            } else {
                pauseBtn.innerHTML = '<i data-feather="pause" class="me-1" style="width: 11px; height: 11px; vertical-align: middle;"></i>Pause';
                progressBar.style.background = 'linear-gradient(90deg, var(--primary) 0%, #1d4ed8 100%)';
            }
            feather.replace();
        });
    </script>
</body>
</html>