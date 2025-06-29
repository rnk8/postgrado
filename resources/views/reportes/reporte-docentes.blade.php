<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Docentes</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; line-height: 1.4; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .header h1, .header h2 { margin: 0; }
        .header h1 { font-size: 16px; }
        .header h2 { font-size: 14px; }
        .table-container { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .footer { font-size: 9px; color: #777; position: fixed; bottom: 0; width: 100%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSIDAD AUTÓNOMA GABRIEL RENÉ MORENO</h1>
        <h2>REPORTE – DOCENTES</h2>
    </div>

    <div class="container">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" class="text-center">No.</th>
                        <th colspan="2" class="text-center">DOCENTE</th>
                        <th colspan="4" class="text-center">PROGRAMA</th>
                        <th colspan="2" class="text-center">MATERIA</th>
                        <th colspan="2" class="text-center">FECHA</th>
                    </tr>
                    <tr>
                        <th>CÓDIGO</th>
                        <th class="text-center">GÉNERO</th>
                        <th>CÓDIGO</th>
                        <th>NOMBRE</th>
                        <th class="text-center">VERSIÓN</th>
                        <th class="text-center">EDICIÓN</th>
                        <th>SIGLA</th>
                        <th>NOMBRE</th>
                        <th class="text-center">INICIO</th>
                        <th class="text-center">FINAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($actividades as $index => $actividad)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $actividad['cod_docente'] }}</td>
                        <td class="text-center">{{ $actividad['genero'] }}</td>
                        <td>{{ $actividad['programa_codigo'] }}</td>
                        <td>{{ $actividad['programa_nombre'] }}</td>
                        <td class="text-center">{{ $actividad['version'] }}</td>
                        <td class="text-center">{{ $actividad['edicion'] }}</td>
                        <td>{{ $actividad['sigla_materia'] }}</td>
                        <td>{{ $actividad['nombre_materia'] }}</td>
                        <td class="text-center">{{ $actividad['fecha_inicio'] }}</td>
                        <td class="text-center">{{ $actividad['fecha_final'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        <p>Fecha de Generación: {{ $fecha_generacion }}</p>
    </div>
</body>
</html> 