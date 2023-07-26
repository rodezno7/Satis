<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ __('rrhh.employee_assistance') }}</title>
    <style>
        @page {
            padding: 0;
            margin: 1.5cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: 14px;
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
    <div id="footer">
        <div class="page-number"></div>
    </div>
    <h2 class="text-center">{{ mb_strtoupper($business->name) }}</h2>
    <h3>{{ mb_strtoupper(__('rrhh.assistance_summary')) }}</h3>
    <table style="width: 100%;">
        <thead>
            <tr>
                <th>{{ __('rrhh.employee') }}</th>
                <th>{{ __('rrhh.schedule') }}</th>
                <th>{{ __('rrhh.time_worked') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assistanceSummary as $item)
                <tr>
                    <td>
                        {{ $item->employee }}
                    </td>
                    <td>
                        {{ $item->start_date }} - {{ $item->end_date }}
                    </td>
                    <td>
                        {{ $item->time_worked }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <h3>{{ mb_strtoupper(__('rrhh.employee_assistance_detail')) }}</h3>
    <table  style="width: 100%;">
        <thead>
            <tr>
                <th>{{ __('rrhh.employee') }}</th>
                <th>{{ __('rrhh.date') }}</th>
                <th>{{ __('rrhh.ip_address') }}</th>
                <th width="30%">{{ __('rrhh.location') }}</th>
                <th>{{ __('rrhh.type') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assistances as $item)
                <tr>
                    <td>
                        {{ $item->employee->first_name }} {{ $item->employee->last_name }}
                    </td>
                    <td>
                        {{ @format_date($item->date) }} {{ @format_time($item->time) }}
                    </td>
                    <td>
                        {{ $item->ip }}
                    </td>
                    <td>
                        <b>{{ __('rrhh.country') }}:</b> {{ $item->country }} <br>
                        <b>{{ __('rrhh.city') }}:</b> {{ $item->city }} <br>
                        <b>{{ __('rrhh.latitude') }}:</b> {{ $item->latitude }} <br>
                        <b>{{ __('rrhh.longitude') }}:</b> {{ $item->longitude }}
                    </td>
                    <td>
                        {{ $item->type }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table> 
</body>
</html>
