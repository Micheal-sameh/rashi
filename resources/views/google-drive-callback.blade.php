<!DOCTYPE html>
<html>
<head>
    <title>Google Drive Authorization</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f9fafb;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1e40af;
        }
        .code-box {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #1e40af;
            margin: 20px 0;
            font-family: monospace;
            word-break: break-all;
        }
        .success {
            color: #059669;
        }
        .instruction {
            background: #fef3c7;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #d97706;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="success">✓ Authorization Successful!</h1>

        <div class="instruction">
            <strong>Next Step:</strong> Copy the authorization code below and paste it back into your terminal where the command is waiting.
        </div>

        <h3>Your Authorization Code:</h3>
        <div class="code-box">
            {{ $code }}
        </div>

        <p>After pasting the code, you'll receive a refresh token to add to your .env file.</p>

        <p style="color: #666; font-size: 14px; margin-top: 30px;">
            You can close this window after copying the code.
        </p>
    </div>
</body>
</html>
