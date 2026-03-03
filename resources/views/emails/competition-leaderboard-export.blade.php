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
            max-width: 620px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #1f7ae0;
            color: #fff;
            padding: 16px;
            text-align: center;
            border-radius: 6px 6px 0 0;
        }
        .content {
            background-color: #f8f9fb;
            padding: 24px;
            border-radius: 0 0 6px 6px;
        }
        .info {
            background-color: #fff;
            border-left: 4px solid #1f7ae0;
            padding: 12px;
            margin: 16px 0;
        }
        .btn {
            display: inline-block;
            background-color: #1f7ae0;
            color: #fff !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin: 8px 4px 0 0;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            color: #7a7a7a;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Competition Leaderboard Export</h2>
        </div>
        <div class="content">
            <p>Dear Admin,</p>

            <p>The leaderboard export has been generated for <strong>{{ $competition->name }}</strong>.</p>

            <div class="info">
                <p><strong>Competition:</strong> {{ $competition->name }}</p>
                @if($groupName)
                    <p><strong>Selected Group:</strong> {{ $groupName }}</p>
                @else
                    <p><strong>Selected Group:</strong> All groups</p>
                @endif
            </div>

            <p>The files are attached to this email (Excel + PDF).</p>

            @if($excelLink || $pdfLink)
                <p>Google Drive links:</p>
                @if($excelLink)
                    <p><a class="btn" href="{{ $excelLink }}">Open Excel on Google Drive</a></p>
                @endif
                @if($pdfLink)
                    <p><a class="btn" href="{{ $pdfLink }}">Open PDF on Google Drive</a></p>
                @endif
            @endif

            <div class="footer">
                <p>This is an automated email. Please do not reply.</p>
            </div>
        </div>
    </div>
</body>
</html>
