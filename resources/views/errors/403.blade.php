<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied | Sws SaaS</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    
    <style>
        :root {
            --primary: #3b71ca;
            --primary-light: #eef4ff;
            --accent: #f43f5e;
            --dark: #0f172a;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Animated Background Blobs */
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(59, 113, 202, 0.15) 0%, rgba(59, 113, 202, 0) 70%);
            border-radius: 50%;
            z-index: -1;
            filter: blur(50px);
            animation: move 20s infinite alternate;
        }
        .blob-1 { top: -10%; left: -10%; background: radial-gradient(circle, rgba(244, 63, 94, 0.1) 0%, rgba(244, 63, 94, 0) 70%); }
        .blob-2 { bottom: -10%; right: -10%; }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(50px, 50px) scale(1.1); }
        }

        .error-container {
            width: 100%;
            max-width: 540px;
            padding: 20px;
            z-index: 10;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 40px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
        }

        .error-header {
            position: relative;
            margin-bottom: 30px;
        }

        .error-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            background: rgba(244, 63, 94, 0.1);
            color: var(--accent);
            border-radius: 100px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .error-code-bg {
            font-family: 'Outfit', sans-serif;
            font-size: 120px;
            font-weight: 800;
            color: rgba(15, 23, 42, 0.03);
            line-height: 1;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
        }

        .icon-box {
            width: 90px;
            height: 90px;
            background: white;
            color: var(--accent);
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            box-shadow: 0 10px 25px -5px rgba(244, 63, 94, 0.2), 0 8px 10px -6px rgba(244, 63, 94, 0.2);
            transform: rotate(-10deg);
            transition: transform 0.3s ease;
        }

        .glass-card:hover .icon-box {
            transform: rotate(0deg) scale(1.05);
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 32px;
            margin-bottom: 12px;
            color: var(--dark);
        }

        p {
            color: #64748b;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .btn-group-custom {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-primary-custom {
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(59, 113, 202, 0.3);
            text-decoration: none;
        }

        .btn-primary-custom:hover {
            background: #2a5bb0;
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(59, 113, 202, 0.4);
            color: white;
        }

        .btn-secondary-custom {
            background: white;
            color: var(--dark);
            border: 1px solid #e2e8f0;
            padding: 14px 28px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-secondary-custom:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: var(--dark);
        }

        .footer-note {
            margin-top: 40px;
            font-size: 13px;
            color: #94a3b8;
        }

        .footer-note a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 576px) {
            .glass-card {
                padding: 30px 20px;
                border-radius: 24px;
            }
            h1 { font-size: 26px; }
            p { font-size: 14px; }
        }
    </style>
</head>
<body>

    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="error-container">
        <div class="glass-card">
            <div class="error-header">
                <div class="error-badge">Error 403</div>
                <div class="error-code-bg">403</div>
                <div class="icon-box">
                    <i data-feather="shield-off" style="width: 42px; height: 42px;"></i>
                </div>
            </div>

            <h1>Access Restricted</h1>
            <p>
                {{ $exception->getMessage() ?: 'I\'m sorry, but you don\'t have the necessary permissions to access this specific area. Please contact your lab administrator to request access.' }}
            </p>

            <div class="btn-group-custom">
                <a href="{{ url()->previous() == url()->current() ? url('/') : url()->previous() }}" class="btn-primary-custom">
                    <i data-feather="arrow-left" style="width: 18px; height: 18px;"></i>
                    Go Back Previous Page
                </a>
                
                <a href="{{ url('/') }}" class="btn-secondary-custom">
                    <i data-feather="home" style="width: 18px; height: 18px;"></i>
                    Return to Dashboard
                </a>
            </div>

            <div class="footer-note">
                Having issues? Reach out to <a href="mailto:support@Sws.com">support@Sws.com</a>
            </div>
        </div>
    </div>

    <script>
        feather.replace()
    </script>
</body>
</html>