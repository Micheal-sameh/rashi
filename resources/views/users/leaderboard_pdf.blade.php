<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <title>Leaderboard</title>
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
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>الترتيب</h1>
    <table>
        <thead>
            <tr>
                <th>الترتيب</th>
                <th>الاسم</th>
                <th>النقاط</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->score }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
