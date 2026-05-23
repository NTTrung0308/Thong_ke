<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Biên bản kiểm kê vũ khí</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
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
        <div class="title">DANH SÁCH KIỂM KÊ VŨ KHÍ TRANG BỊ</div>
        <div>Ngày xuất: {{ date('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Họ và tên</th>
                <th>Đơn vị</th>
                <th>AK</th>
                <th>RPD</th>
                <th>B41</th>
                <th>M79</th>
                <th>T.Nòng</th>
                <th>Phụ tùng</th>
                <th>Dây súng</th>
                <th>Hộp đạn</th>
                <th>Vịt dầu</th>
                <th>Lựu đạn</th>
                <th>Xẻng</th>
                <th>Cuốc</th>
                <th>Tình trạng</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipments as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->soldier->full_name ?? 'N/A' }}</td>
                    <td>{{ $item->unit->name ?? 'N/A' }}</td>
                    <td>{{ $item->ak }}</td>
                    <td>{{ $item->rpd }}</td>
                    <td>{{ $item->b41 }}</td>
                    <td>{{ $item->m79 }}</td>
                    <td>{{ $item->cleaning_rod }}</td>
                    <td>{{ $item->spare_parts }}</td>
                    <td>{{ $item->gun_strap }}</td>
                    <td>{{ $item->magazine_box }}</td>
                    <td>{{ $item->oil_can }}</td>
                    <td>{{ $item->grenade }}</td>
                    <td>{{ $item->infantry_shovel }}</td>
                    <td>{{ $item->infantry_pickaxe }}</td>
                    <td>{{ $item->condition == 'tốt' ? 'Tốt' : ($item->condition == 'hỏng' ? 'Hỏng' : 'Bảo dưỡng') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
