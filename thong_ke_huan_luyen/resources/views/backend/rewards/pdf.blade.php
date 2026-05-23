<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Danh sách khen thưởng</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">DANH SÁCH KHEN THƯỞNG QUÂN NHÂN</div>
        <div>Ngày xuất: {{ date('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Họ và tên</th>
                <th>Đơn vị</th>
                <th>Lý do</th>
                <th>Hình thức</th>
                <th>Ngày QĐ</th>
                <th>Số QĐ</th>
                <th>Cấp QĐ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rewards as $index => $reward)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $reward->soldier_name_at_time }}</td>
                    <td>{{ $reward->unit_name_at_time }}</td>
                    <td>{{ $reward->reason }}</td>
                    <td>{{ $reward->reward_form }}</td>
                    <td>{{ $reward->decision_date ? $reward->decision_date->format('d/m/Y') : '' }}</td>
                    <td>{{ $reward->decision_number }}</td>
                    <td>{{ $reward->decision_level }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
