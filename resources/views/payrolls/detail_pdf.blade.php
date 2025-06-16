<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <h1>Bảng lương {{ $teacher->full_name }}</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Mã lớp HP</th>
                <th>Môn học</th>
                <th>Số tiết</th>
                <th>Sĩ số</th>
                <th>Hs học vị</th>
                <th>Hs sĩ số</th>
                <th>Hs môn</th>
                <th>Lương</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['section']->code }}</td>
                    <td>{{ $row['section']->subject->name }}</td>
                    <td>{{ $row['section']->period_count }}</td>
                    <td>{{ $row['section']->student_count }}</td>
                    <td>{{ $row['degree'] }}</td>
                    <td>{{ $row['class'] }}</td>
                    <td>{{ $row['subject'] }}</td>
                    <td>{{ number_format($row['salary'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p style="text-align: right; font-weight: bold;">Tổng lương: {{ number_format($total, 2) }}</p>
</body>
</html>
