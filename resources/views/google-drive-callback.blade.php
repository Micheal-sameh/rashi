<!DOCTYPE html>
<html>
<head>
    <title>Google Drive Authorization</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #4285f4;
        }
        .code-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #4285f4;
            margin: 20px 0;
            font-family: monospace;
            word-break: break-all;
        }
        .success {
            color: #0f9d58;
        }
        .instruction {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
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
