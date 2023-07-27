<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ __('rrhh.contract') }}</title>
    <style>
        @page {
            padding: 0;
            margin: 1.5cm;
        }

        body {
            /* font-family: 'Helvetica', 'Arial', sans-serif; */
            color: #000000;
            /* font-size: 14px; */
            margin-top: {{ $contract->margin_top }};
            margin-bottom: {{ $contract->margin_bottom }};
            margin-left: {{ $contract->margin_left }};
            margin-right: {{ $contract->margin_right }};
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
    <div>  
    {!!  $template  !!}
    </div>
    <div id="footer">
        <div class="page-number"></div>
    </div>
</body>
</html>
