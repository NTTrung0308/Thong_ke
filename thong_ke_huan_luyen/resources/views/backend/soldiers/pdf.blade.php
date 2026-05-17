<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Danh sách quân nhân</title>
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
        <div class="title">DANH SÁCH QUÂN NHÂN</div>
        <div>Ngày xuất: {{ date('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Họ và tên</th>
                <th>Số hiệu</th>
                <th>Cấp bậc</th>
                <th>Chức vụ</th>
                <th>Đơn vị</th>
                <th>Ngày sinh</th>
                <th>Nhập ngũ</th>
                <th>Học vấn</th>
            </tr>
        </thead>
        <tbody>
            @foreach($soldiers as $index => $soldier)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $soldier->full_name }}</td>
                    <td>{{ $soldier->code }}</td>
                    <td>{{ $soldier->rank }}</td>
                    <td>{{ $soldier->position }}</td>
                    <td>{{ $soldier->unit ? $soldier->unit->name : 'N/A' }}</td>
                    <td>{{ $soldier->birth_date ? $soldier->birth_date->format('d/m/Y') : '' }}</td>
                    <td>{{ $soldier->enlistment_date ? $soldier->enlistment_date->format('m/Y') : '' }}</td>
                    <td>{{ $soldier->education }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
