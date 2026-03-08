<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied | Zytrixon SaaS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    
    <style>
        body {
            background-color: #f8fafc; /* Professional light gray */
            font-family: 'Inter', sans-serif;
        }
        .error-card {
            max-width: 480px;
            width: 100%;
        }
        .error-code {
            font-size: 80px;
            font-weight: 900;
            color: #e2e8f0;
            line-height: 1;
            margin-bottom: -20px;
            position: relative;
            z-index: 1;
        }
        .error-icon-box {
            width: 80px;
            height: 80px;
            background: #fff1f2;
            color: #f43f5e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>

    <div class="min-vh-100 d-flex align-items-center justify-content-center p-4">
        <div class="card error-card border-0 shadow-sm rounded-4 text-center p-4 p-md-5 bg-white">
            
            <div class="error-code">403</div>
            <div class="error-icon-box shadow-sm">
                <i data-feather="lock" style="width: 36px; height: 36px;"></i>
            </div>

            <h3 class="fw-bolder text-dark mb-2">Access Denied</h3>
            
            <p class="text-muted fs-15 mb-4 px-3">
                {{ $exception->getMessage() ?: 'You do not have permission to access this page or your workspace has been restricted.' }}
            </p>

            <div class="d-flex flex-column gap-2">
                <button onclick="window.history.back()" class="btn btn-light border fw-semibold py-2">
                    <i data-feather="arrow-left" class="me-2" style="width: 16px; height: 16px;"></i> Go Back
                </button>
                
                <a href="{{ url('/') }}" class="btn btn-primary fw-semibold py-2 shadow-sm">
                    Return to Homepage
                </a>
            </div>

            <div class="mt-5 border-top pt-4">
                <p class="fs-12 text-muted mb-0">Need help? Contact <a href="mailto:support@zytrixon.com" class="text-primary text-decoration-none fw-medium">support@zytrixon.com</a></p>
            </div>
        </div>
    </div>

    <script>
        feather.replace()
    </script>
</body>
</html>