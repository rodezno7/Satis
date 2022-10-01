<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@lang('report.audit_tape_report')</title>
    <style>
        * { margin: 0;padding: 0; list-style: none; text-decoration: none; border: none; outline: none;}
        div#container {width: 100%; font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; font-size: 7.5pt;}
        div.header {padding: 1cm 0.5cm 0cm 0.5cm;}
        div#details,div.footer { padding: 0cm 0.5cm 0cm 0.5cm;}
        .txt-center {text-align: center; }
        #sell_lines thead tr th, .tot_foot_letter { border-top: 0.5px solid #000;border-bottom: 0.5px solid #000;}
        .txt-rigth {text-align: right;}

        #signatures { width: 100%; }
        #signatures tr td {
            height: 25%;
            padding-bottom: 0;
            vertical-align: bottom;
        }
    </style>
</head>

<body>
    @foreach ($tickets as $ticket)
        @if ($ticket['type'] == "sell")
            @include('reports.partials.ticket', ['receipt_details' => $ticket['ticket']])
        @else
            @include('reports.partials.ticket_return', ['receipt_details' => $ticket['ticket']])
        @endif
        
        <div style="height: 3cm;">
            &nbsp;
        </div>
    @endforeach
</body>

</html>
