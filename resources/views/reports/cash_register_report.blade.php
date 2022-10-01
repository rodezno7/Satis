<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('report.cash_register_report') }}</title>
    <style>
        @page{
            margin: 40px 15px 40px 15px;
        }
        div#cash_register_report{
            font-family: 'Courier New', Courier, monospace;
            font-size: 8pt;
        }
        div#cash_register_report h1, div#cash_register_report h2 { text-align: center; }
        div#main-table table#first{ width: 100%; }
        div#main-table table#second{ width: 75%; border-collapse: collapse; }
        div#main-table table#thrid{ width: 75%; border-collapse: collapse; }
        div#main-table table#second th,
        div#main-table table#second td{ border: 1px solid #000; }
        div#main-table table{ width: 100%; }
        div#main-table table#first thead th{ text-align: left; }
        div#main-table table#thrid tr th{ font-weight: bolder; }
        div#main-table table thead{ border: 1px solid #000; border-left-width: 0; border-right-width: 0; }
        div#main-table table tbody tr th#totals{ border: 1px solid #000; border-left-width: 0; border-right-width: 0; border-bottom-width: 0; }
        div#main-table table tbody tr th#general_totals{ border-top: 1px solid #000; border-bottom: 3px double #000; }
        div#footer{ position: fixed; right: 10px; bottom: -10px; color: #000; font-size: 9pt; font-family: 'Courier New', Courier, monospace; }
        .page-number:before{ content: "Página " counter(page); }
        ._text-left{ text-align: left; }
        ._text-right{ text-align: right; }
        ._text-center{ text-align: center; }
        .bordered{ border: 1px solid #000; }
    </style>
</head>
    <body>
        <div id="footer">
            <div class="page-number"></div>
        </div>
        <div id="cash_register_report">
            <div id="header">
                <h1>{{ $business_name }}</h1>
                <h2>LISTADO DE FACTURAS {{ @format_date($transaction_date) }}</h2>
            </div>
            <div id="main-table">
                <table id="first">
                    <thead>
                        <tr>
                            <th style="width: 7%;">FECHA</th>
                            <th style="width: 5%;">FACTURA</th>
                            <th>CLIENTE</th>
                            <th style="width: 7%;">FORM. PAGO</th>
                            <th style="width: 7.5%;">TIPO SERV.</th>
                            <th style="width: 5.5%;">CONDIC.</th>
                            <th style="width: 7%; text-align: center;">SUBTOTAL</th>
                            <th style="width: 6%; text-align: center;">DESC.</th>
                            <th style="width: 7%; text-align: center;">IVA</th>
                            <th style="width: 6%; text-align: center;">RETENCIÓN</th>
                            <th style="width: 5%; text-align: center;">EXENTO</th>
                            <th style="width: 7%; text-align: center;">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Fiscal credits --}}
                        @php
                            $flag = 0;
                            $trans_id = 0;
                            $row_counts = 0;
                            $ccf_cash = 0;
                            $ccf_card = 0;
                            $ccf_check = 0;
                            $ccf_bank_transfer = 0;
                            $ccf_credit = 0;
                            $subtotal_ccf = 0;
                            $discount_amount_ccf = 0;
                            $withheld_amount_ccf = 0;
                            $tax_amount_ccf = 0;
                            $final_total_ccf = 0;
                        @endphp
                        @if (array_search('CCF', array_column($transactions, 'doc_type')) !== false)
                            <tr>
                                <td colspan="12"><strong>Crédito Fiscal</strong></td>
                            </tr>
                            @foreach ($transactions as $t)
                                @if ($t->doc_type == 'CCF')
                                    @php
                                        if($t->payment_count <= 1 && $trans_id != $t->id){
                                            $flag = 0;
                                        }

                                        switch ($t->pay_method) {
                                            case 'cash':
                                                $ccf_cash += $t->payment_amount;
                                                break;
                                            case 'card':
                                                $ccf_card += $t->payment_amount;
                                                break;
                                            case 'check':
                                                $ccf_check += $t->payment_amount;
                                                break;
                                            case 'bank_transfer':
                                                $ccf_bank_transfer += $t->payment_amount;
                                                break;
                                            case 'credit':
                                                $ccf_credit += $t->payment_amount;
                                                break;
                                        }
                                        if($flag == 1){ continue; }

                                        $subtotal_ccf += floatval($t->subtotal);
                                        $discount_amount_ccf += floatval($t->discount_amount);
                                        $tax_amount_ccf += floatval($t->tax_amount);
                                        $withheld_amount_ccf += floatval($t->withheld_amount);
                                        $final_total_ccf += floatval($t->final_total);
                                        $row_counts ++;
                                    @endphp
                                    <tr>
                                        <td>{{ @format_date($t->transaction_date) }}</td>
                                        <td>{{ $t->correlative }}</td>
                                        <td>{{ $t->customer_name }}</td>
                                        <td>
                                            @if ($t->payment_count > 1)
                                                {{ __('report.multiple') }}
                                            @else
                                                {{ __('report.' . $t->pay_method) }}
                                            @endif
                                        </td>
                                        <td>{{ $t->delivery_type ? __('report.' . $t->delivery_type) : __('report.counter') }}</td>
                                        <td>{{ __('messages.' . $t->payment_condition) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->subtotal, 2) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->discount_amount, ) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->tax_amount, 2) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->withheld_amount, 2) }}</td>
                                        <td class="_text-right">{{ "$ 0.00" }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->final_total, 2) }}</td>
                                    </tr>
                                    @php
                                        if($t->payment_count > 1){
                                            $flag = 1;
                                        }
                                        $trans_id = $t->id;
                                    @endphp
                                @endif
                            @endforeach
                            <tr id="totals">
                                <th colspan="6"></th>
                                <th class="_text-right" id="totals">{{ "$ " . round($subtotal_ccf, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($discount_amount_ccf, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($tax_amount_ccf, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($withheld_amount_ccf, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ 0.00" }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($final_total_ccf, 2) }}</th>
                            </tr>
                        @endif
                        {{-- Invoices --}}
                        @php
                            $flag = 0;
                            $trans_id = 0;
                            $fcf_cash = 0;
                            $fcf_card = 0;
                            $fcf_check = 0;
                            $fcf_bank_transfer = 0;
                            $fcf_credit = 0;
                            $subtotal_fcf = 0;
                            $discount_amount_fcf = 0;
                            $tax_amount_fcf = 0;
                            $withheld_amount_fcf = 0;
                            $final_total_fcf = 0;
                        @endphp
                        @if (array_search('FCF', array_column($transactions, 'doc_type')) !== false)
                            <tr>
                                <td colspan="12"><strong>Consumidor Final</strong></td>
                            </tr>
                            
                            @foreach ($transactions as $t)
                                @if ($t->doc_type == 'FCF')
                                    @php
                                        if($t->payment_count <= 1 && $trans_id != $t->id){
                                            $flag = 0;
                                        }

                                        switch ($t->pay_method) {
                                            case 'cash':
                                                $fcf_cash += $t->payment_amount;
                                                break;
                                            case 'card':
                                                $fcf_card += $t->payment_amount;
                                                break;
                                            case 'check':
                                                $fcf_check += $t->payment_amount;
                                                break;
                                            case 'bank_transfer':
                                                $fcf_bank_transfer += $t->payment_amount;
                                                break;
                                            case 'credit':
                                                $fcf_credit += $t->payment_amount;
                                                break;
                                        }

                                        if($flag == 1){ continue; }

                                        $subtotal_fcf += floatval($t->subtotal);
                                        $discount_amount_fcf += floatval($t->discount_amount);
                                        $tax_amount_fcf += floatval($t->tax_amount);
                                        $withheld_amount_fcf += floatval($t->withheld_amount);
                                        $final_total_fcf += floatval($t->final_total);
                                        $row_counts ++;
                                    @endphp
                                    <tr>
                                        <td>{{ @format_date($t->transaction_date) }}</td>
                                        <td>{{ $t->correlative }}</td>
                                        <td>{{ $t->customer_name }}</td>
                                        <td>
                                            @if ($t->payment_count > 1)
                                                {{ __('report.multiple') }}
                                            @else
                                                {{ __('report.' . $t->pay_method) }}
                                            @endif
                                        </td>
                                        <td>{{ $t->delivery_type ? __('report.' . $t->delivery_type) : __('report.counter') }}</td>
                                        <td>{{ __('messages.' . $t->payment_condition) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->subtotal, 2) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->discount_amount, 2) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->tax_amount, 2) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->withheld_amount, 2) }}</td>
                                        <td class="_text-right">{{ "$ 0.00" }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->final_total, 2) }}</td>
                                    </tr>
                                    @php
                                        if($t->payment_count > 1){
                                            $flag = 1;
                                        }
                                        $trans_id = $t->id;
                                    @endphp
                                @endif
                            @endforeach
                            <tr id="totals">
                                <th colspan="6"></th>
                                <th class="_text-right" id="totals">{{ "$ " . round($subtotal_fcf, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($discount_amount_fcf, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($tax_amount_fcf, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($withheld_amount_fcf, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ 0.00" }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($final_total_fcf, 2) }}</th>
                            </tr>
                        @endif
                        {{-- Tickets --}}
                        @php
                            $flag = 0;
                            $trans_id = 0;
                            $row_counts = 0;
                            $ticket_cash = 0;
                            $ticket_card = 0;
                            $ticket_check = 0;
                            $ticket_bank_transfer = 0;
                            $ticket_credit = 0;
                            $subtotal_ticket = 0;
                            $discount_amount_ticket = 0;
                            $withheld_amount_ticket = 0;
                            $tax_amount_ticket = 0;
                            $final_total_ticket = 0;
                        @endphp
                        @if (array_search('Ticket', array_column($transactions, 'doc_type')) !== false)
                            <tr>
                                <td colspan="12"><strong>Ticket</strong></td>
                            </tr>
                            @foreach ($transactions as $t)
                                @if ($t->doc_type == 'Ticket')
                                    @php
                                        if($t->payment_count <= 1 && $trans_id != $t->id){
                                            $flag = 0;
                                        }

                                        switch ($t->pay_method) {
                                            case 'cash':
                                                $ticket_cash += $t->payment_amount;
                                                break;
                                            case 'card':
                                                $ticket_card += $t->payment_amount;
                                                break;
                                            case 'check':
                                                $ticket_check += $t->payment_amount;
                                                break;
                                            case 'bank_transfer':
                                                $ticket_bank_transfer += $t->payment_amount;
                                                break;
                                            case 'credit':
                                                $ticket_credit += $t->payment_amount;
                                                break;
                                        }
                                        if($flag == 1){ continue; }

                                        $subtotal_ticket += floatval($t->subtotal);
                                        $discount_amount_ticket += floatval($t->discount_amount);
                                        $tax_amount_ticket += floatval($t->tax_amount);
                                        $withheld_amount_ticket += floatval($t->withheld_amount);
                                        $final_total_ticket += floatval($t->final_total);
                                        $row_counts ++;
                                    @endphp
                                    <tr>
                                        <td>{{ @format_date($t->transaction_date) }}</td>
                                        <td>{{ $t->correlative }}</td>
                                        <td>{{ $t->customer_name }}</td>
                                        <td>
                                            @if ($t->payment_count > 1)
                                                {{ __('report.multiple') }}
                                            @else
                                                {{ __('report.' . $t->pay_method) }}
                                            @endif
                                        </td>
                                        <td>{{ $t->delivery_type ? __('report.' . $t->delivery_type) : __('report.counter') }}</td>
                                        <td>{{ __('messages.' . $t->payment_condition) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->subtotal, 2) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->discount_amount, ) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->tax_amount, 2) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->withheld_amount, 2) }}</td>
                                        <td class="_text-right">{{ "$ 0.00" }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->final_total, 2) }}</td>
                                    </tr>
                                    @php
                                        if($t->payment_count > 1){
                                            $flag = 1;
                                        }
                                        $trans_id = $t->id;
                                    @endphp
                                @endif
                            @endforeach
                            <tr id="totals">
                                <th colspan="6"></th>
                                <th class="_text-right" id="totals">{{ "$ " . round($subtotal_ticket, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($discount_amount_ticket, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($tax_amount_ticket, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($withheld_amount_ticket, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ 0.00" }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($final_total_ticket, 2) }}</th>
                            </tr>
                        @endif
                        {{-- Export invoices --}}
                        @php
                            $flag = 0;
                            $trans_id = 0;
                            $exp_cash = 0;
                            $exp_card = 0;
                            $exp_check = 0;
                            $exp_bank_transfer = 0;
                            $exp_credit = 0;
                            $subtotal_exp = 0;
                            $discount_amount_exp = 0;
                            $tax_amount_exp = 0;
                            $withheld_amount_exp = 0;
                            $final_total_exp = 0;
                        @endphp
                        @if (array_search('EXP', array_column($transactions, 'doc_type')) !== false)
                            <tr>
                                <td colspan="12"><strong>Facturas de Exportación</strong></td>
                            </tr>
                            @foreach ($transactions as $t)
                                @if ($t->doc_type == 'EXP')
                                    @php
                                        if($t->payment_count <= 1 && $trans_id != $t->id){
                                            $flag = 0;
                                        }

                                        switch ($t->pay_method) {
                                            case 'cash':
                                                $exp_cash += $t->payment_amount;
                                                break;
                                            case 'card':
                                                $exp_card += $t->payment_amount;
                                                break;
                                            case 'check':
                                                $exp_check += $t->payment_amount;
                                                break;
                                            case 'bank_transfer':
                                                $exp_bank_transfer += $t->payment_amount;
                                                break;
                                            case 'credit':
                                                $exp_credit += $t->payment_amount;
                                                break;
                                        }
                                        if($flag == 1){ continue; }

                                        $subtotal_exp += floatval($t->subtotal);
                                        $discount_amount_exp += floatval($t->discount_amount);
                                        $tax_amount_exp += floatval($t->tax_amount);
                                        $withheld_amount_exp += floatval($t->withheld_amount);
                                        $final_total_exp += floatval($t->final_total);
                                        $row_counts ++;
                                    @endphp
                                    <tr>
                                        <td>{{ @format_date($t->transaction_date) }}</td>
                                        <td>{{ $t->correlative }}</td>
                                        <td>{{ $t->customer_name }}</td>
                                        <td>
                                            @if ($t->payment_count > 1)
                                                {{ __('report.multiple') }}
                                            @else
                                                {{ __('report.' . $t->pay_method) }}
                                            @endif
                                        </td>
                                        <td>{{ $t->delivery_type ? __('report.' . $t->delivery_type) : __('report.counter') }}</td>
                                        <td>{{ __('messages.' . $t->payment_condition) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->subtotal, 2) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->discount_amount, 2) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->tax_amount, 2) }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->withheld_amount, 2) }}</td>
                                        <td class="_text-right">{{ "$ 0.00" }}</td>
                                        <td class="_text-right">{{ "$ " . round($t->final_total, 2) }}</td>
                                    </tr>
                                    @php
                                        if($t->payment_count > 1){
                                            $flag = 1;
                                        }
                                        $trans_id = $t->id;
                                    @endphp
                                @endif
                            @endforeach
                            <tr id="totals">
                                <th colspan="6"></th>
                                <th class="_text-right" id="totals">{{ "$ " . round($subtotal_exp, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($discount_amount_exp, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($tax_amount_exp, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($withheld_amount_exp, 2) }}</th>
                                <th class="_text-right" id="totals">{{ "$ 0.00" }}</th>
                                <th class="_text-right" id="totals">{{ "$ " . round($final_total_exp, 2) }}</th>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="12">&nbsp;</td>
                        </tr>
                        <tr id="totals">
                            <th colspan="6" style="text-align: center;">TOTAL GENERAL</th>
                            <th class="_text-right" id="general_totals">{{ "$ " . @num_format($subtotal_ccf + $subtotal_fcf + $subtotal_ticket + $subtotal_exp) }}</th>
                            <th class="_text-right" id="general_totals">{{ "$ " . @num_format($discount_amount_ccf + $discount_amount_fcf + $discount_amount_ticket + $discount_amount_exp) }}</th>
                            <th class="_text-right" id="general_totals">{{ "$ " . @num_format($tax_amount_ccf + $tax_amount_fcf + $tax_amount_ticket + $tax_amount_exp) }}</th>
                            <th class="_text-right" id="general_totals">{{ "$ " . @num_format($withheld_amount_ccf + $withheld_amount_fcf + $withheld_amount_ticket + $withheld_amount_exp) }}</th>
                            <th class="_text-right" id="general_totals">{{ "$ 0.00" }}</th>
                            <th class="_text-right" id="general_totals">{{ "$ " . @num_format($final_total_ccf + $final_total_fcf + $final_total_ticket + $final_total_exp) }}</th>
                        </tr>
                    </tbody>
                </table>
                <br><br>
                <table id="second" @if (($row_counts >= 19 && $row_counts <= 32) || ($row_counts >= 66 && $row_counts <= 79)) style="page-break-before: always;" @endif>
                    <thead>
                        <tr>
                            <th style="width: 8.5%;">&nbsp;</th>
                            <th style="width: 11%;">CONTADO</th>
                            <th style="width: 8.5%;">CRÉDITO</th>
                            <th style="width: 8.5%;">TARJETA</th>
                            <th style="width: 8.5%;">CHEQUE</th>
                            <th style="width: 8.5%;">TRANSFER.</th>
                            <th style="width: 8.5%;">S. TOTAL</th>
                            <th style="width: 8.5%;">T-REGALO</th>
                            <th style="width: 8.5%;">COBRO</th>
                            <th style="width: 12.5%;">ING. TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>CCF</th>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_cash) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_credit) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_card) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_check) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_bank_transfer) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_cash + $ccf_card + $ccf_credit + $ccf_check + $ccf_bank_transfer) }}</td>
                            <td class="_text-right">{{ "$ 0.00" }}</td>
                            <td class="_text-right">{{ "$ 0.00" }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_cash + $ccf_card + $ccf_credit + $ccf_check + $ccf_bank_transfer) }}</td>
                        </tr>
                        <tr>
                            <th>FCF</th>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_cash) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_credit) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_card) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_check) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_bank_transfer) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_cash + $fcf_card + $fcf_credit + $fcf_check + $fcf_bank_transfer) }}</td>
                            <td class="_text-right">{{ "$ 0.00" }}</td>
                            <td class="_text-right">{{ "$ 0.00" }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_cash + $fcf_card + $fcf_credit + $fcf_check + $fcf_bank_transfer) }}</td>
                        </tr>
                        <tr>
                            <th>Ticket</th>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_cash) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_credit) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_card) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_check) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_bank_transfer) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_cash + $ticket_card + $ticket_credit + $ticket_check + $ticket_bank_transfer) }}</td>
                            <td class="_text-right">{{ "$ 0.00" }}</td>
                            <td class="_text-right">{{ "$ 0.00" }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_cash + $ticket_card + $ticket_credit + $ticket_check + $ticket_bank_transfer) }}</td>
                        </tr>
                        <tr>
                            <th>EXP</th>
                            <td class="_text-right">{{ "$ " . @num_format($exp_cash) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_credit) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_card) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_check) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_bank_transfer) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_cash + $exp_card + $exp_credit + $exp_check + $exp_bank_transfer) }}</td>
                            <td class="_text-right">{{ "$ 0.00" }}</td>
                            <td class="_text-right">{{ "$ 0.00" }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_cash + $exp_card + $exp_credit + $exp_check + $exp_bank_transfer) }}</td>
                        </tr>
                    </tbody>
                </table>
                <br>
                @php
                    $total_cash = $ccf_cash + $fcf_cash + $ticket_cash + $exp_cash;
                    $total_credit = $ccf_credit + $fcf_credit + $ticket_credit + $exp_credit;
                    $total_card = $ccf_card + $fcf_card + $ticket_card + $exp_card;
                    $total_check = $ccf_check + $fcf_check + $ticket_check + $exp_check;
                    $total_bank_transfer = $ccf_bank_transfer + $fcf_bank_transfer + $ticket_bank_transfer + $exp_bank_transfer;
                @endphp
                <table id="thrid">
                    <tr> {{-- Totals table --}}
                        <td style="width: 4.1%;" rowspan="6">&nbsp;</td>
                        <th style="width: 17%;" class="_text-left">Total Efectivo:</th>
                        <th style="width: 13.7%; text-align: right; border-bottom: 1.5px solid #000;">
                            {{ "$ " . @num_format($total_cash) }}
                        </th>
                        <td style="width: 5%;">&nbsp;</td>
                        <td style="width: 9.4%;">&nbsp;</td>
                        <th class="bordered" style="text-align: center;">Vtas. Gravadas</th>
                        <th class="bordered" style="text-align: center;">IVA Débido</th>
                        <th class="bordered" style="text-align: center;">Retención</th>
                        <th class="bordered" style="text-align: center;">Total</th>
                    </tr>
                    <tr>
                        <th class="_text-left">Total Créditos:</th>
                        <th style="text-align: right; border-bottom: 1.5px solid #000;">
                            {{ "$ " . @num_format($total_credit) }}
                        </th>
                        <td>&nbsp;</td>
                        <th class="bordered">CCF</th>
                        <td class="_text-right bordered">{{ "$ " . @num_format($subtotal_ccf - $discount_amount_ccf) }}</td>
                        <td class="_text-right bordered">{{ "$ " . @num_format($tax_amount_ccf) }}</td>
                        <td class="_text-right bordered">{{ "$ " . @num_format($withheld_amount_ccf) }}</td>
                        <td class="_text-right bordered">{{ "$ " . @num_format($final_total_ccf) }}</td>
                    </tr>
                    <tr>
                        <th class="_text-left">Total Tarjeta:</th>
                        <th style="text-align: right; border-bottom: 1.5px solid #000;">
                            {{ "$ " . @num_format($total_card) }}
                        </th>
                        <td>&nbsp;</td>
                        <th class="bordered">FCF</th>
                        <td class="_text-right bordered">{{ "$ " . @num_format($subtotal_fcf - $discount_amount_fcf) }}</td>
                        <td class="_text-right bordered">{{ "$ " . @num_format($tax_amount_fcf) }}</td>
                        <td class="_text-right bordered">{{ "$ " . @num_format($withheld_amount_fcf) }}</td>
                        <td class="_text-right bordered">{{ "$ " . @num_format($final_total_fcf) }}</td>
                    </tr>
                    <tr>
                        <th class="_text-left">Total Cheque:</th>
                        <th style="text-align: right; border-bottom: 1.5px solid #000;">
                            {{ "$ " . @num_format($total_check) }}
                        </th>
                        <td>&nbsp;</td>
                        <th class="bordered">Ticket</th>
                        <td class="_text-right bordered">{{ "$ " . @num_format($subtotal_ticket - $discount_amount_ticket) }}</td>
                        <td class="_text-right bordered">{{ "$ " . @num_format($tax_amount_ticket) }}</td>
                        <td class="_text-right bordered">{{ "$ " . @num_format($withheld_amount_ticket) }}</td>
                        <td class="_text-right bordered">{{ "$ " . @num_format($final_total_ticket) }}</td>
                    </tr>
                    <tr>
                        <th class="_text-left">Total Transfer B.</th>
                        <th style="text-align: right; border-bottom: 1.5px solid #000;">
                            {{ "$ " . @num_format($total_bank_transfer) }}
                        </th>
                        <td>&nbsp;</td>
                        <th class="bordered">EXP</th>
                        <td class="_text-right bordered">{{ "$ " . @num_format($subtotal_exp - $discount_amount_exp) }}</td>
                        <td class="_text-right bordered">{{ "$ " . @num_format($tax_amount_exp) }}</td>
                        <td class="_text-right bordered">{{ "$ " . @num_format($withheld_amount_exp) }}</td>
                        <td class="_text-right bordered">{{ "$ " . @num_format($final_total_exp) }}</td>
                    </tr>
                    <tr>
                        <th class="_text-left">Total:</th>
                        <th style="text-align: right; border-bottom: 1.5px solid #000;">
                            {{ "$ " . @num_format($total_cash + $total_credit + $total_card + $total_check + $total_bank_transfer) }}
                        </th>
                        <td>&nbsp;</td>
                        <th class="bordered">TOTAL</th>
                        <th class="_text-right bordered">{{ "$" . @num_format( ($subtotal_ccf - $discount_amount_ccf) + ($subtotal_fcf - $discount_amount_fcf) + ($subtotal_ticket - $discount_amount_ticket) + ($subtotal_exp - $discount_amount_exp) ) }}</th>
                        <th class="_text-right bordered">{{ "$" . @num_format( $tax_amount_ccf + $tax_amount_fcf + $tax_amount_ticket + $tax_amount_exp ) }}</th>
                        <th class="_text-right bordered">{{ "$" . @num_format( $withheld_amount_ccf + $withheld_amount_fcf + $withheld_amount_ticket + $withheld_amount_exp ) }}</th>
                        <th class="_text-right bordered">{{ "$" . @num_format( $final_total_ccf + $final_total_fcf + $final_total_ticket + $final_total_exp ) }}</th>
                    </tr>
                </table>
            </div>
            {{--"row_count " . $row_counts --}}
        </div>
    </body>
</html>