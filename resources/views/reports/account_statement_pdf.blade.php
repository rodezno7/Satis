<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.account_statement_head')</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: {{ $size }}pt;
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

        .table1 thead tr th,
        .table1 thead tr td,
        .table1 tbody tr th,
        .table1 tbody tr td,
        .table1 tfoot tr th,
        .table1 tfoot tr td {
            border: 0px;
            font-size: 6pt;
        }
        td {
            border: 0.25px solid black;
            padding: 2px;
            text-align: left;
        }
        th {
            border: 0.25px solid black;
            padding: 2px;
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
            font-size: 6pt;
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

        .row {
            margin-right: 0px;
            margin-left: 0px;
        }

        .row:before,
        .row:after {
            display: table;
            content: " ";
        }

        .row:after {
            clear: both;
        }

        .col {
            position: relative;
            min-height: 1px;
            padding-right: 2px;
            padding-left: 2px;
        }

        .col {
            float: left;
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
    <div id="footer">
        <table class="table1" style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 50%;">{{ $business->business_full_name }}</td>
                    <td style="text-align: right; width: 50%;" class="page-number"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="row" style="margin-bottom: 0;">
        <div class="col" style="width: 25%;">
            @if (! empty(Session::get('business.logo')) && file_exists(public_path('uploads/business_logos/' . Session::get('business.logo'))))
            <img src="{{ public_path('/uploads/business_logos/' . Session::get('business.logo')) }}" alt="Logo" style="margin: 0; width: 130px;">
            @else
                <img src="{{ public_path('img/logo.png') }}" alt="Logo" style="margin: 0; width: 130px; margin-top: -40px;">
            @endif
        </div>

        <div class="col alncenter" style="width: 75%;">
            <p style="font-size: 10pt; margin-top: 0; margin-bottom: 2px;">
                <strong>{{ mb_strtoupper($customer->business_name ?? $customer->name) }}</strong>
            </p>

            @if (! empty($customer->business_line))
                <p style="font-size: 6pt; margin-top: 0; margin-bottom: 2px;">
                    {{ mb_strtoupper($customer->business_line) }}
                </p>
            @endif

            @if (! empty($customer->tax_number) || ! empty($customer->reg_number))
                <p style="font-size: 6pt; margin-top: 0; margin-bottom: 2px;">
                    @if (! empty($customer->tax_number))
                        NIT: {{ mb_strtoupper($customer->tax_number) }}
                    @endif
                    @if (! empty($customer->reg_number))
                        &nbsp;&nbsp;&nbsp; NRC: {{ mb_strtoupper($customer->reg_number) }}
                    @endif
                </p>
            @endif

            <p style="font-size: 6pt; margin-top: 0; margin-bottom: 2px;">
                {{ mb_strtoupper($customer->address) }}
                @if (! empty($customer->city))
                    , {{ mb_strtoupper($customer->city->name) }}
                @endif
                @if (! empty($customer->state))
                    , {{ mb_strtoupper($customer->state->name) }}
                @endif
                @if (! empty($customer->country))
                    , {{ mb_strtoupper($customer->country->name) }}
                @endif
            </p>

            @if (! empty($customer->telphone) || ! empty($customer->email))
                <p style="font-size: 6pt; margin-top: 0; margin-bottom: 2px;">
                    @if (! empty($customer->telphone))
                    TEL: {{ mb_strtoupper($customer->telphone) }}
                    @endif
                    @if (! empty($customer->email))
                    &nbsp;&nbsp;&nbsp; {{ mb_strtoupper(__('business.email')) }}: {{ $customer->email }}
                    @endif
                </p>
            @endif
        </div>
    </div>

    <p style="text-align: center; font-size: 8pt; margin-top: 25px; margin-bottom: 5px;">
        <strong>{{ mb_strtoupper(__('report.account_statement_head')) }}</strong>
    </p>

    <p style="text-align: center; font-size: 7pt; margin-top: 5px; margin-bottom: 16px;">
        <strong>
            {{ mb_strtoupper('Fecha de reporte:') }}
            {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </strong>
    </p>

    <table class="table1" style="width: 100%;">
        <thead>
            <tr>
                <th class="alncenter" style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
                    {{ __('accounting.date') }}
                </th>
                <th class="alncenter" style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
                    {{ __('accounting.document') }}
                </th>
                <th class="alncenter" style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
                    {{ __('report.currency') }}
                </th>
                <th style="border-top: 0.25px solid black; border-bottom: 0.25px solid black; width: 30%; text-align: left;">
                    {{ __('report.final_user') }}
                </th>
                <th class="alncenter" colspan="2" style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
                    {{ __('accounting.amount') }}
                </th>
                <th class="alncenter" style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
                    {{ __('report.expiration') }}
                </th>
                <th class="alncenter" style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
                    {{ __('report.payment') }}
                </th>
                <th class="alncenter" colspan="2" style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
                    {{ __('kardex.balance') }}
                </th>
                <th class="alncenter" style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
                    {{ __('report.days_late') }}
                </th>
                <th class="alncenter" style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
                    {{ __('report.accumulated') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @php
            $accumulated = 0;
            $delay_0 = 0;
            $delay_1_30 = 0;
            $delay_31_60 = 0;
            $delay_61_90 = 0;
            $delay_91_120 = 0;
            $delay_121 = 0;
            $total = 0;
            @endphp
            @foreach ($lines as $item)
            <tr>
                <td class="alncenter">
                    {{ @format_date($item['date']) }}
                </td>
                <td>
                    {{ $item['no_doc'] }}
                </td>
                <td class="alncenter">
                    {{ $item['currency'] }}
                </td>
                <td>
                    {{ $item['customer'] }}
                </td>
                <td class="alnright">
                    @if ($item['amount'] > 0)
                    {{ @num_format($item['amount']) }}
                    @endif
                </td>
                <td class="alnright">
                    @if ($item['amount'] < 0)
                    {{ @num_format($item['amount']) }}
                    @endif
                </td>
                <td class="alncenter">
                    {{ @format_date($item['expiration']) }}
                </td>
                <td class="alnright">
                    {{ @num_format($item['payment']) }}
                </td>
                <td class="alnright">
                    @if ($item['balance'] > 0)
                    {{ @num_format($item['balance']) }}
                    @endif
                </td>
                <td class="alnright">
                    @if ($item['balance'] < 0)
                    {{ @num_format($item['balance']) }}
                    @endif
                </td>
                <td class="alncenter">
                    {{ $item['delay'] }}
                </td>

                @php
                    $accumulated += $item['amount'];
                @endphp

                <td class="alnright">
                    {{ @num_format($accumulated) }}
                </td>
            </tr>

            @php
            if ($item['balance'] > 0) {
                if ($item['delay'] <= 0) {
                    $delay_0 += $item['balance'];

                } else if ($item['delay'] >= 1 && $item['delay'] <= 30) {
                    $delay_1_30 += $item['balance'];

                } else if ($item['delay'] >= 31 && $item['delay'] <= 60) {
                    $delay_31_60 += $item['balance'];

                } else if ($item['delay'] >= 61 && $item['delay'] <= 90) {
                    $delay_61_90 += $item['balance'];

                } else if ($item['delay'] >= 91 && $item['delay'] <= 120) {
                    $delay_91_120 += $item['balance'];

                } else if ($item['delay'] >= 121) {
                    $delay_121 += $item['balance'];
                }

                $total += $item['balance'];
            }
            @endphp
            @endforeach
        </tbody>
    </table>

    <table class="table1" style="width: 100%;">
        <tfoot>
            <tr>
                <td style="border-top: 0.25px solid black; text-align: center; width: 8%">
                    <strong>{{ __('report.current') }}</strong>
                </td>
                <td style="border-top: 0.25px solid black; text-align: center; width: 8%">
                    <strong>1 {{ mb_strtolower(__('report.to')) }} 30</strong>
                </td>
                <td style="border-top: 0.25px solid black; text-align: center; width: 8%">
                    <strong>31 {{ mb_strtolower(__('report.to')) }} 60</strong>
                </td>
                <td style="border-top: 0.25px solid black; text-align: center; width: 8%">
                    <strong>61 {{ mb_strtolower(__('report.to')) }} 90</strong>
                </td>
                <td style="border-top: 0.25px solid black; text-align: center; width: 8%">
                    <strong>91 {{ mb_strtolower(__('report.to')) }} 120</strong>
                </td>
                <td style="border-top: 0.25px solid black; text-align: center; width: 8%">
                    <strong>121 +</strong>
                </td>
                <td style="border-top: 0.25px solid black; text-align: center; width: 8%">
                    <strong>{{ __('accounting.total') }}</strong>
                </td>
                <td style="border-top: 0.25px solid black; width: 44%"></td>
            </tr>
            <tr>
                <td style="border-bottom: 0.25px solid black; text-align: right; width: 8%">
                    <strong>{{ @num_format($delay_0) }}</strong>
                </td>
                <td style="border-bottom: 0.25px solid black; text-align: right; width: 8%">
                    <strong>{{ @num_format($delay_1_30) }}</strong>
                </td>
                <td style="border-bottom: 0.25px solid black; text-align: right; width: 8%">
                    <strong>{{ @num_format($delay_31_60) }}</strong>
                </td>
                <td style="border-bottom: 0.25px solid black; text-align: right; width: 8%">
                    <strong>{{ @num_format($delay_61_90) }}</strong>
                </td>
                <td style="border-bottom: 0.25px solid black; text-align: right; width: 8%">
                    <strong>{{ @num_format($delay_91_120) }}</strong>
                </td>
                <td style="border-bottom: 0.25px solid black; text-align: right; width: 8%">
                    <strong>{{ @num_format($delay_121) }}</strong>
                </td>
                <td style="border-bottom: 0.25px solid black; text-align: right; width: 8%">
                    <strong>{{ @num_format($total) }}</strong>
                </td>
                <td style="border-bottom: 0.25px solid black; text-align: right; width: 44%"></td>
            </tr>
        </tfoot>
    </table>

    <p style="font-size: 6pt;">
        {{ $business->account_statement_legend }}
    </p>
</body>
</html>