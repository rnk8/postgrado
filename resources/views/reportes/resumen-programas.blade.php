<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Programas por UPG</title>
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
        <h2>RESUMEN PROGRAMAS POR UPG FACULTATIVA</h2>
    </div>

    <div class="container">
        <table>
            <tr>
                <td><strong>NOMBRE DE LA UPG:</strong> {{ $facultad }}</td>
                <td><strong>CÓDIGO UPG:</strong> {{ $cod_facultad ?? 'TODAS' }}</td>
            </tr>
        </table>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" class="text-center">No.</th>
                        <th colspan="4" class="text-center">PROGRAMA</th>
                        <th colspan="3" class="text-center">ALUMNOS INSCRITOS</th>
                    </tr>
                    <tr>
                        <th>CÓDIGO</th>
                        <th>NOMBRE</th>
                        <th class="text-center">VERSIÓN</th>
                        <th class="text-center">EDICIÓN</th>
                        <th class="text-center">HOMBRES</th>
                        <th class="text-center">MUJERES</th>
                        <th class="text-center">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programas as $index => $programa)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $programa['codigo'] }}</td>
                        <td>{{ $programa['nombre'] }}</td>
                        <td class="text-center">{{ $programa['version'] }}</td>
                        <td class="text-center">{{ $programa['edicion'] }}</td>
                        <td class="text-center">{{ $programa['hombres'] }}</td>
                        <td class="text-center">{{ $programa['mujeres'] }}</td>
                        <td class="text-center">{{ $programa['total'] }}</td>
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