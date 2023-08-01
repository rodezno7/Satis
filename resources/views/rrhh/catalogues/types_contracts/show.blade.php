<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="ISO 8859-1">
    <title>{{ __('rrhh.contract') }}</title>
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

        @page{
            margin-bottom: 75px;
        }
        #header,
        #footer {
            position: fixed;
            left: 0;
            right: 0;
            color: #000000;
            font-size: 0.9em;
        }
        #header {
            top: 0;
            border-bottom: 0.1pt solid #aaa;
        }
        #footer {
            bottom: 0;
            border-top: 0.1pt solid #aaa;
        }
        .page-number:before {
            content: "Página " counter(page);
        }

    </style>
</head>

<body>
    {{-- <div id="footer">
        <div class="page-number"></div>
    </div> --}}
    {!! $type->template !!}
</body>
</html>
