<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Google Drive Authorization</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            --success-gradient: linear-gradient(135deg, #059669 0%, #047857 100%);
            --warning-gradient: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            --card-shadow: 0 4px 24px rgba(0,0,0,0.1);
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f9fafb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            max-width: 640px;
            width: 100%;
        }
        .auth-header {
            background: var(--success-gradient);
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            color: white;
        }
        .code-box {
            background: #f3f4f6;
            border-left: 4px solid #1e40af;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            word-break: break-all;
            color: #1e3a8a;
            position: relative;
        }
        .copy-btn {
            background: var(--primary-gradient);
            border: none;
            border-radius: 8px;
            color: white;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .copy-btn:hover { opacity: 0.85; }
        .instruction-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #d97706;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="container px-3 py-5">
        <div class="auth-card card mx-auto">
            <div class="auth-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fab fa-google-drive fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold">Authorization Successful!</h4>
                        <p class="mb-0 opacity-75">Google Drive access has been granted</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="instruction-box mb-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-info-circle mt-1"></i>
                        <div>
                            <strong>Next Step:</strong> Copy the authorization code below and paste it back into your
                            terminal where the command is waiting.
                        </div>
                    </div>
                </div>

                <h6 class="fw-semibold mb-2">
                    <i class="fas fa-key me-2 text-primary"></i>Your Authorization Code:
                </h6>
                <div class="code-box mb-3">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <span id="authCode">{{ $code }}</span>
                        <button class="copy-btn flex-shrink-0" onclick="copyCode()">
                            <i class="fas fa-copy me-1"></i>Copy
                        </button>
                    </div>
                </div>

                <p class="text-muted small mb-0">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    After pasting the code, you will receive a refresh token to add to your <code>.env</code> file.
                    You can close this window after copying the code.
                </p>
            </div>
        </div>
    </div>

    <script>
        function copyCode() {
            const code = document.getElementById('authCode').textContent.trim();
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.querySelector('.copy-btn');
                btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                btn.style.background = 'linear-gradient(135deg, #059669, #047857)';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-copy me-1"></i>Copy';
                    btn.style.background = '';
                }, 2000);
            });
        }
    </script>
</body>
</html>
