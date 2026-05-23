<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nhật ký huấn luyện</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">NHẬT KÝ HUẤN LUYỆN ĐƠN VỊ</div>
        <div>Ngày xuất: {{ date('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Ngày</th>
                <th>Họ và tên</th>
                <th>Đơn vị</th>
                <th>Nội dung</th>
                <th>Giờ (YC/TT)</th>
                <th>Xếp loại</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->training_date ? $log->training_date->format('d/m/Y') : '' }}</td>
                    <td>{{ $log->soldier_name_at_time }}</td>
                    <td>{{ $log->unit_name_at_time }}</td>
                    <td>{{ strip_tags($log->training_content) }}</td>
                    <td>{{ $log->required_hours }}/{{ $log->actual_hours }}</td>
                    <td>{{ $log->getRatingNameAttribute() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
