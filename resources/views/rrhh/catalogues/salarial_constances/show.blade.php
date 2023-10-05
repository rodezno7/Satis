<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="ISO 8859-1">
    <title>{{ __('rrhh.salarial_constances') }}</title>
    <style>
        @page {
            padding: 0;
            margin: 1.5cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: 14px;
            margin-top: {{  $type->margin_top  }}px;
            margin-bottom: {{  $type->margin_bottom  }}px;
            margin-left: {{  $type->margin_left  }}px;
            margin-right: {{  $type->margin_right  }}px;
        }

        h1 {
            text-align: center;
            margin: 0;
        }

        h2 {
            text-align: center;
            margin: 5px 0 0 0;
        }

        table {
            margin-top: 8px;
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 3px 5px;
        }
    </style>
</head>

<body>
    {!! $type->template !!}
</body>
</html>
