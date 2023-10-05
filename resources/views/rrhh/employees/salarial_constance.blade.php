<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <title>{{ __('rrhh.salarial_constances') }}</title>
    <style>
        @page {
            padding: 0;
            margin: 1.5cm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif; 
            color: #000000;
            margin-top: {{ $salarialConstance->margin_top }};
            margin-bottom: {{ $salarialConstance->margin_bottom }};
            margin-left: {{ $salarialConstance->margin_left }};
            margin-right: {{ $salarialConstance->margin_right }};
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
    <div>  
    {!!  $template  !!}
    </div>
</body>
</html>
