<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Danh sách kỷ luật</title>
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
        <div class="title">DANH SÁCH KỶ LUẬT QUÂN NHÂN</div>
        <div>Ngày xuất: {{ date('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Họ và tên</th>
                <th>Đơn vị</th>
                <th>Sai phạm</th>
                <th>Hình thức</th>
                <th>Ngày QĐ</th>
                <th>Số QĐ</th>
                <th>Tình trạng</th>
            </tr>
        </thead>
        <tbody>
            @foreach($disciplines as $index => $discipline)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $discipline->soldier_name_at_time }}</td>
                    <td>{{ $discipline->unit_name_at_time }}</td>
                    <td>{{ $discipline->violation_details }}</td>
                    <td>{{ $discipline->discipline_form }}</td>
                    <td>{{ $discipline->decision_date ? $discipline->decision_date->format('d/m/Y') : '' }}</td>
                    <td>{{ $discipline->decision_number }}</td>
                    <td>{{ $discipline->status_name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
