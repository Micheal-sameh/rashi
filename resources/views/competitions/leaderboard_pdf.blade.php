<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <title>Competition Leaderboard</title>
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
            color: #191c1e;
        }
        h2 {
            text-align: center;
            color: #3525cd;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #e0e3e5;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #3525cd;
            color: #ffffff;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f4f6;
        }
    </style>
</head>
<body>
    {{-- <h1>ترتيب المسابقة</h1> --}}
    <h2>{{ $competition->name }}</h2>

    <h2>الترتيب العام</h2>
    <table>
        <thead>
            <tr>
                <th>الترتيب</th>
                <th>الاسم</th>
                <th>المجموعة</th>
                <th>إجمالي النقاط</th>
                <th>الإجابات الصحيحة</th>
                <th>إجمالي الأسئلة</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($userStats as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['group_name'] ?? 'غير محدد' }}</td>
                    <td>{{ $user['total_points'] }}</td>
                    <td>{{ $user['total_correct'] }}</td>
                    <td>{{ $user['total_questions'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (!empty($groupRankings))
        <h2 style="margin-top: 30px;">الترتيب حسب المجموعة</h2>

        @foreach ($groupRankings as $groupRanking)
            <h2>{{ $groupRanking['title'] }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>الترتيب</th>
                        <th>الاسم</th>
                        <th>إجمالي النقاط</th>
                        <th>الإجابات الصحيحة</th>
                        <th>إجمالي الأسئلة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupRanking['users'] as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['total_points'] }}</td>
                            <td>{{ $user['total_correct'] }}</td>
                            <td>{{ $user['total_questions'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif
</body>
</html>
