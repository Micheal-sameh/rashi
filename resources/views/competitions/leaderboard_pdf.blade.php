<!DOCTYPE html>
<html dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('messages.leaderboard') }}</title>
    <style>
        @font-face {
            font-family: 'ArabicFont';
            src: url('{{ public_path('fonts/arial.ttf') }}') format('truetype');
        }
        body {
            font-family: 'ArabicFont', Arial, sans-serif;
            margin: 20px;
            direction: {{ app()->isLocale('ar') ? 'rtl' : 'ltr' }};
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
            text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }};
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
    <h2>{{ $competition->name }}</h2>

    <h2>{{ __('messages.overall_ranking') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('messages.rank') }}</th>
                <th>{{ __('messages.name') }}</th>
                <th>{{ __('messages.group') }}</th>
                <th>{{ __('messages.total_points') }}</th>
                <th>{{ __('messages.total_correct_answers') }}</th>
                <th>{{ __('messages.total_questions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($userStats as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['group_name'] ?? __('messages.not_specified') }}</td>
                    <td>{{ $user['total_points'] }}</td>
                    <td>{{ $user['total_correct'] }}</td>
                    <td>{{ $user['total_questions'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (!empty($groupRankings))
        <h2 style="margin-top: 30px;">{{ __('messages.ranking_by_group') }}</h2>

        @foreach ($groupRankings as $groupRanking)
            <h2>{{ $groupRanking['title'] }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>{{ __('messages.rank') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.total_points') }}</th>
                        <th>{{ __('messages.total_correct_answers') }}</th>
                        <th>{{ __('messages.total_questions') }}</th>
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
