<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('payroll.payment_slips')</title>
    <style>
        @page {
            padding: 0;
            margin: 1.8cm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin: 3px 0 0 0;
        }
        h3 {
            text-align: center;
            margin: 0 0 0 0;
            text-transform: uppercase;
            font-size: 14px;
        }

        h4 {
            text-align: center;
            margin: 2px 0 0 0;
            text-transform: uppercase;
            font-size: 12px;
        }

        table {
            margin-top: 8px;
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: none;
            text-align: left;
        }

        .payroll-detail {
            margin-top: 8px;
            margin-bottom: 5px;
            width: 100%;
            border-collapse: collapse;
        }

        .payroll-detail th,
        .payroll-detail td {
            text-align: left;
            padding: 0 5px 0 5px;
        }

        .payroll-detail th{
            background: rgb(228, 228, 228);
        }
    </style>
</head>

<body>
    @foreach ($payroll->payrollDetails as $key => $payrollDetail)
        <h2>
            {{ mb_strtoupper(__('payroll.payment_slips')) }} - 
            @if ($payroll->payrollType->name == "Planilla de sueldos")
                {{ mb_strtoupper(__('payroll.salary')) }}
            @endif
            @if ($payroll->payrollType->name == "Planilla de honorarios")
                {{ mb_strtoupper(__('payroll.honorary')) }}
            @endif
            @if ($payroll->payrollType->name == "Planilla de aguinaldos")
                {{ mb_strtoupper(__('payroll.bonus')) }}
            @endif
        </h2>
        <h3>{{ $business->name }}</h3>
        @if ($payroll->payrollType->name == 'Planilla de aguinaldos')
            <h4>{{ __('payroll.message_period_payroll_1') }} {{ $dateEmployee[$key] }} {{ __('payroll.message_period_payroll_2') }} {{ $end_date }}</h4>
        @else
        <h4>{{ __('payroll.message_period_payroll_1') }} {{ $start_date }} {{ __('payroll.message_period_payroll_2') }} {{ $end_date }}</h4>
        @endif
        
        
        <table>
            <tbody>
                <tr>
                    <th width="17%">{{ __('rrhh.name') }}:</th>
                    <td width="34%">{{ $payrollDetail->employee->first_name }} {{ $payrollDetail->employee->last_name }}</td>
                    <th width="18%">{{ __('rrhh.department') }}:</th>
                    <td width="31%">
                        @foreach ($payrollDetail->employee->positionHistories as $positionHistory)
                            @if ($positionHistory->current == 1)
                                {{ $positionHistory->newDepartment->value }}
                            @endif
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>{{ __('rrhh.position') }}:</th>
                    <td>
                        @foreach ($payrollDetail->employee->positionHistories as $positionHistory)
                            @if ($positionHistory->current == 1)
                                {{ $positionHistory->newPosition1->value }}
                            @endif
                        @endforeach
                    </td>
                    <th>{{ __('payroll.montly_salary') }}:</th>
                    <td>
                        @if ($business->currency_symbol_placement == 'after')
                            {{ @num_format($payrollDetail->montly_salary) }} {{ $business->currency->symbol }}
                        @else
                            {{ $business->currency->symbol }} {{ @num_format($payrollDetail->montly_salary) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    
                    <th>{{ __('payroll.worked_days') }}:</th>
                    <td>{{ $payrollDetail->days }}</td>
                    <th>{{ __('rrhh.way_to_pay') }}: </th>
                    <td>
                        {{ $payrollDetail->employee->payment->value }}
                    </td>
                </tr>
                @if ($payrollDetail->employee->payment->value == "Transferencia bancaria")
                    <tr>
                        <th>
                            {{ __('rrhh.bank') }}:
                        </th>
                        <td>
                            {{ $payrollDetail->employee->bank->name }}
                        </td>
                        <th>
                            {{ __('rrhh.bank_account') }}:
                        </th>
                        <td>
                            {{ $payrollDetail->employee->bank_account }}
                        </td>
                    </tr> 
                @endif
            </tbody>
        </table>
        @if ($payroll->payrollType->name == "Planilla de sueldos")
            <table class="payroll-detail">
                <thead>
                    <tr style="text-align: center !important">
                        <th colspan="2" style="background: rgb(228, 228, 228)">{{ __('rrhh.incomes') }}</th>
                        <th colspan="2" style="background: rgb(228, 228, 228)">{{ __('rrhh.withholdings_deductions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="35%">{{ __('payroll.regular_salary') }}</td>
                        <td width="13%" style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->regular_salary) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->regular_salary) }}
                            @endif   
                        </td>

                        <td width="39%">ISSS</td>
                        <td width="13%" style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->isss) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->isss) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('payroll.daytime_overtime') }}</td>
                        <td style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->daytime_overtime) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->daytime_overtime) }}
                            @endif
                        </td>
                        
                        <td>AFP</td>
                        <td style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->afp) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->afp) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('payroll.night_overtime_hours') }}</td>
                        <td style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->night_overtime_hours) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->night_overtime_hours) }}
                            @endif
                        </td>
                        
                        <td>{{ __('payroll.rent') }}</td>
                        <td style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->rent) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->rent) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('payroll.other_income') }}</td>
                        <td style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->other_income) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->other_income) }}
                            @endif
                        </td>

                        <td>{{ __('payroll.other_deductions') }}</td>
                        <td style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->other_deductions) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->other_deductions) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('payroll.total_income') }}</th>
                        <th style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->total_income) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->total_income) }}
                            @endif
                        </th>
                        <th>{{ __('payroll.total_withholdings_deductions') }}</th>
                        <th style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->total_discount) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->total_discount) }}
                            @endif
                        </th>
                    </tr>
                    <tr>
                        <th>{{ __('payroll.total_to_pay') }}</th>
                        <th style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->total_to_pay) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->total_to_pay) }}
                            @endif
                        </th>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        @endif
        @if ($payroll->payrollType->name == "Planilla de honorarios")
            <br>
            <table class="payroll-detail">
                <thead>
                    <tr style="text-align: center !important">
                        <th colspan="3" style="background: rgb(228, 228, 228)">{{ __('payroll.detail') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="5%"> </td>
                        <td>{{ __('payroll.total_calculation') }}</td>
                        <td width="15%" style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->regular_salary) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->regular_salary) }}
                            @endif   
                        </td>
                    </tr>
                    <tr>
                        <td>(-)</td>
                        <td>{{ __('payroll.rent') }}</td>
                        <td style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->rent) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->rent) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>=</th>
                        <th>{{ __('payroll.total_to_pay') }}</th>
                        <th style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->total_to_pay) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->total_to_pay) }}
                            @endif
                        </th>
                    </tr>
                </tbody>
            </table>
            <br>
        @endif
        @if ($payroll->payrollType->name == "Planilla de aguinaldos")
            <br>
            <table class="payroll-detail">
                <thead>
                    <tr style="text-align: center !important">
                        <th colspan="3" style="background: rgb(228, 228, 228)">{{ __('payroll.detail') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="5%"> </td>
                        <td>{{ __('payroll.bonus') }}</td>
                        <td width="15%" style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->bonus) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->bonus) }}
                            @endif   
                        </td>
                    </tr>
                    <tr>
                        <td>(-)</td>
                        <td>{{ __('payroll.rent') }}</td>
                        <td style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->rent) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->rent) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>=</th>
                        <th>{{ __('payroll.total_to_pay') }}</th>
                        <th style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->total_to_pay) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->total_to_pay) }}
                            @endif
                        </th>
                    </tr>
                </tbody>
            </table>
            <br>
        @endif
        @if ($payroll->payrollType->name == "Planilla de aguinaldos")
            <br>
            <table class="payroll-detail">
                <thead>
                    <tr style="text-align: center !important">
                        <th colspan="3" style="background: rgb(228, 228, 228)">{{ __('payroll.detail') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="5%"> </td>
                        <td>{{ __('payroll.vacation') }}</td>
                        <td width="15%" style="text-align: right;">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($payrollDetail->vacation) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($payrollDetail->vacation) }}
                            @endif   
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
        @endif
        <table>
            <tbody>
                <tr>
                    <td style="text-align: justify;">
                        {{ __('payroll.message_payment_1') }} {{ $business->name }}, {{ __('payroll.message_payment_2') }}
                    </td>
                    <td width="2%"> </td>
                    <td width="16%" style="text-align: center !important; ">
                        <br>
                        <br>
                        <br>
                        _____________________________
                        <br> Firma del empleado
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <br>
        <br>
        <br>
    @endforeach
</body>
</html>