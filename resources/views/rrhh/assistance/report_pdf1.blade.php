<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ __('rrhh.employee_assistance') }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: 12pt;
        }
        h3, h4 {
            text-align: center;
        }
        .table1 {
            border-collapse: collapse;
            border: 0px;
        }
        .table2 {
            border-collapse: collapse;
            border: 0.25px solid black;
        }
        .td2 {
            border: 0px;
        }
        td {
            border: 0.25px solid black;
            padding: 4px;
            text-align: left;
        }
        th {
            border: 0.25px solid black;
            padding: 4px;
            text-align: center;
        }
        .alnright { text-align: right; }
        .alnleft { text-align: left; }
        .alncenter { text-align: center; }
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
        .bt { border-top: 0.25px solid black; }
        .bb { border-bottom: 0.25px solid black; }
        .br { border-right: 0.25px solid black; }
        .bl { border-left: 0.25px solid black; }
        .no-bt { border-top: 0.25px solid white; }
        .no-bb { border-bottom: 0.25px solid white; }
        .no-br { border-right: 0.25px solid white; }
        .no-bl { border-left: 0.25px solid white; }
  </style>   
</head>

<body>
    <table class="table1" style="width: 100%;">
        <tr>
            <td>
                <strong></strong>
            </td>
        </tr>
        <tr>
            <td>
                <strong>{{ mb_strtoupper(__('rrhh.employee_assistance')) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <thead>
            <th>Empleado</th>
            <th>Fecha</th>
            <th>Numero de horas</th>
            <th>Estado</th>
        </thead>
        <tbody>
            @foreach ($assistances as $item)
            <tr>
                <td>
                    {{ $item->employee->first_name }} {{ $item->employee->last_name }}
                </td>
                <td>
                    @php
                        $firstTime = \Carbon\Carbon::now()->timezone($item->business->time_zone)->format('Y-m-d H:i:s');
                        $lastTime = \Carbon\Carbon::now()->timezone($item->business->time_zone)->format('Y-m-d H:i:s');
                        foreach($assistancesEmployee as $key => $assistance){
                            if ($key === 0) {
                                $firstTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->date.' '.$item->time);
                                $lastTime = \Carbon\Carbon::now()->timezone($item->business->time_zone)->format('Y-m-d H:i:s');
                            }
                        
                            if(count($assistancesEmployee) > 1){
                                if ($key === count($assistancesEmployee)-1) {
                                    $lastTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date.' '.$assistance->time);
                                }
                            }
                        }
                        $time = $firstTime->diffInHours($lastTime);
                        $minutos = $firstTime->diffInMinutes($lastTime);
                        $minutos = $minutos - ($time*60);
                        $minutos = number_format($minutos, 0, ',', '.');
                        //sreturn $time.' horas con '.$minutos.' minutos';

                    @endphp
                    {{ $time }} - {{ $minutos }}
                </td>
                <td>
                    
                </td>
                <td>
                    
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>