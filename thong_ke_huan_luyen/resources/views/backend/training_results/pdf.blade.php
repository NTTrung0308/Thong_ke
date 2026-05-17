<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kết quả tập huấn</title>
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
        <div class="title">KẾT QUẢ TẬP HUẤN HUẤN LUYỆN</div>
        <div>Ngày xuất: {{ date('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Đơn vị</th>
                <th>Ngày</th>
                <th>Nội dung</th>
                <th>Thời lượng</th>
                <th>Quân số</th>
                <th>Kết quả</th>
                <th>Tỷ lệ (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $index => $result)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $result->unit_name_at_time }}</td>
                    <td>{{ $result->training_date ? $result->training_date->format('d/m/Y') : '' }}</td>
                    <td>{{ $result->content }}</td>
                    <td>{{ $result->duration_hours }} giờ</td>
                    <td>{{ $result->trung_doi_count + $result->at_count + $result->kdt_count }}</td>
                    <td>{{ $result->result_name }}</td>
                    <td>{{ $result->passing_rate }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
