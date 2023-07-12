<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('rrhh.personnel_action')</title>
    <style>
        @page {
            padding: 0;
            margin: 1.5cm;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
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
    </style>
</head>

<body>
    <h1>{{ mb_strtoupper($business->name) }}</h1>
    <h2>{{ mb_strtoupper(__('rrhh.personnel_action')) }}</h2>
    <br>
    <table>
        <thead>
            <tr>
                <th colspan="4">{{ mb_strtoupper('Informacion general') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>{{ __('rrhh.type_personnel_action') }}</th>
                <td>{{ $personnelAction[0]->type }}</td>
                <th width="15%">{{ __('rrhh.status') }}</th>
                <td width="20%">
                    {{ $personnelAction[0]->status }}
                    @if ($personnelAction[0]->status == 'Autorizada')
                        {{ @format_date($personnelAction[0]->authorization_date) }}
                    @endif
                </td>
            </tr>
            <tr>
                <th width="23%">{{ __('rrhh.requested_by') }}</th>
                <td>{{ $personnelAction[0]->first_name }} {{ $personnelAction[0]->last_name }}</td>
                <th width="15%">{{ __('rrhh.created_date') }}</th>
                <td width="20%">{{ @format_date($personnelAction[0]->created_at) }}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <table>
        <thead>
            <tr>
                <th colspan="4">{{ mb_strtoupper($personnelAction[0]->type) }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th width="23%">{{ __('rrhh.employee') }}</th>
                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                <th width="15%">{{ __('rrhh.employee_status') }}</th>
                <td width="20%">
                    @if ($employee->status == 1)
                    <span>Activo</span>
                    @else
                    <span>Inactivo</span>
                    @endif
                </td>
            </tr>
            @foreach ($actions as $action)
                @if ($action->rrhh_required_action_id == 2) {{-- Cambiar departamento --}}
                    <tr>
                        <th>{{ __('rrhh.previous_department') }}</th>
                        <td>{{ $position->previousDepartment->value }}</td>
                        <th>{{ __('rrhh.previous_position') }}</th>
                        <td>{{ $position->previousPosition1->value }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('rrhh.new_department') }}</th>
                        <td>{{ $position->newDepartment->value }}</td>
                        <th>{{ __('rrhh.new_position') }}</th>
                        <td>{{ $position->newPosition1->value }}</td>
                    </tr>  
                @endif

                @if ($action->rrhh_required_action_id == 3) {{-- Cambiar salario --}}
                    <tr>
                        <th>{{ __('rrhh.previous_salary') }}</th>
                        <td colspan="3">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($salary->previous_salary) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($salary->previous_salary) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('rrhh.new_salary') }}</th>
                        <td colspan="3">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($salary->new_salary) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($salary->new_salary) }}
                            @endif
                        </td>
                    </tr>
                @endif

                @if ($action->rrhh_required_action_id == 4) {{-- Cambiar cuenta bancaria --}}
                    <tr>
                        <th>{{ __('rrhh.period') }}</th>
                        <td colspan="3">{{ @format_date($personnelAction[0]->start_date) }} - {{
                            @format_date($personnelAction[0]->end_date) }}</td>
                    </tr>
                @endif

                @if ($action->rrhh_required_action_id == 5) {{-- Cambiar cuenta bancaria --}}
                    @if ($personnelAction[0]->status == 'No autorizada (En tramite)')
                    <tr>
                        <th>{{ __('rrhh.bank_account') }}</th>
                        <td colspan="3">{{ $personnelAction[0]->bank_account }}</td>
                    </tr>
                    @elseif ($personnelAction[0]->status == 'Autorizada' || $personnelAction[0]->status == 'No requiere autorización')
                    <tr>
                        <th>{{ __('rrhh.bank_account') }}</th>
                        <td colspan="3">{{ $employee->bank_account }}</td>
                    </tr>
                    @endif
                @endif

                @if ($action->rrhh_required_action_id == 6) {{-- Cambiar forma de pago --}}
                    @if ($personnelAction[0]->status == 'No autorizada (En tramite)' )
                        <tr>
                            <th>{{ __('rrhh.way_to_pay') }}</th>
                            <td colspan="3">{{ $payment[0]->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('rrhh.bank') }}</th>
                            <td>{{ $bank[0]->name }}</td>
                            <th>{{ __('rrhh.bank_account') }}</th>
                            <td>{{ $personnelAction[0]->bank_account }}</td>
                        </tr>
                    @elseif ($personnelAction[0]->status == 'Autorizada' || $personnelAction[0]->status == 'No requiere autorización')
                        <tr>
                            <th>{{ __('rrhh.way_to_pay') }}</th>
                            <td colspan="3">{{ $employee->payment->value }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('rrhh.bank') }}</th>
                            <td>{{ $employee->bank->name }}</td>
                            <th>{{ __('rrhh.bank_account') }}</th>
                            <td>{{ $employee->bank_account }}</td>
                        </tr>
                    @endif
                @endif

                @if ($action->rrhh_required_action_id == 7) {{-- Seleccionar la fecha en que entra en vigor --}}
                <tr>
                    <th>{{ __('rrhh.in_force_from') }}</th>
                    <td colspan="3">{{ @format_date($personnelAction[0]->effective_date) }}</td>
                </tr>
                @endif
            @endforeach
            <tr>
                <th>{{ __('rrhh.description') }}</th>
                <td colspan="3">{{ $personnelAction[0]->description }}</td>
            </tr>
        </tbody>
    </table>
    <table style="border: hidden">
        <tbody style="border: hidden">
            @php
                $colNo = count($users);
                $numero = 'impar';
                if ($colNo%2 == 0){
                    $numero = 'par';
                }
                $lengthUser = count($users);
            @endphp
            @if (count($users) != 0)
                @foreach($users as $i=>$user)
                    @if ($numero == 'par')
                        @if($i%2!=0)
                            @php echo "<tr style='border: hidden; text-align: center;'>"; @endphp
                        @endif

                        <td style="border: hidden; text-align: center;">
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <span>____________________________________</span> <br>
                            <span><b>{{ __('rrhh.approved_by') }}</b></span><br>
                            <span>{{ $user->user->first_name }} {{ $user->user->last_name }}</span>
                        </td>

                        @php $colNo++; @endphp

                        @if($colNo%2==0)
                            @php echo "</tr>"; @endphp
                        @endif
                    @else
                        @if ($i != $lengthUser - 1 )
                            @if($i%2!=0)
                                @php echo "<tr style='border: hidden; text-align: center;'>"; @endphp
                            @endif

                            <td style="border: hidden; text-align: center;">
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <span>____________________________________</span> <br>
                                <span><b>{{ __('rrhh.approved_by') }}</b></span><br>
                                <span>{{ $user->user->first_name }} {{ $user->user->last_name }}</span>
                            </td>

                            @php $colNo++; @endphp

                            @if($colNo%2==0)
                                @php echo "</tr>"; @endphp
                            @endif
                        @else
                            <tr>
                                <td colspan="2" style="border: hidden; text-align: center;">
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <span>____________________________________</span> <br>
                                    <span><b>{{ __('rrhh.approved_by') }}</b></span><br>
                                    <span>{{ $user->user->first_name }} {{ $user->user->last_name }}</span>
                                </td>
                            </tr>
                        @endif
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>
</body>

</html>