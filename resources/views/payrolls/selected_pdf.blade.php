<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1 { text-align: center; margin-bottom: 20px; }
        h2 { margin-top: 30px; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; font-size: 12px; }
        .total { text-align: right; font-weight: bold; margin-top: 30px; font-size: 14px; }
    </style>
</head>
<body>
    <h1>Phiếu lương</h1>
    @foreach($details as $detail)
        <h2>Lớp học phần: {{ $detail['section']->code }}</h2>
        <table>
            <tbody>
                <tr>
                    <th>Mã giáo viên</th>
                    <td>{{ $detail['teacher']->teacher_id }}</td>
                    <th>Họ tên</th>
                    <td>{{ $detail['teacher']->full_name }}</td>
                </tr>
                <tr>
                    <th>Môn học</th>
                    <td>{{ $detail['section']->subject->code }} - {{ $detail['section']->subject->name }}</td>
                    <th>Số tiết</th>
                    <td>{{ $detail['section']->period_count }}</td>
                </tr>
                <tr>
                    <th>Sĩ số</th>
                    <td>{{ $detail['section']->student_count }}</td>
                    <th>Trạng thái thanh toán</th>
                    <td>{{ $detail['section']->payment_status_label }}</td>
                </tr>
                <tr>
                    <th>Hệ số học vị</th>
                    <td>{{ number_format($detail['degree'], 2) }}</td>
                    <th>Hệ số sĩ số</th>
                    <td>{{ number_format($detail['class'], 2) }}</td>
                </tr>
                <tr>
                    <th>Hệ số môn</th>
                    <td>{{ number_format($detail['subject'], 2) }}</td>
                    <th>Mức lương cơ bản</th>
                    <td>{{ number_format($detail['base'], 2) }}</td>
                </tr>
                <tr>
                    <th>Tiền lương</th>
                    <td colspan="3">{{ number_format($detail['salary'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <div class="total">
        Tổng Thù Lao Giảng Dạy: {{ number_format($total, 2) }}
    </div>
</body>
</html>
