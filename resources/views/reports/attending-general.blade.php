<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Asistencias - {{ now()->format('d/m/Y') }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10.5px;
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
        .filters-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-size: 10.5px;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .table-data th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 9.5px;
            text-transform: uppercase;
            padding: 6px 7px;
            text-align: left;
            border: 1px solid #1e293b;
        }
        .table-data td {
            padding: 5px 7px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }
        .table-data tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
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

    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="header-title">Reporte General de Asistencias</h1>
                    <div class="header-subtitle">Consolidado Institucional de Sesiones Registradas</div>
                </td>
                <td class="header-badge">
                    <strong>Fecha Emisión:</strong> {{ now()->format('d/m/Y H:i') }}<br>
                    <strong>Total Sesiones:</strong> {{ count($sessions) }}
                </td>
            </tr>
        </table>
    </div>

    @if(!empty($appliedFilters))
        <div class="filters-box">
            <strong>Filtros aplicados:</strong> {{ implode(' | ', $appliedFilters) }}
        </div>
    @endif

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">N°</th>
                <th style="width: 15%;">Fecha</th>
                <th style="width: 20%;">Grupo / Grado</th>
                <th style="width: 25%;">Docente</th>
                <th style="width: 9%; text-align: center;">Total</th>
                <th style="width: 9%; text-align: center;">Asistieron</th>
                <th style="width: 9%; text-align: center;">Faltaron</th>
                <th style="width: 9%; text-align: center;">% Asist.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $idx => $session)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($session['class_date'])->format('d/m/Y') }}</td>
                    <td><strong>{{ $session['group_name'] }}</strong></td>
                    <td>{{ $session['teacher_name'] }}</td>
                    <td class="text-center">{{ $session['total'] }}</td>
                    <td class="text-center" style="color: #15803d; font-weight: bold;">{{ $session['presentes'] }}</td>
                    <td class="text-center" style="color: #b91c1c; font-weight: bold;">{{ $session['inasistentes'] }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $session['pct'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #64748b;">
                        No se encontraron registros de asistencias con los filtros aplicados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el Sistema de Control de Asistencia el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
    </div>

</body>
</html>
