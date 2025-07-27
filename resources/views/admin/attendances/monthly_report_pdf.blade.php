<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi Bulanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 20mm;
        }
        h1, h2, h3, h4, h5, h6 {
            margin: 0;
            padding: 0;
        }
        h1 { font-size: 16pt; margin-bottom: 10pt; }
        h2 { font-size: 14pt; margin-bottom: 8pt; }
        h3 { font-size: 12pt; margin-bottom: 6pt; }
        h4 { font-size: 11pt; margin-bottom: 4pt; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15pt;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 5pt;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .summary-grid {
            display: table; /* Simulate grid for PDF */
            width: 100%;
            margin-bottom: 15pt;
        }
        .summary-cell {
            display: table-cell;
            width: 25%; /* 4 columns */
            padding: 8pt;
            border: 1px solid #eee;
            text-align: center;
            background-color: #f8f8f8;
        }
        .summary-value {
            font-size: 14pt;
            font-weight: bold;
            color: #333;
        }
        .summary-label {
            font-size: 9pt;
            color: #666;
        }
        .status-hadir { color: green; font-weight: bold; }
        .status-libur { color: blue; font-weight: bold; }
        .status-absen { color: red; font-weight: bold; }
        .status-belum-bekerja { color: gray; font-style: italic; }
        .status-libur-nasional { color: purple; font-weight: bold; }
        .status-absen-manual { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan Absensi Bulanan</h1>
    <h3>Bulan: {{ \Carbon\Carbon::create(null, $selectedMonth, 1)->translatedFormat('F') }} {{ $selectedYear }}</h3>
    <p>Tanggal Laporan: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
    <hr style="margin-top: 10pt; margin-bottom: 15pt;">

    @forelse ($reportData as $employeeReport)
        <div style="margin-bottom: 20pt;">
            <h2>{{ $employeeReport['employee']->user->name }} (NIP: {{ $employeeReport['employee']->nip }})</h2>
            <p style="margin-bottom: 5pt;">Shift: {{ $employeeReport['employee']->shift->name ?? 'Belum Ditetapkan' }}</p>
            <p style="margin-bottom: 10pt;">Tanggal Masuk: {{ $employeeReport['employee']->hire_date->format('d M Y') }}</p>

            <div class="summary-grid">
                <div class="summary-cell">
                    <div class="summary-value">{{ round($employeeReport['total_work_hours'], 2) }}</div>
                    <div class="summary-label">Total Jam Kerja</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-value">{{ $employeeReport['days_present'] }}</div>
                    <div class="summary-label">Hari Hadir</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-value">{{ $employeeReport['days_absent'] }}</div>
                    <div class="summary-label">Hari Absen</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-value">{{ $employeeReport['days_off'] }}</div>
                    <div class="summary-label">Hari Libur</div>
                </div>
            </div>

            <h4>Detail Harian:</h4>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Jam Kerja</th>
                        <th>Lembur</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employeeReport['daily_records'] as $record)
                        <tr>
                            <td>{{ $record['date']->format('d M') }}</td>
                            <td>
                                @php
                                    $statusClass = '';
                                    switch ($record['status']) {
                                        case 'Hadir': $statusClass = 'status-hadir'; break;
                                        case 'Libur': $statusClass = 'status-libur'; break;
                                        case 'Libur Nasional': $statusClass = 'status-libur-nasional'; break;
                                        case 'Absen': $statusClass = 'status-absen'; break;
                                        case 'Absen (Manual)': $statusClass = 'status-absen-manual'; break;
                                        case 'Belum Bekerja': $statusClass = 'status-belum-bekerja'; break;
                                        default: $statusClass = ''; break;
                                    }
                                @endphp
                                <span class="{{ $statusClass }}">{{ $record['status'] }}</span>
                            </td>
                            <td>{{ $record['check_in'] ? $record['check_in']->format('H:i') : '-' }}</td>
                            <td>{{ $record['check_out'] ? $record['check_out']->format('H:i') : '-' }}</td>
                            <td>{{ $record['work_hours'] ?? '-' }}</td>
                            <td>{{ $record['overtime_hours'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <p>Tidak ada data absensi untuk bulan yang dipilih.</p>
    @endforelse

</body>
</html>
