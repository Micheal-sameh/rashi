<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <title>Competition Rankings - {{ $competition->name }}</title>
    <style>
        @font-face {
            font-family: 'ArabicFont';
            src: url('{{ public_path('fonts/arial.ttf') }}') format('truetype');
        }
        body {
            font-family: 'ArabicFont', Arial, sans-serif;
            margin: 20px;
            direction: rtl;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
        }
        h2 {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 18px;
        }
        .info {
            margin-bottom: 20px;
            text-align: center;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #4a90e2;
            color: white;
            padding: 12px;
            text-align: right;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: right;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .rank-1 {
            background-color: #ffd700 !important;
            font-weight: bold;
        }
        .rank-2 {
            background-color: #c0c0c0 !important;
            font-weight: bold;
        }
        .rank-3 {
            background-color: #cd7f32 !important;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h1>Competition Rankings</h1>
    <h2>{{ $competition->name }}</h2>

    <div class="info">
        <p><strong>Group:</strong> {{ $group->name }}</p>
        <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($competition->start_at)->format('d M Y') }} - {{ \Carbon\Carbon::parse($competition->end_at)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>User Name</th>
                <th>Quizzes Solved</th>
                <th>Total Points</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rankings as $ranking)
                <tr class="{{ $ranking['rank'] <= 3 ? 'rank-' . $ranking['rank'] : '' }}">
                    <td>{{ $ranking['rank'] }}</td>
                    <td>{{ $ranking['user']->name }}</td>
                    <td>{{ $ranking['quizzes_solved'] }}</td>
                    <td>{{ $ranking['total_points'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>
