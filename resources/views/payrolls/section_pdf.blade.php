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
    <h1>Bảng lương lớp {{ $section->code }}</h1>
    <table>
        <tbody>
            <tr>
                <th>Giáo viên</th>
                <td>{{ $teacher->full_name }}</td>
            </tr>
            <tr>
                <th>Môn học</th>
                <td>{{ $section->subject->name }}</td>
            </tr>
            <tr>
                <th>Số tiết</th>
                <td>{{ $section->period_count }}</td>
            </tr>
            <tr>
                <th>Sĩ số</th>
                <td>{{ $section->student_count }}</td>
            </tr>
            <tr>
                <th>Hs học vị</th>
                <td>{{ $detail['degree'] }}</td>
            </tr>
            <tr>
                <th>Hs sĩ số</th>
                <td>{{ $detail['class'] }}</td>
            </tr>
            <tr>
                <th>Hs môn</th>
                <td>{{ $detail['subject'] }}</td>
            </tr>
            <tr>
                <th>Lương</th>
                <td>{{ number_format($detail['salary'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
