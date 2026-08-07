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
            max-width: 620px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #3525cd;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 16px 16px 0 0;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        .content {
            background-color: #ffffff;
            padding: 24px;
            border-radius: 0 0 16px 16px;
        }
        .info {
            background-color: #f2f4f6;
            border-left: 4px solid #3525cd;
            border-radius: 8px;
            padding: 12px;
            margin: 16px 0;
        }
        .btn {
            display: inline-block;
            background-color: #3525cd;
            color: #fff !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 10px;
            margin: 8px 4px 0 0;
            font-weight: 600;
        }
        .footer {
            margin-top: 20px;
            color: #777587;
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
