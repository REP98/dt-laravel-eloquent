<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos PDF</title>
    <style>
        /* Estilos generales para el PDF */
body {
    font-family: Arial, sans-serif;
    font-size: 12px;
    line-height: 1.6;
    color: #333;
}

/* Estilos para la tabla */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table, th, td {
    border: 1px solid #000;
}

th, td {
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f8f9fa; /* Color de fondo similar a Bootstrap */
    font-weight: bold;
    text-align: center; /* Centrar el texto */
    text-transform: uppercase; /* Texto en mayúsculas */
}

tr:nth-child(even) {
    background-color: #f2f2f2; /* Filas alternas (zebradas) */
}

thead {
    display: table-header-group;
}

tfoot {
    display: table-footer-group;
}
 /* Estilos para el encabezado y pie de página */
 @page {
            margin: 100px 25px;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: left;
            line-height: 35px;
        }

        footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 20px;
            text-align: right;
            line-height: 20px;
        }
    </style>
</head>
<body>
    <header>
        {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </header>
    <table class="table table-striped">
        <thead>
            <tr>
                @foreach ($data[0] as $key => $value)
                    <th>{{ $key }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
