<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Locked | Sws SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --danger: #ef4444;
            --dark: #1e293b;
        }
        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .lock-card {
            background: #fff;
            border-radius: 2rem;
            padding: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .icon-circle {
            width: 100px;
            height: 100px;
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 3rem;
            animation: pulse-danger 2s infinite;
        }
        @keyframes pulse-danger {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { transform: scale(1); box-shadow: 0 0 0 20px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        h1 {
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1rem;
            letter-spacing: -1px;
        }
        p {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .stats-box {
            background: #f1f5f9;
            border-radius: 1rem;
            padding: 1.25rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .btn-renew {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 1rem 2rem;
            border-radius: 1rem;
            font-weight: 700;
            width: 100%;
            transition: all 0.3s;
            margin-bottom: 1rem;
            text-decoration: none;
            display: inline-block;
        }
        .btn-renew:hover {
            background: #3651d1;
            transform: translateY(-2px);
            color: #fff;
        }
        .support-link {
            color: #94a3b8;
            font-size: 0.875rem;
            text-decoration: none;
        }
        .support-link:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="lock-card">
        <div class="icon-circle">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        </div>
        <h1>Account Locked</h1>
        <p>Your subscription has expired. To continue providing seamless healthcare services to your patients, please renew your subscription.</p>
        
        <div class="stats-box">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Lab Name</span>
                <span class="fw-bold text-dark">{{ auth()->user()->company->name }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted small fw-bold text-uppercase">Days Since Expiry</span>
                <span class="fw-bold text-danger">{{ now()->diffInDays(auth()->user()->company->trial_ends_at) }} Days</span>
            </div>
        </div>

        <a href="tel:+91XXXXXXXXXX" class="btn btn-renew">Renew Subscription Now</a>
        
        <div class="mt-3">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="support-link">
                Logout from Dashboard
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
        
        <div class="mt-4 pt-4 border-top">
            <p class="small mb-0">Need help? Contact support at <br><strong>support@swssaas.com</strong></p>
        </div>
    </div>
</body>
</html>
