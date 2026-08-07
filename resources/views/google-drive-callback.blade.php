<!DOCTYPE html>
<html>
<head>
    <title>Google Drive Authorization</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f7f9fb;
            color: #191c1e;
        }
        .container {
            background: #ffffff;
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0px 4px 6px -1px rgba(15, 23, 42, 0.05), 0px 2px 4px -2px rgba(15, 23, 42, 0.05);
        }
        h1 {
            color: #191c1e;
            font-size: 24px;
            font-weight: 700;
        }
        .code-box {
            background: #f2f4f6;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3525cd;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }
        .success {
            color: #11998e;
        }
        .instruction {
            background: #d0e1fb;
            color: #38485d;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #505f76;
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
