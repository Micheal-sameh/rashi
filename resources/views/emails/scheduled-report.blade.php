<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $report->name }}</title>
</head>

<body style="font-family: Arial, sans-serif; color: #1f2937;">
    <h2 style="margin-bottom: 0;">{{ $report->name }}</h2>
    <p style="color: #6b7280; margin-top: 4px;">Run at {{ $ranAt->format('Y-m-d H:i') }}</p>

    @if (empty($rows))
        <p>No data found for this report.</p>
    @else
        <table style="border-collapse: collapse; width: 100%; margin-top: 16px;">
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th
                            style="text-align: left; padding: 8px; border: 1px solid #e5e7eb; background: #f3f4f6;">
                            {{ $column }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($columns as $column)
                            <td style="padding: 8px; border: 1px solid #e5e7eb;">
                                {{ $row[$column] ?? '' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>

</html>
