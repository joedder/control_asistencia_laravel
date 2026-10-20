<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de asistencia - {{ $student->name }} {{ $student->last_name }}</title>
    <style>
        @page { margin: 28px 30px; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1e293b; font-size: 10px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 14px; }
        .header table, .info, .metrics, .records { width: 100%; border-collapse: collapse; }
        h1 { color: #0f172a; font-size: 17px; margin: 0 0 4px; }
        .muted { color: #64748b; }
        .issued { text-align: right; color: #475569; }
        .info { background: #f8fafc; border: 1px solid #cbd5e1; margin-bottom: 12px; }
        .info td { padding: 8px; }
        .metrics { margin: 0 -4px 12px; }
        .metrics td { padding: 9px 4px; text-align: center; }
        .metric { border: 1px solid #cbd5e1; border-radius: 4px; }
        .number { display: block; font-size: 15px; font-weight: bold; }
        .label { color: #475569; font-size: 8px; text-transform: uppercase; }
        .records th { background: #1e293b; color: white; text-align: left; }
        .records th, .records td { border: 1px solid #cbd5e1; padding: 7px 6px; }
        .records tr:nth-child(even) { background: #f8fafc; }
        .status { font-weight: bold; }
        .present { color: #15803d; }
        .absent { color: #b91c1c; }
        .justified { color: #b45309; }
        .empty { text-align: center; padding: 18px !important; color: #64748b; }
        .footer { position: fixed; bottom: -12px; left: 0; right: 0; text-align: center; color: #64748b; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <table><tr>
            <td><h1>REPORTE DE ASISTENCIA DEL ESTUDIANTE</h1><span class="muted">Historial de sesiones registradas</span></td>
            <td class="issued">Fecha de emisión: {{ $generatedAt }}<br>Total de sesiones: {{ $stats['total'] }}</td>
        </tr></table>
    </div>

    <table class="info"><tr>
        <td><strong>Estudiante:</strong> {{ $student->last_name }} {{ $student->name }}</td>
        <td><strong>Identificación:</strong> {{ $student->identity_id ?: 'S/N' }}</td>
        <td><strong>Grupo actual:</strong> {{ $student->group->name ?? '—' }}</td>
    </tr></table>

    <table class="metrics"><tr>
        <td><div class="metric"><span class="number">{{ $stats['total'] }}</span><span class="label">Sesiones registradas</span></div></td>
        <td><div class="metric"><span class="number present">{{ $stats['presentes'] }} ({{ $stats['pct_presentes'] }}%)</span><span class="label">Asistencias</span></div></td>
        <td><div class="metric"><span class="number absent">{{ $stats['inasistentes'] }}</span><span class="label">Inasistencias</span></div></td>
        <td><div class="metric"><span class="number justified">{{ $stats['justificados'] }}</span><span class="label">Justificadas</span></div></td>
    </tr></table>

    <table class="records">
        <thead><tr><th>N°</th><th>Fecha</th><th>Grupo</th><th>Docente</th><th>Estado</th><th>Observación / motivo</th></tr></thead>
        <tbody>
        @forelse ($attendances as $index => $attendance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($attendance->class_date)->format('d/m/Y') }}</td>
                <td>{{ $attendance->group->name ?? '—' }}</td>
                <td>{{ $attendance->substituteTeacher ? trim($attendance->substituteTeacher->name.' '.$attendance->substituteTeacher->last_name).' (suplente)' : (trim(($attendance->teacher->name ?? '').' '.($attendance->teacher->last_name ?? '')) ?: '—') }}</td>
                <td class="status {{ $attendance->status === 'asistente' ? 'present' : ($attendance->status === 'inasistente' ? 'absent' : 'justified') }}">{{ ucfirst($attendance->status) }}</td>
                <td>{{ $attendance->social_reason ?: '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">Este estudiante todavía no tiene asistencias registradas.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="footer">Sistema de Control de Asistencia · Reporte individual</div>
</body>
</html>
