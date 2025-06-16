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
    <h1>Bảng lương giáo viên</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Mã GV</th>
                <th>Họ tên</th>
                <th>Tổng lương</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachers as $index => $t)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $t->teacher_id }}</td>
                    <td>{{ $t->full_name }}</td>
                    <td>{{ number_format($t->total_salary, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
