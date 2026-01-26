<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4a90e2;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .info-box {
            background-color: white;
            border-left: 4px solid #4a90e2;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 12px;
        }
        .download-btn {
            display: inline-block;
            background-color: #4a90e2;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .download-btn:hover {
            background-color: #357abd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Competition Finished!</h1>
        </div>
        <div class="content">
            <p>Dear Admin,</p>

            <p>The competition <strong>{{ $competition->name }}</strong> has finished.</p>

            <div class="info-box">
                <p><strong>Competition:</strong> {{ $competition->name }}</p>
                <p><strong>Group:</strong> {{ $group->name }}</p>
                <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($competition->start_at)->format('d M Y') }} - {{ \Carbon\Carbon::parse($competition->end_at)->format('d M Y') }}</p>
            </div>

            <p>Please find the reports:</p>
            <ul>
                <li><strong>Competition Results (Excel):</strong> Attached to this email - A detailed report showing which users solved each quiz, with statistics</li>
                <li><strong>User Rankings (PDF):</strong> Available on Google Drive</li>
            </ul>

            <div style="text-align: center;">
                <a href="{{ $pdfLink }}" class="download-btn">Download Rankings PDF from Google Drive</a>
            </div>

            <p>Thank you for managing this competition!</p>

            <div class="footer">
                <p>This is an automated email. Please do not reply.</p>
            </div>
        </div>
    </div>
</body>
</html>
