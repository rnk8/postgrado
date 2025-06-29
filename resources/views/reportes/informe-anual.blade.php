<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Anual de Programa</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; line-height: 1.4; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .header h1, .header h2, .header h3 { margin: 0; }
        .header h1 { font-size: 16px; }
        .header h2 { font-size: 14px; }
        .header h3 { font-size: 12px; font-weight: normal; }
        .table-container { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { font-size: 9px; color: #777; position: fixed; bottom: 0; width: 100%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSIDAD AUTÓNOMA GABRIEL RENÉ MORENO</h1>
        <h2>FORMULARIO DE INFORME ANUAL</h2>
    </div>

    <div class="container">
        <table>
            <tr>
                <td><strong>NOMBRE DE LA UPG:</strong> {{ $programa->nombre_facultad }}</td>
                <td><strong>CÓDIGO UPG:</strong> {{ $programa->cod_facultad }}</td>
            </tr>
            <tr>
                <td><strong>PROGRAMA:</strong> {{ $programa->nombre_carrera }}</td>
                <td><strong>TIEMPO DEL PROGRAMA:</strong> {{ $gestion->nombre }}</td>
            </tr>
        </table>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>REGISTRO</th>
                        <th>FECHA INGRESO</th>
                        <th>FECHA CONCLUSION (ESCOLARIDAD)</th>
                        <th>FECHA TESIS</th>
                        <th>PERMANENCIA (ESCOLARIDAD) AÑOS</th>
                        <th>PERMANENCIA (C/TESIS) AÑOS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($estudiantes as $index => $estudiante)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $estudiante['registro'] }}</td>
                        <td class="text-center">{{ $estudiante['fecha_ingreso'] }}</td>
                        <td class="text-center">{{ $estudiante['fecha_conclusion'] }}</td>
                        <td class="text-center">{{ $estudiante['fecha_tesis'] }}</td>
                        <td class="text-center">{{ $estudiante['permanencia_escolaridad'] }}</td>
                        <td class="text-center">{{ $estudiante['permanencia_con_tesis'] }}</td>
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