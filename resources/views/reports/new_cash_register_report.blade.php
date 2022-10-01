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
        div#main-table table#expenses { width: 30%; border-collapse: collapse; }
        div#main-table table#expenses tr td { border: 1px solid #000; padding: 3px 5px; }
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
                <h1>{{ $business_name . " - " . $location_name }}</h1>
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
                            $ccf_return = 0;
                            $subtotal_ccf = 0;
                            $discount_amount_ccf = 0;
                            $withheld_amount_ccf = 0;
                            $tax_amount_ccf = 0;
                            $final_total_ccf = 0;
                        @endphp
                        @if (count($ccf) > 0)
                            <tr>
                                <td colspan="12"><strong>Crédito Fiscal</strong></td>
                            </tr>
                            @foreach ($ccf as $t)
                                @if ($t->doc_type == 'CCF')
                                    @php
                                        if($t->payment_count <= 1 && $trans_id != $t->id){
                                            $flag = 0;
                                        }

                                        $ccf_cash += $t->cash_amount;
                                        $ccf_card += $t->card_amount;
                                        $ccf_check += $t->check_amount;
                                        $ccf_bank_transfer += $t->bank_transfer_amount;
                                        $ccf_credit += $t->credit_amount;
                                        $ccf_return += $t->return_amount;

                                        if($flag == 1){ continue; }

                                        /** determinate pay method */
                                        $pay_method = '';
                                        if($t->cash_amount > 0) $pay_method = 'cash';
                                        if($t->card_amount > 0) $pay_method = 'card';
                                        if($t->check_amount > 0) $pay_method = 'check';
                                        if($t->credit_amount > 0) $pay_method = 'credit';
                                        if($t->bank_transfer_amount > 0) $pay_method = 'bank_transfer';

                                        if ($t->payment_condition == 'sell_return') {
                                            $subtotal_ccf -= floatval($t->subtotal);
                                            $discount_amount_ccf -= floatval($t->discount_amount);
                                            $tax_amount_ccf -= floatval($t->tax_amount);
                                            $withheld_amount_ccf -= floatval($t->withheld_amount);
                                            $final_total_ccf -= floatval($t->final_total);
                                        } else {
                                            $subtotal_ccf += floatval($t->subtotal);
                                            $discount_amount_ccf += floatval($t->discount_amount);
                                            $tax_amount_ccf += floatval($t->tax_amount);
                                            $withheld_amount_ccf += floatval($t->withheld_amount);
                                            $final_total_ccf += floatval($t->final_total);
                                        }
                                        
                                        $row_counts ++;
                                    @endphp
                                    <tr>
                                        <td>{{ @format_date($t->transaction_date) }}</td>
                                        <td>{{ $t->correlative }}</td>
                                        <td>{{ $t->customer_name }}</td>
                                        <td>
                                            @if ($t->payment_count > 1)
                                                {{ __('payment.multiple') }}
                                            @else
                                                @if ($t->payment_condition == 'sell_return')
                                                    {{ __('sale.return') }}
                                                @elseif($t->payment_condition == 'credit' || $t->payment_condition == 'cash' )
                                                    {{ __('payment.' . $pay_method) }}
                                                @else
                                                    -
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($t->payment_condition == 'sell_return')
                                                -
                                            @else
                                                {{ $t->delivery_type ? __('report.' . $t->delivery_type) : __('report.counter') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($t->payment_condition == 'sell_return')
                                                -
                                            @else
                                                {{ __('messages.' . $t->payment_condition) }}
                                            @endif
                                        </td>
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
                            $fcf_return = 0;
                            $subtotal_fcf = 0;
                            $discount_amount_fcf = 0;
                            $tax_amount_fcf = 0;
                            $withheld_amount_fcf = 0;
                            $final_total_fcf = 0;
                        @endphp
                        @if (count($fcf) > 0)
                            <tr>
                                <td colspan="12"><strong>Consumidor Final</strong></td>
                            </tr>
                            
                            @foreach ($fcf as $t)
                                @if ($t->doc_type == 'FCF')
                                    @php
                                        if($t->payment_count <= 1 && $trans_id != $t->id){
                                            $flag = 0;
                                        }

                                        $fcf_cash += $t->cash_amount;
                                        $fcf_card += $t->card_amount;
                                        $fcf_check += $t->check_amount;
                                        $fcf_bank_transfer += $t->bank_transfer_amount;
                                        $fcf_credit += $t->credit_amount;
                                        $fcf_return += $t->return_amount;

                                        if($flag == 1){ continue; }

                                        /** determinate pay method */
                                        $pay_method = '';
                                        if($t->cash_amount > 0) $pay_method = 'cash';
                                        if($t->card_amount > 0) $pay_method = 'card';
                                        if($t->check_amount > 0) $pay_method = 'check';
                                        if($t->credit_amount > 0) $pay_method = 'credit';
                                        if($t->bank_transfer_amount > 0) $pay_method = 'bank_transfer';

                                        if ($t->payment_condition == 'sell_return') {
                                            $subtotal_fcf -= floatval($t->subtotal);
                                            $discount_amount_fcf -= floatval($t->discount_amount);
                                            $tax_amount_fcf -= floatval($t->tax_amount);
                                            $withheld_amount_fcf -= floatval($t->withheld_amount);
                                            $final_total_fcf -= floatval($t->final_total);
                                        } else {
                                            $subtotal_fcf += floatval($t->subtotal);
                                            $discount_amount_fcf += floatval($t->discount_amount);
                                            $tax_amount_fcf += floatval($t->tax_amount);
                                            $withheld_amount_fcf += floatval($t->withheld_amount);
                                            $final_total_fcf += floatval($t->final_total);
                                        }

                                        $row_counts ++;
                                    @endphp
                                    <tr>
                                        <td>{{ @format_date($t->transaction_date) }}</td>
                                        <td>{{ $t->correlative }}</td>
                                        <td>{{ $t->customer_name }}</td>
                                        <td>
                                            @if ($t->payment_count > 1)
                                                {{ __('payment.multiple') }}
                                            @else
                                                @if ($t->payment_condition == 'sell_return')
                                                    {{ __('sale.return') }}
                                                @elseif($t->payment_condition == 'credit' || $t->payment_condition == 'cash' )
                                                    {{ __('payment.' . $pay_method) }}
                                                @else
                                                    -
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($t->payment_condition == 'sell_return')
                                                -
                                            @else
                                                {{ $t->delivery_type ? __('report.' . $t->delivery_type) : __('report.counter') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($t->payment_condition == 'sell_return')
                                                -
                                            @else
                                                {{ __('messages.' . $t->payment_condition) }}
                                            @endif
                                        </td>
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
                            $ticket_return = 0;
                            $subtotal_ticket = 0;
                            $discount_amount_ticket = 0;
                            $withheld_amount_ticket = 0;
                            $tax_amount_ticket = 0;
                            $final_total_ticket = 0;
                        @endphp
                        @if (count($ticket) > 0)
                            <tr>
                                <td colspan="12"><strong>Ticket</strong></td>
                            </tr>
                            @foreach ($ticket as $t)
                                @if ($t->doc_type == 'Ticket')
                                    @php
                                        if($t->payment_count <= 1 && $trans_id != $t->id){
                                            $flag = 0;
                                        }

                                        $ticket_cash += $t->cash_amount;
                                        $ticket_card += $t->card_amount;
                                        $ticket_check += $t->check_amount;
                                        $ticket_bank_transfer += $t->bank_transfer_amount;
                                        $ticket_credit += $t->credit_amount;
                                        $ticket_return += $t->return_amount;

                                        if($flag == 1){ continue; }

                                        /** determinate pay method */
                                        $pay_method = '';
                                        if($t->cash_amount > 0) $pay_method = 'cash';
                                        if($t->card_amount > 0) $pay_method = 'card';
                                        if($t->check_amount > 0) $pay_method = 'check';
                                        if($t->credit_amount > 0) $pay_method = 'credit';
                                        if($t->bank_transfer_amount > 0) $pay_method = 'bank_transfer';

                                        if ($t->payment_condition == 'sell_return') {
                                            $subtotal_ticket -= floatval($t->subtotal);
                                            $discount_amount_ticket -= floatval($t->discount_amount);
                                            $tax_amount_ticket -= floatval($t->tax_amount);
                                            $withheld_amount_ticket -= floatval($t->withheld_amount);
                                            $final_total_ticket -= floatval($t->final_total);
                                        } else {
                                            $subtotal_ticket += floatval($t->subtotal);
                                            $discount_amount_ticket += floatval($t->discount_amount);
                                            $tax_amount_ticket += floatval($t->tax_amount);
                                            $withheld_amount_ticket += floatval($t->withheld_amount);
                                            $final_total_ticket += floatval($t->final_total);
                                        }
                                        
                                        $row_counts ++;
                                    @endphp
                                    <tr>
                                        <td>{{ @format_date($t->transaction_date) }}</td>
                                        <td>{{ $t->correlative }}</td>
                                        <td>{{ $t->customer_name }}</td>
                                        <td>
                                            @if ($t->payment_count > 1)
                                                {{ __('payment.multiple') }}
                                            @else
                                                @if ($t->payment_condition == 'sell_return')
                                                    {{ __('sale.return') }}
                                                @elseif($t->payment_condition == 'credit' || $t->payment_condition == 'cash' )
                                                    {{ __('payment.' . $pay_method) }}
                                                @else
                                                    -
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($t->payment_condition == 'sell_return')
                                                -
                                            @else
                                                {{ $t->delivery_type ? __('report.' . $t->delivery_type) : __('report.counter') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($t->payment_condition == 'sell_return')
                                                -
                                            @else
                                                {{ __('messages.' . $t->payment_condition) }}
                                            @endif
                                        </td>
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
                            $exp_return = 0;
                            $subtotal_exp = 0;
                            $discount_amount_exp = 0;
                            $tax_amount_exp = 0;
                            $withheld_amount_exp = 0;
                            $final_total_exp = 0;
                        @endphp
                        @if (count($exp) > 0)
                            <tr>
                                <td colspan="12"><strong>Facturas de Exportación</strong></td>
                            </tr>
                            @foreach ($exp as $t)
                                @if ($t->doc_type == 'EXP')
                                    @php
                                        if($t->payment_count <= 1 && $trans_id != $t->id){
                                            $flag = 0;
                                        }

                                        $exp_cash += $t->cash_amount;
                                        $exp_card += $t->card_amount;
                                        $exp_check += $t->check_amount;
                                        $exp_bank_transfer += $t->bank_transfer_amount;
                                        $exp_credit += $t->credit_amount;
                                        $exp_return += $t->return_amount;

                                        if($flag == 1){ continue; }

                                        /** determinate pay method */
                                        $pay_method = '';
                                        if($t->cash_amount > 0) $pay_method = 'cash';
                                        if($t->card_amount > 0) $pay_method = 'card';
                                        if($t->check_amount > 0) $pay_method = 'check';
                                        if($t->credit_amount > 0) $pay_method = 'credit';
                                        if($t->bank_transfer_amount > 0) $pay_method = 'bank_transfer';

                                        if ($t->payment_condition == 'sell_return') {
                                            $subtotal_exp -= floatval($t->subtotal);
                                            $discount_amount_exp -= floatval($t->discount_amount);
                                            $tax_amount_exp -= floatval($t->tax_amount);
                                            $withheld_amount_exp -= floatval($t->withheld_amount);
                                            $final_total_exp -= floatval($t->final_total);
                                        } else {
                                            $subtotal_exp += floatval($t->subtotal);
                                            $discount_amount_exp += floatval($t->discount_amount);
                                            $tax_amount_exp += floatval($t->tax_amount);
                                            $withheld_amount_exp += floatval($t->withheld_amount);
                                            $final_total_exp += floatval($t->final_total);
                                        }

                                        $row_counts ++;
                                    @endphp
                                    <tr>
                                        <td>{{ @format_date($t->transaction_date) }}</td>
                                        <td>{{ $t->correlative }}</td>
                                        <td>{{ $t->customer_name }}</td>
                                        <td>
                                            @if ($t->payment_count > 1)
                                                {{ __('payment.multiple') }}
                                            @else
                                                @if ($t->payment_condition == 'sell_return')
                                                    {{ __('sale.return') }}
                                                @elseif($t->payment_condition == 'credit' || $t->payment_condition == 'cash' )
                                                    {{ __('payment.' . $pay_method) }}
                                                @else
                                                    -
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($t->payment_condition == 'sell_return')
                                                -
                                            @else
                                                {{ $t->delivery_type ? __('report.' . $t->delivery_type) : __('report.counter') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($t->payment_condition == 'sell_return')
                                                -
                                            @else
                                                {{ __('messages.' . $t->payment_condition) }}
                                            @endif
                                        </td>
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
                            <th style="width: 5%;">DEL</th>
                            <th style="width: 5%;">AL</th>
                            <th style="width: 11%;">CONTADO</th>
                            <th style="width: 8.5%;">CRÉDITO</th>
                            <th style="width: 8.5%;">TARJETA</th>
                            <th style="width: 8.5%;">CHEQUE</th>
                            <th style="width: 8.5%;">TRANSFER.</th>
                            <th style="width: 8.5%;">DEVOLUC.</th>
                            <th style="width: 12.5%;">TOTAL GENERAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>CCF</th>
                            <td class="_text-right">{{ count($ccf) > 0 ? min(array_column($ccf, 'correlative')) : '' }}</td>
                            <td class="_text-right">{{ count($ccf) > 0 ? max(array_column($ccf, 'correlative')) : '' }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_cash) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_credit) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_card) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_check) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_bank_transfer) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_return) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ccf_cash + $ccf_card + $ccf_credit + $ccf_check + $ccf_bank_transfer - $ccf_return) }}</td>
                        </tr>
                        <tr>
                            <th>FCF</th>
                            <td class="_text-right">{{ count($fcf) > 0 ? min(array_column($fcf, 'correlative')) : '' }}</td>
                            <td class="_text-right">{{ count($fcf) > 0 ? max(array_column($fcf, 'correlative')) : '' }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_cash) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_credit) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_card) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_check) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_bank_transfer) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_return) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($fcf_cash + $fcf_card + $fcf_credit + $fcf_check + $fcf_bank_transfer - $fcf_return) }}</td>
                        </tr>
                        <tr>
                            <th>TICKET</th>
                            <td class="_text-right">{{ count($ticket) > 0 ? min(array_column($ticket, 'correlative')) : '' }}</td>
                            <td class="_text-right">{{ count($ticket) > 0 ? max(array_column($ticket, 'correlative')) : '' }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_cash) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_credit) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_card) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_check) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_bank_transfer) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_return) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($ticket_cash + $ticket_card + $ticket_credit + $ticket_check + $ticket_bank_transfer - $ticket_return) }}</td>
                        </tr>
                        <tr>
                            <th>EXP</th>
                            <td class="_text-right">{{ count($exp) > 0 ? min(array_column($exp, 'correlative')) : '' }}</td>
                            <td class="_text-right">{{ count($exp) > 0 ? max(array_column($exp, 'correlative')) : '' }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_cash) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_credit) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_card) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_check) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_bank_transfer) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_return) }}</td>
                            <td class="_text-right">{{ "$ " . @num_format($exp_cash + $exp_card + $exp_credit + $exp_check + $exp_bank_transfer - $exp_return) }}</td>
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
                    $total_return = $ccf_return + $fcf_return + $ticket_return + $exp_return;
                @endphp
                <table id="thrid">
                    <tr> {{-- Totals table --}}
                        <td style="width: 4.1%;" rowspan="7">&nbsp;</td>
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
                        <th class="bordered">TICKET</th>
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
                        <th class="_text-left">Total Devoluciones:</th>
                        <th style="text-align: right; border-bottom: 1.5px solid #000;">
                            {{ "$ " . @num_format($total_return) }}
                        </th>
                        <td>&nbsp;</td>
                        <th class="bordered">TOTAL</th>
                        <th class="_text-right bordered">{{ "$" . @num_format( ($subtotal_ccf - $discount_amount_ccf) + ($subtotal_fcf - $discount_amount_fcf) + ($subtotal_ticket - $discount_amount_ticket) + ($subtotal_exp - $discount_amount_exp) ) }}</th>
                        <th class="_text-right bordered">{{ "$" . @num_format( $tax_amount_ccf + $tax_amount_fcf + $tax_amount_ticket + $tax_amount_exp ) }}</th>
                        <th class="_text-right bordered">{{ "$" . @num_format( $withheld_amount_ccf + $withheld_amount_fcf + $withheld_amount_ticket + $withheld_amount_exp ) }}</th>
                        <th class="_text-right bordered">{{ "$" . @num_format( $final_total_ccf + $final_total_fcf + $final_total_ticket + $final_total_exp ) }}</th>
                    </tr>
                    <tr>
                        <th class="_text-left">Total general:</th>
                        <th style="text-align: right; border-bottom: 1.5px solid #000;">
                            {{ "$ " . @num_format($total_cash + $total_credit + $total_card + $total_check + $total_bank_transfer - $total_return) }}
                        </th>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                </table><br>
                @php $total_expense = 0; @endphp
                @if ($show_expenses_on_sales_report > 0)
                    <table id="expenses" style="page-break-before: auto;">
                        <tr>
                            <td colspan="2" style="text-align: center; font-weight: bold;">{{ mb_strtoupper('Gastos') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; text-align: center;">{{ mb_strtoupper('Tipo') }}</td>
                            <td style="font-weight: bold; text-align: center; width: 33%;">{{ mb_strtoupper('Monto') }}</td>
                        </tr>
                        @forelse ($expenses as $e)
                        <tr>
                            <td>{{ $e->category }}</td>
                            <td style="text-align: right;">{{ '$ '. @num_format($e->total) }}</td>
                            @php $total_expense += $e->total; @endphp
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2">Sin gastos</td>
                        </tr>
                        @endforelse
                        <tr style="background-color: beige">
                            <td style="font-weight: bold;">TOTAL FINAL</td>
                            <td style="text-align: right; font-weight: bold;">{{ '$ '. @num_format(($total_cash + $total_credit + $total_card + $total_check + $total_bank_transfer - $total_return) - $total_expense ) }}</td>
                        </tr>
                    </table>
                @endif
            </div>
        </div>
    </body>
</html>