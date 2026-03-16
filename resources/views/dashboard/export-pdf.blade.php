<!DOCTYPE html>
<html lang="th">
   <style>
    /* 1. สำหรับตัวธรรมดา  */
    @font-face {
        font-family: 'THSarabunNew';
        font-style: normal;
        font-weight: normal;
        src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
    }

    /* 2. เพิ่มสำหรับตัวหนา  */
    @font-face {
        font-family: 'THSarabunNew';
        font-style: normal;
        font-weight: bold; /* กำหนดว่าเป็นตัวหนา */
        src: url("{{ public_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype'); /* เช็คชื่อไฟล์ให้ตรง */
    }

    body, table, th, td {
        font-family: 'THSarabunNew', sans-serif;
    }
</style>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
    <meta charset="UTF-8">
    <title>Kaizen Export - {{ $typeName }}</title>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
        }
        body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 16pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .bg-light { background-color: #f9f9f9; }
        h3 { text-align: center; margin-bottom: 5px; }
    </style>
</head>
<body>
    <h3>รายงานกิจกรรม Kaizen</h3>
    <h4 class="text-center">ประเภท: {{ $typeName }} | ปีงบประมาณ {{ $selectedYear }}</h4>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ลำดับ</th>
                <th style="width: 40%;">ชื่อกิจกรรม</th>
                <th style="width: 25%;">ผู้ยื่น</th>
                <th style="width: 10%;">ก่อน</th>
                <th style="width: 10%;">หลัง</th>
                <th style="width: 10%;">ผลต่าง</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $i => $p)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $p['title'] ?? '(ไม่มีชื่อ)' }}</td>
                    <td>{{ $p['user'] }}</td>
                    <td class="text-right">{{ number_format($p['before'], 2) }}</td>
                    <td class="text-right">{{ number_format($p['after'], 2) }}</td>
                    <td class="text-right">{{ number_format($p['net'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="fw-bold bg-light">
                <td colspan="3" class="text-center">รวมทั้งหมด</td>
                <td class="text-right">{{ number_format($summary['before'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['after'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['net'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
