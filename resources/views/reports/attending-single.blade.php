<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencia - {{ $attending->group->name ?? 'Grupo' }} - {{ $formattedDate }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .header-subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }
        .header-badge {
            text-align: right;
            font-size: 10px;
            color: #475569;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 6px;
            font-size: 11px;
        }
        .info-label {
            font-weight: bold;
            color: #334155;
            width: 18%;
        }
        .info-value {
            color: #0f172a;
            width: 32%;
        }
        /* Metrics / Summary Cards */
        .metrics-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 15px;
        }
        .metric-cell {
            padding: 8px 10px;
            border-radius: 6px;
            text-align: center;
        }
        .metric-total {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
        }
        .metric-present {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
        }
        .metric-absent {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
        }
        .metric-justified {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
        }
        .metric-num {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }
        .metric-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
            color: #475569;
        }
        /* Students Table */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .table-data th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            padding: 7px 8px;
            text-align: left;
            border: 1px solid #1e293b;
        }
        .table-data td {
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            font-size: 10.5px;
        }
        .table-data tr:nth-child(even) {
            background-color: #f8fafc;
        }
        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            font-size: 9.5px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: capitalize;
        }
        .badge-presente {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .badge-inasistente {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }
        .badge-justificado {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fcd34d;
        }
        /* Signatures section */
        .signatures {
            margin-top: 40px;
            width: 100%;
            border-collapse: collapse;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 40px;
        }
        .signature-line {
            border-top: 1px solid #334155;
            margin-top: 50px;
            padding-top: 6px;
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
        }
        .signature-sub {
            font-size: 10px;
            color: #64748b;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="header-title">Acta de Control de Asistencia</h1>
                    <div class="header-subtitle">Sistema Institucional de Gestión y Control Escolar</div>
                </td>
                <td class="header-badge">
                    <strong>Fecha Emisión:</strong> {{ now()->format('d/m/Y H:i') }}<br>
                    <strong>ID Lote:</strong> #{{ $attending->id }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Info Box -->
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="info-label">Grupo / Grado:</td>
                <td class="info-value"><strong>{{ $attending->group->name ?? 'N/A' }}</strong></td>
                <td class="info-label">Fecha de Clase:</td>
                <td class="info-value"><strong>{{ $formattedDate }}</strong></td>
            </tr>
            <tr>
                <td class="info-label">Docente a Cargo:</td>
                <td class="info-value">{{ $attending->teacher->name ?? 'N/A' }} {{ $attending->teacher->last_name ?? '' }}</td>
                <td class="info-label">Registrado por:</td>
                <td class="info-value">{{ $attending->user->name ?? 'Administración' }}</td>
            </tr>
        </table>
    </div>

    <!-- Metrics Cards -->
    <table class="metrics-table">
        <tr>
            <td class="metric-cell metric-total" style="width: 25%;">
                <div class="metric-num" style="color: #1d4ed8;">{{ $stats['total'] }}</div>
                <div class="metric-label">Total Convocados</div>
            </td>
            <td class="metric-cell metric-present" style="width: 25%;">
                <div class="metric-num" style="color: #15803d;">{{ $stats['presentes'] }} ({{ $stats['pct_presentes'] }}%)</div>
                <div class="metric-label">Asistentes</div>
            </td>
            <td class="metric-cell metric-absent" style="width: 25%;">
                <div class="metric-num" style="color: #b91c1c;">{{ $stats['inasistentes'] }} ({{ $stats['pct_inasistentes'] }}%)</div>
                <div class="metric-label">Inasistentes</div>
            </td>
            <td class="metric-cell metric-justified" style="width: 25%;">
                <div class="metric-num" style="color: #b45309;">{{ $stats['justificados'] }} ({{ $stats['pct_justificados'] }}%)</div>
                <div class="metric-label">Justificados</div>
            </td>
        </tr>
    </table>

    <!-- List of Students -->
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">N°</th>
                <th style="width: 18%;">Identificación</th>
                <th style="width: 37%;">Estudiante (Apellidos y Nombres)</th>
                <th style="width: 16%; text-align: center;">Estado</th>
                <th style="width: 24%;">Observación / Motivo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($group_attendances as $idx => $att)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ $att->student->identity_id ?? 'S/N' }}</td>
                    <td>
                        <strong>{{ $att->student->last_name ?? '' }}</strong> {{ $att->student->name ?? 'Estudiante no encontrado' }}
                    </td>
                    <td style="text-align: center;">
                        @if($att->status === 'asistente')
                            <span class="badge badge-presente">Asistente</span>
                        @elseif($att->status === 'inasistente')
                            <span class="badge badge-inasistente">Inasistente</span>
                        @elseif($att->status === 'justificado')
                            <span class="badge badge-justificado">Justificado</span>
                        @else
                            <span class="badge">{{ $att->status }}</span>
                        @endif
                    </td>
                    <td>{{ $att->social_reason ?: '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 15px; color: #64748b;">
                        No se encontraron registros de estudiantes para esta sesión.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signatures -->
    <table class="signatures">
        <tr>
            <td>
                <div class="signature-line">
                    {{ $attending->teacher->name ?? 'Docente Titular' }} {{ $attending->teacher->last_name ?? '' }}
                </div>
                <div class="signature-sub">Firma del Docente</div>
            </td>
            <td>
                <div class="signature-line">
                    Dirección / Coordinación Académica
                </div>
                <div class="signature-sub">Firma y Sello Institucional</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        Documento generado automáticamente por el Sistema de Control de Asistencia el {{ now()->format('d/m/Y \a \l\a\s H:i') }} — Página 1
    </div>

</body>
</html>
