<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #191c1e;
            background-color: #f7f9fb;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #3525cd;
            color: white;
            padding: 24px;
            text-align: center;
            border-radius: 16px 16px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 0 0 16px 16px;
        }
        .info-box {
            background-color: #f2f4f6;
            border-left: 4px solid #3525cd;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #777587;
            font-size: 12px;
        }
        .download-btn {
            display: inline-block;
            background-color: #3525cd;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 10px;
            margin: 20px 0;
            font-weight: 600;
        }
        .download-btn:hover {
            background-color: #4f46e5;
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
