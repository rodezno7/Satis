<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('payroll.payment_slips')</title>
    <style>
        @page {
            padding: 0;
            margin: 1.5cm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
        }

        h2 {
            text-align: center;
            margin: 5px 0 0 0;
        }

        h4 {
            text-align: center;
            margin: 1px 0 0 0;
            text-transform: uppercase;
            font-size: 12px;
        }

        table {
            margin-top: 2px;
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
    <h2>{{ mb_strtoupper(__('payroll.payment_slips')) }}</h2>
    <h4>{{ __('payroll.message_period_payroll_1') }} {{ $start_date }} {{ __('payroll.message_period_payroll_2') }} {{ $end_date }}</h4>
    <br>
    <table>
        <tbody>
            <tr>
                <th width="18%">{{ __('business.business') }}:</th>
                <td width="32%">{{ $business->name }}</td>
                <th width="18%">{{ __('rrhh.name') }}:</th>
                <td width="32%">{{ $payrollDetail->employee->first_name }} {{ $payrollDetail->employee->last_name }}</td>
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
                <th>{{ __('rrhh.department') }}:</th>
                <td>
                    @foreach ($payrollDetail->employee->positionHistories as $positionHistory)
                        @if ($positionHistory->current == 1)
                            {{ $positionHistory->newDepartment->value }}
                        @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>{{ __('payroll.montly_salary') }}:</th>
                <td>
                    @if ($business->currency_symbol_placement == 'after')
                        {{ @num_format($payrollDetail->montly_salary) }} {{ $business->currency->symbol }}
                    @else
                        {{ $business->currency->symbol }} {{ @num_format($payrollDetail->montly_salary) }}
                    @endif
                </td>
                <th>{{ __('payroll.worked_days') }}:</th>
                <td>{{ $payrollDetail->days }}</td>
            </tr>
            <tr>
                @if ($payrollDetail->employee->payment->value == "Transferencia bancaria")
                    <th>
                        {{ __('rrhh.way_to_pay') }}<br><br>
                    </th>
                    <td>
                        {{ $payrollDetail->employee->payment->value }}<br><br>
                    </td>
                    <th width="19%">
                        {{ __('rrhh.bank') }}: <br>
                        {{ __('rrhh.bank_account') }}:
                    </th>
                    <td>
                        {{ $payrollDetail->employee->bank->name }}<br>
                        {{ $payrollDetail->employee->bank_account }}
                    </td>
                @else
                    <th>
                        {{ __('rrhh.way_to_pay') }}
                    </th>
                    <td>
                        {{ $payrollDetail->employee->payment->value }}
                    </td>
                    <th></th>
                    <td></td>
                @endif
            </tr>  
            
        </tbody>
    </table>
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
                    @php
                        $total_income = $payrollDetail->regular_salary + $payrollDetail->daytime_overtime + $payrollDetail->night_overtime_hours + $payrollDetail->other_income;
                    @endphp
                    @if ($business->currency_symbol_placement == 'after')
                        {{ @num_format($total_income) }} {{ $business->currency->symbol }}
                    @else
                        {{ $business->currency->symbol }} {{ @num_format($total_income) }}
                    @endif
                </th>
                <th>{{ __('payroll.total_withholdings_deductions') }}</th>
                <th style="text-align: right;">
                    @php
                        $total_discount = $payrollDetail->isss + $payrollDetail->afp + $payrollDetail->rent + $payrollDetail->other_deductions;
                    @endphp
                    @if ($business->currency_symbol_placement == 'after')
                        {{ @num_format($total_discount) }} {{ $business->currency->symbol }}
                    @else
                        {{ $business->currency->symbol }} {{ @num_format($total_discount) }}
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
    <table>
        <tbody>
            <tr>
                <td style="text-align: justify;">
                    {{ __('payroll.message_payment_1') }} {{ $business->name }}, {{ __('payroll.message_payment_2') }}
                </td>
                <td width="3%"> </td>
                <td width="20%" style="text-align: center !important; ">
                    <br>
                    <br>
                    <br>
                    _____________________________
                    <br> Firma del empleado
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>